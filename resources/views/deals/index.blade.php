@extends('layouts.admin')
@section('page-title')
    {{__('Manage Deals')}} @if($pipeline) - {{$pipeline->name}} @endif
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}" id="main-style-link">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        #default_pipeline_id{
            width: 300px !important;
            margin-right: 10px !important;
        }
        .dealMain a{
            margin-right: 10px !important;

        }
        #rotatingImage {
            width: 200px;
            height: 200px;
            margin: 50px auto;
            animation: rotate 2s infinite linear;
        }
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        #preloader{
            display: flex;
            justify-content: center;
        }
    </style>
@endpush
@push('script-page')
    {{-- date Range  --}}
        <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    {{-- date Range --}}
    <script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
    <script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>
    <script>
        !function (a) {
            "use strict";
            var t = function () {
                this.$body = a("body")
            };
            t.prototype.init = function () {
                a('[data-plugin="dragula"]').each(function () {
                    var t = a(this).data("containers"), n = [];
                    if (t) for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]); else n = [a(this)[0]];
                    var r = a(this).data("handleclass");
                    r ? dragula(n, {
                        moves: function (a, t, n) {
                            return n.classList.contains(r)
                        }
                    }) : dragula(n).on('drop', function (el, target, source, sibling) {

                        var order = [];
                        $("#" + target.id + " > div").each(function () {
                            order[$(this).index()] = $(this).attr('data-id');
                        });

                        var id = $(el).attr('data-id');

                        var old_status = $("#" + source.id).data('status');
                        var new_status = $("#" + target.id).data('status');
                        var stage_id = $(target).attr('data-id');
                        var pipeline_id = '{{$pipeline->id}}';

                        $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div").length);
                        $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div").length);
                        $.ajax({
                            url: '{{route('deals.order')}}',
                            type: 'POST',
                            data: {deal_id: id, stage_id: stage_id, order: order, new_status: new_status, old_status: old_status, pipeline_id: pipeline_id, "_token": $('meta[name="csrf-token"]').attr('content')},
                            success: function (data) {
                            },
                            error: function (data) {
                                data = data.responseJSON;
                                show_toastr('error', data.error, 'error')
                            }
                        });
                    });
                })
            }, a.Dragula = new t, a.Dragula.Constructor = t
        }(window.jQuery), function (a) {
            "use strict";

            a.Dragula.init()

        }(window.jQuery);


    </script>
    <script>
        $(document).on("change", ".change-pipeline select[name=default_pipeline_id]", function () {
            $('#change-pipeline').submit();
        });
        $(function () {
            let deal_id = `{{\Auth::user()->default_pipeline}}`;
            var url = "{{ route('deal.pipeline', ":id") }}";
            $("#preloader").show();
            $("#allDealsContent").hide();
            url = url.replace(':id', deal_id);
            $.ajax({
                type: "GET",
                url: url,
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}',
            },
            dataType: "html",
            success: function (response) {
                $("#allDealsContent").show();
                $('#allDealsContent').html(response);
                $("#preloader").hide();
            }
           });
        });
        $(document).on('click', '.getDealById' ,function () {
            let deal_id = $(this).attr("data-id");
            var url = "{{ route('deal.pipeline', ":id") }}";
            url = url.replace(':id', deal_id);
            $("#preloader").show();
            $("#allDealsContent").hide();
           $.ajax({
            type: "GET",
            url: url,
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}',
            },
            dataType: "html",
            success: function (response) {
                $("#allDealsContent").show();
                $('#allDealsContent').html(response);
                $("#preloader").hide();
                let newUrl = "{{ route('deal.gettotal', ":id") }}";
                newUrl = newUrl.replace(':id', deal_id);
                $.ajax({
                    type: "GET",
                    url: newUrl,
                    headers: {
                        'X-CSRF-Token': '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        $('.total').html(response.total);
                        $('.curt-total').html(response.curr_month);
                        $('.total-col').html(response.collection);
                        $('.curt-total-col').html(response.collection_month);
                        $('.total-reject').html(response.reject);
                        $('.curt-total-reject').html(response.reject_month);

                    }
                });
            }
           });
        });
        // Search deal By Users
        // $(document).on("click", "#getSearchByUser", function () {
        //     let user = null ;
        //     let pipeline = 'null';
        //     let datetime= null;
        //     if (($('#user_id').val()).length > 0) {
        //         user = $('#user_id').val();
        //         if (($('#pipeline_id').val()).length > 0) {
        //             pipeline = $('#pipeline_id').val();
        //         }
        //         if($('#datetimes').val() != null){
        //             datetime = $('#datetimes').val();
        //             var array = datetime.split(" ");

        //             var start = array[0];
        //             var end = array[3];
        //         }else{
        //             start = "null";
        //             end = "null";
        //         }
        //         $('#modalId').modal('hide');
        //         $.ajax({
        //             type: "POST",
        //             url: "{{ URL::to('deals/filterSearch') }}",
        //             data: {
        //                     user : user,
        //                     pipeline : pipeline,
        //                     startDate : start,
        //                     endDate : end,
        //                 },
        //             dataType: "html",
        //             headers: {
        //                 'X-CSRF-Token': '{{ csrf_token() }}',
        //             },
        //             success: function (response) {
        //                 $('.mainPage').html(response);

        //             }
        //         });
        //     }else{
        //         $('#error').html('Please Select User First');
        //     }
        // });
        // $('.rangeDate').daterangepicker({
        //     maxDate: new Date(),
        //     autoUpdateInput: false,
        //     locale: {
        //         cancelLabel: 'Clear',
        //         format: 'Y-M-DD'
        //     }
        // });

    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Deal')}}</li>
