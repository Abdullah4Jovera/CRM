{{ Form::model($pipeline, array('route' => array('pipelines.update', $pipeline->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-12">
            {{ Form::label('name', __('Pipeline Name'),['class'=>'form-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group d-none">
            <select id="my-select" class="form-control" name="stages[]" multiple>
                @foreach ($stages as $stage)
                    <option value="{{$stage->id}}" selected></option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{Form::close()}}
