<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\Notify;
use App\Models\PayoutLog;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PayoutRecordController extends Controller
{
    use Notify;

    public function index()
    {
        $page_title = "Payout Logs";
        $records = PayoutLog::where('status', '!=', 0)->orderBy('id', 'DESC')->with('user', 'method')->paginate(config('basic.paginate'));
        return view('admin.payout.logs', compact('records', 'page_title'));
    }


    public function request()
    {
        $page_title = "Payout Request";
        $records = PayoutLog::where('status', 1)->orderBy('id', 'DESC')->with('user', 'method')->paginate(config('basic.paginate'));
        return view('admin.payout.logs', compact('records', 'page_title'));
    }

    public function view($id)
    {
        $data['payout'] = PayoutLog::findOrFail($id);
        return view('admin.payout.view', $data);
    }

    public function search(Request $request)
    {
        $search = $request->all();
        $dateSearch = $request->date_time;
        $date = preg_match("/^[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $dateSearch);

        $records = PayoutLog::when(isset($search['name']), function ($query) use ($search) {
            return $query->where('trx_id', 'LIKE', $search['name'])
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('email', 'LIKE', "%{$search['name']}%")
                        ->orWhere('username', 'LIKE', "%{$search['name']}%");
                });

        })
            ->when($date == 1, function ($query) use ($dateSearch) {
                return $query->whereDate("created_at", $dateSearch);
            })
            ->when(isset($search['status']), function ($query) use ($search) {
                return $query->where('status', $search['status']);
            })
            ->where('status', '!=', 0)
            ->with('user', 'method')
            ->paginate(config('basic.paginate'));
        $records->appends($search);

        $page_title = "Search Payout Logs";
        return view('admin.payout.logs', compact('records', 'page_title'));
    }

    public function action(Request $request, $id)
    {

        $this->validate($request, [
            'status' => ['required', Rule::in(['2', '3'])],
        ]);

        $data = PayoutLog::where('id', $request->id)->whereIn('status', [1])->with('user', 'method')->firstOrFail();
        $basic = (object)config('basic');
        $method = $data->method;

        if ($request->status == '2') {

            if ($method->is_automatic == 1) {
                $methodObj = 'App\\Services\\Payout\\' . $method->code . '\\Card';
                $info = $methodObj::payouts($data);
                if (!$info) {
                    return back()->with('error', 'Method not available or unknown errors occur');
                }

                if ($info['status'] == 'error') {
                    $data->last_error = $info['data'];
                    $data->save();
                    return back()->with('error', $info['data']);
                }
            }
            if ($method->is_automatic == 0) {
                $data->status = 2;
                $data->feedback = $request->feedback;
                $data->save();
                $this->userSuccessNotify($data);
            } else {
                if ($method->code == 'coinbase' || $method->code == 'perfectmoney') {
                    $data->feedback = $request->feedback;
                    $data->status = 2;
                    $data->save();
                    $this->userSuccessNotify($data, 1);
                } else {
                    $data->feedback = $request->feedback;
                    $data->response_id = $info['response_id'];
                    $data->save();
                }
            }

            session()->flash('success', 'Approve Successfully');
            return back();

        } elseif ($request->status == '3') {

            $data->status = 3;
            $data->feedback = $request->feedback;
            $data->save();

            $user = $data->user;
            $user->balance += $data->net_amount;
            $user->save();

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = getAmount($data->net_amount);
            $transaction->final_balance = $user->balance;
            $transaction->charge = $data->charge;
            $transaction->trx_type = '+';
            $transaction->remarks = getAmount($data->amount) . ' ' . $basic->currency . ' withdraw amount has been refunded';
            $transaction->trx_id = $data->trx_id;
            $transaction->save();


            $this->userSuccessNotify($data, 0);
            session()->flash('success', 'Reject Successfully');
            return back();
        }
    }

    public function payout(Request $request, $code)
    {
        $apiResponse = json_decode($request->all());
        $methodObj = 'App\\Services\\Payout\\' . $code . '\\Card';
        $data = $methodObj::getResponse($apiResponse);
        if (!$data) {
            return false;
        }

        if ($data['status'] == 'success') {
            $this->userSuccessNotify($data['payout'], 1);
        } elseif ($data['status'] == 'rejected') {
            $this->userSuccessNotify($data['payout'], 0);
        }
    }

    public function userSuccessNotify($data, $type = 1)
    {
        $user = $data->user;
        $basic = (object)config('basic');
        try {
            $this->sendMailSms($user, 'PAYOUT_APPROVE', [
                'method' => optional($data->method)->name,
                'amount' => getAmount($data->amount),
                'charge' => getAmount($data->charge),
                'currency' => $basic->currency,
                'transaction' => $data->trx_id,
                'feedback' => $data->feedback
            ]);


            $msg = [
                'amount' => getAmount($data->amount),
                'currency' => $basic->currency,
            ];
            $action = [
                "link" => '#',
                "icon" => "fa fa-money-bill-alt "
            ];

            if ($type == 1) {
                $template = 'PAYOUT_APPROVE';
            } else {
                $template = 'PAYOUT_REJECTED';
            }

            $this->userPushNotification($user, $template, $msg, $action);
        } catch (\Exception $e) {
            return 0;
        }

        return 0;
    }

}
