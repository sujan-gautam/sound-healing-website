<?php

namespace App\Services\Payout\paystack;

use App\Models\Payout;
use App\Models\PayoutLog;
use App\Models\PayoutMethod;
use App\Models\Transaction;
use Facades\App\Services\BasicCurl;

class Card
{
    public static function getBank($currencyCode)
    {
        $method = PayoutMethod::where('code', 'paystack')->first();
        $url = 'https://api.paystack.co/bank/?currency=' . strtoupper($currencyCode);
        $SEC_KEY = optional($method->parameters)->Secret_key;
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

        if ($result->status == 'true') {
            return [
                'status' => 'success',
                'data' => $result->data
            ];
        } else {
            return [
                'status' => 'error',
                'data' => $result->message
            ];
        }
    }

    public static function payouts($payout)
    {
        $method = PayoutMethod::where('code', 'paystack')->first();
        $api = 'https://api.paystack.co/';
        $SEC_KEY = optional($method->parameters)->Secret_key;
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $SEC_KEY
        ];

        $card = new Card();
        $res = $card->createRecipient($api, $headers, $payout);
        if ($res['status'] == 'error') {
            return [
                'status' => 'error',
                'data' => $res['data']
            ];
        }

        $recipient_code = $res['data'];
        $url = $api . 'transfer';
        $info = $payout->information;
        $amount = (int)$info->amount->fieldValue;

        $postParam = [
            "source" => 'balance',
            "amount" => $amount,
            "recipient" => $recipient_code,
        ];
        $response = BasicCurl::curlPostRequestWithHeaders($url, $headers, $postParam);
        $result = json_decode($response);

        if ($result->status == false) {
            return [
                'status' => 'error',
                'data' => $result->message
            ];
        } elseif ($result->status == true) {
            return [
                'status' => 'success',
                'response_id' => $result->data->id
            ];
        }

    }

    public static function createRecipient($api, $headers, $payout)
    {
        $url = $api . 'transferrecipient';
        $info = $payout->information;

        $postParam = [
            "type" => $info->type->fieldValue,
            "name" => $info->name->fieldValue,
            "account_number" => $info->account_number->fieldValue,
            "bank_code" => $info->bank_code->fieldValue,
            "currency" => $payout->currency_code,
        ];
        $response = BasicCurl::curlPostRequestWithHeaders($url, $headers, $postParam);
        $result = json_decode($response);

        if ($result->status == false) {
            return [
                'status' => 'error',
                'data' => $result->message
            ];
        } elseif ($result->status == true) {
            return [
                'status' => 'success',
                'data' => $result->data->recipient_code
            ];
        }
    }

    public static function getResponse($apiResponse)
    {
        $basic = (object)config('basic');
        if ($apiResponse) {
            if ($apiResponse->data) {
                $payout = PayoutLog::with('user')->where('response_id', $apiResponse->data->id)->first();
                $user = $payout->user;
                if ($payout) {
                    if ($apiResponse->event == 'transfer.success') {
                        $payout->status = 2;
                        $payout->save();
                        return [
                            'status' => 'success',
                            'payout' => $payout,
                        ];
                    } elseif ($apiResponse->event == 'transfer.failed') {
                        $payout->status = 4;
                        $payout->last_error = $apiResponse->summary;
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
