<?php

namespace App\Services\Payout\flutterwave;

use App\Models\Payout;
use App\Models\PayoutLog;
use App\Models\PayoutMethod;
use App\Models\Transaction;
use Facades\App\Services\BasicCurl;

class Card
{
    public static function getBank($countryCode)
    {
        $method = PayoutMethod::where('code', 'flutterwave')->first();
        $url = 'https://api.flutterwave.com/v3/banks/' . strtoupper($countryCode);
        $SEC_KEY = optional($method->parameters)->Secret_Key;
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $SEC_KEY
        ];


        $response = BasicCurl::curlGetRequestWithHeaders($url, $headers);
        $result = json_decode($response);

        if (!isset($result->status)) {
            return [
                'status' => 'error',
                'data' => 'Something went wrong try again'
            ];
        }

        if ($result->status == 'error') {
            return [
                'status' => 'error',
                'data' => $result->message
            ];
        } elseif ($result->status == 'success') {
            return [
                'status' => 'success',
                'data' => $result->data
            ];
        }
    }

    public static function payouts($payout)
    {
        $method = PayoutMethod::where('code', 'flutterwave')->first();
        $url = 'https://api.flutterwave.com/v3/transfers';
        $SEC_KEY = optional($method->parameters)->Secret_Key;
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $SEC_KEY
        ];

        $postParam['currency'] = $payout->currency_code;
        foreach ($payout->information as $key => $info) {
            $postParam[$key] = $info->fieldValue;
        }
        if ($payout->meta_field) {
            foreach ($payout->meta_field as $key => $info) {
                $postParam['meta'][$key] = $info->fieldValue;
            }
        }
        $postParam['amount'] = (int)$postParam['amount'];
        $postParam['callback_url'] = route('payout', $method->code);

        $response = BasicCurl::curlPostRequestWithHeaders($url, $headers, $postParam);
        $result = json_decode($response);

        if (isset($result->status) && $result->status == 'error') {
            return [
                'status' => 'error',
                'data' => $result->message
            ];
        } elseif (isset($result->status) && $result->status == 'success') {
            return [
                'status' => 'success',
                'response_id' => $result->data->id
            ];
        }
    }

    public static function getResponse($apiResponse)
    {
        $basic = (object)config('basic');
        if ($apiResponse) {
            if ($apiResponse->event == 'transfer.completed') {
                if ($apiResponse->data) {
                    $payout = PayoutLog::with('user')->where('response_id', $apiResponse->data->id)->first();
                    $user = $payout->user;
                    if ($payout) {
                        if ($apiResponse->data->status == 'SUCCESSFUL') {
                            $payout->status = 2;
                            $payout->save();
                            return [
                                'status' => 'success',
                                'payout' => $payout,
                            ];
                        }
                        if ($apiResponse->data->status == 'FAILED') {
                            $payout->status = 4;
                            $payout->last_error = $apiResponse->data->complete_message;
                            $payout->save();

                            $user->balance += $payout->net_amount;
                            $user->save();

                            $transaction = new Transaction();
                            $transaction->user_id = $user->id;
                            $transaction->amount = getAmount($payout->net_amount);
                            $transaction->final_balance = $user->balance;
                            $transaction->charge = $payout->charge;
                            $transaction->trx_type = '+';
                            $transaction->remarks = getAmount($payout->amount) . ' ' . $basic->currency . ' withdraw amount has been refunded';
                            $transaction->trx_id = $payout->trx_id;
                            $transaction->save();

                            return [
                                'status' => 'rejected',
                                'payout' => $payout,
                            ];
                        }
                    }
                }
            }
        }
    }
}
