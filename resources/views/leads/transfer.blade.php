{{ Form::model($lead, array('route' => array('leads.is_transfered', $lead->id), 'method' => 'POST')) }}
<div class="modal-body">
    <div class="row">
        <div class="row mb-2 me-1 p-2 ms-auto rounded-2 border border-dark-subtle bg-info-subtle">
            <h5 class="text-capitalize text-primary d-flex justify-content-center">Client Info </h5>
            <div class="col-4 form-group">
                <select class="form-control select productsError" name="products" id="products">
                    @foreach ($products as $product)
                        <option {{($product->id == $lead->product->id)?"selected":''}} value="{{$product->id}}">{{$product->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4 form-group">
                <input type="hidden" name="lead_id" id="lead_id" value="{{ optional($lead)->id }}">
                <input type="tel" name="phone" id="phone" value="{{$lead->client->phone}}" disabled class="form-control productsError" required minlength="10" maxlength="10" placeholder="Phone Ex. 05xxxxxxxx"  oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');">
            </div>
            <div class="col-4 form-group">
                {{ Form::text('name', $lead->client->name, array('class' => 'form-control name','required'=>'required', 'placeholder'=>'Name','disabled'=>'disabled')) }}
            </div>
            <div class="col-4 form-group">
                {{ Form::email('email', $lead->client->email, array('class' => 'form-control email', 'placeholder'=>'Email','disabled'=>'disabled')) }}
            </div>
            <div class="col-4 form-group">
                <input type="text" name="eid" disabled id="eid" class="form-control eid" value="{{$lead->client->e_id}}" required  maxlength="15" placeholder="Emirate ID Ex. xxxxxxxxxxxxxxx"  oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');">
            </div>
        </div>
        <div class="row mb-2 me-1 p-2 ms-auto rounded-2 border border-dark-subtle bg-info-subtle">
            <h5 class="text-capitalize text-primary d-flex justify-content-center">Operation Info</h5>
            <div class="col-12 form-group">
                <select id="choices-multiple2" class="form-control select2 choices-multiple2" name="user_id[]" multiple data-placeholder= "Select Users">
                    @foreach ($users as $user)
                        <option {{ (in_array($user->id, old('users', [])) || isset($lead) && $lead->users->contains($user->id)) ? 'selected' : '' }} value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 form-group">
                <select class="form-control select" name="pipeline" id="pipeline">
                    <option selected disabled >Select Pipeline</option>
                    @foreach ($pipelines as $pipe)
                        @if ($pipe->id !=1)
                            <option {{($pipe->id == $lead->pipeline_id)?"selected":''}} value="{{$pipe->id}}">{{$pipe->name}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-6 form-group">
                {{ Form::label('stage_id', __('Stage'), ['class' => 'form-label d-none']) }}<span class="text-danger d-none">*</span>
                {{ Form::select('stage_id', ['' => __('Select Stage')], null, ['class' => 'form-control select', 'required' => 'required']) }}
            </div>
            <div class="col-6 form-group">
                <select class="form-control select" disabled name="lead_type" id="lead_type_form">
                    <option selected disabled>Select Lead From</option>
                    @foreach ($lead_types as $lead_type)
                        <option {{ $lead->lead_type == $lead_type->id ? "selected" : '' }} value="{{ $lead_type->id }}">{{ $lead_type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 form-group">
                {{ Form::select('sources[]', $sources,null, array('class' => 'form-control select2 choices-multiple1','id'=>'choices-multiple1','multiple'=>'','data-placeholder'=>"Select Sources",'disabled'=>'disabled')) }}
            </div>
            <div class="row mb-2 me-1 p-2 ms-auto service_type rounded-2 border border-dark-subtle bg-info-subtle">
                <h5 class="text-capitalize text-primary d-flex justify-content-center">Service Info</h5>

            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>


{{Form::close()}}


<script>
    // $(function () {
    //     let products = $('#products').val();
    //     let phone = $('#phone').val();
    //     let lead_id = $('#lead_id').val();
    //     if (products != null) {
    //         $('#phone').removeAttr('disabled', 'disabled');
    //         let id = products;
    //         var dat = "{{ route('leads.getserviceinfo', ":id") }}";
    //         dat = dat.replace(':id', id);
    //         $.ajax({
    //             type: "GET",
    //             url: dat,
    //             dataType: "html",
    //             success: function (response) {
    //                 $('.service_type').html(response);
    //             },
    //             error: function (xhr, status, error) {
    //                 console.error('Error fetching service info:', error);
    //             }
    //         });
    //     }
    //     if (products != '' && phone != '') {
    //         $.ajax({
    //             type: "POST",
    //             url: "{{ url('leads/getClientData') }}",
    //             data: {'phone':phone , 'products':products},
    //             dataType: "json",
    //             headers: {
    //                 'X-CSRF-Token': '{{ csrf_token() }}',
    //             },
    //             success: function (response) {
    //                 $('.createBtn').removeAttr('disabled', 'disabled');
    //                 if (response.length > 0) {
    //                     $.each(response, function (ind, vle) {
    //                         $('.name').val(vle.name);
    //                         $('.email').val(vle.email);
    //                         $('.eid').val(vle.e_id);
    //                         $('.address').val(vle.address);
    //                         $('.name').attr('disabled', 'disabled');
    //                         $('#phone').attr('disabled', 'disabled');
    //                         $('.email').attr('disabled', 'disabled');
    //                         $('.eid').attr('disabled', 'disabled');
    //                         $('.address').attr('disabled', 'disabled');
    //                     });
    //                 }
    //             }
    //         });
    //     }
    // });
    $(function () {
        let products = $('#products').val();
        let phone = $('#phone').val();
        let lead_id = $('#lead_id').val();
        let id = products;
        $.ajax({
            type: "GET",
            url: "{{ route('leads.editserviceinfo', ['id' => 'id', 'lead_id' => 'lead_id']) }}".replace('id', id).replace('lead_id', lead_id),
            dataType: "html",
            success: function (response) {
                console.log(response);
                $('.service_type').html(response);
                if (products !== '' && phone !== '') {
                    $.ajax({
                        type: "POST",
                        url: "{{ url('leads/getClientData') }}",
                        data: {'phone': phone, 'products': products},
                        dataType: "json",
                        headers: {'X-CSRF-Token': '{{ csrf_token() }}'},
                        success: function (response) {
                            $('.createBtn').removeAttr('disabled', 'disabled');
                            if (response.length > 0) {
                                $.each(response, function (ind, vle) {
                                    $('.name').val(vle.name);
                                    $('.email').val(vle.email);
                                    $('.eid').val(vle.e_id);
                                    $('.address').val(vle.address);
                                    $('#client_id').val(vle.id);
                                });
                            }
                        }
                    });
                }
            }
        });
    });
    $('.productsError').change(function () {
        let products = $('#products').val();
        let phone = $('#phone').val();
        if (products != null) {

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
                data: {'phone':phone , 'products':products},
                dataType: "json",
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}',
                },
                success: function (response) {
                    if (response == 1) {
                        $('.createBtn').attr('disabled', 'disabled');
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Client Already Take This  Service"
                        });
                    }else{
                        $('.createBtn').removeAttr('disabled', 'disabled');
                        if (response.length > 0) {
                            $.each(response, function (ind, vle) {
                                $('.name').val(vle.name);
                                $('.email').val(vle.email);
                                $('.eid').val(vle.e_id);
                                $('.address').val(vle.address);
                                $('.name').attr('disabled', 'disabled');
                                $('#phone').attr('disabled', 'disabled');
                                $('.email').attr('disabled', 'disabled');
                                $('.eid').attr('disabled', 'disabled');
                                $('.address').attr('disabled', 'disabled');
                            });
                        }

                    }

                }
            });
        }
    });
    var stage_id = '{{ $lead->stage_id }}';
    $(document).ready(function () {
        var pipeline_id = $('[name=pipeline]').val();
        getStages(pipeline_id);
    });
    $(document).on("change", "#commonModal select[name=pipeline]", function () {
        var currVal = $(this).val();
        getStages(currVal);
    });
    function getStages(id) {
        $.ajax({
            url: '{{ route('leads.json') }}',
            data: {pipeline_id: id, _token: $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            success: function (data) {
                var stage_cnt = Object.keys(data).length;
                $("#stage_id").empty();
                if (stage_cnt > 0) {
                    $.each(data, function (key, data1) {
                        var select = key === '{{ $lead->stage_id }}' ? 'selected' : '';
                        $("#stage_id").append('<option value="' + key + '" ' + select + '>' + data1 + '</option>');
                    });
                }
                $("#stage_id").val(stage_id);
                $('#stage_id').select2({
                    placeholder: "{{ __('Select Stage') }}"
                });
            }
        })
    }

</script>
