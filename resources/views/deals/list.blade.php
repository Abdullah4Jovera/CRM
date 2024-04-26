@extends('layouts.admin')
@section('page-title')
    {{__('Manage Deals')}} @if($pipeline) - {{$pipeline->name}} @endif
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
@endpush
@push('script-page')
    <script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
    <script>
        $(document).on("change", ".change-pipeline select[name=default_pipeline_id]", function () {
            $('#change-pipeline').submit();
        });
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Lead')}}</li>
@endsection
@section('action-btn')
    {{-- <div class="float-end">
        <a href="{{ route('deals.index') }}" data-bs-toggle="tooltip" title="{{__('Kanban View')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-layout-grid"></i>
        </a>
        <a href="#" data-size="lg" data-url="{{ route('deals.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create New Deal')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div> --}}
@endsection

@section('content')
    @if($pipeline)
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                <tr>
                                    <th>{{__('#')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Phone')}}</th>
                                    <th>{{__('Price')}}</th>
                                    <th>{{__('Stage')}}</th>
                                    <th>{{__('Users')}}</th>
                                    <th width="300px">{{__('Action')}}</th>

                                </tr>
                                </thead>
                                <tbody>
                                @if(count($deals) > 0)
                                        @php
                                            $i = 1;
                                        @endphp
                                    @foreach ($deals as $deal)
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ optional($deal)->client_name }}</td>
                                            <td>{{ optional($deal)->client_phone }}</td>
                                            <td>{{\Auth::user()->priceFormat(optional($deal->serviceCommission)->without_vat_commission)}}</td>
                                            <td>{{ $deal->stage_name }}</td>
                                            <td>{{optional($deal->product)->name}}</td>
                                            <td>
                                                @foreach($deal->leads->users as $user)
                                                    @if($deal->leads->created_by != $user->id && $user->designation != 'Jovera' && $user->type != 'company')
                                                        <a href="#" class="btn btn-sm mr-1 p-0 rounded-circle">
                                                            <img alt="image" data-toggle="tooltip" data-original-title="{{$user->name}}" @if($user->avatar) src="{{asset('/storage/uploads/avatar/'.$user->avatar)}}" @else src="{{asset('/storage/uploads/avatar/avatar.png')}}" @endif class="rounded-circle " width="25" height="25">
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </td>
                                            @if(\Auth::user()->type != 'Client')
                                                <td class="Action">
                                                    <span>
                                                        @can('view deal')
                                                            @if($deal->is_active)
                                                                <div class="action-btn bg-warning ms-2">
                                                                <a href="{{route('deals.show',$deal->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-size="xl" data-bs-toggle="tooltip" title="{{__('View')}}" data-title="{{__('Lead Detail')}}">
                                                                    <i class="ti ti-eye text-white"></i>
                                                                </a>
                                                            </div>
                                                            @endif
                                                        @endcan
                                                        {{-- @can('edit deal')
                                                            <div class="action-btn bg-info ms-2">
                                                                <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-url="{{ URL::to('deals/'.$deal->id.'/edit') }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Lead Edit')}}">
                                                                    <i class="ti ti-pencil text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endcan --}}
                                                        {{-- @can('delete deal')
                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['deals.destroy', $deal->id],'id'=>'delete-form-'.$deal->id]) !!}
                                                                <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                                {!! Form::close() !!}
                                                             </div>
                                                        @endif --}}
                                                    </span>

                                                </td>
                                            @endif
                                        </tr>
                                    @php
                                        $i++;
                                    @endphp
                                    @endforeach
                                @else
                                    <tr class="font-style">
                                        <td colspan="6" class="text-center">{{ __('No data available in table') }}</td>
                                    </tr>
                                @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
