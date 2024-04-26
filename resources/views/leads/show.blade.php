@extends('layouts.admin')
@section('page-title')
    {{optional($lead->client)->name}}
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/plugins/dropzone.min.css')}}">
@endpush
@push('script-page')
    <script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
    <script src="{{asset('assets/js/plugins/dropzone-amd-module.min.js')}}"></script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#lead-sidenav',
            offset: 300
        })
        Dropzone.autoDiscover = false;
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("#dropzonewidget", {
            maxFiles: 20,
            // maxFilesize: 2000,
            parallelUploads: 1,
            filename: false,
            // acceptedFiles: ".jpeg,.jpg,.png,.pdf,.doc,.txt",
            url: "{{route('leads.file.upload',$lead->id)}}",
            success: function (file, response) {
                if (response.is_success) {
                    dropzoneBtn(file, response);
                } else {
                    myDropzone.removeFile(file);
                    show_toastr('error', response.error, 'error');
                }
            },
            error: function (file, response) {
                myDropzone.removeFile(file);
                if (response.error) {
                    show_toastr('error', response.error, 'error');
                } else {
                    show_toastr('error', response, 'error');
                }
            }
        });
        myDropzone.on("sending", function (file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("lead_id", {{$lead->id}});
        });

        function dropzoneBtn(file, response) {
            var download = document.createElement('a');
            download.setAttribute('href', response.download);
            download.setAttribute('class', "badge bg-info mx-1");
            download.setAttribute('data-toggle', "tooltip");
            download.setAttribute('data-original-title', "{{__('Download')}}");
            download.innerHTML = "<i class='ti ti-download'></i>";

            var del = document.createElement('a');
            del.setAttribute('href', response.delete);
            del.setAttribute('class', "badge bg-danger mx-1");
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
                            if (response.is_success) {
                                btn.closest('.dz-image-preview').remove();
                            } else {
                                show_toastr('error', response.error, 'error');
                            }
                        },
                        error: function (response) {
                            response = response.responseJSON;
                            if (response.is_success) {
                                show_toastr('error', response.error, 'error');
                            } else {
                                show_toastr('error', response, 'error');
                            }
                        }
                    })
                }
            });

            var html = document.createElement('div');
            html.appendChild(download);
            @if(Auth::user()->type != 'client')
            @can('filedelete lead')
            html.appendChild(del);
            @endcan
            @endif

            file.previewTemplate.appendChild(html);
        }

        @foreach($lead->files as $file)
        @if (file_exists(storage_path('lead_files/'.$file->file_path)))
        // Create the mock file:
        var mockFile = {name: "{{$file->file_name}}", size: {{\File::size(storage_path('lead_files/'.$file->file_path))}}};
        // Call the default addedfile event handler
        myDropzone.emit("addedfile", mockFile);
        // And optionally show the thumbnail of the file:
        myDropzone.emit("thumbnail", mockFile, "{{asset(Storage::url('lead_files/'.$file->file_path))}}");
        myDropzone.emit("complete", mockFile);

            dropzoneBtn(mockFile, {download: "{{route('leads.file.download',[$lead->id,$file->id])}}", delete: "{{route('leads.file.delete',[$lead->id,$file->id])}}"});
        @endif
        @endforeach

        @can('edit lead')
        $('.summernote-simple').on('summernote.blur', function () {

            $.ajax({
                url: "{{route('leads.note.store',$lead->id)}}",
                data: {_token: $('meta[name="csrf-token"]').attr('content'), notes: $(this).val()},
                type: 'POST',
                success: function (response) {
                    if (response.is_success) {
                        // show_toastr('Success', response.success,'success');
                    } else {
                        show_toastr('error', response.error, 'error');
                    }
                },
                error: function (response) {
                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('error', response.error, 'error');
                    } else {
                        show_toastr('error', response, 'error');
                    }
                }
            })
        });
        @else
        $('.summernote-simple').summernote('disable');
        @endcan

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
            }).then((result) =>{
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {"note": result.value},
                        dataType: "html",
                        headers: {
                            'X-CSRF-Token': '{{ csrf_token() }}',
                        },
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

            })

        });
        $('.convertServiceApplication').click(function () {
            Swal.fire({
                title: "Alert",
                html: "Please check all <span style='color:red;'> Lead Information </span>. Once a lead is converted to <span style='color:#3ec9d6;'>Service Application</span>, it can't be <span style='color:red;'> changed</span>.",
                icon: "warning"
            });

        });

    </script>
    <style>
        .swal2-styled.swal2-confirm{
            /* border: 1px dashed #333 !important; */
            background-color: #ffa000 !important;
        }
        .swal2-title{
            text-transform: capitalize !important;
        }
    </style>

