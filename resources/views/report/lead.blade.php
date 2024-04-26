@extends('layouts.admin')
@section('page-title')
    {{__('Manage Lead')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Lead Report')}}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                {{-- <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#general-report" class="list-group-item list-group-item-action border-0">{{ __('General Report') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#staff-report" class="list-group-item list-group-item-action border-0">{{ __('Staff Report') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#pipeline-report" class="list-group-item list-group-item-action border-0">{{ __('Pipelines Report') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                        </div>
                    </div>
                </div> --}}

                <div class="col-xl-12">
                    <div id="general-report">

                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Lead From') }}</h5>
                            </div>
                            <div class="card-body pt-0">
                                <div class="leads-sources-report" id="leads-sources-report"
                                     data-color="primary" data-height="280">
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-9">
                                        <h5>{{ __('Monthly') }}</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="mt-3">
                                    <div id="leads-monthly" data-color="primary" data-height="280"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="pipeline-report" class="card">
                        <div class="card-header">
                            <h5>{{ __('Pipeline Report') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div id="leads-piplines-report"
                                     data-color="primary" data-height="280"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection

@push('script-page')

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

        function check_theme(color_val) {
            $('#theme_color').prop('checked', false);
            $('input[value="' + color_val + '"]').prop('checked', true);
        }
    </script>

    <script>

        $(".generate_button").click(function(){
            var from_date = $('.from_date').val();
            if(from_date == ''){
                $("#fromDate").text("Please select date");
            }else{
                $("#fromDate").empty();
            }
            var to_date = $('.to_date').val();
            if(to_date == ''){
                $("#toDate").text("Please select date");
            }else{
                $("#toDate").empty();
            }
            $.ajax({
                url: "{{ route('report.lead') }}",
                type: "get",
                data: {
                    "From_Date": from_date,
                    "To_Date": to_date,
                    "type": 'staff_repport',
                    "_token": "{{ csrf_token() }}",
                },

                cache: false,
                success: function(data) {

                    $("#leads-staff-report").empty();
                    var chartBarOptions = {
                        series: [{
                            name: 'Lead',
                            data: data.data,
                        }],

                        chart: {
                            height: 300,
                            type: 'bar',
                            dropShadow: {
                                enabled: true,
                                color: '#000',
                                top: 18,
                                left: 7,
                                blur: 10,
                                opacity: 0.2
                            },
                            toolbar: {
                                show: false
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            width: 2,
                            curve: 'smooth'
                        },
                        title: {
                            text: '',
                            align: 'left'
                        },
                        xaxis: {
                            categories: data.name,


                        },
                        colors: ['#6fd944', '#6fd944'],


                        grid: {
                            strokeDashArray: 4,
                        },
                        legend: {
                            show: false,
                        }

                    };
                    var arChart = new ApexCharts(document.querySelector("#leads-staff-report"), chartBarOptions);
                    arChart.render();

                }
            })


        });


        $(document).ready(function(){
            $("#selectmonth").change(function(){
                var selectedVal = $("#selectmonth option:selected").val();
                var start_month = $('.selectpicker').val();
                $.ajax({
                    url:  "{{route('report.lead')}}",
                    type: "get",
                    data:{
                        "start_month": start_month,
                        "_token": "{{ csrf_token() }}",
                    },
                    cache: false,
                    success: function(data){

                        $("#leads-monthly").empty();
                        var chartBarOptions = {
                            series: [{
                                name: 'Lead',
                                data: data.data,
                            }],

                            chart: {
                                height: 300,
                                type: 'bar',
                                dropShadow: {
                                    enabled: true,
                                    color: '#000',
                                    top: 18,
                                    left: 7,
                                    blur: 10,
                                    opacity: 0.2
                                },
                                toolbar: {
                                    show: false
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                width: 2,
                                curve: 'smooth'
                            },
                            title: {
                                text: '',
                                align: 'left'
                            },
                            xaxis: {
                                categories:data.name,

                                title: {
                                    text: '{{ __("Lead Per Month") }}'
                                }
                            },
                            colors: ['#6fd944', '#6fd944'],


                            grid: {
                                strokeDashArray: 4,
                            },
                            legend: {
                                show: false,
                            }

                        };
                        var arChart = new ApexCharts(document.querySelector("#leads-monthly"), chartBarOptions);
                        arChart.render();


                    }
                })
            });
        });


        $(document).on('click', '.lang-tab .nav-link', function() {
            $('.lang-tab .nav-link').removeClass('active');
            $('.tab-pane').removeClass('active');
            $(this).addClass('active');
            var id = $('.lang-tab .nav-link.active').attr('data-href');
            $(id).addClass('active');
        });
    </script>
    <script src="{{asset('assets/js/plugins/apexcharts.min.js')}}"></script>
    <script>
        var options = {
            series: {!! json_encode($devicearray['data']) !!},
            chart: {
                width: 350,
                type: 'pie',
            },

            colors: ["#35abb6","#ffa21d","#ff3a6e","#6fd943","#5c636a","#181e28","#0288d1"],
            labels: {!! json_encode($devicearray['label']) !!},
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom',
                    }
                }
            }]
        };
        var chart = new ApexCharts(document.querySelector("#leads-this-week"), options);
        chart.render();

        (function () {
            var chartBarOptions = {
                series: [{
                    name:'{{__("Marketing")}}',
                    data: {!! json_encode($products['Marketing']) !!},
                },{
                    name:'{{__("TeleSales")}}',
                    data: {!! json_encode($products['TeleSales']) !!},
                },{
                    name:'{{__("Other")}}',
                    data: {!! json_encode($products['Other']) !!},
                },
            ],

                chart: {
                    height: 300,
                    type: 'area',
                    dropShadow: {
                        enabled: false,
                        color: '#000fff',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                title: {
                    text: '',
                    align: 'left'
                },
                xaxis: {
                    categories: {!! json_encode($labels) !!},

                    title: {
                        text: '{{ __("Deal Per Year") }}'
                    }
                },


                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                }

            };
            var arChart = new ApexCharts(document.querySelector("#leads-sources-report"), chartBarOptions);
            arChart.render();
        })();

        (function () {
            var chartBarOptions = {
                series: [{
                    name:'{{__("Business Banking")}}',
                    data: {!! json_encode($products['Bb']) !!},
                },{
                    name:'{{__("Personal Loan")}}',
                    data: {!! json_encode($products['Pl']) !!},
                },{
                    name:'{{__("Mortgage Loan")}}',
                    data: {!! json_encode($products['Ml']) !!},
                },
            ],

                chart: {
                    height: 300,
                    type: 'bar',
                    dropShadow: {
                        enabled: false,
                        color: '#000fff',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                title: {
                    text: '',
                    align: 'left'
                },
                xaxis: {
                    categories: {!! json_encode($labels) !!},

                    title: {
                        text: '{{ __("Lead Per Month") }}'
                    }
                },
                colors: ['#6610f2', '#3ec9d6','#fd7e14'],
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'center',
                },
                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                }

            };
            var arChart = new ApexCharts(document.querySelector("#leads-monthly"), chartBarOptions);
            arChart.render();
        })();


        (function () {
            var chartBarOptions = {
                series: [{
                    name: 'Pipeline',
                    data: {!! json_encode($leadpipelineeData) !!},
                }],

                chart: {
                    height: 300,
                    type: 'bar',
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                title: {
                    text: '',
                    align: 'left'
                },
                xaxis: {
                    categories: {!! json_encode($leadpipelineName) !!},

                    title: {
                        text: '{{ __("Pipelines") }}'
                    }
                },
                yaxis: {
                    title: {
                        text: '{{ __("Leads") }}'
                    }
                },
                colors: ['#6fd944', '#6fd944'],


                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                }

            };
            var arChart = new ApexCharts(document.querySelector("#leads-piplines-report"), chartBarOptions);
            arChart.render();
        })();

    </script>
@endpush

