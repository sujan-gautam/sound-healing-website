<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use App\Http\Traits\Notify;
use App\Http\Traits\Upload;
use App\Models\GiftCardSell;
use App\Models\GiftCardService;
use App\Models\Gateway;
use Illuminate\Http\Request;
use Facades\App\Services\BasicService;

class GiftCardController extends Controller
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

    public function giftCardOrder()
    {
        $data['giftCardOrders'] = $this->user->giftCard()->wherePayment_status(1)->orderBy('id', 'DESC')->paginate(config('basic.paginate'));
        return view($this->theme . 'user.gift_card.index', $data);
    }

    public function giftCardSearch(Request $request)
    {
        $search = $request->all();
        $dateSearch = $request->datetrx;
        $date = preg_match("/^[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $dateSearch);
        $giftCardOrders = GiftCardSell::where('user_id', $this->user->id)->with('user')
            ->when(@$search['transaction_id'], function ($query) use ($search) {
                return $query->where('transaction', 'LIKE', "%{$search['transaction_id']}%");
            })
            ->when($date == 1, function ($query) use ($dateSearch) {
                return $query->whereDate("created_at", $dateSearch);
            })
            ->paginate(config('basic.paginate'));
        $giftCardOrders = $giftCardOrders->appends($search);


        return view($this->theme . 'user.gift_card.index', compact('giftCardOrders'));

    }

    public function giftCardPayment(Request $request)
    {

        $this->validate($request, [
            'gateway' => ['required', 'numeric'],
            'service' => ['required', 'numeric']
        ], [
            'gateway.required' => 'Please select a payment method',
            'service.required' => 'Please select a recharge option'
        ]);

        $service = GiftCardService::with('giftCard')->whereStatus(1)
            ->whereHas('giftCard', function ($query) {
                $query->where('status', 1);
            })->findOrFail($request->service);

        $serviceGiftCard = $service->giftCard;

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
        if ($serviceGiftCard->discount_status == 1) {
            if ($serviceGiftCard->discount_type == 0) {
                $discount = $serviceGiftCard->discount_amount; // fixed Discount
            } else {
                $discount = ($service->price * $serviceGiftCard->discount_amount) / 100; // percent Discount
            }
        }

        $user = $this->user;
        $reqAmount = $service->price - $discount;

        if ($request->gateway == '0' && $user->balance < $reqAmount) {
            return back()->with('error', 'Insufficient Wallet Balance')->withInput();
        }

        $reqAmount = $service->price - $discount;
        $charge = getAmount($gate->fixed_charge + ($reqAmount * $gate->percentage_charge / 100));
        $payable = getAmount($reqAmount + $charge);
        $final_amo = getAmount($payable * $gate->convention_rate);

        $giftCardSell = new GiftCardSell();
        $giftCardSell->user_id = $user->id;
        $giftCardSell->gift_card_service_id = $service->id;
        $giftCardSell->gift_card_id = $serviceGiftCard->id;
        $giftCardSell->price = $reqAmount;
        $giftCardSell->qty = 1;
        $giftCardSell->discount = $discount;
        $giftCardSell->transaction = strRandom();
        $collection = collect($request);

        $giftCardSell->save();

        if ($request->gateway == '0' && $user->balance >= $reqAmount) {

            $user->balance -= $reqAmount;
            $user->save();

            $lackingCode = 0;
            $qty = $giftCardSell->qty;
            $vCodes = $giftCardSell->service->giftCardActiveCodes->take($qty);
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

            $giftCardSell->stock_short = $lackingCode;
            $giftCardSell->code = $orderCode;
            $giftCardSell->status = 1;
            $giftCardSell->payment_status = 1;
            $giftCardSell->save();

            BasicService::makeTransaction($user, getAmount($reqAmount), getAmount($charge), '-', $giftCardSell->transaction, (optional($giftCardSell->giftCard)->details->name) ?? 'Giftcard');
            session()->flash('success', 'Your order has been processed');
            return redirect()->route('user.giftCardOrder');
        } else {
            $fund = PaymentController::staticNewFund($request, $user, $gate, $charge, $final_amo, $reqAmount);
            $giftCardSell->fundable()->save($fund);
            session()->put('track', $fund['transaction']);
            return redirect()->route('user.addFund.confirm');

        }
    }

}
