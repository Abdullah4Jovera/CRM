@extends('layouts.admin')


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


@endpush

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Lead')}}</li>
@endsection


@section('content')

@php
$last_id = null;
@endphp
<ul class="nav nav-tabs nav-justified mb-3" id="ex1" role="tablist">
    @foreach ($pipelines as $pipe)
        @if ($pipe->id != 1)
            @foreach ($searchLeads as $search)
                @if ($pipe->id == $search->pipeline_id)
                    @if ($pipe->id != $last_id)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $last_id == null ? "active" : "" }}" id="home-tab" data-bs-toggle="tab" data-bs-target="#{{str_replace(' ', '_', $pipe->name);}}" type="button" role="tab" aria-controls="{{$pipe->name}}" aria-selected="true">{{$pipe->name}}</button>
                        </li>
                    @endif
                @php
                    $last_id = $pipe->id;
                @endphp
                @endif
            @endforeach
        @endif
    @endforeach
</ul>
@php
    $lastId = null ;
@endphp
<div class="tab-content" id="ex2-content">
    @foreach ($pipelines as $new_pipe)
        @if ($new_pipe->id != 1)
            @foreach ($searchLeads as $searchLead)
                @if ($new_pipe->id == $searchLead->pipeline_id)
                    {{-- @dd($searchLead) --}}
                    @if ($new_pipe->id != $lastId)
                        <div class="tab-pane fade {{ $lastId == null ? "show active" : "" }}" id="{{str_replace(' ', '_', $new_pipe->name);}}}}" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                            <!-- ========== Leads Tab Section ========== -->
                                <div class="row">
                                    <div class="col-sm-12">
                                        @php
                                            $new_lead_stages = $new_pipe->leadStages;
                                            $json = [];
                                            foreach ($new_lead_stages as $new_lead_stage){
                                                $json[] = 'task-list-'.$new_lead_stage->id;
                                            }
                                        @endphp
                                        <div class="row kanban-wrapper horizontal-scroll-cards" data-containers='{!! json_encode($json) !!}' data-plugin="dragula">
                                            @foreach ($new_lead_stages as $new_lead_stage )
                                                @php
                                                    $new_leads = $new_lead_stage->lead()
                                                @endphp

                                                <div class="col" style="width: 275px !important">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h4 class="mb-0">{{$new_lead_stage->name}}</h4>
                                                        </div>
                                                        <div class="card-body kanban-box" id="task-list-{{$new_lead_stage->id}}" data-id="{{$new_lead_stage->id}}">
                                                            @foreach($new_leads as $new_lead)
                                                                @foreach ($searchLeads as $item)
                                                                        @if ($new_lead->id == $item->id)
                                                                            <div class="card" data-id="{{$new_lead->id}}">
                                                                                <div class="pt-3 ps-3">
                                                                                        @php
                                                                                            $labels = $new_lead->labels()
                                                                                        @endphp
                                                                                    @if($labels)
                                                                                        @foreach($labels as $label)
                                                                                            <div class="badge-xs badge bg-{{$label->color}} p-2 px-3 rounded">{{$label->name}}</div>
                                                                                        @endforeach
                                                                                    @endif
                                                                                </div>
                                                                                <div class="card-header border-0 pb-0 position-relative">
                                                                                    <h5><a href="@can('view lead')@if($new_lead->is_active){{route('leads.show',$new_lead->id)}}@else#@endif @else#@endcan">{{$new_lead->name}}</a></h5>
                                                                                    <div class="card-header-right">
                                                                                        @if(Auth::user()->type != 'client')
                                                                                            <div class="btn-group card-option">
                                                                                                <button type="button" class="btn dropdown-toggle"
                                                                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                                                                        aria-expanded="false">
                                                                                                    <i class="ti ti-dots-vertical"></i>
                                                                                                </button>
                                                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                                                    @can('edit lead')
                                                                                                        <a href="#!" data-size="md" data-url="{{ URL::to('leads/'.$new_lead->id.'/labels') }}" data-ajax-popup="true" class="dropdown-item" data-bs-original-title="{{__('Labels')}}">
                                                                                                            <i class="ti ti-bookmark"></i>
                                                                                                            <span>{{__('Labels')}}</span>
                                                                                                        </a>

                                                                                                        <a href="#!" data-size="lg" data-url="{{ URL::to('leads/'.$new_lead->id.'/edit') }}" data-ajax-popup="true" class="dropdown-item" data-bs-original-title="{{__('Edit Lead')}}">
                                                                                                            <i class="ti ti-pencil"></i>
                                                                                                            <span>{{__('Edit')}}</span>
                                                                                                        </a>
                                                                                                    @endcan
                                                                                                    @can('delete lead')
                                                                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['leads.destroy', $new_lead->id],'id'=>'delete-form-'.$new_lead->id]) !!}
                                                                                                        <a href="#!" class="dropdown-item bs-pass-para">
                                                                                                            <i class="ti ti-archive"></i>
                                                                                                            <span> {{__('Delete')}} </span>
                                                                                                        </a>
                                                                                                        {!! Form::close() !!}
                                                                                                    @endcan


                                                                                                </div>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                                    @php
                                                                                        $products = $new_lead->products();
                                                                                        $sources = $new_lead->sources();
                                                                                    @endphp

                                                                                <div class="card-body">
                                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                                        <ul class="list-inline mb-0">

                                                                                            <li class="list-inline-item d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{__('Product')}}">
                                                                                                <i class="f-16 text-primary ti ti-shopping-cart"></i> {{count($products)}}
                                                                                            </li>

                                                                                            <li class="list-inline-item d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{__('Source')}}">
                                                                                                <i class="f-16 text-primary ti ti-social"></i>{{count($sources)}}
                                                                                            </li>
                                                                                        </ul>
                                                                                        {{-- @dd($lead->users) --}}
                                                                                        <div class="user-group">
                                                                                            @foreach($new_lead->users as $user)
                                                                                                <img src="@if($user->avatar) {{asset('/storage/uploads/avatar/'.$user->avatar)}} @else {{asset('storage/uploads/avatar/avatar.png')}} @endif" alt="image" data-bs-toggle="tooltip" title="{{$user->name}}">
                                                                                            @endforeach
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                @endforeach
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            <!-- ========== Leads Tab Section ========== -->
                        </div>
                    @endif
                    @php
                        $lastId = $new_pipe->id;
                    @endphp
                @endif

            @endforeach

        @endif

    @endforeach
</div>


@endsection
