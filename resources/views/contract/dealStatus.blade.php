{{ Form::model($status, array('route' => array('contract.statusUpdate', $status->id), 'method' => 'POST')) }}
<div class="modal-body">
    <div class="form-group">
        <label for="contract_status">Status Update</label>
        <select id="contract_status" class="form-control" name="contract_status">
            <option {{($status->contract_stage == 'unsigned')?'selected':''}} value="unsigned">New</option>
            <option {{($status->contract_stage == 'pending')?'selected':''}} value="pending">Pending</option>
            <option {{($status->contract_stage == 'readToSign')?'selected':''}} value="readToSign">Ready To Sign</option>
            @if (\Auth::user()->designation == 'Jovera')
                <option value="cm_signed">Convert To Deal</option>
            @endif
        </select>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>

{{Form::close()}}

