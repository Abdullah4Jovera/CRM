@extends('layouts.admin')
@section('page-title')
    {{$deal->name}}
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
            target: '#deal-sidenav',
            offset: 300
        })
        Dropzone.autoDiscover = false;
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("#dropzonewidget", {
            maxFiles: 20,
            // maxFilesize: 20,
            parallelUploads: 1,
            filename: false,
            // acceptedFiles: ".jpeg,.jpg,.png,.pdf,.doc,.txt",
            url: "{{route('deals.file.upload',$deal->id)}}",
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
                    show_toastr('error', response.error, 'error');
                }
            }
        });
        myDropzone.on("sending", function (file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("deal_id", {{$deal->id}});
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
            @can('edit deal')
            html.appendChild(del);
            @endcan
            @endif

            file.previewTemplate.appendChild(html);
        }

        @foreach($deal->files as $file)
        @if (file_exists(storage_path('deal_files/'.$file->file_path)))
        // Create the mock file:
        var mockFile = {name: "{{$file->file_name}}", size: {{\File::size(storage_path('deal_files/'.$file->file_path))}}};
        // Call the default addedfile event handler
        myDropzone.emit("addedfile", mockFile);
        // And optionally show the thumbnail of the file:
        myDropzone.emit("thumbnail", mockFile, "{{asset(Storage::url('deal_files/'.$file->file_path))}}");
        myDropzone.emit("complete", mockFile);

        dropzoneBtn(mockFile, {download: "{{route('deals.file.download',[$deal->id,$file->id])}}", delete: "{{route('deals.file.delete',[$deal->id,$file->id])}}"});
        @endif
        @endforeach

        @can('edit deal')
        $('.summernote-simple').on('summernote.blur', function () {

            $.ajax({
                url: "{{route('deals.note.store',$deal->id)}}",
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

        @can('edit task')
        $(document).on("click", ".task-checkbox", function () {
            var chbox = $(this);
            var lbl = chbox.parent().parent().find('label');

            $.ajax({
                url: chbox.attr('data-url'),
                data: {_token: $('meta[name="csrf-token"]').attr('content'), status: chbox.val()},
                type: 'PUT',
                success: function (response) {
                    if (response.is_success) {
                        chbox.val(response.status);
                        if (response.status) {
                            lbl.addClass('strike');
                            lbl.find('.badge').removeClass('badge-warning').addClass('badge-success');
                        } else {
                            lbl.removeClass('strike');
                            lbl.find('.badge').removeClass('badge-success').addClass('badge-warning');
                        }
                        lbl.find('.badge').html(response.status_label);

                        show_toastr('success', response.success);
                    } else {
                        show_toastr('error', response.error);
                    }
                },
                error: function (response) {
                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('success', response.success);
                    } else {
                        show_toastr('error', response.error);
                    }
                }
            })
        });
        @endcan
        $(selector).click(function (e) {
            e.preventDefault();

        });
        function downloadFile() {
            // e.preventDefault();

            var download = document.createElement('a');
                download.setAttribute('href', response.download);
                download.setAttribute('class', "badge bg-info mx-1");
                download.setAttribute('data-toggle', "tooltip");
                download.setAttribute('data-original-title', "{{__('Download')}}");
                download.innerHTML = "<i class='ti ti-download'></i>";
        }

    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('deals.index')}}">{{__('Deal')}}</a></li>
    <li class="breadcrumb-item"> {{$deal->name}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        @can('edit contract')
           @if (\Auth::user()->id == 6 || \Auth::user()->id == 13)
             @if ($deal->leads->deal_stage_id != 6 )
                 <div class="action-btn bg-info ms-2">
                     <a href="#" class="btn btn-sm btn-primary btn-icon m-1" data-url="{{ route('contract.edit', $deal->id) }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="{{__('Service Edit')}}" data-title="{{__('Service Edit')}}">
                         <i class="ti ti-pencil text-white"></i>
                     </a>
                 </div>
             @endif
           @endif
        @endcan
        <a href="{{ route('get.contract',$deal->id) }}"  target="_blank" class="btn btn-sm btn-primary btn-icon m-1" >
            <i class="ti ti-eye text-white" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Service Application') }}"> </i>
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="deal-sidenav">

                            <a href="#general" class="list-group-item list-group-item-action border-0">{{__('General')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#users_products" class="list-group-item list-group-item-action border-0">{{__('Users').' | '.__('Products')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>


                            <a href="#sources_emails" class="list-group-item list-group-item-action border-0">{{__('Sources').' | '.__('Emails')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>


                            <a href="#discussion_note" class="list-group-item list-group-item-action border-0">{{__('Discussion').' | '.__('Notes')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>


                            <a href="#files" class="list-group-item list-group-item-action border-0">{{__('Files')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>


                            <a href="#calls" class="list-group-item list-group-item-action border-0">{{__('Calls')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>


                            <a href="#activity" class="list-group-item list-group-item-action border-0">{{__('Activity')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>


                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="general" class="card">
                        <div class="card-body">
                            <h4 class="mb-4 mt-2 text-capitalize text-primary d-flex justify-content-center">{{optional($deal->leads->client)->name ?? 'N/A'}}</h4>
                            <div class="row">
                                <div class="col-md-3 col-sm-3">
                                    <div class="d-flex align-items-start">
                                            <div class="theme-avtar bg-primary">
                                                <i class="ti ti-phone"></i>
                                            </div>
                                            <div class="ms-2">
                                                <p class="text-muted text-sm mb-0">{{__('Phone')}}</p>
                                                <h5 class="mb-0 text-primary">{{optional($deal->leads->client)->phone ?? 'N/A'}}</h5>

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
                                                <h5 class="mb-0 text-warning">{{optional($deal->leads->client)->email ?? 'N/A'}}</h5>

                                            </div>
                                        </div>
                                    </div>

                                <div class="col-md-3 col-sm-3">
                                    <div class="d-flex align-items-start">
                                        <div class="theme-avtar bg-info">
                                            <i class="ti ti-flag"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{__('Nationality')}}</p>
                                            <h5 class="mb-0 text-info">{{optional($deal->leads->client)->nationality ?? 'N/A'}}</h5>

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
                                            <h5 class="mb-0 text-danger">{{optional($deal->leads->client)->language ?? 'N/A'}}</h5>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    @php
                        $sources = optional($deal)->source;
                        $personalLoan= optional($deal->leads)->personalLoan;
                        $mortgageLoan= optional($deal->leads)->mortgageLoan;
                        $businessBanking= optional($deal->leads)->businessBanking;
                        $realEstate= optional($deal->leads)->realEstate;
                        $is_reject = optional($deal->leads)->is_reject;
                        $leadType = optional($deal->leads->leadType)->name;
                        $lead = $deal->leads;
                        $products = $deal->product;
                    @endphp
                    @if (optional($deal->leads->product)->id == 1 && $businessBanking !=null)
                        @include('businessBanking.view')
                    @elseif (optional($deal->leads->product)->id == 2 && $personalLoan !=null)
                        @include('personalLoan.view')
                    @elseif(optional($deal->leads->product)->id == 3 && $mortgageLoan !=null)
                        @include('mortgageLoan.view')
                    @elseif(optional($deal->leads->product)->id == 4 && $realEstate !=null)
                        @include('mortgageLoan.view')
                    @else
                        <h4 class="mb-4 mt-3 text-capitalize text-danger d-flex justify-content-center">{{__('Service Not Found')}}
                        </h4>

                    @endif
                    <div id="sources_emails">
                        <div class="row">
                            {{-- <div class="col-4">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('Deal Users')}}</h5>

                                            <div class="float-end">
                                                <a data-size="md" data-url="{{ route('deals.users.edit',$deal->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add User')}}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>{{__('Name')}}</th>
                                                    <th>{{__('Action')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($deal->users as $user)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div>
                                                                    <img @if($user->avatar) src="{{asset('/storage/uploads/avatar/'.$user->avatar)}}" @else src="{{asset('/storage/uploads/avatar/avatar.png')}}" @endif class="wid-30 rounded-circle me-3" >
                                                                </div>
                                                                <p class="mb-0">{{$user->name}}</p>
                                                            </div>
                                                        </td>
                                                        @can('edit deal')
                                                            <td>
                                                                <div class="action-btn bg-danger ms-2">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['deals.users.destroy', $deal->id,$user->id],'id'=>'delete-form-'.$deal->id]) !!}
                                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                                    {!! Form::close() !!}
                                                                </div>
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('Deal Users')}}</h5>

                                            <div class="float-end">
                                                <a data-size="md" data-url="{{ route('leads.users.edit',$deal->leads->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add User')}}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>{{__('Name')}}</th>
                                                    {{-- <th>{{__('Action')}}</th> --}}
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($deal->leads->users as $user)
                                                    @if($deal->leads->created_by != $user->id && $user->designation != 'Jovera' && $user->type != 'company')
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div>
                                                                        <img @if($user->avatar) src="{{asset('/storage/uploads/avatar/'.$user->avatar)}}" @else src="{{asset('/storage/uploads/avatar/avatar.png')}}" @endif class="wid-30 rounded-circle me-3" >
                                                                    </div>
                                                                    <p class="mb-0">{{$user->name}}</p>
                                                                </div>
                                                            </td>
                                                            @can('edit deal')
                                                                @if (\Auth::user()->email =='info@jovera.ae' || \Auth::user()->email =='fady@jovera.ae' )
                                                                    <td>
                                                                        <div class="action-btn bg-danger ms-2">
                                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['leads.users.destroy', $deal->leads->id,$user->id],'id'=>'delete-form-'.$deal->leads->id]) !!}
                                                                            <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                                            {!! Form::close() !!}
                                                                        </div>
                                                                    </td>
                                                                @endif
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
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('Sources')}}</h5>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>{{__('Name')}}</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{optional($deal->leads->source)->name}} </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="files" class="card">
                        <div class="row">
                            <div class="col-6">
                                <div class="card-header ">
                                    <h5>{{__('Deal Files')}}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="col-md-12 dropzone top-5-scroll browse-file" id="dropzonewidget"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card-header ">
                                    <h5>{{__('Lead Files')}}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="scrollbar-inner">
                                        <div class="card-wrapper p-3 lead-common-box">
                                            @if(!empty($deal->leads->files))
                                                <div class="card mb-3 border shadow-none">
                                                    <div class="px-3 py-3">

                                                        @foreach ($deal->leads->files as $file)
                                                            <div class="row align-items-center mt-3">
                                                                <div class="col">
                                                                    <h6 class="text-sm mb-0">
                                                                        <a href="#!">{{ $file->file_name }}</a>
                                                                    </h6>
                                                                    <p class="card-text small text-muted">
                                                                        {{-- {{ number_format(\File::size(storage_path('lead_files/' . $file->file_name)) / 1048576, 2) . ' ' . __('MB') }} --}}
                                                                    </p>
                                                                </div>
                                                                <div class="action-btn bg-warning ">
                                                                    <a href="{{ url('leads/'.$file->lead_id.'/file/'.$file->id.'') }}"
                                                                       class=" btn btn-sm d-inline-flex align-items-center"
                                                                       download="" data-bs-toggle="tooltip" title="Download">
                                                                        <span class="text-white"> <i class="ti ti-download"></i></span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="discussion_note">
                        <div class="row">
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('Deal Discussion')}}</h5>

                                            <div class="float-end">
                                                <a data-size="lg" data-url="{{ route('deals.discussions.create',$deal->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Message')}}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush mt-2">
                                            @if(!$deal->discussions->isEmpty())
                                                @foreach($deal->discussions as $discussion)
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
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('Lead Discussion')}}</h5>

                                            {{-- <div class="float-end">
                                                <a data-size="lg" data-url="{{ route('deals.discussions.create',$deal->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Message')}}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus"></i>
                                                </a>
                                            </div> --}}
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush mt-2">
                                            @if(!empty($lead->discussions))
                                                @foreach($lead->discussions as $discussion)
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
                        </div>
                    </div>

                    <div id="activity" class="card">
                        <div class="row">
                            <div class="col-6">

                                <div class="card-header">
                                    <h5>{{__('Deal Activity')}}</h5>
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
                                                                            {{-- <img src="@if($activity->user->avatar) {{asset('/storage/uploads/avatar/'.$activity->user->avatar)}} @else {{asset('storage/uploads/avatar/avatar.png')}} @endif" alt="image" data-bs-toggle="tooltip" title="{{$activity->user->name}}"> --}}
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
                                                                        {{-- <img src="@if($activity->user->avatar) {{asset('/storage/uploads/avatar/'.$activity->user->avatar)}} @else {{asset('storage/uploads/avatar/avatar.png')}} @endif" alt="image" data-bs-toggle="tooltip" title="{{$activity->user->name}}"> --}}
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
    </div>
@endsection
