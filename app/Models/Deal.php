<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = [
        'is_transfer',
        'client_id',
        'lead_type',
        'sources',
        'products',
        'labels',
        'created_by',
        'date',
        'pipeline_id',
        'stage_id',
        'group_id',
        'created_by',
        'permissions',
        'status',
        'is_active',
    ];

    public static $permissions = [
        'Client View Tasks',
        'Client View Products',
        'Client View Sources',
        'Client View Contacts',
        'Client View Files',
        'Client View Invoices',
        'Client View Custom fields',
        'Client View Members',
        'Client Add File',
        'Client Deal Activity',
    ];

    public static $statues = [
        'Active' => 'Active',
        'Won' => 'Won',
        'Loss' => 'Loss',
    ];

    public $customField;

    public function labels()
    {
        if($this->labels)
        {
            return Label::whereIn('id', explode(',', $this->labels))->get();
        }

        return false;
    }

    public function pipeline()
    {
        return $this->hasOne('App\Models\Pipeline', 'id', 'pipeline_id');
    }

    public function stage()
    {
        return $this->hasOne('App\Models\Stage', 'id', 'stage_id');
    }

    public function leads()
    {
        return $this->hasOne('App\Models\Lead', 'is_converted', 'id');
    }

    public function leadType()
    {
        return $this->hasOne('App\Models\LeadType', 'id', 'lead_type');
    }

    public function serviceCommission()
    {
        return $this->hasOne('App\Models\ServiceCommission', 'deal_id', 'id');
    }
    public function comments()
    {
        return $this->hasMany('App\Models\ContractComment', 'contract_id', 'id');
    }

    public function contractAttachment()
    {
        return $this->hasOne('App\Models\Contract_attachment', 'deal_id', 'id');
    }


    public function group()
    {
        return $this->hasOne('App\Models\Group', 'id', 'group_id');
    }

    public function clients()
    {
        return $this->belongsToMany('App\Models\User', 'client_deals', 'deal_id', 'client_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_deals', 'deal_id', 'user_id');
    }

    public function client()
    {
        return $this->hasOne('App\Models\Client', 'id', 'client_id');
    }
    public function service()
    {
        return $this->hasOne('App\Models\Services', 'id', 'service_id');
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

    public function sources()
    {
        if($this->sources)
        {
            return Source::whereIn('id', explode(',', $this->sources))->get();
        }

        return [];
    }

    public function files()
    {
        return $this->hasMany('App\Models\DealFile', 'deal_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\DealTask', 'deal_id', 'id');
    }

    public function complete_tasks()
    {
        return $this->hasMany('App\Models\DealTask', 'deal_id', 'id')->where('status', '=', 1);
    }

    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice', 'deal_id', 'id');
    }

    public function calls()
    {
        return $this->hasMany('App\Models\DealCall', 'deal_id', 'id');
    }

    public function emails()
    {
        return $this->hasMany('App\Models\DealEmail', 'deal_id', 'id')->orderByDesc('id');
    }

    public function activities()
    {
        return $this->hasMany('App\Models\ActivityLog', 'deal_id', 'id')->orderBy('id', 'desc');
    }

    public function discussions()
    {
        return $this->hasMany('App\Models\DealDiscussion', 'deal_id', 'id')->orderBy('id', 'desc');
    }

    public static function getDealSummary($deals)
    {
        $total = 0;

        foreach($deals as $deal)
        {
            $total += $deal->price;
        }

        return \Auth::user()->priceFormat($total);
    }
}
