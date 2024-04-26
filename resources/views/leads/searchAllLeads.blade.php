@section('page-title')
    {{__('Pipeline')}} @if($pipeline) - {{$pipeline->name}}  @endif
@endsection

<link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}" id="main-style-link">
<script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
<script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>
<script>
    $(document).ready(function () {
        function initDragula() {
            $('[data-plugin="dragula"]').each(function () {
                var containers = $(this).data("containers") || [$(this)[0]];
                var handleClass = $(this).data("handleclass");

                var config = handleClass
                    ? { moves: function (el, source, handle, sibling) { return handle.classList.contains(handleClass); } }
                    : undefined;

                dragula(containers, config).on('drop', function (el, target, source, sibling) {
                    var order = $("#" + target.id + " > div").map(function () { return $(this).attr('data-id'); }).get();
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
                        data: {
                            lead_id: id,
                            stage_id: stage_id,
                            order: order,
                            new_status: new_status,
                            old_status: old_status,
                            pipeline_id: pipeline_id,
                            "_token": $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            // Handle success
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            show_toastr('error', data.error, 'error');
                        }
                    });
                });
            });
        }

        initDragula();
    });
</script>

<script>
    $('.reject').click(function (e) {
        var url = $(this).attr('data-url');
        Swal.fire({
            title: 'Reason of rejection',
            input: 'textarea',
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonColor: "#ffa000"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {"note": result.value},
                    dataType: "html",
                    headers: {'X-CSRF-Token': '{{ csrf_token() }}'},
                    success: function (response) {
                        if (response =='true') {
                            Swal.fire({
                                title: "Lead Rejected Successfully",
                                text: "You clicked the button!",
                                icon: "success",
                                showConfirmButton: false,
                                timer: 2000
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    }
                });
            }
        });
    });
</script>

<style>
    .swal2-styled.swal2-confirm {
        background-color: #ffa000 !important;
    }
    .swal2-title {
        text-transform: capitalize !important;
    }
</style>

