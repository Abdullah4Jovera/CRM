@extends('layouts.admin')
@php
   // $profile=asset(Storage::url('uploads/avatar/'));
    $profile=\App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('page-title')
    {{__('Manage Client')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Client')}}</li>
@endsection
@section('action-btn')

@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>

                                <th>{{__('#')}}</th>
                                <th>{{__('Client')}}</th>
                                <th>{{__('Phone')}}</th>
                                <th>{{__('Email')}}</th>
                                <th>{{__('Pipeline')}}</th>
                                <th>{{__('Stage')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i =1;
                                @endphp
                            @foreach ($clients as $client)
                                <tr>
                                    <td class="Id">
                                        {{$i}}
                                    </td>
                                    <td>
                                        <a href="@can('view lead')@if($client->is_active){{route('leads.show',$client->id)}}@else#@endif @else#@endcan">
                                            {{ $client->client_name, }}
                                        </a>
                                    </td>
                                    <td>{{ $client->client_phone }}</td>
                                    <td>{{ $client->client_email }}</td>
                                    <td>{{optional($client->pipeline)->name}}</td>
                                    <td>{{optional($client->stage)->name}}</td>
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
