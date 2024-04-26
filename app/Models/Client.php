<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'email',
        'phone',
        'e_id',
        'address',
        'nationality',
        'language',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
    public function client_deals()
    {
        return $this->hasMany(Deal::class);
    }
}
