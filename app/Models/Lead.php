<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'client_id',
        'is_transfer',
        'pipeline_id',
        'lead_type',
        'stage_id',
        'sources',
        'products',
        'notes',
        'labels',
        'priority',
        'order',
        'created_by',
        'is_active',
        'is_reject',
        'is_converted',
        'date',
    ];

    public function labels()
    {
        if($this->labels)
        {
            return Label::whereIn('id', explode(',', $this->labels))->get();
        }

        return false;
    }

    public function stage()
    {
        return $this->hasOne('App\Models\LeadStage', 'id', 'stage_id');
    }
    public function dealstage()
    {
        return $this->hasOne('App\Models\Stage', 'id', 'deal_stage_id');
    }
    public function leadType()
    {
        return $this->hasOne('App\Models\LeadType', 'id', 'lead_type');
    }

    public function personalLoan()
    {
        return $this->hasOne(PersonalLoan::class ,'lead_id','id');
    }
    public function mortgageLoan()
    {
        return $this->hasOne(MortgageLoan::class ,'lead_id','id');
    }
    public function businessBanking()
    {
        return $this->hasOne(BusinessBanking::class ,'lead_id','id');
    }
    public function realEstate()
    {
        return $this->hasOne(RealEstate::class ,'lead_id','id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function files()
    {
        return $this->hasMany('App\Models\LeadFile', 'lead_id', 'id');
    }

    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class, 'pipeline_id');
    }

    public function product()
    {
        // if($this->products)
        // {
        //     return ProductService::whereIn('id', explode(',', $this->products))->get();
        // }

        // return [];
        return $this->hasOne('App\Models\ProductService', 'id', 'products');
    }

    public function source()
    {
        // if($this->sources)
        // {
        //     return Source::whereIn('id', explode(',', $this->sources))->get();
        // }

        // return [];
        return $this->hasOne('App\Models\Source', 'id', 'sources');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_leads', 'lead_id', 'user_id');
    }

    public function activities()
    {
        return $this->hasMany('App\Models\LeadActivityLog', 'lead_id', 'id')->orderBy('id', 'desc');
    }

    public function discussions()
    {
        return $this->hasMany('App\Models\LeadDiscussion', 'lead_id', 'id')->orderBy('id', 'desc');
    }

    public function calls()
    {
        return $this->hasMany('App\Models\LeadCall', 'lead_id', 'id');
    }

    public function emails()
    {
        return $this->hasMany('App\Models\LeadEmail', 'lead_id', 'id')->orderByDesc('id');
    }
}
