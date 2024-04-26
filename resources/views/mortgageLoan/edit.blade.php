<h5 class="text-capitalize text-primary d-flex justify-content-center">Mortgage Service Info</h5>
<div class="col-4 form-group">
    <select class="form-control select" name="type_of_property" id="type_of_property">
        <option selected >Type Of Property</option>
        <option {{($data->type_of_property =="Villa")?'selected':""}} value="Villa">Villa</option>
        <option {{($data->type_of_property =="Apartment")?'selected':""}} value="Apartment">Apartment</option>
        <option {{($data->type_of_property =="Townhouse")?'selected':""}} value="Townhouse">Townhouse</option>
        <option {{($data->type_of_property =="Land")?'selected':""}} value="Land">Land</option>
    </select>
</div>
<div class="col-4 form-group">
    <select class="form-control select" name="location" id="location">
        <option disabled selected>Property Location</option>
        <option {{($data->location == "abu_dhabi") ?'selected' : ""}} value="abu_dhabi">Abu Dhabi</option>
        <option {{($data->location == "dubai") ?'selected' : ""}} value="dubai">Dubai</option>
        <option {{($data->location == "sharjah") ?'selected' : ""}} value="sharjah">Sharjah</option>
        <option {{($data->location == "ajman") ?'selected' : ""}} value="ajman">Ajman</option>
        <option {{($data->location == "umm_al_quwain") ?'selected' : ""}} value="umm_al_quwain">Umm Al Quwain</option>
        <option {{($data->location == "ras_al_khaimah") ?'selected' : ""}} value="ras_al_khaimah ">Ras Al Khaimah </option>
        <option {{($data->location == "fujairah") ?'selected' : ""}} value="fujairah">Fujairah</option>
    </select>
</div>
<div class="col-4 form-group">
    <select class="form-control select" name="monthly_income" id="monthly_income">
        <option disabled selected>Monthly Income</option>
        <option {{($data->monthly_income =="20,000 to 30,000 AED")?'selected':""}} value="20,000 to 30,000 AED">From 20,000 to 30,000 AED</option>
        <option {{($data->monthly_income =="30,000 to 40,000 AED")?'selected':""}} value="30,000 to 40,000 AED">From 30,000 to 40,000 AED</option>
        <option {{($data->monthly_income =="40,000 to 50,000 AED")?'selected':""}} value="40,000 to 50,000 AED">From 40,000 to 50,000 AED</option>
        <option {{($data->monthly_income =="+50,000 AED")?'selected':""}} value="+50,000 AED">+50,000 AED</option>
    </select>
</div>
<div class="col-6 form-group">
    <select class="form-control select" name="have_any_other_loan" id="have_any_other_loan">
        <option disabled selected>Have Any Other Loan</option>
        <option {{($data->have_any_other_loan =="yes")?'selected':""}} value="yes">Yes</option>
        <option {{($data->have_any_other_loan =="no")?'selected':""}} value="no">No</option>
    </select>
</div>
<div class="col-6 form-group">
    <input type="text" name="loanAmount" value="{{$data->loanAmount }}" id="loanAmount" class="form-control" disabled required placeholder="Loan Amount"  >
</div>
<div class="col-12 form-group">
    {{ Form::textarea('notes',$data->notes , array('placeholder'=>'Message','maxlength'=>'500' ,'class' => 'form-control' ,'id'=>'notes',)) }}
</div>
<script>
     $(document).ready(function () {
        let have_any_other_loan = $("#have_any_other_loan").val();
        if (have_any_other_loan == 'yes') {
            $('#loanAmount').removeAttr('disabled','disabled');
        }else{
            $('#loanAmount').val('');
            $('#loanAmount').attr('disabled','disabled');
        }
    });
</script>
