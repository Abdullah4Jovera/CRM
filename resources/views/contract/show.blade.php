@extends('layouts.admin')
@php
    $attachments=\App\Models\Utility::get_file('contract_attechment');
@endphp
@push('css-page')
    <link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/plugins/dropzone.min.css')}}">
@endpush
{{-- @dd($deal) --}}
@section('page-title')
    {{ __('Contract Detail') }}
@endsection
@push('script-page')
    <script>
        $(document).on("click", ".status", function() {
            var status = $(this).attr('data-id');
            var url = $(this).attr('data-url');
            $.ajax({
                url: url,
                type: 'POST',
                data: {

                    "status": status ,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    show_toastr('{{__("success")}}', 'Status Update Successfully!', 'success');
                    location.reload();
                }

            });
        });
    </script>

    <script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
    <script src="{{asset('assets/js/plugins/dropzone-amd-module.min.js')}}"></script>
    <script>
        @can('manage contract')
        $('.summernote-simple').on('summernote.blur', function () {

            $.ajax({
                url: "{{route('contract.contract_description.store',$deal->id)}}",
                data: {_token: $('meta[name="csrf-token"]').attr('content'), contract_description: $(this).val()},
                type: 'POST',
                success: function (response) {
                    console.log(response)
                    if (response.is_success) {
                        show_toastr('success', response.success,'success');
                    } else {
                        show_toastr('error', response.error, 'error');
                    }
                },
                error: function (response) {

                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('error', response.error, 'error');
                    } else {
                        show_toastr('error', response.error, 'error');
                    }
                }
            })
        });
        @else
        // $('.summernote-simple').summernote('disable');
        @endcan
    </script>
    <script>
        Dropzone.autoDiscover = true;
        myDropzone = new Dropzone("#dropzonewidget", {
            maxFiles: 20,
            parallelUploads: 1,

            url: "{{route('contract.file.upload',[$deal->id])}}",
            success: function (file, response) {
                location.reload()
                if (response.is_success) {
                    show_toastr('{{__("success")}}', 'Attachment Create Successfully!', 'success');
                    dropzoneBtn(file, response);
                } else {

                    myDropzone.removeFile(file);
                    show_toastr('{{__("Error")}}', 'The attachment must be same as stoarge setting', 'Error');
                }
            },
            error: function (file, response) {
                myDropzone.removeFile(file);
                if (response.error) {
                    show_toastr('{{__("Error")}}', 'The attachment must be same as stoarge setting', 'error');
                } else {
                    show_toastr('{{__("Error")}}', 'The attachment must be same as stoarge setting', 'error');
                }
            }
        });
        myDropzone.on("sending", function (file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("contract_id", {{$deal->id}});
        });

        function dropzoneBtn(file, response) {
            var download = document.createElement('a');
            download.setAttribute('href', response.download);
            download.setAttribute('class', "action-btn btn-primary mx-1 mt-1 btn btn-sm d-inline-flex align-items-center");
            download.setAttribute('data-toggle', "tooltip");
            download.setAttribute('data-original-title', "{{__('Download')}}");
            download.innerHTML = "<i class='fas fa-download'></i>";

            var del = document.createElement('a');
            del.setAttribute('href', response.delete);
            del.setAttribute('class', "action-btn btn-danger mx-1 mt-1 btn btn-sm d-inline-flex align-items-center");
            del.setAttribute('data-toggle', "tooltip");
            del.setAttribute('data-original-title', "{{__('Delete')}}");
            del.innerHTML = "<i class='ti ti-trash'></i>";

            del.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm("Are you sure ?")) {
                    var btn = $(this);
                    $.ajax({
                        url: btn.attr('href'),
                        data: {_token: $('meta[name="csrf-token"]').attr('content')},
                        type: 'DELETE',
                        success: function (response) {
                            location.reload();
                            if (response.is_success) {
                                btn.closest('.dz-image-preview').remove();
                            } else {
                                show_toastr('{{__("Error")}}', response.error, 'error');
                            }
                        },
                        error: function (response) {
                            response = response.responseJSON;
                            if (response.is_success) {
                                show_toastr('{{__("Error")}}', response.error, 'error');
                            } else {
                                show_toastr('{{__("Error")}}', response.error, 'error');
                            }
                        }
                    })
                }
            });

            var html = document.createElement('div');
            html.setAttribute('class', "text-center mt-10");
            file.previewTemplate.appendChild(html);
        }
        $(document).on('click', '#comment_submit', function (e) {
            console.log('askjjdghjksahdjkh');
            var curr = $(this);

            var comment = $.trim($("#form-comment textarea[name='comment']").val());
            if (comment != '') {
                debugger
                $.ajax({
                    url: $("#form-comment").data('action'),
                    data: {comment: comment, "_token": "{{ csrf_token() }}"},
                    type: 'POST',
                    success: function (data) {
                        show_toastr('{{__("success")}}', 'Comment Create Successfully!', 'success');
                        setTimeout(function () {
                            location.reload();
                        }, 500)
                        data = JSON.parse(data);
                        console.log(data);
                        var html = "<div class='list-group-item px-0'>" +
                            "                    <div class='row align-items-center'>" +
                            "                        <div class='col-auto'>" +
                            "                            <a href='#' class='avatar avatar-sm rounded-circle ms-2'>" +
                            "                                <img src="+data.default_img+" alt='' class='avatar-sm rounded-circle'>" +
                            "                            </a>" +
                            "                        </div>" +
                            "                        <div class='col ml-n2'>" +
                            "                            <p class='d-block h6 text-sm font-weight-light mb-0 text-break'>" + data.comment + "</p>" +
                            "                            <small class='d-block'>"+data.current_time+"</small>" +
                            "                        </div>" +
                            "                        <div class='action-btn bg-danger me-4'><div class='col-auto'><a href='#' class='mx-3 btn btn-sm  align-items-center delete-comment' data-url='" + data.deleteUrl + "'><i class='ti ti-trash text-white'></i></a></div></div>" +
                            "                    </div>" +
                            "                </div>";

                        $("#comments").prepend(html);
                        $("#form-comment textarea[name='comment']").val('');
                        load_task(curr.closest('.task-id').attr('id'));
                        show_toastr('{{__('success')}}', '{{ __("Comment Added Successfully!")}}');
                    },
                    error: function (data) {
                        show_toastr('error', '{{ __("Some Thing Is Wrong!")}}');
                    }
                });
            } else {
                show_toastr('error', '{{ __("Please write comment!")}}');
            }
        });

        $(document).on("click", ".delete-comment", function () {
            var btn = $(this);

            $.ajax({
                url: $(this).attr('data-url'),
                type: 'DELETE',
                dataType: 'JSON',
                data: {"_token": "{{ csrf_token() }}"},
                success: function (data) {
                    load_task(btn.closest('.task-id').attr('id'));
                    show_toastr('{{__('success')}}', '{{ __("Comment Deleted Successfully!")}}');
                    btn.closest('.list-group-item').remove();
                },
                error: function (data) {
                    data = data.responseJSON;
                    if (data.message) {
                        show_toastr('error', data.message);
                    } else {
                        show_toastr('error', '{{ __("Some Thing Is Wrong!")}}');
                    }
                }
            });
        });


    </script>


    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,
        })
        $(".list-group-item").click(function(){
            $('.list-group-item').filter(function(){
                return this.href == id;
            }).parent().removeClass('text-primary');
        });
    </script>
