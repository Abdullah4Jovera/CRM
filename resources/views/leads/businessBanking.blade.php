<div class="form-group row">

    <h5 class="text-capitalize text-primary d-flex justify-content-center">Business Banking  Service Info</h5>
    <div class="col-4 form-group">
        <select class="form-control select" name="business_banking_services" id="business_banking_services">
            <option selected >Business Banking Services</option>
            <option value="business_loan">Business Loan</option>
            <option value="fleet_finance">Fleet Finance ( Auto Loans)</option>
            <option value="lgcs">LGs / LCs</option>
            <option value="account_opening">Account Opening</option>
        </select>
    </div>
    <div class="col-4 form-group">
        {{ Form::text('company_name', null, array('class' => 'form-control company_name','required'=>'required', 'placeholder'=>' Company Name')) }}
    </div>
    <div class="col-4 form-group">
        <input type="number" name="yearly_turnover" id="yearly_turnover" class="form-control" required  placeholder="Company Turnover in Last Year" >
    </div>
    <div class="row businessBankingServices"></div>
    <div class="col-12 form-group">
        {{ Form::textarea('notes',null, array('placeholder'=>'Message','maxlength'=>'200' ,'class' => 'form-control' ,'id'=>'notes', 'rows'=>"3")) }}
    </div>
</div>


<script>
    $(document).on('change','#have_any_pos', function () {
        let have_any_pos = $("#have_any_pos").val();
        if (have_any_pos == 'yes') {
            $('.monthly_amount').removeAttr('disabled','disabled');
        }else{
            $('.monthly_amount').val('');
            $('.monthly_amount').attr('disabled','disabled');
        }
    });
    $(document).on('change','#have_auto_finance', function () {
        let have_auto_finance = $("#have_auto_finance").val();
        if (have_auto_finance == 'yes') {
            $('.monthly_emi').removeAttr('disabled','disabled');
        }else{
            $('.monthly_emi').val('');
            $('.monthly_emi').attr('disabled','disabled');
        }
    });
    $(document).on('change','#business_banking_services', function () {
        let business_banking_services = $("#business_banking_services").val();
        if (business_banking_services =='business_loan') {
            var businessLoan = `<div class="col-6 form-group">
                    <select class="form-control select" name="have_any_pos" id="have_any_pos">
                        <option selected >Company Have POS (Point Of Sale)</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div class="col-6 form-group">
                    {{ Form::text('monthly_amount', null, array('class' => 'form-control monthly_amount','required'=>'required', 'placeholder'=>'POS Turnover Monthly (Approx)','disabled'=> '')) }}
                </div>`;
                $('.businessBankingServices').html(businessLoan);
               $('#yearly_turnover').removeAttr('disabled','disabled');
        }else if (business_banking_services =='fleet_finance') {
            let fleetFinance = `<div class="col-6 form-group">
                    <select class="form-control select" name="have_auto_finance" id="have_auto_finance">
                        <option selected >Company Have Auto Finance</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div class="col-6 form-group">
                    {{ Form::text('monthly_emi', null, array('class' => 'form-control monthly_emi','required'=>'required', 'placeholder'=>'Total EMI Paid (Monthly)','disabled'=> '')) }}
                </div>`;
               $('.businessBankingServices').html(fleetFinance);
               $('#yearly_turnover').removeAttr('disabled','disabled');
        }else if (business_banking_services =='lgcs') {
            let lgcs = `<div class="col-12 form-group">
                    <select class="form-control select" name="lgcs" id="lgcs">
                        <option value="" class="placeholder" disabled="" selected="selected">LG Requested for</option>
                        <option value="Govt. Project">Govt. Project</option>
                        <option value="Semi Govt. Project">Semi Govt. Project</option>
                        <option value="Private Project">Private Project</option>
                        <option value="National Housing Loan (NHL)">National Housing Loan (NHL)</option>
                    </select>
                </div>`;
               $('.businessBankingServices').html(lgcs);
               $('#yearly_turnover').removeAttr('disabled','disabled');
        }
        else {
            $('#yearly_turnover').val(' ');
            $('#yearly_turnover').attr('disabled','disabled');
            $('.businessBankingServices').html(' ');
        }
    });
</script>
