{{ Form::open(array('url' => 'leads/filterSearch')) }}
<style>
    /* .choices[data-type*="select-one"] .choices__inner{
        width: 235px !important;
    } */
</style>
<div class="modal-body">
    <div class="row">
        {{-- <div class="col-6 form-group">
            {{ Form::label('user_id', __('Select User'),['class'=>'form-label']) }}
            <select name="user[]" id="user_id" class="from-control select2" multiple="multiple" required>
                <option value="" disabled>Select User</option>
                @foreach ($users as $user)
                    @if (\Auth::user()->id != $user->id)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                    @endif
                @endforeach
            </select>
            <span id="error" style="color:rgba(15, 13, 13, 0.671);"></span>
        </div> --}}
        <div class="col-6 form-group">
            {{ Form::label('lead_type', __('Lead From'),['class'=>'form-label']) }}
            <select class="form-control select" name="lead_type" id="lead_type">
                <option selected disabled >Select Lead From</option>
                <option value="Marketing">Marketing</option>
                <option value="Tele Sales">Tele Sales</option>
                <option value="Other">Other</option>
            </select>
            <span id="error" style="color:rgba(15, 13, 13, 0.671);"></span>
        </div>
        <div class="col-6 form-group">
            {{ Form::label('user_id', __('Select Service'),['class'=>'form-label']) }}
            <select class="form-control select productsError" name="products" id="products">
                <option selected disabled >Select Service</option>
                @foreach ($products as $product)
                    <option value="{{$product->id}}">{{$product->name}}</option>
                @endforeach
            </select>
            <span id="error" style="color:rgba(15, 13, 13, 0.671);"></span>
        </div>
        <div class="col-6 form-group">
            {{ Form::label('searchFrom', __('Search By '),['class'=>'form-label']) }} <br>
            <i class="ti ti-user"></i> <input type="radio" value="name"  name="searchFrom" id="searchFrom">
            <i class="ti ti-phone"></i> <input type="radio" value="phone"  checked name="searchFrom" id="searchFrom">
        </div>
        <div class="col-6 form-group clinetPhone">
            {{ Form::label('clinets', __('Select Client'),['class'=>'form-label']) }}
            <select class="form-control select2 " name="clinetPhone" id="clinetPhone">
                <option value="" disabled selected>Select Client</option>
                @foreach ($clients as $client)
                    <option value="{{$client->id}}">{{$client->phone}}</option>
                @endforeach
            </select>

            <span id="error" style="color:rgba(15, 13, 13, 0.671);"></span>
        </div>
        <div class="col-4 form-group clinetName">
            {{ Form::label('clinets', __('Select Client'),['class'=>'form-label']) }}
            <select class="form-control select2 " name="clinetName" id="clinetName">
                <option value="" disabled selected>Select Client</option>
                @foreach ($clients as $client)
                    <option value="{{$client->id}}">{{$client->name}}</option>
                @endforeach
            </select>
            <span id="error" style="color:rgba(15, 13, 13, 0.671);"></span>
        </div>

        <div class="col-6 form-group">
            {{-- {{ Form::label('user_id', __('Select Pipeline'),['class'=>'form-label']) }}
            <select name="pipeline[]" id="pipeline_id" class="from-control select2" multiple="multiple">
                <option value="" disabled>Select Pipeline</option>
                @foreach ($pipelines as $pipeline)
                    @if ($pipeline->id != 1)
                        <option value="{{$pipeline->id}}">{{$pipeline->name}}</option>
                    @endif
                @endforeach
            </select>
        </div> --}}
        {{-- <div class="col-12 form-group">
            {{ Form::label('date_to', __('Date Range'),['class'=>'form-label']) }}
            <input type="text" name="datefilter" id="datetimes" value=""  class= 'form-control rangeDate' />
        </div> --}}
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" id="getSearchByUser" value="{{__('Search')}}" class="btn  btn-primary">
</div>
{{Form::close()}}


<script>
    $('.rangeDate').daterangepicker({
        maxDate: new Date(),
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'Y-M-DD'
        }
    });
    $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + '   ' + picker.endDate.format('YYYY-MM-DD'));
  });
</script>
<script>
    $(function () {
        $(".clinetName").hide();
    });
    $(document).on('click','#searchFrom',function (e) {
        // debugger;
       var searchBy = $('input[name="searchFrom"]:checked').val();
       console.log(searchBy);
       if (searchBy == "name") {
           $(".clinetPhone").hide();
           $(".clinetName").show();
        } else {
            $(".clinetPhone").show();
            $(".clinetName").hide();
       }
    });
</script>
