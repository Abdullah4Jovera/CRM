@push('css-page')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

@endpush
{{ Form::open(array('url' => 'leads')) }}
<div class="modal-body">
    <div class="row">
        <div class="row mb-2 me-1 p-2 ms-auto rounded-2 border border-dark-subtle bg-info-subtle">
            <h5 class="text-capitalize text-primary d-flex justify-content-center">Client Info </h5>
            <div class="col-4 form-group">
                <select class="form-control select productsError" name="products" id="createProducts">
                    <option selected disabled >Select Service</option>
                    @foreach ($products as $product)
                    <option value="{{$product->id}}">{{$product->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4 form-group">
                <div class="row">
                    <div class="col-3">
                        <input type="text" disabled value="+971" disabled class="form-control">
                    </div>
                    <div class="col-9 ">
                        <input type="tel" name="phone" id="phone" disabled class="form-control productsError" required minlength="9" maxlength="9" placeholder="Phone Ex. 5xxxxxxxx"  oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');">
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-4 form-group">
                {{ Form::text('name', null, array('class' => 'form-control name','required'=>'required', 'placeholder'=>'Name')) }}
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-6 form-group">
                {{ Form::email('email', null, array('class' => 'form-control email', 'placeholder'=>'Email')) }}
            </div>
            <div class="col-6 form-group">
                <input type="hidden" id="client_id" value="" name="client_id">
                <input type="text" name="eid" id="eid" class="form-control eid"  maxlength="15" placeholder="Emirate ID Ex. xxxxxxxxxxxxxxx"  oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');">
            </div>
        </div>
        <div class="row mb-2 me-1 p-2 ms-auto rounded-2 border border-dark-subtle bg-info-subtle">
            <h5 class="text-capitalize text-primary d-flex justify-content-center">Operation Info</h5>
            <div class="col-4 form-group">
                {{ Form::select('user_id[]', $users,null, array('class' => 'form-control select2 choices-multiple2','id'=>'choices-multiple2','multiple'=>'','data-placeholder'=>"Select Users")) }}
                @if(count($users) == 1)
                <div class="text-muted text-xs">
                    {{__('Please create new users')}} <a href="{{route('users.index')}}">{{__('here')}}</a>.
                </div>
                @endif
            </div>
            <div class="col-4 form-group">
                <select class="form-control select" name="pipeline" id="pipeline">
                    <option selected disabled >Select Pipeline</option>
                    @foreach ($pipeline as $pipe)
                        @if ($pipe->id !=1 && $pipe->id !=10)
                            <option value="{{$pipe->id}}">{{$pipe->name}}</option>
                        @endif
                    @endforeach
                </select>
                @error('pipeline')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-4 form-group">
                {{ Form::label('stage_id', __('Stage'),['class'=>'form-label d-none']) }}<span class="text-danger d-none">*</span>
                {{ Form::select('stage_id', [''=>__('Select Stage')],null, array('class' => 'form-control select','required'=>'required')) }}
                @error('stage_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-6 form-group">
                <select class="form-control select lead_type_id" name="lead_type" id="lead_type">
                    <option selected disabled >Select Lead From</option>
                    @foreach ($lead_types as $lead_type )
                        <option  value="{{$lead_type->id}}">{{$lead_type->name}}</option>
                    @endforeach
                </select>
                @error('lead_type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-6 form-group">
                {{ Form::label('sources', __('Sources'),['class'=>'form-label d-none']) }}<span class="text-danger d-none">*</span>
                {{ Form::select('sources', [''=>__('Select Sources')],null, array('class' => 'form-control select','required'=>'required', 'id'=>'sources')) }}
                @error('sources')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="row mb-2 me-1 p-2 ms-auto service_type rounded-2 border border-dark-subtle bg-info-subtle">
            <h5 class="text-capitalize text-primary d-flex justify-content-center">Service Info</h5>

        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn createBtn btn-primary">
</div>

{{Form::close()}}
<script>
$('.productsError').change(function () {
    var products = $('#createProducts').val();
    let phone = '+971' + $('#phone').val();

    if (products != null) {
        $('#phone').removeAttr('disabled', 'disabled');
        let id = products;
        var dat = "{{ route('leads.getserviceinfo', ":id") }}";
        dat = dat.replace(':id', id);
        $.ajax({
            type: "GET",
            url: dat,
            dataType: "html",
            success: function (response) {
                $('.service_type').html(response);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching service info:', error);
            }
        });
    }


    if (products != '' && phone != '') {
    $.ajax({
        type: "POST",
        url: "{{ url('leads/getClientData') }}",
        data: { 'phone': phone, 'products': products },
        dataType: "json",
        headers: {
            'X-CSRF-Token': '{{ csrf_token() }}',
        },
        success: function (response) {
            if (response.clientData) {
                // Populate client information fields
                $('.name').val(response.clientData.client.name);
                $('.email').val(response.clientData.client.email);
                $('.eid').val(response.clientData.client.eid);
                $('.address').val(response.clientData.client.address);
                $('#client_id').val(response.clientData.client.id);
                if (!response.leadData) {
                    $('.name, .email, .eid, .address').attr('disabled', 'disabled');
                    $('.createBtn').removeAttr('disabled');
                } else {
                    $('.name, .email, .eid, .address').attr('disabled', 'disabled');
                    $('.createBtn').attr('disabled', 'disabled');
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        html: `<h5>Client Already Exists in <span style="color:red;"><b>${response.leadData.pipeline_name}</b></span> at <span style="color:red;"><b>${response.leadData.stage_name}</b></span></h5>`
                    }).
                    then((result) => {
                        $('#client_id').val('');
                    });
                }
            } else {
                $('.name, .email, .eid, .address').val('');
                $('.name').attr('placeholder', 'Name');
                $('.email').attr('placeholder', 'Email');
                $('.eid').attr('placeholder', 'Emirate ID');
                $('.address').attr('placeholder', 'Address');
                $('.name, .email, .eid, .address').removeAttr('disabled');
                $('.createBtn').removeAttr('disabled');
                $('#client_id').val('');
            }
        },
        error: function(xhr, status, error) {
            if (xhr.status === 404) {
                console.error('Resource not found:', error);
            } else {
                console.error('Error:', error);
            }
                $('.name, .email, .eid, .address').val('');
                $('.name').attr('placeholder', 'Name');
                $('.email').attr('placeholder', 'Email');
                $('.eid').attr('placeholder', 'Emirate ID');
                $('.address').attr('placeholder', 'Address');
                $('.name, .email, .eid, .address').removeAttr('disabled');
                $('.createBtn').removeAttr('disabled');
        }
    });
}


});



    $(document).ready(function () {
        var pipeline_id = $('[name=pipeline]').val();
        getStages(pipeline_id);
    });

    $(document).on("change", "#commonModal select[name=pipeline]", function () {
        var currVal = $(this).val();
        console.log('current val ', currVal);
        getStages(currVal);
    });

    function getStages(id) {
        $.ajax({
            url: '{{route('leads.json')}}',
            data: {pipeline_id: id, _token: $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            success: function (data) {
                var stage_cnt = Object.keys(data).length;
                $("#stage_id").empty();
                if (stage_cnt > 0) {
                    $.each(data, function (key, data1) {
                        var select = '';

                        $("#stage_id").append('<option value="' + key + '" ' + select + '>' + data1 + '</option>');
                    });
                }
                $("#stage_id").val(stage_id);
                $('#stage_id').select2({
                    placeholder: "{{__('Select Stage')}}"
                });
            }
        })
    }

    $(document).on("change", "#commonModal select[name=lead_type]", function () {
        // debugger;
        var currVal = $(this).val();
        getSource(currVal);
    });

    function getSource(id) {
        $.ajax({
            url: '{{route('leads.newjson')}}',
            data: {lead_type_id: id, _token: $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            success: function (data) {
                var source_cnt = Object.keys(data).length;
                $("#sources").empty();
                if (source_cnt > 0) {
                    $.each(data, function (key, data1) {
                        var select = '';

                        $("#sources").append('<option value="' + key + '" ' + select + '>' + data1 + '</option>');
                    });
                }
                $("#sources").val(sources);
                $('#sources').select2({
                    placeholder: "{{__('Select Sources')}}"
                });
            }
        })
    }


</script>



