<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use App\Http\Traits\Notify;
use App\Http\Traits\Upload;
use App\Models\VoucherService;
use App\Models\VoucherSell;
use App\Models\Gateway;
use Illuminate\Http\Request;
use Facades\App\Services\BasicService;

class VoucherController extends Controller
{
    use Upload, Notify;

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
        $this->theme = template();
    }

    public function voucherOrder()
    {
        $data['voucherOrders'] = $this->user->voucher()->wherePayment_status(1)->orderBy('id', 'DESC')->paginate(config('basic.paginate'));
        return view($this->theme . 'user.voucher.index', $data);
    }

    public function voucherSearch(Request $request)
    {
        $search = $request->all();
        $dateSearch = $request->datetrx;
        $date = preg_match("/^[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $dateSearch);
        $voucherOrders = VoucherSell::where('user_id', $this->user->id)->with('user')
            ->when(@$search['transaction_id'], function ($query) use ($search) {
                return $query->where('transaction', 'LIKE', "%{$search['transaction_id']}%");
            })
            ->when($date == 1, function ($query) use ($dateSearch) {
                return $query->whereDate("created_at", $dateSearch);
            })
            ->paginate(config('basic.paginate'));
        $voucherOrders = $voucherOrders->appends($search);


        return view($this->theme . 'user.voucher.index', compact('voucherOrders'));

    }

    public function voucherPayment(Request $request)
    {

        $this->validate($request, [
            'gateway' => ['required', 'numeric'],
            'service' => ['required', 'numeric']
        ], [
            'gateway.required' => 'Please select a payment method',
            'service.required' => 'Please select a recharge option'
        ]);

        $service = VoucherService::with('voucher')->whereStatus(1)
            ->whereHas('voucher', function ($query) {
                $query->where('status', 1);
            })->findOrFail($request->service);

        $serviceVoucher = $service->voucher;

        if ($request->gateway == '0') {
            $wallet['name'] = "Wallet";
            $wallet['id'] = 0;
            $wallet['fixed_charge'] = 0;
            $wallet['percentage_charge'] = 0;
            $wallet['convention_rate'] = 1;
            $wallet['currencies'] = (object)[
                '0' => (object)[
                    config('basic.currency') => config('basic.currency')
                ]
            ];
            $wallet['currency'] = config('basic.currency');
            $gate = (object)$wallet;
        } else {
            $gate = Gateway::where('status', 1)->findOrFail($request->gateway);
        }
        $discount = 0;
        if ($serviceVoucher->discount_status == 1) {
            if ($serviceVoucher->discount_type == 0) {
                $discount = $serviceVoucher->discount_amount; // fixed Discount
            } else {
                $discount = ($service->price * $serviceVoucher->discount_amount) / 100; // percent Discount
            }
        }

        $user = $this->user;
        $reqAmount = $service->price - $discount;

        if ($request->gateway == '0' && $user->balance < $reqAmount) {
            return back()->with('error', 'Insufficient Wallet Balance')->withInput();
        }
        $charge = getAmount($gate->fixed_charge + ($reqAmount * $gate->percentage_charge / 100));
        $payable = getAmount($reqAmount + $charge);
        $final_amo = getAmount($payable * $gate->convention_rate);

        $voucherSell = new VoucherSell();
        $voucherSell->user_id = $user->id;
        $voucherSell->voucher_service_id = $service->id;
        $voucherSell->voucher_id = $serviceVoucher->id;
        $voucherSell->price = $reqAmount;
        $voucherSell->discount = $discount;
        $voucherSell->qty = 1;
        $voucherSell->transaction = strRandom();
        $collection = collect($request);

        $voucherSell->save();

        if ($request->gateway == '0' && $user->balance >= $reqAmount) {
            $user->balance -= $reqAmount;
            $user->save();

            $lackingCode = 0;
            $qty = $voucherSell->qty;
            $vCodes = $voucherSell->service->voucherActiveCodes->take($qty);
            if ($qty > count($vCodes)) {
                $lackingCode = $qty - count($vCodes);
            }
            $orderCode = [];
            $i = 0;

            foreach ($vCodes as $vCode) {
                if ($i < $qty) {
                    array_push($orderCode, $vCode->code);
                    $vCode->status = 2;
                    $vCode->save();
                }
                $i++;
            }

            $voucherSell->stock_short = $lackingCode;
            $voucherSell->code = $orderCode;
            $voucherSell->status = 1;
            $voucherSell->payment_status = 1;
            $voucherSell->save();

            BasicService::makeTransaction($user, getAmount($reqAmount), getAmount($charge), '-', $voucherSell->transaction, (optional($voucherSell->voucher)->details->name) ?? 'Voucher');

            session()->flash('success', 'Your order has been processed');
            return redirect()->route('user.voucherOrder');
        } else {
            $fund = PaymentController::staticNewFund($request, $user, $gate, $charge, $final_amo, $reqAmount);
            $voucherSell->fundable()->save($fund);
            session()->put('track', $fund['transaction']);
            return redirect()->route('user.addFund.confirm');
        }
    }
}