<div class="tab-pane fade show active" id="{{str_replace(' ', '_', $pipeline->name);}}" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
    <div class="row">
        <div class="col-sm-12">
            @php
                $lead_stages = $pipeline->leadStages;
                $json = [];
                foreach ($lead_stages as $lead_stage) {
                    $json[] = 'task-list-'.$lead_stage->id;
                }
            @endphp

            <div class="row kanban-wrapper horizontal-scroll-cards" data-containers='{!! json_encode($json) !!}' data-plugin="dragula">
                @foreach($lead_stages as $lead_stage)
                    @php $leads = $lead_stage->lead(); @endphp
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <div class="float-end">
                                    @php $dealCount = 0; @endphp
                                    @foreach ($lead_stage->lead() as $key)
                                        @if ($lead_stage->id == $key->stage_id && $key->is_converted == null )
                                            @foreach ($searchLead as $sl)
                                                @if ($sl->id == $key->id)
                                                    @php $dealCount += 1; @endphp
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                    <span class="btn btn-sm btn-primary btn-icon count">
                                        {{$dealCount}}
                                    </span>
                                </div>
                                <h4 class="mb-0">{{$lead_stage->name}}</h4>
                            </div>
                            <div class="card-body kanban-box" id="task-list-{{$lead_stage->id}}" data-id="{{$lead_stage->id}}">
                                @foreach($leads as $lead)
                                    @foreach ($searchLead as $item)
                                        @if ($lead->id == $item->id)
                                            <div class="card" data-id="{{$lead->id}}">
                                                <div class="pt-3 ps-3">
                                                    @php($labels = $lead->labels())
                                                    @if($labels)
                                                        @foreach($labels as $label)
                                                            <div class="badge-xs badge bg-{{$label->color}} p-2 px-3 rounded">{{$label->name}}</div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div class="card-header border-0 pb-0 position-relative">
                                                    <h5><a href="@can('view lead')@if($lead->is_active){{route('leads.show',$lead->id)}}@else#@endif @else#@endcan">{{optional($lead->client)->name ?? 'N/A'}}</a></h5>
                                                    <div class="card-header-right">
                                                        @if(Auth::user()->type != 'client')
                                                            <div class="btn-group card-option">
                                                                <button type="button" class="btn dropdown-toggle"
                                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                                        aria-expanded="false">
                                                                    <i class="ti ti-dots-vertical"></i>
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <a href="#!" data-size="md" data-url="{{ URL::to('leads/'.$lead->id.'/labels') }}" data-ajax-popup="true" class="dropdown-item" data-bs-original-title="{{__('Labels')}}">
                                                                        <i class="ti ti-bookmark"></i>
                                                                        <span>{{__('Labels')}}</span>
                                                                    </a>
                                                                    <a href="#!" data-size="md" data-url="{{ URL::to('leads/'.$lead->id.'/priority') }}" data-ajax-popup="true" class="dropdown-item" data-bs-original-title="{{__('Priority')}}">
                                                                        <i class="ti ti-bolt"></i>
                                                                        <span>{{__('Priority')}}</span>
                                                                    </a>
                                                                    @can('edit lead')
                                                                        <a href="#!" class="dropdown-item reject" data-url="{{ URL::to('leads/'.$lead->id.'/reject') }}"  data-bs-original-title="{{__('Edit Lead')}}">
                                                                            <i class="ti ti-ban"></i>
                                                                            <span>{{__('Reject Lead')}}</span>
                                                                        </a>
                                                                        <a href="#!" data-size="xl" data-url="{{ URL::to('leads/'.$lead->id.'/edit') }}" data-ajax-popup="true" class="dropdown-item" data-bs-original-title="{{__('Edit Lead')}}">
                                                                            <i class="ti ti-pencil"></i>
                                                                            <span>{{__('Service Edit')}}</span>
                                                                        </a>
                                                                    @endcan
                                                                    @can('transfer lead')
                                                                        <a href="#!" data-size="xl" data-url="{{ URL::to('leads/'.$lead->id.'/transfer') }}" data-ajax-popup="true" class="dropdown-item" data-bs-original-title="{{__('Edit Lead')}}">
                                                                            <i class="ti ti-transfer"></i>
                                                                            <span>{{__('Service Transfer')}}</span>
                                                                        </a>
                                                                    @endcan
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="user-group">
                                                            @foreach($lead->users as $user)
                                                                @if($lead->created_by != $user->id && $user->designation != 'Jovera' && $user->type != 'company')
                                                                    <img src="@if($user->avatar) {{asset('/storage/uploads/avatar/'.$user->avatar)}} @else {{asset('storage/uploads/avatar/avatar.png')}} @endif" alt="image" data-bs-toggle="tooltip" title="{{$user->name}}">
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="badge-xs mt-3 badge bg-primary p-2 fs-6 px-3 rounded">{{optional($lead->leadType)->name}} <br> {{optional($lead->source)->name}}</div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between row">
                                                        <div class="col-8">
                                                            @if (optional($lead->product)->id == 1)
                                                                <div class="badge-xs mt-2 badge fs-6 rounded" style="background-color: #023553 ">{{$lead->product->name}}

                                                                   @if (!empty($lead->businessBanking))
                                                                     @if ($lead->businessBanking->business_banking_services == 'business_loan')
                                                                         <br><b>{{__('Business Loan')}}</b>
                                                                     @elseif($lead->businessBanking->business_banking_services == 'fleet_finance')
                                                                         <br><b>{{__('Fleet Finance')}}</b>
                                                                     @elseif($lead->businessBanking->business_banking_services == 'lgcs')
                                                                         <br><b>{{__('LGs / LCs')}}</b>
                                                                     @else
                                                                         <br><b>{{__('Account Opening')}}</b>
                                                                     @endif
                                                                   @endif
                                                                </div>
                                                            @elseif (optional($lead->product)->id == 2)
                                                                <div class="badge-xs mt-2 badge fs-6 rounded" style="background-color: #4c0353 ">{{optional($lead->product)->name ?? 'N/A'}}</div>
                                                            @else
                                                                <div class="badge-xs mt-2 badge fs-6 rounded" style="background-color: #0dc4b5 ">{{optional($lead->product)->name ?? 'N/A'}}</div>
                                                            @endif
                                                            <br>
                                                        </div>
                                                        <div class="col-4 ">
                                                            <div class="badge-xs mt-2 badge  fs-6 rounded"
                                                            @switch($lead->priority)
                                                                @case(1)
                                                                    style="background-color: #6c757d"
                                                                    @break
                                                                @case(2)
                                                                    style="background-color: #313a0b"
                                                                    @break
                                                                @default
                                                                    style="background-color: #ffa21d"
                                                            @endswitch
                                                            ><b>
                                                            @switch($lead->priority)
                                                                @case(1)
                                                                        {{__('Low')}}
                                                                    @break
                                                                @case(2)
                                                                        {{__('Medium')}}
                                                                    @break
                                                                @default
                                                                        {{__('High')}}

                                                            @endswitch
                                                            </b></div>
                                                        </div>
                                                    </div>
                                                    <div class="badge-xs mt-2 badge  fs-6 rounded {{!empty($lead->is_transfer)? '':'d-none'}} " style="background-color: #313a0b "><b>{{__('Tansfered')}}</b></div>
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
</div>
