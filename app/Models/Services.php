<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'service',
        'finance_amount',
        'bank_commission',
        'customer_commission',
        'with_vat_commission',
        'without_vat_commission',
        'term',
        'b_type',
        'plot_no',
        'sector',
        'emirate',
        'description',
    ];

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }
    public function commissions()
    {
        return $this->belongsTo(ServiceCommission::class,'id','service_id');
    }
}
