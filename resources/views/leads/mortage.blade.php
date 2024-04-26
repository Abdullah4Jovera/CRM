<section class="ml row">
    <div class="col-6 form-group">
        {{ Form::label('loan_amount', __('Loan Amount'),['class'=>'form-label']) }}
        {{ Form::number('finance_amount', 0, array('class' => 'form-control','min'=>0)) }}
    </div>
    <div class="col-6 form-group">
        {{ Form::label('commission_amount', __('Commission Amount'),['class'=>'form-label']) }}
        {{ Form::number('customer_commission', 0, array('class' => 'form-control commission_amount','min'=>0)) }}
    </div>
    <div class="col-6 form-group">
        {{ Form::label('bank_commission', __('With Vat Amount'),['class'=>'form-label']) }}
        <input type="hidden" value="" name="with_vat_commission" id="with_vat_commission">
        <input type="text" value="" class="form-control wva " disabled >
    </div>
    <div class="col-6 form-group">
        {{ Form::label('bank_commission', __('Without Vat Amount'),['class'=>'form-label']) }}
        <input type="hidden" value="" name="without_vat_commission" id="without_vat_commission">
        <input type="text" value="" class="form-control wova " disabled >
    </div>

    <div class="col-6 form-group">
        {{ Form::label('b_type', __('Building Type'),['class'=>'form-label']) }}
        {{ Form::text('b_type', ' ', array('class' => 'form-control')) }}
    </div>
    <div class="col-6 form-group">
        {{ Form::label('p_no', __('Plot NO'),['class'=>'form-label']) }}
        {{ Form::text('p_no', ' ', array('class' => 'form-control')) }}
    </div>
    <div class="col-6 form-group">
        {{ Form::label('sector', __('Sector'),['class'=>'form-label']) }}
        {{ Form::text('sector', ' ', array('class' => 'form-control')) }}
    </div>
    <div class="col-6 form-group">
        {{ Form::label('emirate', __('Emirate'),['class'=>'form-label']) }}
        <select id="my-select" class="form-control" name="emirate">
            <option selected disabled>Select Emirate</option>
            <option value="abu_dhabi">Abu Dhabi</option>
            <option value="dubai">Dubai</option>
            <option value="sharjah">Sharjah</option>
            <option value="ajman">Ajman</option>
            <option value="umm_al_quwain">Umm Al Quwain</option>
            <option value="ras_al_khaimah ">Ras Al Khaimah </option>
            <option value="fujairah">Fujairah</option>
        </select>
    </div>
    <div class="col-12 form-group">
        {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
        <textarea name="description" id="description" cols="10" rows="3" class="form-control"></textarea>
    </div>
</section>
<script>
    $('.commission_amount').change(function (e) {
        let bc =  $('.commission_amount').val();
        $('.wva').val(bc);
        $('#with_vat_commission').val(bc);
        let wova = (bc * 5) / 100;
        $('.wova').val( bc-wova);
        $('#without_vat_commission').val( bc-wova);
    });
</script>
