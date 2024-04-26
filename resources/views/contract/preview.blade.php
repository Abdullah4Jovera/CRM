@extends('layouts.contractheader')
@php
$SITE_RTL = !empty($settings['SITE_RTL'] ) ? $settings['SITE_RTL']  : 'off';

@endphp
@push('script-page')

<script>
    function closeScript() {
        setTimeout(function () {
            window.open(window.location, '_self').close();
        }, 1000);
    }

    $(window).on('load', function () {
        var element = document.getElementById('boxes');
        var opt = {
            filename: '{{App\Models\Utility::contractNumberFormat($deal->id)}}',
            image: {type: 'jpeg', quality: 1},
            html2canvas: {scale: 4, dpi: 72, letterRendering: true},
            jsPDF: {unit: 'in', format: 'A4'}
        };

        html2pdf().set(opt).from(element).save().then(closeScript);
    });
</script>

@endpush
@section('page-title')
    {{__('Contract')}}
@endsection
@section('title')

{{__('Contract')}} {{ '('. $deal->name .')' }}

@endsection


@section('content')
<div class="mt-3 ">
    <div class="row justify-content-center mb-3">
        <div class="col-sm-9 text-end me-2">
            <div class="all-button-box ">
            @if(((\Auth::user()->type =='company') && ($deal->company_signature == '')||(\Auth::user()->type =='client') && ($deal->client_signature == ''))&&$deal->status == 'Start')
                    <a href="#" class="btn btn-sm btn-primary btn-icon m-" data-bs-toggle="modal"
                        data-bs-target="#exampleModal" data-size="md" data-url="{{ route('signature',$deal->id) }}"
                        data-bs-whatever="{{__('signature')}}" > <span class="text-white"> <i
                                class="ti ti-pencil text-white" data-bs-toggle="tooltip" data-bs-original-title="{{__('signature')}}"></i></span></a>
                    </a>
                    @endif
                <a href="{{route('contract.download.pdf',\Crypt::encrypt($deal->id))}}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Download')}}" target="_blanks">
                    <i class="ti ti-download"></i>
                </a>

            </div>
        </div>
    </div>


    <div class="row justify-content-center pl-10 pr-10">
        <div class="row col-sm-9">
            <div class="card">
                <div class="card-body">
                    <h3 style="display: flex;justify-content: center;"> <u>Service Application Form</u></h3>
                    <div class="row invoice-title mt-2">
                        <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12 ">
                            <img  src="{{url('storage/bill_logo/logo.png')}}" style="max-width: 150px;"/>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12 text-end">
                            <h4 class="invoice-number">{{\Auth::user()->contractNumberFormat($deal->id)}}</h4>
                        </div>
                    </div>
                    <div class="row align-items-center mb-4">
                        <div class="col-sm-6 mb-3 mb-sm-0 mt-3">
                            <div class="col-lg-12 col-md-8 mb-3">
                                <h4 class="d-inline-block m-0 d-print-none">{{__('Service Type  :')}}</h4>
                                <span class="col-md-8">
                                    <span class="text-lg">
                                        @if ($deal->leads->product->id == 1)
                                            <b>{{optional($deal->leads->product)->name}}</b>
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
                                            <b>{{optional($deal->leads->product)->name}} </b>
                                        @else
                                            <b>{{optional($deal->leads->product)->name}} </b>
                                        @endif
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-8">
                            <dl class="row align-items-center">
                                <h4 style="display: flex;justify-content: center;">Client Details</h4>
                                <dt class="col-sm-2 border border-secondary text-lg">{{ __('Name') }}</dt>
                                <dt class="col-sm-4 border border-secondary text-lg fw-normal">{{ optional($deal->leads->client)->name}}</dt>
                                <dt class="col-sm-2 border border-secondary text-lg">{{ __('Email') }}</dt>
                                <dt class="col-sm-4 border border-secondary text-lg fw-normal">{{ $deal->leads->client->email}}</dt>
                                <dt class="col-sm-2 border border-secondary text-lg">{{ __('Phone') }}</dt>
                                <dt class="col-sm-4 border border-secondary text-lg fw-normal">{{ $deal->leads->client->phone}}</dt>
                                <dt class="col-sm-2 border border-secondary text-lg">{{ __('Emirate ID') }}</dt>
                                <dt class="col-sm-4 border border-secondary text-lg fw-normal">{{ $deal->leads->client->e_id}}</dt>
                                <dt class="col-sm-2 border border-secondary text-lg">{{ __('Address') }}</dt>
                                <dt class="col-sm-10 border border-secondary text-lg fw-normal">{{ $deal->leads->client->address}}</dt>
                           </dl>
                        </div>
                        <div class="col-lg-12 col-md-8">
                            <dl class="row align-items-center">
                                <h4 style="display: flex;justify-content: center;">Service Details</h4>
                                <dt class="col-sm-3 border border-secondary text-lg">{{ __('Service Name') }}</dt>
                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">
                                    @if ($deal->leads->product->id == 1)
                                        <b>{{optional($deal->leads->product)->name}}</b>
                                        @if (optional($deal->leads->businessBanking)->business_banking_services == 'business_loan')
                                            <span style="font-size:12px ">({{__('Business Loan')}})</span>
                                        @elseif(optional($deal->leads->businessBanking)->business_banking_services == 'fleet_finance')
                                            <span style="font-size:12px ">({{__('Fleet Finance')}})</span>
                                        @elseif(optional($deal->leads->businessBanking)->business_banking_services == 'lgcs')
                                            <span style="font-size:12px ">({{__('LGs / LCs')}})</span>
                                        @else
                                            <span style="font-size:12px ">({{__('Account Opening')}})</span>
                                        @endif
                                    @elseif ($deal->leads->product->id == 2)
                                        <b>{{optional($deal->leads->product)->name}} </b>
                                    @else
                                        <b>{{optional($deal->leads->product)->name}} </b>
                                    @endif

                                </dt>

                                <dt class="col-sm-3 border border-secondary text-lg">{{ __('Finance Amount') }}</dt>
                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format(optional($deal->serviceCommission)->finance_amount ?? 0) }} AED</dt>

                                @if (!empty($deal->serviceCommission) && !is_null($deal->serviceCommission->bank_commission))
                                    <dt class="col-sm-3 border border-secondary text-lg">{{ __('Bank Commission') }}</dt>
                                    <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format($deal->serviceCommission->bank_commission) }} AED</dt>
                                @endif

                                <dt class="col-sm-3 border border-secondary text-lg">{{ __('Client Commission') }}</dt>
                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format(optional($deal->serviceCommission)->customer_commission ?? 0) }} AED</dt>

                                <dt class="col-sm-3 border border-secondary text-lg">{{ __("Total Revenue ") }} <span style="font-size:12px">{{ __('(with vat 5%)') }}</span></dt>
                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format(optional($deal->serviceCommission)->with_vat_commission ?? 0) }} AED</dt>

                                <dt class="col-sm-3 border border-secondary text-lg">{{ __("Total Revenue") }} <span style="font-size:12px">{{ __('(without vat 5%)') }}</span></dt>
                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format(optional($deal->serviceCommission)->without_vat_commission ?? 0) }} AED</dt>

                           </dl>
                            <dl class="align-items-center">
                                <h4 style="display: flex;justify-content: center;">Commission Details</h4>
                                <!-- ========== Sales Start Section ========== -->
                                    <div class="row">
                                        <h5 style="display: flex;justify-content: center;">Sales</h5>
                                        <!-- ========== Headings Start Section ========== -->
                                            <dt class="col-sm-3 border border-secondary text-lg">{{__('Title')}}</dt>
                                            <dt class="col-sm-3 border border-secondary text-lg">{{__('Name')}}</dt>
                                            <dt class="col-sm-3 border border-secondary text-lg">{{__('Commission')}}</dt>
                                            <dt class="col-sm-3 border border-secondary text-lg">{{__('Amount')}}</dt>
                                        <!-- ========== Headings End Section ========== -->
                                        @if (!empty($deal->serviceCommission->hodsalecommission) && !empty($deal->serviceCommission->hodsale) )
                                            <dt class="col-sm-3 border border-secondary text-lg">{{ __('HOD') }}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->hodsaleCommission)->name}}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->hodsalecommission}} %</dt>
                                            <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->hodsalecommission) /100)}} AED</dt>
                                        @endif
                                        @if (!empty($deal->serviceCommission->salemanagercommission) && !empty($deal->serviceCommission->salemanager) )
                                            <dt class="col-sm-3 border border-secondary text-lg">{{ __('Sales Manager') }}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->salemanagerCommission)->name}}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->salemanagercommission}} %</dt>
                                            <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->salemanagercommission) /100)}} AED</dt>
                                        @endif
                                        @if (!empty($deal->serviceCommission->coordinator_commission) && !empty($deal->serviceCommission->coordinator) )
                                            <dt class="col-sm-3 border border-secondary text-lg">{{ __('Coordinator') }}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->coordinatorCommission)->name}}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->coordinator_commission}} %</dt>
                                            <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->coordinator_commission) /100)}} AED</dt>
                                        @endif
                                        @if (!empty($deal->serviceCommission->team_leader_commission) && !empty($deal->serviceCommission->team_leader) )
                                            <dt class="col-sm-3 border border-secondary text-lg">{{ __('1st Team Leader') }}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->team_leaderCommission)->name}}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->team_leader_commission}} %</dt>
                                            <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->team_leader_commission) /100)}} AED</dt>
                                        @endif
                                        @if (!empty($deal->serviceCommission->team_leader_one_commission) && !empty($deal->serviceCommission->team_leader_one) )
                                            <dt class="col-sm-3 border border-secondary text-lg">{{ __('2nd Team Leader') }}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->team_leaderoneCommission)->name}}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->team_leader_one_commission}} %</dt>
                                            <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->team_leader_one_commission) /100)}} AED</dt>
                                        @endif
                                        @if (!empty($deal->serviceCommission->salesagent_commission) && !empty($deal->serviceCommission->salesagent) )
                                            <dt class="col-sm-3 border border-secondary text-lg">{{ __('1st Sales Agent') }}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->salesagentCommission)->name}}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->salesagent_commission}} %</dt>
                                            <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->salesagent_commission) /100)}} AED</dt>
                                        @endif
                                        @if (!empty($deal->serviceCommission->sale_agent_one_commission) && !empty($deal->serviceCommission->sale_agent_one) )
                                            <dt class="col-sm-3 border border-secondary text-lg">{{ __('2nd Sales Agent') }}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->salesagentoneCommission)->name}}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->sale_agent_one_commission}} %</dt>
                                            <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->sale_agent_one_commission) /100)}} AED</dt>
                                        @endif
                                    </div>
                                <!-- ========== Sales End Section ========== -->
                                @if ($deal->leads->is_transfer == 1)
                                    <!-- ========== Referral Start Section ========== -->
                                        <div class="row mt-2">
                                            <h5 style="display: flex;justify-content: center;">Referral</h5>
                                            <!-- ========== Headings Start Section ========== -->
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Title')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Name')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Commission')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Amount')}}</dt>
                                            <!-- ========== Headings End Section ========== -->
                                            @if (!empty($deal->serviceCommission->salemanagerrefcommission) && !empty($deal->serviceCommission->salemanagerref) )
                                                <dt class="col-sm-3 border border-secondary text-lg">{{ __('Sales Manager Ref.') }}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->salemanagerrefCommission)->name}}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->salemanagerrefcommission}} %</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->salemanagerrefcommission) /100)}} AED</dt>
                                            @endif
                                            @if (!empty($deal->serviceCommission->agent_commission) && !empty($deal->serviceCommission->agentref) )
                                                <dt class="col-sm-3 border border-secondary text-lg">{{ __('Sales Agent Ref.') }}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->agentrefCommission)->name}}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->agent_commission}} %</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->agent_commission) /100)}} AED</dt>
                                            @endif
                                        </div>
                                    <!-- ========== Referral End Section ========== -->
                                @endif
                                @if ($deal->leads->lead_type == '1')
                                    <!-- ==========Tele Sale Start Section ========== -->
                                        <div class="row mt-2">
                                            <h5 style="display: flex;justify-content: center;" >Tele Sales</h5>
                                            <!-- ========== Headings Start Section ========== -->
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Title')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Name')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Commission')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Amount')}}</dt>
                                            <!-- ========== Headings End Section ========== -->
                                            @if (!empty($deal->serviceCommission->ts_hod_commision) && !empty($deal->serviceCommission->ts_hod ) )
                                                <dt class="col-sm-3 border border-secondary text-lg">{{ __('HOD') }}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->tshodCommission)->name}}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->ts_hod_commision}} %</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->ts_hod_commission) /100)}} AED</dt>
                                            @endif
                                            @if (!empty($deal->serviceCommission->ts_team_leader_commission) && !empty($deal->serviceCommission->ts_team_leader) )
                                                <dt class="col-sm-3 border border-secondary text-lg">{{ __('Team Leader') }}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->ts_team_leaderCommission)->name}}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->ts_team_leader_commission}} %</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->ts_team_leader_commission) /100)}} AED</dt>
                                            @endif
                                            @if (!empty($deal->serviceCommission->tsagent_commission) && !empty($deal->serviceCommission->tsagent) )
                                                <dt class="col-sm-3 border border-secondary text-lg">{{ __('Agent') }}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->tsagentCommission)->name}}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->tsagent_commission}} %</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->tsagent_commission) /100)}} AED</dt>
                                            @endif
                                        </div>
                                    <!-- ==========Tele Sale End Section ========== -->
                                @elseif ($deal->leads->lead_type == '2')
                                    <!-- ==========Marketing Start Section ========== -->
                                        <div class="row mt-2">
                                            <h5 style="display: flex;justify-content: center;" >Marketing</h5>
                                            <!-- ========== Headings Start Section ========== -->
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Title')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Name')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Commission')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Amount')}}</dt>
                                            <!-- ========== Headings End Section ========== -->
                                            @if (!empty($deal->serviceCommission->marketingmanagercommission) && !empty($deal->serviceCommission->marketingmanager) )
                                                <dt class="col-sm-3 border border-secondary text-lg">{{ __('Marketing Manager') }}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->marketingmanagerCommission)->name}}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->marketingmanagercommission}} %</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->marketingmanagercommission) /100)}} AED</dt>
                                            @endif
                                            @if (!empty($deal->serviceCommission->marketingagentcommission) && !empty($deal->serviceCommission->marketingagent) )
                                                <dt class="col-sm-3 border border-secondary text-lg">{{ __('Marketing Agent') }}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->marketingagentCommission)->name}}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->marketingagentcommission}} %</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->marketingagentcommission) /100)}} AED</dt>
                                            @endif
                                            @if (!empty($deal->serviceCommission->marketingagentcommissionone) && !empty($deal->serviceCommission->marketingagentone) )
                                                <dt class="col-sm-3 border border-secondary text-lg">{{ __('Marketing Agent 1') }}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->marketingagentCommissionone)->name}}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->marketingagentcommissionone}} %</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->marketingagentcommissionone) /100)}} AED</dt>
                                            @endif
                                            @if (!empty($deal->serviceCommission->marketingagentcommissiontwo) && !empty($deal->serviceCommission->marketingagenttwo) )
                                                <dt class="col-sm-3 border border-secondary text-lg">{{ __('Marketing Agent 2') }}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->marketingagentCommissiontwo)->name}}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->marketingagentcommissiontwo}} %</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->marketingagentcommissiontwo) /100)}} AED</dt>
                                            @endif
                                        </div>
                                    <!-- ==========Marketing End Section ========== -->
                                @else
                                    <!-- ==========Other Start Section ========== -->
                                        <div class="row mt-2">
                                            <h5 style="display: flex;justify-content: center;" >Other Agents</h5>
                                            <!-- ========== Headings Start Section ========== -->
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Title')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Name')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Commission')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Amount')}}</dt>
                                            <!-- ========== Headings End Section ========== -->
                                            @if (!empty($deal->serviceCommission->other_name_commission) && !empty($deal->serviceCommission->other_name->name) )
                                                <dt class="col-sm-3 border border-secondary text-lg">{{ __('Agent') }}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ optional($deal->serviceCommission->other_name)->name}}</dt>
                                                <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->other_name_commission}} %</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->other_name_commission) /100)}} AED</dt>
                                            @endif
                                        </div>
                                    <!-- ==========Other End Section ========== -->


                                @endif
                                @if (!empty($deal->serviceCommission->broker_name_commission) && !empty($deal->serviceCommission->broker_name) )
                                    <!-- ==========3rd Party Start Section ========== -->
                                        <div class="row mt-2">
                                            <h5 style="display: flex;justify-content: center;" >3rd Party Agents</h5>
                                            <!-- ========== Headings Start Section ========== -->
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Title')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Name')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Commission')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Amount')}}</dt>
                                            <!-- ========== Headings End Section ========== -->
                                            <dt class="col-sm-3 border border-secondary text-lg">{{ __('Agent') }}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ $deal->serviceCommission->broker_name}}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->broker_name_commission}} %</dt>
                                            <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->broker_name_commission) /100)}} AED</dt>
                                        </div>
                                    <!-- ==========3rd Party End Section ========== -->
                                @endif
                                {{-- @if (!empty($deal->serviceCommission->alondra) && !empty($deal->serviceCommission->a_commission) )
                                    <!-- ==========3rd Party Start Section ========== -->
                                        <div class="row mt-2">
                                            <h5 style="display: flex;justify-content: center;" >Alondra Service Charge (1%)</h5>
                                            <!-- ========== Headings Start Section ========== -->
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Title')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Name')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Commission')}}</dt>
                                                <dt class="col-sm-3 border border-secondary text-lg">{{__('Amount')}}</dt>
                                            <!-- ========== Headings End Section ========== -->
                                            <dt class="col-sm-3 border border-secondary text-lg">{{ __('Service Charge') }}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg ">{{ $deal->serviceCommission->alondra}}</dt>
                                            <dt class="col-sm-3 border border-secondary fw-normal text-lg">{{ $deal->serviceCommission->a_commission}} %</dt>
                                            <dt class="col-sm-3 border border-secondary text-lg fw-normal">{{ number_format((optional($deal->serviceCommission)->without_vat_commission * $deal->serviceCommission->a_commission) /100)}} AED</dt>
                                        </div>
                                    <!-- ==========3rd Party End Section ========== -->
                                @endif --}}
                                {{-- <div class="row  align-items-center g-2" >
                                    <div class="col-6 justify-content-start d-flex">
                                        <p>
                                            <b> Note: </b> Maintenance Charge (0.25%) = {{number_format($deal->serviceCommission->without_vat_commission * 0.25 /100)}}
                                        </p>
                                    </div>
                                    <div class="col-6 justify-content-end d-flex">
                                        <p>
                                           <b> Develop & Maintenance By Alondra</b>
                                        </p>
                                    </div>

                                </div> --}}

                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
