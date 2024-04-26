{{ Form::open(array('route' => ['leads.priority.store',$lead->id])) }}
<div class="modal-body">
    <div class="row">

        <div class="col-12 form-group">
            <div class="row gutters-xs">
                <select name="priority" class="form-control" id="priority">
                    <option selected disabled > Select Priority</option>
                    <option {{($lead->priority == 1) ? 'selected' : "" }}  value="1">Low</option>
                    <option {{($lead->priority == 2) ? 'selected' : "" }}  value="2">Medium</option>
                    <option {{($lead->priority == 3) ? 'selected' : "" }}  value="3">High</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>

{{Form::close()}}
