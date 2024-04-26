{{ Form::model($lead, ['route' => ['leads.update', $lead->id], 'method' => 'PUT']) }}
    <div class="modal-body">
        <div class="row">
            <h4 class="text-capitalize text-default d-flex justify-content-center">{{ optional($lead->product)->name }}</h4>
            <div class="row mb-2 me-1 p-2 ms-auto rounded-2 border border-dark-subtle bg-info-subtle">
                <h5 class="text-capitalize text-primary d-flex justify-content-center">Client Info </h5>
                <div class="col-6 form-group">
                    <input type="hidden" name="products" id="editLeadProduct" value="{{ optional($lead->product)->id }}">
                    <input type="hidden" name="products" id="lead_id" value="{{ optional($lead)->id }}">
                    <input type="tel" name="phone" id="phone" value="{{ $lead->client->phone }}" {{(\Auth::user()->id == 2)? "" :'disabled' }} class="form-control productsError" required minlength="10" maxlength="10" placeholder="Phone Ex. 05xxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');">
                </div>
                <div class="col-6 form-group">
                    {{ Form::text('name', $lead->client->name, ['class' => 'form-control name', 'required' => 'required', 'placeholder' => 'Name']) }}
                </div>
                <div class="col-6 form-group">
                    {{ Form::email('email', $lead->client->email, ['class' => 'form-control email', 'placeholder' => 'Email']) }}
                </div>
                <div class="col-6 form-group">
                    <input type="hidden" id="client_id" value="{{ $lead->client_id }}" name="client_id">
                    <input type="text" name="eid" id="eid" class="form-control eid" value="{{ $lead->client->e_id }}" maxlength="15" placeholder="Emirate ID Ex. xxxxxxxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');">
                </div>
            </div>
            <div class="row mb-2 me-1 p-2 ms-auto rounded-2 border border-dark-subtle bg-info-subtle">
                <h5 class="text-capitalize text-primary d-flex justify-content-center">Operation Info</h5>
                <div class="col-6 form-group">
                    <select class="form-control select" name="pipeline" id="pipeline">
                        <option selected disabled>Select Pipeline</option>
                        @foreach ($pipelines as $pipe)
                            @if ($pipe->id != 1 && $pipe->id != 10)
                                <option {{ $pipe->id == $lead->pipeline_id ? "selected" : '' }} value="{{ $pipe->id }}">{{ $pipe->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-6 form-group">
                    {{ Form::label('stage_id', __('Stage'), ['class' => 'form-label d-none']) }}<span class="text-danger d-none">*</span>
                    {{ Form::select('stage_id', ['' => __('Select Stage')], null, ['class' => 'form-control select', 'required' => 'required']) }}
                </div>
                <div class="col-6 form-group">
                    <select class="form-control select" {{(\Auth::user()->id == 2) ?  " ": 'disabled'}} name="lead_type" id="lead_type_form">
                        <option selected disabled>Select Lead From</option>
                        @foreach ($lead_types as $lead_type)
                            <option {{ $lead->lead_type == $lead_type->id ? "selected" : '' }} value="{{ $lead_type->id }}">{{ $lead_type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 form-group">
                    {{ Form::label('sources', __('Sources'), ['class' => 'form-label d-none']) }}<span class="text-danger d-none">*</span>
                    {{ Form::select('sources', ['' => __('Select Stage')], null, ['class' => 'form-control select', 'required' => 'required']) }}
                </div>
            </div>
            <div class="row mb-2 me-1 p-2 ms-auto service_type rounded-2 border border-dark-subtle bg-info-subtle">
                <h5 class="text-capitalize text-primary d-flex justify-content-center">Service Info</h5>

            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
    </div>

{{ Form::close() }}

<script>

    $(function () {
        let products = $('#editLeadProduct').val();
        let phone = $('#phone').val();
        let lead_id = $('#lead_id').val();
        let id = products;
        $.ajax({
            type: "GET",
            url: "{{ route('leads.editserviceinfo', ['id' => 'id', 'lead_id' => 'lead_id']) }}".replace('id', id).replace('lead_id', lead_id),
            dataType: "html",
            success: function (response) {
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
    var stage_id = '{{$lead->stage_id}}';
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
    var sources = '{{ $lead->lead_type }}';

    $(document).ready(function () {
        var lead_type = $('[name=lead_type]').val();
        getSource(lead_type);
    });
    $(document).on("change", "#commonModal select[name=lead_type]", function () {
        var currVal = $(this).val();
        getSource(currVal);
    });
    function getSource(id) {
        $.ajax({
            url: '{{route('leads.newjson')}}',
            data: { lead_type_id: id, _token: $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            success: function (data) {
                var sourceDropdown = $('#sources');
                sourceDropdown.empty();

                if (Object.keys(data).length > 0) {
                    $.each(data, function (key, data1) {
                        var select = '';
                        if (key == '{{ $lead->sources }}') {
                            select = 'selected';
                        }
                        sourceDropdown.append('<option value="' + key + '" ' + select + '>' + data1 + '</option>');
                    });
                }
                sourceDropdown.val('{{ $lead->sources }}');
                sourceDropdown.select2({
                    placeholder: "{{__('Select Sources')}}"
                });
            }
        });
    }
</script>
