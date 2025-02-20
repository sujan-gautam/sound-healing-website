@extends($theme.'layouts.user')
@section('title')
    @lang('Add Fund')
@endsection
@section('content')

    <section class="login-section">
        <div class="container">
            <div class="custom-card bg-gradient">
                <div class="card-body gradient-bg">
                    <div class="row">

                        @foreach($gateways as $key => $gateway)
                            <div class="col-xl-2 col-lg-3 col-md-3 col-sm-6 mb-4">
                                <div class="card card-type-1 text-center">
                                    <div class="card-icon">
                                        <img
                                            src="{{ getFile(config('location.gateway.path').$gateway->image)}}"
                                            alt="{{$gateway->name}}" class="w-100 gateway ">
                                    </div>

                                    <button type="button"
                                            data-id="{{$gateway->id}}"
                                            data-name="{{$gateway->name}}"
                                            data-currency="{{$gateway->currency}}"
                                            data-gateway="{{$gateway->code}}"
                                            data-min_amount="{{getAmount($gateway->min_amount, $basic->fraction_number)}}"
                                            data-max_amount="{{getAmount($gateway->max_amount,$basic->fraction_number)}}"
                                            data-percent_charge="{{getAmount($gateway->percentage_charge,$basic->fraction_number)}}"
                                            data-fix_charge="{{getAmount($gateway->fixed_charge, $basic->fraction_number)}}"
                                            class="btn btn-site btn-block  w-100 addFund"
                                            data-bs-backdrop='static' data-bs-keyboard='false'
                                            data-bs-toggle="modal" data-bs-target="#addFundModal">@lang('Pay Now')</button>

                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </section>





    {{--    @push('loadModal')--}}
    <div id="addFundModal" class="modal fade addFundModal" data-bs-backdrop="static" data-bs-keyboard="false"  tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content form-block">
                <div class="modal-header-custom modal-colored-header bg-custom">
                    <h4 class="modal-title method-name" id="myModalLabel">@lang('Pay Out')</h4>
                    <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>

                <div class="modal-body-custom">
                    <div class="payment-form ">
                        @if(0 == $totalPayment)
                            <p class="text-danger depositLimit"></p>
                            <p class="text-danger depositCharge"></p>
                        @endif

                        <input type="hidden" class="gateway" name="gateway" value="">


                        <div class="form-group mb-30">
                            <label>@lang('Amount')</label>
                            <div class="input-group">
                                <input type="text" class="amount form-control earn" name="amount"
                                       @if($totalPayment != null) value="{{$totalPayment}}" readonly @endif>
                                <div class="input-group-append">
                                    <span class="btn btn-success-custom copy-btn  show-currency"></span>
                                </div>
                            </div>
                            <pre class="text-danger errors"></pre>
                        </div>


                    </div>

                    <div class="payment-info text-center">
                        <img class="w-15" id="loading" src="{{asset('assets/admin/images/loading.gif')}}" alt="..."/>
                    </div>
                </div>
                <div class="modal-footer-custom ">
                    <button type="button" class="btn btn-success-custom checkCalc">@lang('Next')</button>
                </div>

            </div>
        </div>
    </div>
    {{--    @endpush--}}


@endsection



@push('script')

    @if(count($errors) > 0 )
        <script>
            @foreach($errors->all() as $key => $error)
            Notiflix.Notify.Failure("@lang($error)");
            @endforeach
        </script>
    @endif


    <script>
        "use strict";
        $('#loading').hide();
        var id, minAmount, maxAmount, baseSymbol, fixCharge, percentCharge, currency, amount, gateway;
        $('.addFund').on('click', function () {

            console.log($(this).data())
            id = $(this).data('id');
            gateway = $(this).data('gateway');
            minAmount = $(this).data('min_amount');
            maxAmount = $(this).data('max_amount');
            baseSymbol = "{{config('basic.currency_symbol')}}";
            fixCharge = $(this).data('fix_charge');
            percentCharge = $(this).data('percent_charge');
            currency = $(this).data('currency');
            $('.depositLimit').text(`@lang('Transaction Limit:') ${minAmount} - ${maxAmount}  ${baseSymbol}`);

            var depositCharge = `@lang('Charge:') ${fixCharge} ${baseSymbol}  ${(0 < percentCharge) ? ' + ' + percentCharge + ' % ' : ''}`;
            $('.depositCharge').text(depositCharge);

            $('.method-name').text(`@lang('Payment By') ${$(this).data('name')} - ${currency}`);
            $('.show-currency').text("{{config('basic.currency')}}");
            $('.gateway').val(currency);

            // amount
        });


        $(".checkCalc").on('click', function () {
            $('.payment-form').addClass('d-none');

            $('#loading').show();
            $('.modal-backdrop.fade').addClass('show');
            amount = $('.amount').val();
            $.ajax({
                url: "{{route('user.addFund.request')}}",
                type: 'POST',
                data: {
                    amount,
                    gateway
                },
                success(data) {

                    $('.payment-form').addClass('d-none');
                    $('.checkCalc').closest('.modal-footer-custom').addClass('d-none');

                    var htmlData = `
                     <ul class="list-group text-center">
                        <li class="list-group-item bg-transparent">
                            <img class="w-100"src="${data.gateway_image}"
                                style="max-width:100px; max-height:100px; margin:0 auto;"/>
                        </li>
                        <li class="list-group-item bg-transparent">
                            @lang('Amount'):
                            <strong>${data.amount} </strong>
                        </li>
                        <li class="list-group-item bg-transparent">@lang('Charge'):
                                <strong>${data.charge}</strong>
                        </li>
                        <li class="list-group-item bg-transparent">
                            @lang('Payable'): <strong> ${data.payable}</strong>
                        </li>
                        <li class="list-group-item bg-transparent">
                            @lang('Conversion Rate'): <strong>${data.conversion_rate}</strong>
                        </li>
                        <li class="list-group-item bg-transparent">
                            <strong>${data.in}</strong>
                        </li>

                        ${(data.isCrypto == true) ? `
                        <li class="list-group-item bg-transparent">
                            ${data.conversion_with}
                        </li>
                        ` : ``}

                        <li class="list-group-item bg-transparent">
                        <a href="${data.payment_url}" class="btn btn-success-custom  btn-block addFund ">@lang('Pay Now')</a>
                        </li>
                        </ul>`;

                    $('.payment-info').html(htmlData)
                },
                complete: function () {
                    $('#loading').hide();
                },
                error(err) {
                    var errors = err.responseJSON;
                    for (var obj in errors) {
                        $('.errors').text(`${errors[obj]}`)
                    }

                    $('.payment-form').removeClass('d-none');
                }
            });
        });


        $('.close').on('click', function (e) {
            $('#loading').hide();
            $('.payment-form').removeClass('d-none');
            $('.checkCalc').closest('.modal-footer-custom').removeClass('d-none');
            $('.payment-info').html(``)
            $('.amount').val(``);
            $("#addFundModal").modal("hide");
        });

    </script>
@endpush

