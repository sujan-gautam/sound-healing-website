<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\Upload;
use App\Models\PayoutMethod;
use Illuminate\Http\Request;

class PayoutGatewayController extends Controller
{
    use Upload;

    public function index()
    {
        $data['page_title'] = "Payout Method";
        $data['methods'] = PayoutMethod::all();
        return view('admin.payout.index', $data);
    }

    public function create()
    {
        $page_title = "Add Payout Method ";
        return view('admin.payout.create', compact('page_title'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'minimum_amount' => 'required|numeric',
            'maximum_amount' => 'required|numeric',
            'fixed_charge' => 'required|numeric',
            'percent_charge' => 'required|numeric',
        ];

        $this->validate($request, $rules);


        $gateway = new PayoutMethod();
        $gateway->methodName = $request->name;
        $gateway->min_limit = $request->minimum_amount;
        $gateway->max_limit = $request->maximum_amount;
        $gateway->percentage_charge = $request->percent_charge;
        $gateway->fixed_charge = $request->fixed_charge;
        $gateway->is_active = $request->status;

        $input_form = [];
        if ($request->has('field_name')) {
            for ($a = 0; $a < count($request->field_name); $a++) {
                $arr = array();
                $arr['field_name'] = clean($request->field_name[$a]);
                $arr['label'] = $request->field_name[$a];
                $arr['type'] = $request->type[$a];
                $arr['validation'] = $request->validation[$a];
                $input_form[$arr['field_name']] = $arr;
            }
        }

        $gateway->inputForm = $input_form;

        if ($request->hasFile('image')) {

            try {
                $gateway->logo = $this->uploadImage($request->image, config('location.withdraw.path'), config('location.withdraw.size'));
            } catch (\Exception $exp) {
                return back()->with('error', 'Image could not be uploaded.');
            }
        }

        $gateway->save();
        session()->flash('success', 'Saved Successfully');
        return back();
    }


    public function edit($id)
    {
        $method = PayoutMethod::findOrFail($id);
        $page_title = $method->methodName;
        return view('admin.payout.edit', compact('method', 'page_title'));
    }

    public function update(Request $request, $id)
    {
        $gateway = PayoutMethod::findOrFail($id);

        $rules = [
            'name' => 'required',
            'minimum_amount' => 'required|numeric',
            'maximum_amount' => 'required|numeric',
            'fixed_charge' => 'required|numeric',
            'percent_charge' => 'required|numeric',
        ];

        $input_form = [];
        if ($request->has('field_name')) {
            for ($a = 0; $a < count($request->field_name); $a++) {
                $arr = array();
                $arr['name'] = clean($request->field_name[$a]);
                $arr['label'] = $request->field_name[$a];
                $arr['type'] = $request->type[$a];
                $arr['validation'] = $request->validation[$a];
                $input_form[$arr['name']] = $arr;
            }
        }
        $parameters = [];
        if ($gateway->parameters) {
            foreach ($request->except('_token', '_method', 'image') as $k => $v) {
                foreach ($gateway->parameters as $key => $cus) {
                    if ($k != $key) {
                        continue;
                    } else {
                        $rules[$key] = 'required|max:191';
                        $parameters[$key] = $v;
                    }
                }
            }
        }

        $supported_params = [];
        if ($request->has('supported_currency')) {
            foreach ($request->supported_currency as $k => $v) {
                $supported_params[$v] = $v;
            }
        }

        $collectionSpecification = collect($request->rate);
        $rate_params = [];
        if ($gateway->supported_currency) {
            foreach ($collectionSpecification as $k => $v) {
                foreach ($gateway->supported_currency as $key => $cus) {
                    if ($k != $key) {
                        continue;
                    } else {
                        if ($v == null) {
                            $v = 1.00;
                        }
                        $rate_params[$key] = $v;
                    }
                }
            }
        }
        $this->validate($request, $rules);

        $gateway->methodName = $request->name;
        $gateway->min_limit = $request->minimum_amount;
        $gateway->max_limit = $request->maximum_amount;
        $gateway->percentage_charge = $request->percent_charge;
        $gateway->fixed_charge = $request->fixed_charge;
        $gateway->is_active = $request->status;
        $gateway->banks = @$request->banks;
        $gateway->parameters = @$parameters;
        $gateway->supported_currency = $supported_params;
        $gateway->convert_rate = $rate_params;
        $gateway->environment = @$request->environment;
        if ($gateway->is_automatic == 0) {
            $gateway->inputForm = (empty($input_form)) ? null : $input_form;
        }

        if ($request->hasFile('image')) {
            try {
                $old = $gateway->logo ?: null;
                $image = $this->uploadImage($request->image, config('location.withdraw.path'), config('location.withdraw.size'), $old);
                $gateway->logo = $image ?: $gateway->image;
            } catch (\Exception $exp) {
                return back()->with('error', 'Image could not be uploaded.');
            }
        }

        $gateway->save();
        session()->flash('success', 'Update Successfully');
        return back();
    }
}
