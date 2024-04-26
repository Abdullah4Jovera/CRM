@extends('layouts.admin')
@section('page-title', __('Manage Contract'))
@push('script-page')
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Contract') }}</li>
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
                                <th scope="col">{{ __('#') }}</th>
                                @if(\Auth::user()->type!='client')
                                    <th scope="col">{{ __('Client') }}</th>
                                @endif
                                <th scope="col">{{ __('Service') }}</th>
                                <th scope="col">{{ __('Pipeline') }}</th>
                                <th scope="col">{{ __('Lead From') }}</th>
                                <th scope="col">{{ __('Finance Amount') }}</th>
                                <th scope="col">{{ __('Contract Status') }}</th>
                                <th scope="col">{{ __('Action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($contracts as $deals)
                            {{-- @dd($deals->leads->users) --}}
                                @php
                                    $deal = $deals->leads;
                                @endphp
                               @if ($deal && $deal->users)
                                 @foreach ($deal->users as $item)
                                     @if ($item->id == \Auth::user()->id)
                                         <tr class="font-style">
                                             <td>
                                                 <a href="{{ route('contract.show', $deals->id) }}" class="btn btn-outline-primary">{{ \Auth::user()->contractNumberFormat($deals->id) }}</a>
                                             </td>
                                             <td>{{ $deal->client->name }}</td>
                                             <td>{{ $deal->product->name }}</td>
                                             <td style="text-transform: capitalize;">{{ $deal->pipeline->name }}</td>
                                             <td style="text-transform: capitalize;">{{ $deal->leadType->name }}</td>
                                             <td>{{ optional($deals->serviceCommission)->finance_amount ? number_format($deals->serviceCommission->finance_amount) : '-' }}</td>

                                             <td>
                                                 @if ($deals->contract_stage == 'unsigned')
                                                     <span class="fs-6 p-2 mt-2 badge rounded bg-primary">{{ __('New') }}</span>
                                                 @elseif ($deals->contract_stage == 'pending')
                                                     <span class="fs-6 p-2 mt-2 badge rounded bg-danger">{{ __('Pending') }}</span>
                                                 @else
                                                     <span class="fs-6 p-2 mt-2 badge rounded bg-info">{{ __('Ready To Sign') }}</span>
                                                 @endif
                                             </td>
                                             <td class="action">
                                                 @can('show contract')
                                                     <div class="action-btn bg-warning ms-2">
                                                         <a href="{{ route('contract.show', $deals->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-whatever="{{ __('View Budget Planner') }}" data-bs-toggle="tooltip" title="{{ __('View') }}"><span class="text-white"><i class="ti ti-eye"></i></span></a>
                                                     </div>
                                                 @endcan
                                                 @can('edit contract')
                                                     <div class="action-btn bg-info ms-2">
                                                         <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-url="{{ route('contract.edit', $deals->id) }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="{{ __('Edit') }}" data-title="{{ __('Edit Contract') }}"><i class="ti ti-pencil text-white"></i></a>
                                                     </div>
                                                 @endcan
                                                 @can('edit contract')
                                                     <div class="action-btn bg-primary ms-2">
                                                         <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-url="{{ route('contract.dealStatus', $deals->id) }}" data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title="{{ __('Status Update') }}" data-title="{{ __('Contract Status Update') }}"><i class="ti ti-status-change text-white"></i></a>
                                                     </div>
                                                 @endcan
                                             </td>
                                         </tr>
                                     @endif
                                 @endforeach
                               @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