@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('leads.index')}}">{{__('Lead')}}</a></li>
    <li class="breadcrumb-item"> {{optional($lead->client)->name ?? "N/A"}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @if (empty($lead->is_reject))
            @can('convert lead')
                <a href="#" data-size="xl" data-url="{{ URL::to('leads/'.$lead->id.'/show_convert') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Convert Lead ['.optional($lead->client)->name.'] To Service Appliaction Form')}}" class="btn btn-sm btn-primary convertServiceApplication">
                    <i class="ti ti-exchange"></i>
                </a>
            @endcan
            @can('edit lead')
                <a href="#!"class="btn btn-sm btn-primary reject" data-url="{{ URL::to('leads/'.$lead->id.'/reject') }}" data-bs-toggle="tooltip" data-bs-original-title="{{__('Reject Lead')}}">
                    <i class="ti ti-ban"></i>
                </a>
                <a href="#!" data-size="xl" data-url="{{ URL::to('leads/'.$lead->id.'/edit') }}" data-ajax-popup="true" data-bs-toggle="tooltip" data-bs-original-title="{{__('Edit Lead')}}" class="btn btn-sm btn-primary">
                    <i class="ti ti-pencil"></i>
                </a>
            @endcan
        @endif
            @can('transfer lead')
                <a href="#!" data-size="xl" data-url="{{ URL::to('leads/'.$lead->id.'/transfer') }}" data-ajax-popup="true" data-bs-toggle="tooltip" data-bs-original-title="{{__('Transfer Lead')}}" class="btn btn-sm btn-primary">
                    <i class="ti ti-transfer"></i>
                </a>
            @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="lead-sidenav">
                            @if(Auth::user()->type != 'client')
                                <a href="#general" class="list-group-item list-group-item-action border-0">{{__('Client Info')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if(Auth::user()->type != 'client')
                                <a href="#service_info" class="list-group-item list-group-item-action border-0">{{__('Service Info')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if(Auth::user()->type != 'client')
                                <a href="#users_products" class="list-group-item list-group-item-action border-0">{{__('Users').' | '.__('Sources')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if(Auth::user()->type != 'client')
                                <a href="#discussion_file" class="list-group-item list-group-item-action border-0">{{__('Discussion').' | '.__('Files')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if(Auth::user()->type != 'client')
                                <a href="#activity" class="list-group-item list-group-item-action border-0">{{__('Activity')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <?php
                    $products = $lead->product;
                    $sources = $lead->source;
                    $calls = $lead->calls;
                    $emails = $lead->emails;
                    $personalLoan= $lead->personalLoan;
                    $mortgageLoan= $lead->mortgageLoan;
                    $businessBanking= $lead->businessBanking;
                    $realEstate= $lead->realEstate;
                    $is_reject = $lead->is_reject;
                    $leadType =  optional($lead->leadType)->name;
                    ?>

                    <div id="general" class="card">
                        <div class="card-body">
                            <h4 class="mb-4 mt-2 text-capitalize text-primary d-flex justify-content-center">{{optional($lead->client)->name ?? 'N/A'}}</h4>
                            <div class="row">
                                <div class="col-md-3 col-sm-3">
                                    <div class="d-flex align-items-start">
                                            <div class="theme-avtar bg-primary">
                                                <i class="ti ti-phone"></i>
                                            </div>
                                            <div class="ms-2">
                                                <p class="text-muted text-sm mb-0">{{__('Phone')}}</p>
                                                <h5 class="mb-0 text-primary">{{!empty($lead->client->phone)?$lead->client->phone:''}}</h5>

                                            </div>
                                        </div>
                                </div>
                                <div class="col-md-3 col-sm-3">
                                        <div class="d-flex align-items-start">
                                            <div class="theme-avtar bg-warning">
                                                <i class="ti ti-mail"></i>
                                            </div>
                                            <div class="ms-2">
                                                <p class="text-muted text-sm mb-0">{{__('Email')}}</p>
                                                <h5 class="mb-0 text-warning">{{!empty($lead->client->email)?$lead->client->email:''}}</h5>

                                            </div>
                                        </div>
                                    </div>

                                <div class="col-md-3 col-sm-3">
                                    <div class="d-flex align-items-start">
                                        <div class="theme-avtar bg-info">
                                            <i class="ti ti-flag"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{__('Emirate ID')}}</p>
                                            <h5 class="mb-0 text-info">{{!empty($lead->client->e_id)?$lead->client->e_id:''}}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3">
                                    <div class="d-flex align-items-start">
                                        <div class="theme-avtar bg-danger">
                                            <i class="ti ti-microphone"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{__('Language')}}</p>
                                            <h5 class="mb-0 text-danger">{{!empty($lead->client->language)?$lead->client->language:''}}</h5>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    @if (optional($products)->id == 1 && $businessBanking !=null)
                        @include('businessBanking.view')
                    @elseif (optional($products)->id == 2 && $personalLoan !=null)
                        @include('personalLoan.view')
                    @elseif(optional($products)->id == 3 && $mortgageLoan !=null)
                        @include('mortgageLoan.view')
                    @elseif(optional($products)->id == 4 && $realEstate !=null)
                        @include('realEstate.view')
                    @else
                        <h4 class="mb-4 mt-3 text-capitalize text-danger d-flex justify-content-center">{{__('Please Edit the Service')}}
                        </h4>
                        <a href="#!" data-size="xl" data-url="{{ URL::to('leads/'.$lead->id.'/edit') }}" data-ajax-popup="true" class="btn btn-danger d-flex justify-content-center mb-3" data-bs-original-title="{{__('Edit Lead')}}">
                            <i class="ti ti-pencil"></i>
                            <span>{{__('Service Edit')}}</span>
                        </a>
                    @endif
                    <div class="card" id="users_products">
                        <h4 class="mb-4 mt-3 text-capitalize text-primary d-flex justify-content-center">{{__('Operation Info')}}</h4>
                            <div class="row">
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">

                                            <h5>{{__('Users')}}</h5>
                                            @can ('addUser lead')
                                                <div class="float-end">
                                                    <a  data-size="md" data-url="{{ route('leads.users.edit',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add User')}}" class="btn btn-sm btn-primary ">
                                                        <i class="ti ti-plus"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>{{__('Name')}}</th>
                                                    @can ('deleteUser lead')
                                                        <th>{{__('Action')}}</th>
                                                    @endcan
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($lead->users as $user)
                                                    @if ($user->designation != 'Jovera' && $user->type != 'company')
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div>
                                                                        <img @if($user->avatar) src="{{asset('/storage/uploads/avatar/'.$user->avatar)}}" @else src="{{asset('/storage/uploads/avatar/avatar.png')}}" @endif class="wid-30 rounded-circle me-3" alt="avatar image">
                                                                    </div>
                                                                    <p class="mb-0">{{$user->name}}</p>
                                                                </div>
                                                            </td>
                                                            @can('deleteUser lead')
                                                                <td>
                                                                    <div class="action-btn bg-danger ms-2">
                                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['leads.users.destroy', $lead->id,$user->id],'id'=>'delete-form-'.$lead->id]) !!}
                                                                        <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                                        {!! Form::close() !!}
                                                                    </div>
                                                                </td>
                                                            @endcan
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>

                                <div class="col-6 row">
                                    <div class="col-12">
                                        <div class="card" >
                                            <div class="card-header">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <h5>{{__('Message')}}</h5>

                                                </div>

                                            </div>
                                            <div class="card-body">
                                                <textarea class="summernote-simple">{!! $lead->notes !!}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @if (!empty($lead->is_reject))
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h5>{{__('Reason OF Rejection')}}</h5>
                                                <div class="float-end">
                                                {{-- <a data-size="md" data-url="{{ route('leads.sources.edit',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Source')}}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus"></i>
                                                </a> --}}
                                            </div>
                                            </div>

                                        </div>
                                        <div class="card-body">
                                            <p class="text-center">
                                                {{$lead->notes}}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div id="discussion_file">
                            <div class="row">
                                <div class="col-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h5>{{__('Discussion')}}</h5>
                                            <div class="float-end">
                                                <a data-size="lg" data-url="{{ route('leads.discussions.create',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Message')}}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus"></i>
                                                </a>
                                            </div>

                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush mt-2">
                                                @if(!$lead->discussions->isEmpty())
                                                    @foreach($lead->discussions as $discussion)
                                                        <li class="list-group-item px-0">
                                                            <div class="d-block d-sm-flex align-items-start">
                                                                <img src="@if(optional($discussion->user)->avatar) {{asset('/storage/uploads/avatar/'.optional($discussion->user)->avatar)}} @else {{asset('/storage/uploads/avatar/avatar.png')}} @endif"
                                                                     class="img-fluid wid-40 me-3 mb-2 mb-sm-0" alt="image">
                                                                <div class="w-100">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <div class="mb-3 mb-sm-0">
                                                                            <h6 class="mb-0"> {{$discussion->comment}}</h6>
                                                                            <span class="text-muted text-sm">{{optional($discussion->user)->name}}</span>
                                                                        </div>
                                                                        <div class="form-check form-switch form-switch-right mb-2">
                                                                            {{$discussion->created_at}}
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
                                <div class="col-6">
                                    <div id="files" class="card">
                                        <div class="card-header ">
                                            <h5>{{__('Files')}}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="col-md-12 dropzone top-5-scroll browse-file" id="dropzonewidget"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="activity" class="card">
                        <div class="card-header">
                            <h5>{{__('Activity')}}</h5>
                        </div>
                        <div class="card-body ">

                            <div class="row leads-scroll" >
                                <ul class="event-cards list-group list-group-flush mt-3 w-100">
                                    @if(!$lead->activities->isEmpty())
                                        @foreach($lead->activities as $activity)
                                            <li class="list-group-item card mb-3">
                                                <div class="row align-items-center justify-content-between">
                                                    <div class="col-auto mb-3 mb-sm-0">
                                                        <div class="d-flex align-items-center">
                                                            <div class="user-group">
                                                                <img src="@if(optional($activity->user)->avatar) {{asset('/storage/uploads/avatar/'.optional($activity->user)->avatar)}} @else {{asset('storage/uploads/avatar/avatar.png')}} @endif" alt="image" data-bs-toggle="tooltip" title="{{optional($activity->user)->name}}">
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
@endsection
