<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'deal_id',
        'finance_amount',
        'bank_commission',
        'customer_commission',
        'with_vat_commission',
        'without_vat_commission',
        "hodsale",
        "hodsalecommission",
        "ts_hod",
        "ts_hod_commision",
        "salemanager",
        "salemanagercommission",
        "coordinator",
        "coordinator_commission",
        "team_leader",
        "team_leader_commission",
        "salesagent",
        "salesagent_commission",
        "team_leader_one",
        "team_leader_one_commission",
        "sale_agent_one",
        "sale_agent_one_commission",
        "salemanagerref",
        "salemanagerrefcommission",
        "agentref",
        "agent_commission",
        "ts_team_leader",
        "ts_team_leader_commission",
        "tsagent",
        "tsagent_commission",
        "marketingmanager",
        "marketingmanagercommission",
        "marketingagent",
        "marketingagentcommission",
        "marketingagentone",
        "marketingagentcommissionone",
        "marketingagenttwo",
        "marketingagentcommissiontwo",
        'other_name',
        'other_name_commission',
        'broker_name',
        'broker_name_commission',
    ];

    public function hodsaleCommission()
    {
        return $this->belongsTo(User::class ,'hodsale' ,'id');
    }
    public function tshodCommission()
    {
        return $this->belongsTo(User::class ,'ts_hod' ,'id');
    }
    public function salemanagerCommission()
    {
        return $this->belongsTo(User::class ,'salemanager' ,'id');
    }
    public function marketingmanagerCommission()
    {
        return $this->belongsTo(User::class ,'marketingmanager' ,'id');
    }
    public function salemanagerrefCommission()
    {
        return $this->belongsTo(User::class ,'salemanagerref' ,'id');
    }
    public function team_leaderoneCommission()
    {
        return $this->belongsTo(User::class ,'team_leader_one' ,'id');
    }
    public function team_leaderCommission()
    {
        return $this->belongsTo(User::class ,'team_leader' ,'id');
    }
    public function ts_team_leaderCommission()
    {
        return $this->belongsTo(User::class ,'ts_team_leader' ,'id');
    }
    public function coordinatorCommission()
    {
        return $this->belongsTo(User::class ,'coordinator' ,'id');
    }
    public function agentrefCommission()
    {
        return $this->belongsTo(User::class ,'agentref' ,'id');
    }
    public function tsagentCommission()
    {
        return $this->belongsTo(User::class ,'tsagent' ,'id');
    }
    public function marketingagentCommission()
    {
        return $this->belongsTo(User::class ,'marketingagent' ,'id');
    }
    public function marketingagentCommissionone()
    {
        return $this->belongsTo(User::class ,'marketingagentone' ,'id');
    }
    public function marketingagentCommissiontwo()
    {
        return $this->belongsTo(User::class ,'marketingagenttwo' ,'id');
    }
    public function salesagentCommission()
    {
        return $this->belongsTo(User::class ,'salesagent' ,'id');
    }
    public function salesagentoneCommission()
    {
        return $this->belongsTo(User::class ,'sale_agent_one' ,'id');
    }
    public function other_name()
    {
        return $this->belongsTo(User::class ,'other_name' ,'id');
    }
}
