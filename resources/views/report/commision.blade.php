@extends('layouts.admin')
@section('page-title')
    {{__('Manage Commission')}}

@endsection
<link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
@push('script-page')
    {{-- <script src="{{ asset('js/jspdf.min.js') }} "></script>
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jszip.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pdfmake.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/vfs_fonts.js') }}"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js "></script>
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script> --}}

    <script>

        var filename = $('#filename').val();

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {type: 'jpeg', quality: 1},
                html2canvas: {scale: 4, dpi: 72, letterRendering: true},
                jsPDF: {unit: 'in', format: 'A4'}
            };
            html2pdf().set(opt).from(element).save();
        }

        $(document).ready(function () {
            var filename = $('#filename').val();
            $('#report-dataTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        title: filename
                    },
                    {
                        extend: 'pdf',
                        title: filename
                    },  {
                        extend: 'csv',
                        title: filename
                    }
                ]
            });
        });
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Commission Report')}}</li>
@endsection


@section('action-btn')
    <div class="float-end">
        {{-- <a class="btn btn-sm btn-primary" data-bs-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1" data-bs-toggle="tooltip" title="{{__('Filter')}}">
            <i class="ti ti-filter"></i>
        </a> --}}

        {{-- <a href="{{route('commission.export')}}" data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-export"></i>
        </a> --}}

        {{-- <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()"data-bs-toggle="tooltip" title="{{__('Download')}}" data-original-title="{{__('Download')}}">
            <span class="btn-inner--icon"><i class="ti ti-download"></i></span>
        </a> --}}

    </div>
    @endsection