@endsection
@section('action-btn')

    <div class="float-end d-flex dealMain">

        {{-- <a href="{{ route('deals.list') }}" data-size="lg" data-bs-toggle="tooltip" title="{{__('List View')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-list"></i>
        </a>
        @can('create deal')
        <a href="#" data-size="lg" data-url="{{ route('deals.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create New Deal')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
        @endcan --}}
        {{-- <button class="btn btn-sm btn-primary"  data-bs-toggle="modal" data-bs-target="#modalId" title="{{__('Filter')}}" >
            <i class="ti ti-filter"></i>
        </button> --}}
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-2">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <small class="text-muted">{{__('Total Deal Amount')}}</small>
                            <h4 class="m-0 total">{{ $cnt_deal['total'] }}</h4>
                        </div>
                        <div class="col-auto">
                            <div class="theme-avtar bg-info">
                                <i class="ti ti-layers-difference"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <small class="text-muted">{{__('Curt. Month Deal')}}</small>
                            <h4 class="m-0 curt-total">{{ $cnt_deal['curr_month'] }}</h4>
                        </div>
                        <div class="col-auto">
                            <div class="theme-avtar bg-danger">
                                <i class="ti ti-layers-difference"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <small class="text-muted">{{__('Total Rejection')}}</small>
                            <h4 class="m-0 total-reject">{{ $cnt_deal['reject'] }}</h4>
                        </div>
                        <div class="col-auto">
                            <div class="theme-avtar bg-primary">
                                <i class="ti ti-layers-difference"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <small class="text-muted">{{__('Curt. Month Rejection')}}</small>
                            <h4 class="m-0 curt-total-reject">{{ $cnt_deal['reject_month'] }}</h4>
                        </div>
                        <div class="col-auto">
                            <div class="theme-avtar bg-warning">
                                <i class="ti ti-layers-difference"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <small class="text-muted">{{__('Total Collection')}}</small>
                            <h4 class="m-0 total-col">{{ $cnt_deal['collection'] }}</h4>
                        </div>
                        <div class="col-auto">
                            <div class="theme-avtar bg-primary">
                                <i class="ti ti-layers-difference"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <small class="text-muted">{{__('Curt. Month Collection ')}}</small>
                            <h4 class="m-0 curt-total-col">{{ $cnt_deal['collection_month'] }}</h4>
                        </div>
                        <div class="col-auto">
                            <div class="theme-avtar bg-primary">
                                <i class="ti ti-layers-difference"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="mainPage">
        {{-- @if ($pipeline->id == '1') --}}
            <ul class="nav nav-tabs nav-justified mb-3" id="ex1" role="tablist">
                    @php
                        // get id as the last id
                        $lastPipelineId = \Auth::user()->default_pipeline ;
                    @endphp
                @foreach ($pipelineDeals as $pipelineDeal)
                    @if ($pipelineDeal->id != 1 && $pipelineDeal->id != 10 && $pipelineDeal->id != 8)
                    {{-- @dd($pipelineDeal->stages) --}}
                        {{-- @if(count($pipelineDeal->stages)>0) --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link getDealById {{ $lastPipelineId == $pipelineDeal->id ? "active" : "" }}" data-id="{{$pipelineDeal->id}}" id="home-tab" data-bs-toggle="tab" data-bs-target="#{{str_replace(' ', '_', $pipelineDeal->name);}}" type="button" role="tab" aria-controls="{{$pipelineDeal->name}}" aria-selected="true">{{$pipelineDeal->name}}</button>
                            </li>

                        {{-- @endif --}}
                    @endif
                @endforeach
            </ul>
        {{-- @endif --}}
        <div id="preloader" >
            <img src="{{ asset('assets/gear-spinner.svg') }}" alt="Rotating Image">
        </div>
        <div class="tab-content" id="allDealsContent">

        </div>
    </div>


@endsection
