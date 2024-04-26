{{Form::model($user,array('route' => array('users.update', $user->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group ">
                {{Form::label('name',__('Name'),['class'=>'form-label']) }}
                {{Form::text('name',null,array('class'=>'form-control font-style','placeholder'=>__('Enter User Name')))}}
                @error('name')
                <small class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('email',__('Email'),['class'=>'form-label'])}}
                {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter User Email')))}}
                @error('email')
                <small class="invalid-email" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="designation" class="form-label">Designation</label>
                <select name="designation" id="designation" class="form-control">
                    <option selected disabled>Select Designation</option>
                    <option {{($user->designation == 'Jovera')? 'selected' : ''}} value="Jovera">Jovera</option>
                    <option {{($user->designation == 'HOD')? 'selected' : ''}} value="HOD">HOD</option>
                    <option {{($user->designation == 'Manager')? 'selected' : ''}} value="Manager">Manager</option>
                    <option {{($user->designation == 'Team Leader')? 'selected' : ''}} value="Team Leader">Team Leader</option>
                    <option {{($user->designation == 'Coordinator')? 'selected' : ''}} value="Coordinator">Coordinator</option>
                    <option {{($user->designation == 'Sales Agent')? 'selected' : ''}} value="Sales Agent">Sales</option>
                    <option {{($user->designation == 'Marketing Manager')? 'selected' : ''}} value="Marketing Manager">Marketing Manager</option>
                    <option {{($user->designation == 'Marketing Agent')? 'selected' : ''}} value="Marketing Agent">Marketing Agent</option>
                    <option {{($user->designation == 'TS Team Leader')? 'selected' : ''}} value="TS Team Leader">TS Team Leader</option>
                    <option {{($user->designation == 'TS Agent')? 'selected' : ''}} value="TS Agent">TS Agent</option>
                </select>
                @error('designation')
                <small class="invalid-designation" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        </div>
        @if(\Auth::user()->type != 'super admin')
            <div class="form-group col-md-6">
                {{ Form::label('role', __('User Role'),['class'=>'form-label']) }}
                {!! Form::select('role', $roles, $user->roles,array('class' => 'form-control select2','required'=>'required')) !!}
                @error('role')
                <small class="invalid-role" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        @endif
        <div class="col-md-6">
            <div class="form-group">
                <label for="branch" class="form-label">Branch</label>
                <select name="branch" id="branch" class="form-control">
                    <option selected disabled>Select Branch</option>
                    <option {{($user->branch == 'Jovera')? 'selected' : 'ajman'}} value="ajman">Ajman</option>
                    <option {{($user->branch == 'Jovera')? 'selected' : 'abu dhabi'}} value="abu dhabi">Abu Dhabi</option>
                </select>
                @error('branch')
                <small class="invalid-branch" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        </div>
        {{-- <div class="col-md-6">
            <div class="form-group">
                <label for="designation" class="form-label">Designation</label>
                <select name="designation" id="designation" class="form-control">
                    <option selected disabled>Select Designation</option>
                    <option value="Jovera">Jovera</option>
                    <option value="HOD">HOD</option>
                    <option value="Manager">Manager</option>
                    <option value="Team Leader">Team Leader</option>
                    <option value="Coordinator">Coordinator</option>
                    <option value="Sales Agent">Sales</option>
                    <option value="Marketing Manager">Marketing Manager</option>
                    <option value="Marketing Agent">Marketing Agent</option>
                    <option value="TS Team Leader">TS Team Leader</option>
                    <option value="TS Agent">TS Agent</option>
                </select>
                @error('designation')
                <small class="invalid-designation" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        </div> --}}
        @if(!$customFields->isEmpty())
            <div class="col-md-6">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('customFields.formBuilder')
                </div>
            </div>
        @endif
    </div>

</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light"data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>

{{Form::close()}}