@section('content')
    <div class="row">
        <div class="col-sm-2">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <small class="text-muted">{{__('Total Amount')}}</small>
                            <h4 class="m-0 total">{{ $cnt_deal['total'] }}</h4>
                        </div>
                        <div class="col-auto">
                            <div class="theme-avtar bg-info">
                                <i class="ti ti-layers-difference"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <small class="text-muted">{{__('Current Month Amount')}}</small>
                            <h4 class="m-0 curt-total">{{ $cnt_deal['curr_month'] }}</h4>
                        </div>
                        <div class="col-auto">
                            <div class="theme-avtar bg-danger">
                                <i class="ti ti-layers-difference"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <small class="text-muted">{{__('Total Rejection')}}</small>
                            <h4 class="m-0 total-reject">{{ $cnt_deal['reject'] }}</h4>
                        </div>
                        <div class="col-auto">
                            <div class="theme-avtar bg-primary">
                                <i class="ti ti-layers-difference"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <small class="text-muted">{{__('Curt. Month Rejection')}}</small>
                            <h4 class="m-0 curt-total-reject">{{ $cnt_deal['reject_month'] }}</h4>
                        </div>
                        <div class="col-auto">
                            <div class="theme-avtar bg-warning">
                                <i class="ti ti-layers-difference"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <small class="text-muted">{{__('Total Collection')}}</small>
                            <h4 class="m-0 total-col">{{ $cnt_deal['collection'] }}</h4>
                        </div>
                        <div class="col-auto">
                            <div class="theme-avtar bg-primary">
                                <i class="ti ti-layers-difference"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <small class="text-muted">{{__('Curt. Month Collection ')}}</small>
                            <h4 class="m-0 curt-total-col">{{ $cnt_deal['collection_month'] }}</h4>
                        </div>
                        <div class="col-auto">
                            <div class="theme-avtar bg-primary">
                                <i class="ti ti-layers-difference"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(array('route' => array('report.commission'),'method'=>'get','id'=>'report_account')) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row">
                                    <select name="start_month" class="form-control selectpicker" id="selectmonth" data-none-selected-text="Nothing selected" >
                                        <option value=" ">{{__('Select Month')}}</option>
                                        <option @if($month == 1 ) selected @endif value="1">{{__('January')}}</option>
                                        <option @if($month == 2 ) selected @endif value="2">{{__('February')}}</option>
                                        <option @if($month == 3 ) selected @endif value="3">{{__('March')}}</option>
                                        <option @if($month == 4 ) selected @endif value="4">{{__('April')}}</option>
                                        <option @if($month == 5 ) selected @endif value="5">{{__('May')}}</option>
                                        <option @if($month == 6 ) selected @endif value="6">{{__('June')}}</option>
                                        <option @if($month == 7 ) selected @endif value="7">{{__('July')}}</option>
                                        <option @if($month == 8 ) selected @endif value="8">{{__('August')}}</option>
                                        <option @if($month == 9 ) selected @endif value="9">{{__('September')}}</option>
                                        <option @if($month == 10 ) selected @endif value="10">{{__('October')}}</option>
                                        <option @if($month == 11 ) selected @endif value="11">{{__('November')}}</option>
                                        <option @if($month == 12 ) selected @endif value="12">{{__('December')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="row">
                                    <div class="col-auto">
                                        <a href="#" class="btn btn-sm btn-primary" onclick="document.getElementById('report_account').submit(); return false;" data-bs-toggle="tooltip" title="{{__('Apply')}}" data-original-title="{{__('apply')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>
                                        <a href="{{route('report.commission')}}" class="btn btn-sm btn-danger " data-bs-toggle="tooltip"  title="{{ __('Reset') }}" data-original-title="{{__('Reset')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    {{-- <div id="printableArea">
        <div class="row mt-3">
            <div class="col">
                <input type="hidden" value="{{__('Account Statement').'  '.'Report of'.' '.$filter['startDateRange'].' to '.$filter['endDateRange']}}" id="filename">
                <div class="card p-4 mb-4">
                    <p class="report-text gray-text mb-0">{{__('Report')}} :</p>
                    <h6 class="report-text mb-0">{{__('Commission Statement Summary')}}</h6>
                </div>
            </div>
            @if($filter['account']!=__('All'))
                <div class="col">
                    <div class="card p-4 mb-4">
                        <p class="report-text gray-text mb-0">{{__('Account')}} :</p>
                        <h6 class="report-text mb-0">{{$filter['account']}}</h6>
                    </div>
                </div>
            @endif
            <div class="col">
                <div class="card p-4 mb-4">
                    <p class="report-text gray-text mb-0">{{__('Type')}} :</p>
                </div>
            </div>
            <div class="col">
                <div class="card p-4 mb-4">
                    <p class="report-text gray-text mb-0">{{__('Duration')}} :</p>
                    <h6 class="report-text mb-0">{{$filter['startDateRange'].' to '.$filter['endDateRange']}}</h6>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                {{-- <th></th> --}}
                                <th>{{__('User Name ')}}</th>
                                <th>{{__('Total Deal Amount')}}</th>
                                <th>{{__('Total Collection Amount')}}</th>
                                <th>{{__('Total Commission')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($reportData['hod'] as $hod)
                                    <tr class="font-style">
                                        <td>{{$hod->name}} </td>
                                        <td>{{$hod->total}} </td>
                                        <td>{{$hod->totalAmountBySix}} </td>
                                        <td>{{($hod->total_commission * $hod->totalAmountBySix)/100}} </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>{{__('Sales Manager')}}</th>
                                    <th></th>
                                    <th></th>
                                </tr>

                                @foreach ($reportData['salemanager'] as $salemanager)
                                    <tr class="font-style">
                                        <td>{{$salemanager->name}} </td>
                                        <td>{{$salemanager->total}} </td>
                                        <td>{{$salemanager->totalAmountBySix}} </td>
                                        <td>{{($salemanager->total_commission *$salemanager->totalAmountBySix)/100}} </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>{{__('Coordinator')}}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                @foreach ($reportData['coordinator'] as $coordinator)
                                    <tr class="font-style">
                                        <td>{{$coordinator->name}} </td>
                                        <td>{{$coordinator->total}} </td>
                                        <td>{{$coordinator->totalAmountBySix}} </td>
                                        <td>{{($coordinator->total_commission *$coordinator->totalAmountBySix)/100}} </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>{{__('Sales Team_Leader')}}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                @foreach ($reportData['team_leader'] as $team_leader)
                                    <tr class="font-style">
                                        <td>{{$team_leader->name}} </td>
                                        <td>{{$team_leader->total}} </td>
                                        <td>{{$team_leader->totalAmountBySix}} </td>
                                        <td>{{($team_leader->total_commission *$team_leader->totalAmountBySix)/100}} </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>{{__('Sales Agent')}}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                @foreach ($reportData['salesagent'] as $salesagent)
                                    <tr class="font-style">
                                        <td>{{$salesagent->name}} </td>
                                        <td>{{$salesagent->total}} </td>
                                        <td>{{$salesagent->totalAmountBySix}} </td>
                                        <td>{{($salesagent->total_commission *$salesagent->totalAmountBySix)/100}} </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>{{__('Sale Manager Ref.')}}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                @foreach ($reportData['salemanagerref'] as $salemanagerref)
                                    <tr class="font-style">
                                        <td>{{$salemanagerref->name}} </td>
                                        <td>{{$salemanagerref->total}} </td>
                                        <td>{{$salemanagerref->totalAmountBySix}} </td>
                                        <td>{{($salemanagerref->total_commission *$salemanagerref->totalAmountBySix)/100}} </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>{{__('Sale Agent Ref.')}}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                @foreach ($reportData['agentref'] as $agentref)
                                    <tr class="font-style">
                                        <td>{{$agentref->name}} </td>
                                        <td>{{$agentref->total}} </td>
                                        <td>{{$agentref->totalAmountBySix}} </td>
                                        <td>{{($agentref->total_commission *$agentref->totalAmountBySix)/100}} </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>{{__('Tele Sales Team Leader')}}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                @foreach ($reportData['ts_team_leader'] as $ts_team_leader)
                                    <tr class="font-style">
                                        <td>{{$ts_team_leader->name}} </td>
                                        <td>{{$ts_team_leader->total}} </td>
                                        <td>{{$ts_team_leader->totalAmountBySix}} </td>
                                        <td>{{($ts_team_leader->total_commission *$ts_team_leader->totalAmountBySix)/100}} </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>{{__('Tele Sales Agent')}}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                @foreach ($reportData['tsagent'] as $tsagent)
                                    <tr class="font-style">
                                        <td>{{$tsagent->name}} </td>
                                        <td>{{$tsagent->total}} </td>
                                        <td>{{$tsagent->totalAmountBySix}} </td>
                                        <td>{{($tsagent->total_commission *$tsagent->totalAmountBySix)/100}} </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>{{__('Marketing Manager')}}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                @foreach ($reportData['marketingmanager'] as $marketingmanager)
                                    <tr class="font-style">
                                        <td>{{$marketingmanager->name}} </td>
                                        <td>{{$marketingmanager->total}} </td>
                                        <td>{{$marketingmanager->totalAmountBySix}} </td>
                                        <td>{{($marketingmanager->total_commission *$marketingmanager->totalAmountBySix)/100}} </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>{{__('Marketing Agent')}}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                @foreach ($reportData['marketingagent'] as $marketingagent)
                                    <tr class="font-style">
                                        <td>{{$marketingagent->name}} </td>
                                        <td>{{$marketingagent->total}} </td>
                                        <td>{{$marketingagent->totalAmountBySix}} </td>
                                        <td>{{($marketingagent->total_commission *$marketingagent->totalAmountBySix)/100}} </td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
