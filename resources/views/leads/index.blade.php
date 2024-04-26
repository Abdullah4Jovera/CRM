@extends('layouts.admin')
@section('page-title')
    {{__('Pipeline')}}@if($pipeline) - {{$pipeline->name}}  @endif
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}" id="main-style-link">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        #default_pipeline_id{
            width: 300px !important;
        }
        .choices[data-type*="select-one"] .choices__inner{
            width: 300px !important;
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
            margin-top: 10em !important;
        }
        .sources{
            margin-bottom: 0rem !important;
        }
        .indexPage{
            padding: 11px !important;

        }

    </style>
@endpush
@push('script-page')
    <script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
    <script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
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
                            url: '{{route('leads.order')}}',
                            type: 'POST',
                            data: {lead_id: id, stage_id: stage_id, order: order, new_status: new_status, old_status: old_status, pipeline_id: pipeline_id, "_token": $('meta[name="csrf-token"]').attr('content')},
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
        $(document).on("change", "#default_pipeline_id", function () {
            $('#change-pipeline').submit();

        });
        $(document).on("change", ".searchData",function () {
            let id= $('#choices-multiple23').val();
            let lead_type_id= $('#choices-multiple24').val();
            let source_id= $('.btn.btn-sm.btn-default.sources select#choices-multiple25').val();
            let priority= $('#priority').val();
            let client_id = ($('#clientPhone').val() !== null && $('#clientPhone').val() !== '') ? $('#clientPhone').val() : $('#clientName').val();
            console.log(client_id);
            $("#preloader").show();
            $("#allLeadsContent").hide();
            if (id != null) {
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/leads/search') }}",
                    data: {
                        id : id,
                        lead_type_id : lead_type_id,
                        source_id : source_id,
                        client_id : client_id,
                        priority : priority,
                    },
                    dataType: "html",
                    headers: {
                        'X-CSRF-Token': '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        $("#allLeadsContent").show();
                        $('#allLeadsContent').html(response);
                        $("#preloader").hide();
                    }
                });
            }else{
                location.reload()
            }
        });
        $('.rangeDate').daterangepicker({
        maxDate: new Date(),
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'Y-M-DD'
        }
        });
        $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + '   ' + picker.endDate.format('YYYY-MM-DD'));
        });
        $(function () {
            var preloader = $("#preloader");
            var allLeadsContent = $("#allLeadsContent");

            preloader.show();
            allLeadsContent.hide();

            let deal_id = '{{ \Auth::user()->default_pipeline }}';
            let user_id = $('#choices-multiple23').val();
            var url = "{{ route('lead.pipeline') }}";

            $.ajax({
                type: "POST",
                url: url,
                data: { id: deal_id, user_id: user_id },
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}',
                },
                dataType: "html",
                success: function (response) {
                    allLeadsContent.show();
                    allLeadsContent.html(response);
                    preloader.hide();
                }
            });
        });


        $(document).on('click', '.getLeadById' ,function () {
            $("#preloader").show();
            $("#allLeadsContent").hide();
            let deal_id = $(this).attr("data-id");
            let pipelineName = $(this).attr("data-value");
            let user_id = $('#choices-multiple23').val();
            $('#pipelineName').html(`Pipeline - `+pipelineName);
                var url = "{{ route('lead.pipeline') }}";
                $.ajax({
                    type: "POST",
                    url: url,
                    data:{id:deal_id,user_id:user_id},
                    headers: {
                        'X-CSRF-Token': '{{ csrf_token() }}',
                },
                dataType: "html",
                success: function (response) {

                    $("#allLeadsContent").show();
                    $('#allLeadsContent').html(response);
                    $("#preloader").hide();
                }
            });
        });


        $(function () {
            $(".clinetName").hide();
        });
        $(document).on('click','#searchFrom',function (e) {
           var searchBy = $('input[name="searchFrom"]:checked').val();
           if (searchBy == "name") {
               $(".clinetPhone").hide();
               $(".clinetName").show();
            } else {
                $(".clinetPhone").show();
                $(".clinetName").hide();
           }
        });

        $(document).on('change','#choices-multiple24',function () {
            lead_type_id = $('#choices-multiple24').val();
            if (lead_type_id == null) {
                $('.sources').html('');
            }
            var old_url = "{{ route('leads.getsourses', ":id") }}";
            url = old_url.replace(':id', lead_type_id);
            $.ajax({
                type: "GET",
                url: url,
                // data:
                dataType: "html",
                success: function (response) {
                    if (response.length <= 0) {
                        $('.sources').hide();
                    }
                    $('.sources').html(response );
                }
            });
        });
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Lead')}}</li>
@endsection
@section('action-btn')

    <div class="float-end" id="buttonDiv">
        {{-- @dd($allLeads) --}}

        <div  class="btn btn-sm btn-default" style="text-align: left !important;">
            <select name="choices-multiple23" id="choices-multiple23" class="form-control searchData select2" >
                <option value="" selected>Search User Lead</option>
                @foreach ($allLeads as $al)
                @if ($al->designation != "Jovera" && $al->id != \Auth::user()->id)
                <option value="{{$al->id}}">{{$al->name}} </option>
                @endif
                @endforeach
            </select>
        </div>
        <div  class="btn btn-sm btn-default" style="text-align: left !important;">
            <select name="priority" class="form-control searchData select2" id="priority">
                <option value="" selected > Select Priority</option>
                <option value="1">Low</option>
                <option value="2">Medium</option>
                <option value="3">High</option>
            </select>
        </div>
        <div  class="btn btn-sm btn-default" style="text-align: left !important;">
            <select name="choices-multiple23" id="choices-multiple24" class="form-control  searchData select2" >
                <option value="" selected>Search Lead Type</option>
                @foreach ($lead_types as $lead_type)
                    <option value="{{$lead_type->id}}">{{$lead_type->name}} </option>
                @endforeach
            </select>
        </div>
        <div  class="btn btn-sm btn-default sources " style="text-align: left !important;">
        </div>

       @can ('create lead')
         <a href="#" data-size="xl"  data-url="{{ route('leads.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create New Lead')}}" class="btn btn-sm btn-primary">
             <i class="ti ti-plus"></i>
         </a>
       @endcan
    </div>
@endsection
@section('content')
<div class="mainPage">
    {{-- @if ($pipeline->id == '1') --}}

        <ul class="nav nav-tabs nav-justified mb-3" id="ex1" role="tablist">
                @php
                    $lastPipelineId = \Auth::user()->default_pipeline;
                @endphp
            @foreach ($pipelineLeads as $pipelineLead)
                @if ($pipelineLead->id != 1 && $pipelineLead->id != 10)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link getLeadById {{ $lastPipelineId == $pipelineLead->id ? "active" : "" }}" id="home-tab" data-bs-toggle="tab" data-bs-target="#{{str_replace(' ', '_', $pipelineLead->name);}}" data-id="{{$pipelineLead->id}}" data-value="{{$pipelineLead->name}}" type="button" role="tab" aria-controls="{{$pipelineLead->name}}" aria-selected="true">{{$pipelineLead->name}}</button>
                    </li>
                @endif
            @endforeach
        </ul>
    {{-- @endif --}}
    <div id="preloader" >
        <img id="" src="{{ asset('assets/gear-spinner.svg') }}" alt="Rotating Image">
    </div>
    <div class="tab-content" id="allLeadsContent">

    </div>
</div>



@endsection

