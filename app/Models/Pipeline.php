<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pipeline extends Model
{
    protected $fillable = [
        'name',
        'created_by',
    ];

    public function stages()
    {
        return $this->belongsToMany(Stage::class);
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class);
    }

    public function leadStages()
    {
        return $this->hasMany('App\Models\LeadStage', 'pipeline_id', 'id')->where('created_by', '=', \Auth::user()->ownerId())->orderBy('order');
    }
}
