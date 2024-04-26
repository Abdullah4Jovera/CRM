<div id="service_info" class="card">
    <div class="card-body">
        <h4 class="mb-4 mt-2 text-capitalize text-primary d-flex justify-content-center">{{!empty($businessBanking->company_name)?$businessBanking->company_name:''}}</h4>
        <div class="row">
            <div class="col-md-3 col-sm-3">
                <div class="d-flex align-items-start">
                    <div class="theme-avtar bg-primary">
                        <i class="ti ti-building-bank"></i>
                    </div>
                    <div class="ms-2">
                        <p class="text-muted text-sm mb-0">{{__('Business Banking Service')}}</p>
                        <h5 class="mb-0 text-warning">
                            @if ($businessBanking->business_banking_services == 'business_loan')
                                {{__('Business Loan')}}
                            @elseif($businessBanking->business_banking_services == 'fleet_finance')
                                {{__('Fleet Finance')}}
                            @elseif($businessBanking->business_banking_services == 'lgcs')
                                {{__('LGs / LCs')}}
                            @else
                                {{__('Account Opening')}}
                            @endif
                        </h5>

                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-3">
                <div class="d-flex align-items-start">
                    <div class="theme-avtar bg-warning">
                        <i class="ti ti-cash-banknote"></i>
                    </div>
                    <div class="ms-2">
                        <p class="text-muted text-sm mb-0">{{__('Company Yearly Turnover')}}</p>
                        <h5 class="mb-0 text-warning">{{!empty($businessBanking->yearly_turnover)?number_format($businessBanking->yearly_turnover).' AED':''}}</h5>
                    </div>
                </div>
            </div>

            @if ($businessBanking->business_banking_services == 'business_loan')
                <div class="col-md-3 col-sm-3">
                    <div class="d-flex align-items-start">
                        <div class="theme-avtar bg-info">
                            <i class="ti ti-cash"></i>
                        </div>
                        <div class="ms-2">
                            <p class="text-muted text-sm mb-0">{{__('POS Turnover Monthly (Approx)')}}</p>
                            <h5 class="mb-0 text-info">{{!empty($businessBanking->monthly_amount)?number_format($businessBanking->monthly_amount).' AED':''}}</h5>

                        </div>
                    </div>
                </div>
            @elseif($businessBanking->business_banking_services == 'fleet_finance')
                <div class="col-md-3 col-sm-3">
                    <div class="d-flex align-items-start">
                        <div class="theme-avtar bg-info">
                            <i class="ti ti-cash"></i>
                        </div>
                        <div class="ms-2">
                            <p class="text-muted text-sm mb-0">{{__('Total EMI Paid (Monthly)')}}</p>
                            <h5 class="mb-0 text-info">{{!empty($businessBanking->monthly_emi)?number_format($businessBanking->monthly_emi).' AED':''}}</h5>

                        </div>
                    </div>
                </div>
            @elseif($businessBanking->business_banking_services == 'lgcs')
                {{__('LGs / LCs')}}
            @else
                {{__('Account Opening')}}
            @endif

            <div class="col-md-3 col-sm-3 ">
                <div class="d-flex align-items-start">
                    <div class="theme-avtar bg-danger">
                        <i class="ti ti-file"></i>
                    </div>
                    <div class="ms-2">
                        <p class="text-muted text-sm mb-0">{{__('Created')}}</p>
                        <h5 class="mb-0 text-danger">{{\Auth::user()->dateFormat($lead->date ?? $deal->leads->date )}}</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-3 {{($businessBanking->business_banking_services != 'account_opening')?'mt-4':''}}">
                <div class="d-flex align-items-start">
                        <div class="theme-avtar bg-primary">
                            <i class="ti ti-affiliate"></i>
                        </div>
                        <div class="ms-2">
                            <p class="text-muted text-sm mb-0">{{__('Pipeline')}}</p>
                            <h5 class="mb-0 text-primary">{{!empty($lead->pipeline->name)?$lead->pipeline->name:''}}</h5>

                        </div>
                    </div>
            </div>
            <div class="col-md-3 col-sm-3 {{($businessBanking->business_banking_services != 'account_opening')?'mt-4':''}}">
                <div class="d-flex align-items-start">
                    <div class="theme-avtar bg-warning">
                        <i class="ti ti-server"></i>
                    </div>
                    <div class="ms-2">
                        <p class="text-muted text-sm mb-0">{{__('Stage')}}</p>

                            {{-- @if (!empty($is_reject))
                                <h5 class="mb-0 text-warning">{{('Rejected')}}</h5>
                            @else
                                <h5 class="mb-0 text-warning">{{$lead->stage->name ?? $deal->leads->stage->name }}</h5>
                            @endif --}}

                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 mt-4">
                <div class="d-flex align-items-start">
                    <div class="theme-avtar bg-info">
                        <i class="ti ti-calendar"></i>
                    </div>
                    <div class="ms-2">
                        <p class="text-muted text-sm mb-0">{{__('Lead From')}}</p>
                        <h5 class="mb-0 text-info">{{$leadType}}</h5>

                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 mt-4">
                <div class="d-flex align-items-start">
                    <div class="theme-avtar bg-danger">
                        <i class="ti ti-file"></i>
                    </div>
                    <div class="ms-2">
                        <p class="text-muted text-sm mb-0">{{__('Source')}}</p>
                        <h5 class="mb-0 text-danger">{{optional($sources)->name}}</h5>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
