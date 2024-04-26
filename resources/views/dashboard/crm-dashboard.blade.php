@extends('layouts.admin')
@section('page-title')
    {{__('Dashboard')}}
@endsection
@push('script-page')
    <script>
        (function () {
            var options = {
                chart: {
                    height: 400,
                    type: 'bar',
                    toolbar: {
                        show: true,
                    },
                },
                dataLabels: {
                    enabled: true
                },
                stroke: {
                    width: 3,
                    curve: 'smooth'
                },
                series: [{
                    name: "{{__('Leads')}}",
                    data: {!! json_encode($incExpBarChartData['leads']) !!}
                }, {
                    name: "{{__('Contracts')}}",
                    data: {!! json_encode($incExpBarChartData['contracts']) !!}
                }, {
                    name: "{{__('Deals')}}",
                    data: {!! json_encode($incExpBarChartData['deals']) !!}
                },{
                    name: "{{__('Clinets')}}",
                    data: {!! json_encode($incExpBarChartData['clients']) !!}
                }],
                xaxis: {
                    categories: {!! json_encode($incExpBarChartData['month']) !!},
                },
                colors: ['#6610f2', '#3ec9d6','#fd7e14','#ff3a6e'],
                fill: {
                    type: 'solid',
                    width: 6,
                },
                grid: {
                    strokeDashArray: 5,
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'center',
                }
            };
            var chart = new ApexCharts(document.querySelector("#incExpBarChart"), options);
            chart.render();
        })();

    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('CRM')}}</li>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="card">
            <div class="card-body">
                <div class="theme-avtar bg-primary">
                    <i class="ti ti-users"></i>
                </div>
                <p class="text-muted text-sm mt-4 mb-2">{{__('Total')}}</p>
                <h6 class="mb-3">{{__('Leads')}}</h6>
                <h3 class="mb-0">{{$crm_data['total_leads']}}</h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="card">
            <div class="card-body">
                <div class="theme-avtar bg-info">
                    <i class="ti ti-users"></i>
                </div>
                <p class="text-muted text-sm mt-4 mb-2">{{__('Total')}}</p>
                <h6 class="mb-3">{{__('Contracts')}}</h6>
                <h3 class="mb-0">{{$crm_data['total_contracts']}}</h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="card">
            <div class="card-body">
                <div class="theme-avtar bg-warning">
                    <i class="ti ti-report-money"></i>
                </div>
                <p class="text-muted text-sm mt-4 mb-2">{{__('Total')}}</p>
                <h6 class="mb-3">{{__('Deals')}}</h6>
                <h3 class="mb-0">{{$crm_data['total_deals']}}</h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="card">
            <div class="card-body">
                <div class="theme-avtar bg-danger">
                    <i class="ti ti-report-money"></i>
                </div>
                <p class="text-muted text-sm mt-4 mb-2">{{__('Total')}}</p>
                <h6 class="mb-3">{{__('Customers')}}</h6>
                <h3 class="mb-0">{{$crm_data['total_clients']}}</h3>
            </div>
        </div>
    </div>
</div>
<div class="col-xxl-12">
    <div class="card">
        <div class="card-header">
            <h5>{{__('Leads & Contract & Deal')}}
                <span class="float-end text-muted">{{__('Current Year').' - '.$crm_data['currentYear']}}</span>
            </h5>

        </div>
        <div class="card-body">
            <div id="incExpBarChart"></div>
        </div>
    </div>
</div>


@endsection
