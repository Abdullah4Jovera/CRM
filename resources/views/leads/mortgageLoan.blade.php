<h5 class="text-capitalize text-primary d-flex justify-content-center">Mortgage Service Info</h5>
<div class="col-4 form-group">
    <select class="form-control select" name="type_of_property" id="type_of_property">
        <option selected >Type Of Property</option>
        <option value="Villa">Villa</option>
        <option value="Apartment">Apartment</option>
        <option value="Townhouse">Townhouse</option>
        <option value="Land">Land</option>
    </select>
</div>
<div class="col-4 form-group">
    <select class="form-control select" name="location" id="location">
        <option disabled selected>Property Location</option>
        <option value="abu_dhabi">Abu Dhabi</option>
        <option value="dubai">Dubai</option>
        <option value="sharjah">Sharjah</option>
        <option value="ajman">Ajman</option>
        <option value="umm_al_quwain">Umm Al Quwain</option>
        <option value="ras_al_khaimah ">Ras Al Khaimah </option>
        <option value="fujairah">Fujairah</option>
    </select>
</div>
<div class="col-4 form-group">
    <select class="form-control select" name="monthly_income" id="monthly_income">
        <option disabled selected>Monthly Income</option>
        <option value="20,000 to 30,000 AED">From 20,000 to 30,000 AED</option>
        <option value="30,000 to 40,000 AED">From 30,000 to 40,000 AED</option>
        <option value="40,000 to 50,000 AED">From 40,000 to 50,000 AED</option>
        <option value="+50,000 AED">+50,000 AED</option>
    </select>
</div>
<div class="col-6 form-group">
    <select class="form-control select" name="have_any_other_loan" id="have_any_other_loan">
        <option disabled selected>Have Any Other Loan</option>
        <option value="yes">Yes</option>
        <option value="no">No</option>
    </select>
</div>
<div class="col-6 form-group">
    <input type="text" name="loanAmount" id="loanAmount" class="form-control" disabled required placeholder="Loan Amount"  >
</div>
<div class="col-12 form-group">
    {{ Form::textarea('notes',null, array('placeholder'=>'Message','maxlength'=>'200' ,'class' => 'form-control' ,'id'=>'notes', 'rows'=>"3")) }}
</div>

<script>
    $(document).on('change','#have_any_other_loan', function () {
        let have_any_other_loan = $("#have_any_other_loan").val();
        if (have_any_other_loan == 'yes') {
            $('#loanAmount').removeAttr('disabled','disabled');
        }else{
            $('#loanAmount').val('');
            $('#loanAmount').attr('disabled','disabled');
        }
    });
</script>
