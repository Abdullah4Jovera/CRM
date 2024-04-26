{{ Form::model($lead, array('route' => array('leads.convert.to.deal', $lead->id), 'method' => 'POST')) }}

<div class="modal-body">
    <h4 style="display: flex;flex-direction: row;justify-content: center;">Service Application From </h4>
    <p style="display: flex;flex-direction: row;justify-content: center;">{{$lead->product->name}}</p>
    <div class="row border border-2 mt-2  rounded ">
        <div class="col-12 form-group">
            <label for="finanical_amount" class="form-label">{{__('Finanical Amount')}}</label>
            <input type="number" value="" name="finance_amount" required class ='form-control finanical_amount' >
        </div>
        <div class="col-3 form-group">
            <label for="bank_commission" class="form-label">{{__('Bank Commission')}}</label>
            <input type="number" value="" name="bank_commission" required class='form-control bank_commission'>
        </div>
        <div class="col-3 form-group">
            <label for="customer_commission" class="form-label">{{__('Customer Commission')}}</label>
            <input type="number" value="" name="customer_commission"  class='form-control customer_commission'>
        </div>
        <div class="col-3 form-group">
            {{ Form::label('with_vat_commission', __('Total Revenue  (with vat 5%)'),['class'=>'form-label']) }}
            <input type="hidden" value="" name="with_vat_commission" id="wva">
            <input type="number" value="" class="form-control wva " disabled >
        </div>
        <div class="col-3 form-group">
            {{ Form::label('without_vat_commission', __('Total Revenue (without vat 5%)'),['class'=>'form-label']) }}
            <input type="hidden" value="" name="without_vat_commission" id="wova">
            <input type="number" value="" class="form-control wova " disabled >
        </div>
    </div>
    <div class="row border border-2 mt-2  rounded ">
        <!-- ========== Sales Start Section ========== -->
            <h5 style="display: flex;flex-direction: row;justify-content: center;">Sales </h5>
            <div class="col-3  form-group">
                <select name="hodsale" id="hodsale" required class="form-control select2">
                    <option value="">{{ __('HOD Sales') }}</option>
                    @foreach($users as $user)
                        @if ($user->designation =='HOD')
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                {{ Form::number('hodsalecommission','' , array('required'=>'required' ,'class' => 'form-control','placeholder'=>'HOD Commission (%)')) }}
            </div>
            <div class="col-3 form-group">
                <select name="salemanager" id="salemanager" class="form-control select2">
                    <option value="">{{ __('Sales Manager') }}</option>
                    @foreach($users as $user)
                        @if ($user->designation =='Manager')
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                {{ Form::number('salemanagercommission', '', array('class' => 'form-control','step' => '0.1','placeholder'=>'Sales Manager Commission (%)')) }}
            </div>
            <div class="col-3 form-group">
                <select name="coordinator" id="coordinator" class="form-control select2">
                    <option value="">{{ __('Coordinator Name') }}</option>
                    @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                {{ Form::number('coordinator_commission', '', array('class' => 'form-control','step' => '0.1','placeholder'=>'Coordinator Commission (%)')) }}
            </div>
            <div class="col-3 form-group">
                <select name="team_leader" id="team_leader" class="form-control select2">
                    <option value="">{{ __('Team Leader') }}</option>
                    @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                {{ Form::number('team_leader_commission',' ', array('class' => 'form-control','step' => '0.1','placeholder'=>'Team Leader Commission (%)')) }}
            </div>
            <div class="col-3 form-group">
                <select name="team_leader_one" id="team_leader_one" class="form-control select2">
                    <option value="">{{ __('Team Leader') }}</option>
                    @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                {{ Form::number('team_leader_one_commission',' ', array('class' => 'form-control','step' => '0.1','placeholder'=>'Team Leader Commission (%)')) }}
            </div>
            <div class="col-3 form-group">
                <select name="salesagent" id="salesagent" class="form-control select2">
                    <option value="">{{ __('Sales Agent') }}</option>
                    @foreach($users as $user)
                        {{-- <!-- @if ($user->designation =='Sales Agent') --> --}}
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        {{-- <!-- @endif --> --}}
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                {{ Form::number('salesagent_commission', ' ', array('class' => 'form-control','step' => '0.1','placeholder'=>'Agent Commission (%)')) }}
            </div>
            <div class="col-3 form-group">
                <select name="sale_agent_one" id="sale_agent_one" class="form-control select2">
                    <option value="">{{ __('Sales Agent') }}</option>
                    @foreach($users as $user)
                        {{-- <!-- @if ($user->designation =='Sales Agent') --> --}}
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        {{-- <!-- @endif --> --}}
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                {{ Form::number('sale_agent_one_commission', ' ', array('class' => 'form-control','step' => '0.1','placeholder'=>'Agent Commission (%)')) }}
            </div>
        <!-- ========== Sales End Section ========== -->
    </div>
    @if ($lead->is_transfer ==1)
        <div class="row border border-2 mt-2  rounded">
            <!-- ==========Referral Start Section ========== -->
                <h5 style="display: flex;flex-direction: row;justify-content: center;">Referral </h5>
                <div class="col-3 form-group">
                    <select name="salemanagerref" id="salemanagerref" class="form-control select2">
                        <option value="">{{ __('Sale Manager  Ref.') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3 form-group">
                    {{ Form::number('salemanagerrefcommission','', array('class' => 'form-control','step' => '0.1','placeholder'=>'Sale Manager  Ref. Commission (%)')) }}
                </div>
                <div class="col-3 form-group">
                    <select name="agentref" id="agentref" class="form-control select2">
                        <option value="">{{ __('Agent Ref.') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3 form-group">
                    {{ Form::number('agent_commission',' ', array('class' => 'form-control','step' => '0.1','placeholder'=>'Agent Ref. Commission (%)')) }}
                </div>

            <!-- ========= Referral End Section ========== -->
        </div>
    @endif

    @if ($lead->lead_type == '1')
        <div class="row border border-2 mt-2  rounded">
            <!-- ========== Tele Sales Start Section ========== -->
            <h5 style="display: flex;flex-direction: row;justify-content: center;">Tele Sales </h5>
            <div class="col-3 form-group">
                <select name="ts_hod" id="ts_hod" class="form-control select2">
                    <option value="">{{ __('Tele Sales HOD') }}</option>
                    @foreach($users as $user)
                        {{-- <!-- @if ($user->designation == 'TS HOD') --> --}}
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        {{-- <!-- @endif --> --}}
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                {{ Form::number('ts_hod_commission',' ', array('class' => 'form-control','step' => '0.1','placeholder'=>'Tele Sales HOD Commission (%)')) }}
            </div>

            <div class="col-3 form-group">
                <select name="ts_team_leader" id="ts_team_leader" class="form-control select2">
                    <option value="">{{ __('Tele Sales Team Leader') }}</option>
                    @foreach($users as $user)
                        {{-- <!-- @if ($user->designation == 'TS Team Leader') --> --}}
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        {{-- <!-- @endif --> --}}
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                {{ Form::number('ts_team_leader_commission',' ', array('class' => 'form-control','step' => '0.1','placeholder'=>'Tele Sales Team Leader Commission (%)')) }}
            </div>

            <div class="col-3 form-group">
                <select name="tsagent" id="tsagent" class="form-control select2">
                    <option value="">{{ __('Tele Sales Agent') }}</option>
                    @foreach($users as $user)
                        {{-- <!-- @if ($user->designation == 'TS Agent') --> --}}
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        {{-- <!-- @endif --> --}}
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                {{ Form::number('tsagent_commission', ' ', array('class' => 'form-control','step' => '0.1','placeholder'=>'Tele Sales Agent Commission (%)')) }}
            </div>
            <!-- ========== Tele Sales End Section ========== -->
        </div>
    @elseif ($lead->lead_type == '2')
        <div class="row border border-2 mt-2  rounded">
            <!-- ==========Marketing Start Section ========== -->
            <h5 style="display: flex;flex-direction: row;justify-content: center;">Marketing </h5>
            <div class="col-3 form-group">
                <select name="marketingmanager" id="marketingmanagercommission" class="form-control select2">
                    <option value="">{{ __('Marketing Manager') }}</option>
                    @foreach($users as $user)
                    @if ($user->designation == 'Marketing Manager')
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                {{ Form::number('marketingmanagercommission','' , array('class' => 'form-control','step' => '0.1','placeholder'=>'Marketing Manager Commission (%)')) }}
            </div>
            <div class="col-3 form-group">
                <select name="marketingagent" id="marketingagentcommission" class="form-control select2">
                    <option value="">{{ __('Marketing Agent') }}</option>
                    @foreach($users as $user)
                    {{-- <!-- @if ($user->designation == 'Marketing Agent') --> --}}
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    {{-- <!-- @endif --> --}}
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                {{ Form::number('marketingagentcommission','' , array('class' => 'form-control','step' => '0.1','placeholder'=>'Marketing Agent Commission (%)')) }}
            </div>
            <div class="col-3 form-group">
                <select name="marketingagentone" id="marketingagentcommissionone" class="form-control select2">
                    <option value="">{{ __('Marketing Agent') }}</option>
                    @foreach($users as $user)
                    {{-- <!-- @if ($user->designation == 'Marketing Agent') --> --}}
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    {{-- <!-- @endif --> --}}
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                {{ Form::number('marketingagentcommissionone','' , array('class' => 'form-control','step' => '0.1','placeholder'=>'Marketing Agent Commission (%)')) }}
            </div>
            <div class="col-3 form-group">
                <select name="marketingagenttwo" id="marketingagentcommissiontwo" class="form-control select2">
                    <option value="">{{ __('Marketing Agent') }}</option>
                    @foreach($users as $user)
                    {{-- <!-- @if ($user->designation == 'Marketing Agent') --> --}}
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    {{-- <!-- @endif --> --}}
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                {{ Form::number('marketingagentcommissiontwo','' , array('class' => 'form-control','step' => '0.1','placeholder'=>'Marketing Agent Commission (%)')) }}
            </div>
            <!-- ==========Marketing End Section ========== -->
        </div>
    @else
        <div class="row border border-2 mt-2  rounded">
            <!-- ==========3rd Party Start Section ========== -->
            <h5 style="display: flex;flex-direction: row;justify-content: center;">Other</h5>
            <div class="col-6 form-group">
                <select name="other_name" id="other_name" class="form-control select2">
                    <option value="">{{ __('Other Person') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>

                    @endforeach
                </select>
            </div>
            <div class="col-6 form-group">
                {{ Form::number('other_commission',' ', array('class' => 'form-control','step' => '0.1','placeholder'=>'Other Commission (%)')) }}
            </div>

            <!-- ==========3rd Party End Section ========== -->
        </div>
    @endif
    <div class="row border border-2 mt-2  rounded">
        <!-- ==========3rd Party Start Section ========== -->
        <h5 style="display: flex;flex-direction: row;justify-content: center;">3rd Party </h5>
        <div class="col-6 form-group">
            <input type="text" placeholder="3rd Party Broker Name" name="broker_name" id="broker_name" class="form-control" value="">
        </div>
        <div class="col-6 form-group">
            {{ Form::number('broker_commission',' ', array('class' => 'form-control','step' => '0.1','placeholder'=>'3rd Party Broker Commission (%)')) }}
        </div>

        <!-- ==========3rd Party End Section ========== -->
    </div>

</div>


<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Convert')}}" class="btn  btn-primary">
</div>

{{Form::close()}}



<script>
    $('.bank_commission').change(function (e) {
        let bc =  $('.bank_commission').val();
        $('.wva').val(bc);
        let wova = Math.ceil(bc / 1.05);
        $('.wova').val( wova);
        $('#total_commission').val( bc);
        $('.total_commission').val( bc);
    });
    $('.customer_commission').change(function (e) {
        let totl_amount = (parseInt($('.bank_commission').val()) + parseInt($('.customer_commission').val()));
        $('#wva').val(' ');
        $('.wva').val(' ');
        $('.wva').val(totl_amount);
        $('#wva').val(totl_amount);
        let withoutVatAmount = Math.ceil(totl_amount /1.05);
        $('#wova').val(' ');
        $('.wova').val(' ');
        $('#wova').val(withoutVatAmount);
        $('.wova').val(withoutVatAmount);
    });
    $('.finanical_amount').on("input", function () {
        var finanical_amount= $('.finanical_amount').val();
        if(finanical_amount != null ){
            $('.bank_commission').removeAttr('disabled');
            $('.customer_commission').removeAttr('disabled');
        }
    });

</script>
