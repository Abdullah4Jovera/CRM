<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealEstate extends Model
{
    use HasFactory;

    protected $fillable =[
        'lead_id',
        'locationChoice',
        'propertyPurpose',
        'propertyType',
        'priceRange',
        'propertyTypeSale',
        'bedrooms',
        'notes',
    ];
}