@endpush


@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('contract.index') }}">{{ __('contract') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{\Auth::user()->contractNumberFormat($deal->id)}}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex align-items-center">
        <p>
            @if ($deal->contract_stage == 'unsigned')
                <span class="fs-5 p-2 me-3 mt-2 badge rounded bg-primary">  {{__('New')}}</span>
            @elseif ($deal->contract_stage == 'pending')
                <span class="fs-5 p-2 me-3 mt-2 badge rounded bg-danger">  {{__('Pending')}}</span>
            @else
                <span class="fs-5 p-2 me-3 mt-2 badge rounded bg-info"> {{__('Ready To Sign')}}</span>
            @endif
        </p>
        @can('edit contract')
            <div class="action-btn bg-info ms-2">
                <a href="#" class="btn btn-sm btn-primary btn-icon m-1" data-url="{{ route('contract.edit', $deal->id) }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Contract')}}">
                    <i class="ti ti-pencil text-white"></i>
                </a></div>
        @endcan
        @can('edit contract')
            <div class="action-btn bg-primary ms-2">
                <a href="#" class="btn btn-sm btn-primary btn-icon m-1" data-url="{{ route('contract.dealStatus', $deal->id) }}" data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title="{{__('Status Update')}}" data-title="{{__('Contract Status Update')}}">
                    <i class="ti ti-status-change text-white"></i>
                </a>
            </div>
        @endcan

        <a href="{{ route('get.contract',$deal->id) }}"  target="_blank" class="btn btn-sm btn-primary btn-icon m-1" >
            <i class="ti ti-eye text-white" data-bs-toggle="tooltip" data-bs-original-title="{{ __('PreView') }}"> </i>
        </a>



