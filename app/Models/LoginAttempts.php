<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempts extends Model
{
    use HasFactory;
    protected $fillable = [
        'ip_address',
        'email',
        'password',
        'login_attempt_date',
    ];

}
