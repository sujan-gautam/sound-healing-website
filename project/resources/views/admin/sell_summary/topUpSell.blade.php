@extends('admin.layouts.app')
@section('title',trans("Topup Payment"))
@section('content')

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <form action="{{route('admin.topUpSell.search')}}" method="get">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" name="transaction_id" value="{{@request()->transaction_id}}" class="form-control get-trx-id"
                                       placeholder="@lang('Search for Transaction ID')">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" name="user_name" value="{{@request()->user_name}}" class="form-control get-username"
                                       placeholder="@lang('Username')">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control mb-3" name="top_status"
                                    aria-label=".form-select-lg example">
                                <option value="0" @if (@request()->top_status==0) selected @endif>@lang('All Top Up')
                                </option>
                                <option value="1" @if (@request()->top_status==1) selected @endif>@lang('Complete Top Up')
                                </option>
                                <option value="2" @if (@request()->top_status==2) selected @endif>
                                    @lang('Pending Top Up')</option>
                            </select>

                        </div>


                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="date" class="form-control" name="datetrx" id="datepicker"/>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="fas fa-search"></i> @lang('Search')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <table class="categories-show-table table table-hover table-striped table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">@lang('No.')</th>
                    <th scope="col">@lang('TRX')</th>
                    <th scope="col">@lang('User')</th>
                    <th scope="col">@lang('Category')</th>
                    <th scope="col">@lang('Service')</th>
                    <th scope="col">@lang('Price')</th>
                    <th scope="col">@lang('Date - Time')</th>
                    <th scope="col">@lang('Status')</th>
                    <th scope="col">@lang('More')</th>
                </tr>
                </thead>
                <tbody>

                @forelse($topUpSell as $k => $row)
                    <tr>
                        <td data-label="@lang('No.')">{{++$k}}</td>
                        <td data-label="@lang('TRX')">@lang($row->transaction)</td>
                        <td data-label="@lang('User')">

                            <a href="{{route('admin.user-edit',$row->user_id)}}">
                                <div class="d-flex no-block align-items-center">
                                    <div class="mr-3"><img src="{{getFile(config('location.user.path').optional($row->user)->image) }}" alt="user" class="rounded-circle" width="45" height="45"></div>
                                    <div class="">
                                        <h5 class="text-dark mb-0 font-16 font-weight-medium">@lang(optional($row->user)->username)</h5>
                                        <span class="text-muted font-14">{{optional($row->user)->email}}</span>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td data-label="@lang('Category')">{{@optional($row->category)->details->name}}</td>
                        <td data-label="@lang('Service')">{{optional($row->service)->name}}</td>
                        <td data-label="@lang('Price')" ><span class="font-weight-bold">{{config('basic.currency_symbol')}}</span>{{getAmount($row->price)}}
                            @if(0 <$row->discount)
                            <sup class="badge badge-light badge-pill ">  {{config('basic.currency_symbol')}}{{getAmount($row->discount)}} @lang('Off')</sup>
                            @endif
                        </td>
                        <td data-label="@lang('Date - Time')">{{dateTime($row->created_at, 'd M, Y H:i')}}</td>
                        <td data-label="@lang('Status')">
                            @if($row->status == 1)
                                <span class="badge badge-light"><i class="fa fa-circle text-success   font-12" ></i> @lang('Complete')</span>
                            @elseif($row->status == 0)
                                <span class="badge badge-light"><i class="fa fa-circle text-warning pending font-12" ></i> @lang('Pending')</span>
                            @endif
                        </td>
                            <td data-label="@lang('More')">
                                @php
                                    $details = ($row->information != null) ? json_encode($row->information) : null;
                                @endphp
                                <button type="button" class="btn btn-sm btn-outline-primary btn-icon edit_button"
                                        data-toggle="modal" data-target="#myModal"
                                        data-route="{{route('admin.topUpSell.action',$row->id)}}"
                                        data-feedback="{{$row->feedback}}"
                                        data-info="{{$details}}"
                                        data-id="{{$row->id}}"
                                        data-status="{{$row->status}}">
                                    @if($row->status == 0)
                                        <i class="fa fa-pencil-alt"></i>
                                    @else
                                        <i class="fa fa-eye"></i>
                                    @endif
                                </button>
                            </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan="100%">@lang('No Data Found')</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {{ $topUpSell->links('partials.pagination') }}
        </div>
    </div>

    <!-- Modal for top up information button -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="myModalLabel">@lang('Top Up Information')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>


                            <div class="form-group addForm">

                            </div>

                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')
                        </button>
                            <input type="hidden" class="action_id" name="id">
                            <button type="submit" class="btn btn-primary action-btn" name="status"
                                    value="1">@lang('Complete')</button>
                    </div>

                </form>


            </div>
        </div>
    </div>


@endsection

@push('js')
    <script>
        "use strict";
        $(document).ready(function () {
            $(document).on("click", '.edit_button', function (e) {
                var id = $(this).data('id');
                var status = $(this).data('status');

                if(status == 0){
                    $('.action-btn').removeClass('d-none');
                    $('.action-btn').addClass('d-block');
                }else{
                    $('.action-btn').removeClass('d-block');
                    $('.action-btn').addClass('d-none');
                }



                $(".action_id").val(id);
                $(".actionRoute").attr('action', $(this).data('route'));
                var details = Object.entries($(this).data('info'));
                var list = [];
                var ImgPath = "{{asset(config('location.withdrawLog.path'))}}";
                details.map(function (item, i) {
                    if (item[1].type == 'file') {
                        var singleInfo = `<br><img src="${ImgPath}/${item[1].field_name}" alt="..." class="w-50">`;
                    } else {
                        var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>  `;
                    }
                    list[i] = ` <li class="list-group-item"><span class="font-weight-bold "> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`
                });

                $('.withdraw-detail').html(list);
            });
        });
    </script>
@endpush
