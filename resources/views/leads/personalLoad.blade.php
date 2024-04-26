<h5 class="text-capitalize text-primary d-flex justify-content-center">Personal Load  Service Info</h5>
<div class="col-6 form-group">
    {{ Form::text('company_name', null, array('class' => 'form-control company_name','required'=>'required', 'placeholder'=>' Company Name')) }}
</div>
<div class="col-6 form-group">
    <input type="number" name="monthly_salary" id="monthly_salary" class="form-control" required placeholder="Monthly Salary AED" >
</div>
<div class="col-4 form-group">
    {{ Form::text('load_amount', null, array('class' => 'form-control load_amount','required'=>'required', 'placeholder'=>'Loan Amount')) }}
</div>
<div class="col-4 form-group">
    <select class="form-control select" name="have_any_loan" id="have_any_loan">
        <option selected >Have Any Loan </option>
        <option value="yes">Yes</option>
        <option value="no">No</option>
    </select>
</div>
<div class="col-4 form-group">
    {{ Form::number('taken_loan_amount', null, array('class' => 'form-control taken_loan_amount','required'=>'required', 'placeholder'=>'Previous Loan Amount','disabled'=> '')) }}
</div>
<div class="col-12 form-group">
    {{ Form::textarea('notes',null, array('placeholder'=>'Message','maxlength'=>'200' ,'class' => 'form-control' ,'id'=>'notes', 'rows'=>"3")) }}
</div>

<script>
    $(document).on('change','#have_any_loan', function () {
        let loan = $("#have_any_loan").val();
        if (loan == 'yes') {
            $('.taken_loan_amount').removeAttr('disabled','disabled');
        }else{
            $('.taken_loan_amount').val('');
            $('.taken_loan_amount').attr('disabled','disabled');
        }
    });
</script>
