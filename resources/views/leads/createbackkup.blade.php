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
                    </div>
                </div>
            </div>
            <div class="col-4 form-group">
                {{ Form::text('name', null, array('class' => 'form-control name','required'=>'required', 'placeholder'=>'Name')) }}
            </div>
            <div class="col-6 form-group">
                {{ Form::email('email', null, array('class' => 'form-control email', 'placeholder'=>'Email')) }}
            </div>
            {{-- <div class="col-4 form-group">
                <select class="form-control select" name="nationality" id="nationality">
                    <option selected disabled >Select Nationality</option>
                    <option value="UAE National">UAE National</option>
                    <option value="Expatriates">Expatriates</option>
                </select>
            </div>
            <div class="col-4 form-group">
                <select class="form-control select" name="language" id="language">
                    <option selected disabled >Select Language</option>
                    <option value="Arabic">Arabic</option>
                    <option value="English">English</option>
                    <option value="Other">Other</option>
                </select>
            </div> --}}
            <div class="col-6 form-group">
                <input type="hidden" id="client_id" value="" name="client_id">
                <input type="text" name="eid" id="eid" class="form-control eid"  maxlength="15" placeholder="Emirate ID Ex. xxxxxxxxxxxxxxx"  oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');">
            </div>
            {{-- <div class="col-6 form-group">
                {{ Form::text('address', null, array('class' => 'form-control address' , 'placeholder'=>'Address')) }}
            </div> --}}
        </div>
        <div class="row mb-2 me-1 p-2 ms-auto rounded-2 border border-dark-subtle bg-info-subtle">
            <h5 class="text-capitalize text-primary d-flex justify-content-center">Operation Info</h5>
            <div class="col-3 form-group">
                {{ Form::select('user_id[]', $users,null, array('class' => 'form-control select2 choices-multiple2','required'=>'required','id'=>'choices-multiple2','multiple'=>'','data-placeholder'=>"Select Users")) }}
                @if(count($users) == 1)
                <div class="text-muted text-xs">
                    {{__('Please create new users')}} <a href="{{route('users.index')}}">{{__('here')}}</a>.
                </div>
                @endif
            </div>
            <div class="col-3 form-group">
                <select class="form-control select" name="pipeline" id="pipeline">
                    <option selected disabled >Select Pipeline</option>
                    @foreach ($pipeline as $pipe)
                        @if ($pipe->id !=1 && $pipe->id !=10)
                            <option value="{{$pipe->id}}">{{$pipe->name}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                <select class="form-control select lead_type_id" name="lead_type" id="lead_type">
                    <option selected disabled >Select Lead From</option>
                    @foreach ($lead_types as $lead_type )
                        <option  value="{{$lead_type->id}}">{{$lead_type->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group sourcesCreateForm">

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
        // debugger;
        var products = $('#createProducts').val();
        let phone = $('#phone').val();
        if (products != null) {
            $('#phone').removeAttr('disabled','disabled');
            let id = products;
            console.log(id);
            var dat = "{{ route('leads.getserviceinfo', ":id") }}";
            dat = dat.replace(':id', id);
            $.ajax({
                type: "GET",
                url: dat,
                dataType: "html",
                success: function (response) {
                    $('.service_type').html(response);

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
                                $('#client_id').val(vle.id);
                                $(`#nationality option[value='${vle.nationality}']`).prop('selected', true);
                                $(`#language option[value='${vle.language}']`).prop('selected', true);
                                $('.name').attr('disabled', 'disabled');
                                $('.email').attr('disabled', 'disabled');
                                $('.eid').attr('disabled', 'disabled');
                                $('.address').attr('disabled', 'disabled');
                                $('#nationality').attr('disabled', 'disabled');
                                $('#language').attr('disabled', 'disabled');
                            });
                        }
                        if (response == 0) {
                            $('.name').val('');
                            $('.email').val('');
                            $('.eid').val('');
                            $('.address').val('');

                            $('.name').attr('placeholder', 'Name');
                            $('.email').attr('placeholder', 'Email');
                            $('.eid').attr('placeholder', 'Emirate ID');
                            $('.address').attr('placeholder', 'Address');
                            $('.name').removeAttr('disabled', 'disabled');
                            $('.email').removeAttr('disabled', 'disabled');
                            $('.eid').removeAttr('disabled', 'disabled');
                            $('.address').removeAttr('disabled', 'disabled');
                            $('#nationality').removeAttr('disabled', 'disabled');
                            $('#language').removeAttr('disabled', 'disabled');
                        }
                    }

                }
            });
        }

    });
    $(document).on('change','.lead_type_id',function () {

        lead_type_id = $('.lead_type_id').val();
        var old_url = "{{ route('leads.getsourses', ":id") }}";
        url = old_url.replace(':id', lead_type_id);
        $.ajax({
            type: "GET",
            url: url,
            // data:
            dataType: "html",
            success: function (response) {
                $('.sourcesCreateForm').html(response );
            }
        });

    });

    var stage_id = '{{$lead->stage_id}}';

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
                        if (key == '{{ $lead->stage_id }}') {
                            select = 'selected';
                        }
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


</script>



