<link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}" id="main-style-link">
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
                    console.log(stage_id);
                    if (stage_id == 6) {
                        Swal.fire({
                            title: "Are you sure?",
                            text: "You won't be able to revert this!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Confirm!"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: '{{route('deals.order')}}',
                                    type: 'POST',
                                    data: {deal_id: id, stage_id: stage_id, order: order, new_status: new_status, old_status: old_status, pipeline_id: pipeline_id, "_token": $('meta[name="csrf-token"]').attr('content')},
                                    success: function (data) {
                                        Swal.fire({
                                            title: "Collected!",
                                            text: "Your deal has been collected.",
                                            icon: "success"
                                        });
                                    },
                                    error: function (data) {
                                        data = data.responseJSON;
                                        show_toastr('error', data.error, 'error')
                                    }
                                });

                            }
                        });
                    }else{
                        $.ajax({
                            url: '{{route('deals.order')}}',
                            type: 'POST',
                            data: {deal_id: id, stage_id: stage_id, order: order, new_status: new_status, old_status: old_status, pipeline_id: pipeline_id, "_token": $('meta[name="csrf-token"]').attr('content')},
                            success: function (data) {
                                console.log(data);
                            },
                            error: function (data) {
                                data = data.responseJSON;
                                show_toastr('error', data.error, 'error')
                            }
                        });
                    }
                });
            })
        }, a.Dragula = new t, a.Dragula.Constructor = t
    }(window.jQuery), function (a) {
        "use strict";

        a.Dragula.init()

    }(window.jQuery);


</script>

<div class="tab-pane fade show active" id="{{str_replace(' ', '_', $pipeline->name);}}" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
    <div class="row">
        <div class="col-sm-12">
            @php
                $stages = $pipeline->stages;

                    $json = [];
                    foreach ($stages as $stage){
                        $json[] = 'task-list-'.$stage->id;
                    }

            @endphp
            <div class="row kanban-wrapper horizontal-scroll-cards" data-containers='{!! json_encode($json) !!}' data-plugin="dragula">
                @foreach($stages as $stage)
                    @php $deals = $stage->deals(); @endphp
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <div class="float-end">
                                    @php
                                        $totalA = 0;
                                        $dealCount = 0;
                                    @endphp
                                    @foreach ($deals as $key )
                                        @if ($key->pipeline_id == $pipeline->id && $stage->id ==$key->deal_stage_id)
                                            @php
                                                $dealCount += 1;
                                                $totalA += $key->serviceCommission->with_vat_commission ?? 0;
                                            @endphp
                                        @endif
                                    @endforeach

                                        <span class="btn btn-sm btn-primary btn-icon count">
                                            {{$dealCount}}
                                        </span>
                                </div>
                                <h4 class="mb-0">{{$stage->name}}</h4>
                                <div class="float-end mt-2">
                                @if ($dealCount>0)
                                    <span class="btn btn-sm btn-primary btn-icon ">
                                        Total :   AED {{number_format($totalA)}}
                                    </span>
                                @endif
                            </div>
                            </div>
                            <div class="card-body kanban-box" id="task-list-{{$stage->id}}" data-id="{{$stage->id}}">
                                @foreach($deals as $deal)
                                    @if ($deal->pipeline_id == $pipeline->id)
                                        <div class="card" data-id="{{$deal->id}}">
                                            <div class="pt-3 pe-3  rounded" style="display:flex;justify-content: end;">
                                                <a href="#!" data-size="md" data-url="{{ URL::to('deals/'.$deal->id.'/labels') }}" data-ajax-popup="true" class="btn btn-primary btn-sm" data-bs-original-title="{{__('Labels')}}">
                                                    <i class="ti ti-bookmark"></i>
                                                    <span>{{__('Labels')}}</span>
                                                </a>
                                            </div>
                                            <div class="ps-3">
                                                <br>
                                                @php($labels = $deal->labels())
                                                @if($labels)
                                                    @foreach($labels as $label)
                                                        <div class="badge-xs badge bg-{{$label->color}} p-2 px-3 rounded">{{$label->name}}</div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="card-header border-0 pb-0 position-relative d">
                                                <h5><a href="@can('view deal')@if($deal->is_active){{route('deals.show',$deal->id)}}@else#@endif @else#@endcan">{{$deal->client->name}}</a></h5>
                                            </div>

                                            <?php
                                            $products = $deal->product;
                                            $sources = $deal->source;
                                            ?>
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <ul class="list-inline mb-0">
                                                        <li class="list-inline-item d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{__('Tasks')}}">
                                                            {{-- <i class="f-16 text-primary ti ti-list"></i> {{count($deal->tasks)}}/{{count($deal->complete_tasks)}} --}}
                                                        </li>
                                                    </ul>
                                                    <div class="user-group">
                                                        AED&nbsp;{{number_format(optional($deal->serviceCommission)->with_vat_commission)}}
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <ul class="list-inline mb-0">

                                                        <li class="list-inline-item d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{optional($deal->leads->product)->name}}">
                                                            <i class="f-16 text-primary ti ti-brand-linktree"> </i>
                                                        </li>


                                                        @if (!empty($deal->leads->is_transferd))
                                                            <li class="list-inline-item d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{__('Transfered')}}">
                                                                <i class="f-16 text-primary ti ti-transform"></i>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                    <div class="user-group">
                                                        @foreach($deal->leads->users as $user)
                                                        @if($deal->leads->created_by != $user->id && $user->designation != 'Jovera' && $user->type != 'company')
                                                        <img src="@if($user->avatar) {{asset('/storage/uploads/avatar/'.$user->avatar)}} @else {{asset('storage/uploads/avatar/avatar.png')}} @endif"  data-bs-toggle="tooltip" title="{{$user->name}}">
                                                        @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="badge-xs mt-2 badge bg-primary fs-6 rounded">{{optional($deal->leads->leadType)->name}} <br> {{optional($deal->leads->source)->name}} </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
