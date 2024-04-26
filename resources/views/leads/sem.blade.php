<section class="sme row">
    <div class="col-12 form-group">
        <label for="finanical_amount" class="form-label">{{__('Finanical Amount')}}</label>
        <input type="number" value="" name="finance_amount" class ='form-control finanical_amount' >
    </div>
    <div class="col-3 form-group">
        <label for="bank_commission" class="form-label">{{__('Bank Commission')}}</label>
        <input type="number" value="" name="bank_commission" class='form-control bank_commission'>
    </div>
    <div class="col-3 form-group">
        <label for="customer_commission" class="form-label">{{__('Customer Commission')}}</label>
        <input type="number" value="" name="customer_commission"  class='form-control customer_commission'>
    </div>
    <div class="col-3 form-group">
        {{ Form::label('with_vat_commission', __('With Vat Amount'),['class'=>'form-label']) }}
        <input type="hidden" value="" name="with_vat_commission" id="wva">
        <input type="number" value="" class="form-control wva " disabled >
    </div>
    <div class="col-3 form-group">
        {{ Form::label('without_vat_commission', __('Without Vat Amount'),['class'=>'form-label']) }}
        <input type="hidden" value="" name="without_vat_commission" id="wova">
        <input type="number" value="" class="form-control wova " disabled >
    </div>
    <div class="col-6 form-group">
        {{ Form::label('short_term', __('Loan Type'),['class'=>'form-label']) }}
        <div>
            {{ Form::label('short_term', __('Short Term'),['class'=>'form-label']) }}
            <input type="radio" name="term" id="short_term" value="short_term" checked>
            {{ Form::label('long_term', __('Long Term'),['class'=>'form-label']) }}
            <input type="radio" name="term" id="long_term" value="long_term">
        </div>
    </div>
    <div class="col-12 form-group">
        {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
        <textarea name="description" id="description" cols="10" rows="3" class="form-control"></textarea>
    </div>
</section>
<script>
    $('.bank_commission').change(function (e) {
        let bc =  $('.bank_commission').val();
        $('.wva').val(bc);
        let wova = (bc * 5) / 100;
        $('.wova').val( bc-wova);
        $('#total_commission').val( bc);
        $('.total_commission').val( bc);

    });
    $('.customer_commission').change(function (e) {
        let totl_amount = (parseInt($('.bank_commission').val()) + parseInt($('.customer_commission').val()));
        $('#wva').val(' ');
        $('.wva').val(' ');
        $('.wva').val(totl_amount);
        $('#wva').val(totl_amount);
        let withoutVatAmount = (totl_amount * 5) / 100;
        $('#wova').val(' ');
        $('.wova').val(' ');
        $('#wova').val(totl_amount - withoutVatAmount);
        $('.wova').val(totl_amount - withoutVatAmount);
    });
    $('.finanical_amount').on("input", function () {
        var finanical_amount= $('.finanical_amount').val();
        if(finanical_amount != null ){
            $('.bank_commission').removeAttr('disabled');
            $('.customer_commission').removeAttr('disabled');
        }
    });
</script>