</div>
@endsection

@section('content')
<div class="row">
   <div class="col-xl-3">
       <div class="card sticky-top" style="top:30px">
           <div class="list-group list-group-flush" id="useradd-sidenav">
               <a href="#useradd-1" class="list-group-item list-group-item-action border-0">{{ __('Client Details') }}
                   <div class="float-end"><i class="ti ti-chevron-right"></i></div>
               </a>
               <a href="#useradd-2" class="list-group-item list-group-item-action border-0">{{ __('Service Details') }}
                   <div class="float-end"><i class="ti ti-chevron-right"></i></div>
               </a>
               <a href="#useradd-3" class="list-group-item list-group-item-action border-0">{{ __('Commission Details') }}
                   <div class="float-end"><i class="ti ti-chevron-right"></i></div>
               </a>
               <a href="#useradd-4" class="list-group-item list-group-item-action border-0">{{ __('Contract Attachments') }}
                   <div class="float-end"><i class="ti ti-chevron-right"></i></div>
               </a>
               @can ('upload contract')
                <a href="#useradd-5" class="list-group-item list-group-item-action border-0">{{ __('Discussions') }}
                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                </a>
               @endcan
               <a href="#useradd-6" class="list-group-item list-group-item-action border-0">{{ __('Discussions') }}
                   <div class="float-end"><i class="ti ti-chevron-right"></i></div>
               </a>
               <a href="#useradd-7" class="list-group-item list-group-item-action border-0">{{ __('Discussions') }}
                   <div class="float-end"><i class="ti ti-chevron-right"></i></div>
               </a>
           </div>
       </div>
   </div>
   @php
      $thirdParty = (optional($deal->serviceCommission)->without_vat_commission * optional($deal->serviceCommission)->broker_name_commission) /100;

   @endphp

   <div class="col-xl-9">
       <div id="useradd-1">
           <div class="row">
               <div class="col-xl-7">
               </div>
               <div class="col-xxl-5">
               </div>
           </div>
           <div class="card">
               <div class="card-header">
                   <h5 class="mb-0">{{ __('Client Details ') }}</h5>
               </div>
               <div class="card-body" >
                   <dl class="row align-items-center">
                        <dt class="col-sm-2 pt-2 pb-2 h6 text-lg">{{ __('Name') }}</dt>
                        <dd class="col-sm-4 pt-2 pb-2 text-lg ">{{ $deal->leads->client->name}}</dd>
                        <dt class="col-sm-2 pt-2 pb-2 h6 text-lg">{{ __('Email') }}</dt>
                        <dd class="col-sm-4 pt-2 pb-2 text-lg">{{ $deal->leads->client->email}}</dd>
                        <dt class="col-sm-2 pt-2 pb-2 h6 text-lg">{{ __('Phone') }}</dt>
                        <dd class="col-sm-4 pt-2 pb-2 text-lg">{{ $deal->leads->client->phone}}</dd>
                        <dt class="col-sm-2 pt-2 pb-2 h6 text-lg">{{ __('Emirate ID') }}</dt>
                        <dd class="col-sm-4 pt-2 pb-2 text-lg">{{ $deal->leads->client->e_id}}</dd>
                        <dt class="col-sm-2 pt-2 pb-2 h6 text-lg">{{ __('Address') }}</dt>
                        <dd class="col-sm-10 pt-2 pb-2 text-lg">{{ $deal->leads->client->address}}</dd>
                   </dl>
               </div>
           </div>

       </div>

       <div id="useradd-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Service Details ') }}</h5>
                </div>
                <div class="card-body" >
                    <dl class="row align-items-center">
                        <dt class="col-sm-2 pt-2 pb-2 h6 text-lg">{{ __('Service Name') }}</dt>
                        <dd class="col-sm-4 pt-2 pb-2 text-lg ">
                            @if ($deal->leads->product->id == 1)
                                <b>{{$deal->leads->product->name}}</b>
                                @if (optional($deal->leads->businessBanking)->business_banking_services == 'business_loan')
                                    ({{__('Business Loan')}})
                                @elseif(optional($deal->leads->businessBanking)->business_banking_services == 'fleet_finance')
                                    ({{__('Fleet Finance')}})
                                @elseif(optional($deal->leads->businessBanking)->business_banking_services == 'lgcs')
                                    ({{__('LGs / LCs')}})
                                @else
                                    ({{__('Account Opening')}})
                                @endif
                            @elseif ($deal->leads->product->id == 2)
                                <b>{{$deal->leads->product->name}} </b>
                            @else
                                <b>{{$deal->leads->product->name}} </b>
                            @endif

                        </dd>

                        <dt class="col-sm-2 pt-2 pb-2 h6 text-lg">{{ __('Finance Amount') }}</dt>
                        <dd class="col-sm-4 pt-2 pb-2 text-lg">{{ optional($deal->serviceCommission)->finance_amount ? number_format($deal->serviceCommission->finance_amount) : '-' }} AED</dd>
                        @if (!empty($deal->serviceCommission->bank_commission))
                            <dt class="col-sm-2 pt-2 pb-2 h6 text-lg">{{ __('Bank Commission') }}</dt>
                            <dd class="col-sm-4 pt-2 pb-2 text-lg">{{ optional($deal->serviceCommission)->bank_commission ? number_format($deal->serviceCommission->bank_commission) . ' AED' : '-' }}</dd>
                        @endif
                        <dt class="col-sm-2 pt-2 pb-2 h6 text-lg">{{ __('Client Commission') }}</dt>
                        <dd class="col-sm-4 pt-2 pb-2 text-lg">{{ optional($deal->serviceCommission)->customer_commission ? number_format($deal->serviceCommission->customer_commission) . ' AED' : '-' }}</dd>
                        <dt class="col-sm-2 pt-2 pb-2 h6 text-lg">{{__("Total Revenue ")}} <br> <span style="font-size:12px "> {{__('(with vat 5%)') }}</span> </dt>
                        <dd class="col-sm-4 pt-2 pb-2 text-lg">{{ optional($deal->serviceCommission)->with_vat_commission ? number_format($deal->serviceCommission->with_vat_commission) . ' AED' : '-' }}</dd>
                        <dt class="col-sm-2 pt-2 pb-2 h6 text-lg">{{__("Total Revenue")}} <br> <span style="font-size:12px "> {{ __('(without vat 5%)') }}</span></dt>
                        <dd class="col-sm-4 pt-2 pb-2 text-lg">{{ optional($deal->serviceCommission)->without_vat_commission ? number_format($deal->serviceCommission->without_vat_commission) . ' AED' : '-' }}</dd>


                    </dl>
                </div>
            </div>
       </div>

        <div id="useradd-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Commission Details ') }}</h5>
                    </div>
                    <div class="card-body" >
                        <dl class="align-items-center">
                            <!-- ========== Sales Start Section ========== -->
                                <div class="row border border-2 p-4 rounded">
                                    <h4 style="display: flex;flex-direction: row;justify-content: center;">Sales</h4>
                                    <!-- ========== Headings Start Section ========== -->
                                        <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Title')}}</dt>
                                        <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Name')}}</dt>
                                        <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Commission')}}</dt>
                                        <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Amount')}}</dt>
                                    <!-- ========== Headings End Section ========== -->
                                    @if (!empty($deal->serviceCommission->hodsalecommission) && !empty($deal->serviceCommission->hodsale) )
                                        <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('HOD') }}</dt>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->hodsaleCommission)->name}}</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->hodsalecommission}} %</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->hodsalecommission) /100)}} AED</dd>
                                    @endif
                                    @if (!empty($deal->serviceCommission->salemanagercommission) && !empty($deal->serviceCommission->salemanager) )
                                        <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Sales Manager') }}</dt>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->salemanagerCommission)->name}}</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->salemanagercommission}} %</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->salemanagercommission) /100)}} AED</dd>
                                    @endif
                                    @if (!empty($deal->serviceCommission->coordinator_commission) && !empty($deal->serviceCommission->coordinator) )
                                        <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Coordinator') }}</dt>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->coordinatorCommission)->name}}</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->coordinator_commission}} %</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->coordinator_commission) /100)}} AED</dd>
                                    @endif
                                    @if (!empty($deal->serviceCommission->team_leader_commission) && !empty($deal->serviceCommission->team_leader) )
                                        <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Team Leader') }}</dt>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->team_leaderCommission)->name}}</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->team_leader_commission}} %</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->team_leader_commission) /100)}} AED</dd>
                                    @endif
                                    @if (!empty($deal->serviceCommission->team_leader_one_commission) && !empty($deal->serviceCommission->team_leader_one) )
                                        <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Team Leader') }}</dt>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->team_leaderoneCommission)->name}}</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->team_leader_one_commission}} %</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->team_leader_one_commission) /100)}} AED</dd>
                                    @endif
                                    @if (!empty($deal->serviceCommission->salesagent_commission) && !empty($deal->serviceCommission->salesagent) )
                                        <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Sales Agent') }}</dt>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->salesagentCommission)->name}}</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->salesagent_commission}} %</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->salesagent_commission) /100)}} AED</dd>
                                    @endif
                                    @if (!empty($deal->serviceCommission->sale_agent_one_commission) && !empty($deal->serviceCommission->sale_agent_one) )
                                        <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Sales Agent') }}</dt>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->salesagentoneCommission)->name}}</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->sale_agent_one_commission}} %</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->sale_agent_one_commission) /100)}} AED</dd>
                                    @endif
                                </div>
                            <!-- ========== Sales End Section ========== -->

                            @if ($deal->leads->is_transfer == 1)
                                <!-- ========== Referral Start Section ========== -->
                                    <div class="row border border-2 mt-2 p-4 rounded">
                                        <h4 style="display: flex;flex-direction: row;justify-content: center;" class="p-4">Referral</h4>
                                        <!-- ========== Headings Start Section ========== -->
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Title')}}</dt>
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Name')}}</dt>
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Commission')}}</dt>
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Amount')}}</dt>
                                        <!-- ========== Headings End Section ========== -->
                                        @if (!empty($deal->serviceCommission->salemanagerrefcommission) && !empty($deal->serviceCommission->salemanagerref) )
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Sales Manager Ref.') }}</dt>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->salemanagerrefCommission)->name}}</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->salemanagerrefcommission}} %</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->salemanagerrefcommission) /100)}} AED</dd>
                                        @endif
                                        @if (!empty($deal->serviceCommission->agent_commission) && !empty($deal->serviceCommission->agentref) )
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Sales Agent Ref.') }}</dt>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->agentrefCommission)->name}}</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->agent_commission}} %</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->agent_commission) /100)}} AED</dd>
                                        @endif
                                    </div>
                                <!-- ========== Referral End Section ========== -->
                            @endif
                            @if ($deal->leads->lead_type == '1')
                                <!-- ==========Tele Sale Start Section ========== -->
                                    <div class="row border border-2 mt-2 p-4 rounded">
                                        <h4 style="display: flex;flex-direction: row;justify-content: center;" class="p-4">Tele Sales</h4>
                                        <!-- ========== Headings Start Section ========== -->
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Title')}}</dt>
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Name')}}</dt>
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Commission')}}</dt>
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Amount')}}</dt>
                                        <!-- ========== Headings End Section ========== -->
                                        @if (!empty($deal->serviceCommission->tshodCommission) && !empty($deal->serviceCommission->ts_hod) )
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Team Leader') }}</dt>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->tshodCommission)->name}}</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->ts_hod_commision}} %</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->ts_hod_commision) /100)}} AED</dd>
                                        @endif
                                        @if (!empty($deal->serviceCommission->ts_team_leader_commission) && !empty($deal->serviceCommission->ts_team_leader) )
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Team Leader') }}</dt>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->ts_team_leaderCommission)->name}}</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->ts_team_leader_commission}} %</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->ts_team_leader_commission) /100)}} AED</dd>
                                        @endif
                                        @if (!empty($deal->serviceCommission->tsagent_commission) && !empty($deal->serviceCommission->tsagent) )
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Agent') }}</dt>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->tsagentCommission)->name}}</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->tsagent_commission}} %</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->tsagent_commission) /100)}} AED</dd>
                                        @endif
                                    </div>
                                <!-- ==========Tele Sale End Section ========== -->

                            @elseif ($deal->leads->lead_type == '2')
                                <!-- ==========Marketing Start Section ========== -->
                                    <div class="row border border-2 mt-2 p-4 rounded">
                                        <h4 style="display: flex;flex-direction: row;justify-content: center;" class="p-4">Marketing</h4>
                                        <!-- ========== Headings Start Section ========== -->
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Title')}}</dt>
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Name')}}</dt>
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Commission')}}</dt>
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Amount')}}</dt>
                                        <!-- ========== Headings End Section ========== -->
                                        @if (!empty($deal->serviceCommission->marketingmanagercommission) && !empty($deal->serviceCommission->marketingmanager) )
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Marketing Manager') }}</dt>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->marketingmanagerCommission)->name}}</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->marketingmanagercommission}} %</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->marketingmanagercommission) /100)}} AED</dd>
                                        @endif
                                        @if (!empty($deal->serviceCommission->marketingagentcommission) && !empty($deal->serviceCommission->marketingagent) )
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Marketing Agent') }}</dt>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->marketingagentCommission)->name}}</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->marketingagentcommission}} %</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->marketingagentcommission) /100)}} AED</dd>
                                        @endif
                                        @if (!empty($deal->serviceCommission->marketingagentcommissionone) && !empty($deal->serviceCommission->marketingagentone) )
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Marketing Agent 1') }}</dt>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->marketingagentCommissionone)->name}}</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->marketingagentcommissionone}} %</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->marketingagentcommissionone) /100)}} AED</dd>
                                        @endif
                                        @if (!empty($deal->serviceCommission->marketingagentcommissiontwo) && !empty($deal->serviceCommission->marketingagenttwo) )
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Marketing Agent 2') }}</dt>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ optional($deal->serviceCommission->marketingagentCommissiontwo)->name}}</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->marketingagentcommissiontwo}} %</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->marketingagentcommissiontwo) /100)}} AED</dd>
                                        @endif
                                    </div>
                                <!-- ==========Marketing End Section ========== -->
                            @else
                                <!-- ==========Other Start Section ========== -->
                                    <div class="row border border-2 mt-2 p-4 rounded">
                                        <h4 style="display: flex;flex-direction: row;justify-content: center;" class="p-4">Other Agents</h4>
                                        <!-- ========== Headings Start Section ========== -->
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Title')}}</dt>
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Name')}}</dt>
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Commission')}}</dt>
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Amount')}}</dt>
                                        <!-- ========== Headings End Section ========== -->
                                        @if (!empty($deal->serviceCommission->other_name_commission) && !empty(optional($deal->serviceCommission->other_name)->name) )
                                            <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Agent') }}</dt>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ $deal->serviceCommission->other_name->name}}</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->other_name_commission}} %</dd>
                                            <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission - $thirdParty) * $deal->serviceCommission->other_name_commission) /100)}} AED</dd>
                                        @endif
                                    </div>
                                <!-- ==========Other End Section ========== -->
                           @endif
                           @if (!empty($deal->serviceCommission->broker_name) && !empty($deal->serviceCommission->broker_name_commission) )
                                <!-- ==========3rd Party Start Section ========== -->
                                    <div class="row border border-2 mt-2 p-4 rounded">
                                    <h4 style="display: flex;flex-direction: row;justify-content: center;" class="p-4">3rd Party Agents</h4>
                                   <!-- ========== Headings Start Section ========== -->
                                       <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Title')}}</dt>
                                       <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Name')}}</dt>
                                       <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Commission')}}</dt>
                                       <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{__('Amount')}}</dt>
                                   <!-- ========== Headings End Section ========== -->
                                        <dt class="col-sm-3 pt-2 pb-2 h6 text-lg">{{ __('Agent') }}</dt>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg ">{{ $deal->serviceCommission->broker_name}}</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ $deal->serviceCommission->broker_name_commission}} %</dd>
                                        <dd class="col-sm-3 pt-2 pb-2 text-lg">{{ number_format((($deal->serviceCommission->without_vat_commission) * $deal->serviceCommission->broker_name_commission) /100)}} AED</dd>
                                    </div>
                                <!-- ==========3rd Party End Section ========== -->
                            @endif
                        </dl>
                    </div>
                </div>
        </div>
        <div id ="useradd-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Comments') }}</h5>
                </div>
                <div class="card-body">
                        <div class="col-12 d-flex">
                            <div class="form-group mb-0 form-send w-100">
                                        <form method="post" class="card-comment-box" id="form-comment" data-action="{{route('comment.store', [$deal->id])}}">
                                            <textarea rows="1" class="form-control" name="comment" data-toggle="autosize" placeholder="{{__('Add a comment...')}}"></textarea>
                                        </form>
                                    </div>
                            <button id="comment_submit" class="btn btn-send mt-2"><i class="f-16 text-primary ti ti-brand-telegram"></i></button>
                        </div>
                    <div class="list-group list-group-flush mb-0" id="comments">
                        @foreach($deal->comments as $comment)
                            @php
                                $user = \App\Models\User::find($comment->user_id);
                                $logo=\App\Models\Utility::get_file('uploads/avatar/');
                            @endphp

                            <div class="list-group-item ">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <a href="{{ !empty($user->avatar) ? $logo . '/' . $user->avatar : $logo . '/avatar.png' }}" target="_blank">
                                            <img class="rounded-circle"  width="40" height="40" src="{{ !empty($user->avatar) ? $logo . '/' . $user->avatar : $logo . '/avatar.png' }}">
                                        </a>
                                    </div>
                                    <div class="col-auto">
                                    </div>
                                    <div class="col ml-n2">
                                        <p class="d-block h6 text-sm font-weight-light mb-0 text-break">{{ $comment->comment }}</p>
                                        <small class="d-block">{{$comment->created_at->diffForHumans()}}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div id ="useradd-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Lead Discussion') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mt-2">
                        @if(!$deal->leads->discussions->isEmpty())
                            @foreach($deal->leads->discussions as $discussion)
                                <li class="list-group-item px-0">
                                    <div class="d-block d-sm-flex align-items-start">
                                        <img src="@if($discussion->user->avatar) {{asset('/storage/uploads/avatar/'.$discussion->user->avatar)}} @else {{asset('/storage/uploads/avatar/avatar.png')}} @endif"
                                             class="img-fluid wid-40 me-3 mb-2 mb-sm-0" alt="image">
                                        <div class="w-100">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="mb-3 mb-sm-0">
                                                    <h5 class="mb-0"> {{$discussion->comment}}</h5>
                                                    <span class="text-muted text-sm">{{$discussion->user->name}}</span>
                                                </div>
                                                <div class=" form-switch form-switch-right mb-4">
                                                    {{$discussion->created_at->diffForHumans()}}
                                                </div>



                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        @else
                            <li class="text-center">
                                {{__(' No Data Available.!')}}
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div id ="useradd-7">
            <div class="card">
                <div class="row">
                    <div class="col-6">

                        <div class="card-header">
                            <h5>{{__('Service Application Activity')}}</h5>
                        </div>
                        <div class="card-body ">

                            <div class="row leads-scroll" >
                                <ul class="event-cards list-group list-group-flush mt-3 w-100">
                                    @if(!$deal->activities->isEmpty())
                                        @foreach($deal->activities as $activity)
                                            <li class="list-group-item card mb-3">
                                                <div class="row align-items-center justify-content-between">
                                                    <div class="col-auto mb-3 mb-sm-0">
                                                        <div class="d-flex align-items-center">
                                                            <div class="theme-avtar">
                                                                <div class="user-group">
                                                                    <img src="@if($activity->user->avatar) {{asset('/storage/uploads/avatar/'.$activity->user->avatar)}} @else {{asset('storage/uploads/avatar/avatar.png')}} @endif" alt="image" data-bs-toggle="tooltip" title="{{$activity->user->name}}">
                                                                </div>
                                                            </div>
                                                            <div class="ms-3">
                                                                <span class="text-dark text-sm">{{ __($activity->log_type) }}</span>
                                                                <h6 class="m-0">{!! $activity->getRemark() !!}</h6>
                                                                <small class="text-muted">{{$activity->created_at->diffForHumans()}}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">

                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @else
                                        No activity found yet.
                                    @endif
                                </ul>
                            </div>

                        </div>
                    </div>
                    <div class="col-6">

                        <div class="card-header">
                            <h5>{{__('Lead Activity')}}</h5>
                        </div>
                        <div class="card-body ">

                            <div class="row leads-scroll" >
                                <ul class="event-cards list-group list-group-flush mt-3 w-100">
                                    @if(!$deal->activities->isEmpty())
                                        @foreach($deal->leads->activities as $activity)
                                            <li class="list-group-item card mb-3">
                                                <div class="row align-items-center justify-content-between">
                                                    <div class="col-auto mb-3 mb-sm-0">
                                                        <div class="d-flex align-items-center">
                                                            <div class="user-group">
                                                                <img src="@if($activity->user->avatar) {{asset('/storage/uploads/avatar/'.$activity->user->avatar)}} @else {{asset('storage/uploads/avatar/avatar.png')}} @endif" alt="image" data-bs-toggle="tooltip" title="{{$activity->user->name}}">
                                                            </div>
                                                            <div class="ms-3">
                                                                <span class="text-dark text-sm">{{ __($activity->log_type) }}</span>
                                                                <h6 class="m-0">{!! $activity->getLeadRemark() !!}</h6>
                                                                <small class="text-muted">{{$activity->created_at}}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">

                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @else
                                        No activity found yet.
                                    @endif
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>



   </div>
</div>
@endsection
