@extends($theme.'layouts.user')
@section('title', trans($title))

@section('content')
    <section class="login-section">
        <div class="container ">
            <div class="row">
                <div class="col-md-4 shadow-none p-3 bg-gradient rounded">
                    <div class="card-preview card-type-1 text-center">
                        <ul class="list-grouppreview-">
                            <li class="preview-list-group-item font-weight-bold bg-transparent">
                                <img
                                    src="{{getFile(config('location.withdraw.path').optional($withdraw->method)->image)}}"
                                    class="card-img-top w-50" alt="{{optional($withdraw->method)->name}}">
                            </li>
                            <li class="preview-list-group-item font-weight-bold bg-transparent">@lang('Request Amount')
                                :
                                <span
                                    class="float-right text-success">{{@$basic->currency_symbol}}{{getAmount($withdraw->amount)}} </span>
                            </li>
                            <li class="preview-list-group-item font-weight-bold bg-transparent">@lang('Charge Amount') :
                                <span
                                    class="float-right text-danger">{{@$basic->currency_symbol}}{{getAmount($withdraw->charge)}} </span>
                            </li>
                            <li class="preview-list-group-item font-weight-bold bg-transparent">@lang('Total Payable') :
                                <span
                                    class="float-right text-danger">{{@$basic->currency_symbol}}{{getAmount($withdraw->net_amount)}} </span>
                            </li>
                            <li class="preview-list-group-item font-weight-bold bg-transparent">@lang('Available Balance')
                                :
                                <span
                                    class="float-right text-success">{{@$basic->currency_symbol}}{{$remaining}} </span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="custom-card card-lg bg-gradient">
                        <div class="card-header custom-header text-center">
                            <h5 class="card-title mt-3 mb-3">@lang('Additional Information To Withdraw Confirm')</h5>
                        </div>
                        <div class="card-body gradient-bg">
                            <div class="contact-box mt-2">
                                <form @if($layout == 'layouts.payment') action="{{route('user.payout.submit',$billId)}}"
                                      @else action="" @endif method="post" enctype="multipart/form-data"
                                      class="form-row text-left preview-form">
                                    @csrf

                                    @if($payoutMethod->supported_currency)
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <div class="form-group input-box search-currency-dropdown">
                                                    <label for="from_wallet">@lang('Select Bank Currency')</label>
                                                    <select id="from_wallet" name="currency_code"
                                                            class="form-control form-control-sm transfer-currency"
                                                            required>
                                                        <option value="" disabled=""
                                                                selected="">@lang('Select Currency')</option>
                                                        @foreach($payoutMethod->supported_currency as $singleCurrency)
                                                            <option
                                                                value="{{$singleCurrency}}"
                                                                @foreach($payoutMethod->convert_rate as $key => $rate)
                                                                    @if($singleCurrency == $key) data-rate="{{$rate}}" @endif
                                                                @endforeach {{old('transfer_name') == $singleCurrency ?'selected':''}}>{{$singleCurrency}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('currency_code')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($payoutMethod->code == 'paypal')
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group input-box search-currency-dropdown">
                                                    <label for="from_wallet">@lang('Select Recipient Type')</label>
                                                    <select id="from_wallet" name="recipient_type"
                                                            class="form-control form-control-sm mb-3" required>
                                                        <option value="" disabled=""
                                                                selected="">@lang('Select Recipient')</option>
                                                        <option value="EMAIL">@lang('Email')</option>
                                                        <option value="PHONE">@lang('phone')</option>
                                                        <option value="PAYPAL_ID">@lang('Paypal Id')</option>
                                                    </select>
                                                    @error('recipient_type')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if(optional($withdraw->method)->inputForm)
                                        @foreach($withdraw->method->inputForm as $k => $v)
                                            @if($v->type == "text")
                                                <div class="col-md-12">
                                                    <div class="form-group mt-2">
                                                        <label><strong>@lang(@$v->label??$v->field_level) @if($v->validation == 'required')
                                                                    <span class="text-danger">*</span>
                                                                @endif</strong></label>
                                                        <input type="text" name="{{$k}}"
                                                               class="form-control"
                                                               @if($v->validation == "required") required @endif>
                                                        @if ($errors->has($k))
                                                            <span
                                                                class="text-danger">{{ trans($errors->first($k)) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif($v->type == "textarea")
                                                <div class="col-md-12">
                                                    <div class="form-group mt-2">
                                                        <label><strong>@lang(@$v->label??$v->field_level) @if($v->validation == 'required')
                                                                    <span class="text-danger">*</span>
                                                                @endif
                                                            </strong></label>
                                                        <textarea name="{{$k}}" class="form-control" rows="3"
                                                                  @if($v->validation == "required") required @endif></textarea>
                                                        @if ($errors->has($k))
                                                            <span
                                                                class="text-danger">{{ trans($errors->first($k)) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif($v->type == "file")

                                                <div class="col-md-12">
                                                    <label><strong>@lang(@$v->label??$v->field_level) @if($v->validation == 'required')
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </strong></label>

                                                    <div class="form-group mt-2">
                                                        <div class="fileinput fileinput-new " data-provides="fileinput">
                                                            <div
                                                                class="fileinput-new thumbnail withdraw-thumbnail fileinput-preview wh-200-150"
                                                                data-trigger="fileinput">
                                                                <img class="w-100 "
                                                                     src="{{ getFile(config('location.default')) }}"
                                                                     alt="...">
                                                            </div>

                                                            <div class="img-input-div">
                                                                <span class="btn btn-info btn-file text-white">
                                                                    <span
                                                                        class="fileinput-new "> @lang('Select') {{@$v->label??$v->field_level}}</span>
                                                                    <span
                                                                        class="fileinput-exists"> @lang('Change')</span>
                                                                    <input type="file" name="{{$k}}" accept="image/*"
                                                                           @if($v->validation == "required") required @endif>
                                                                </span>
                                                                <a href="#"
                                                                   class="btn btn-danger text-white fileinput-exists"
                                                                   data-dismiss="fileinput"> @lang('Remove')</a>
                                                            </div>

                                                        </div>
                                                        @if ($errors->has($k))
                                                            <br>
                                                            <span
                                                                class="text-danger">{{ __($errors->first($k)) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif

                                    <div class="col-md-12">
                                        <div class=" form-group mt-4">
                                            <button type="submit" class="btn btn-custom btn-lg w-100">
                                                <span>@lang('Confirm Now')</span>
                                            </button>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('css-lib')
    <link rel="stylesheet" href="{{asset($themeTrue.'css/bootstrap-fileinput.css')}}">
@endpush

@push('extra-js')
    <script src="{{asset($themeTrue.'js/bootstrap-fileinput.js')}}"></script>
@endpush

@push('script')

@endpush

