<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MortgageLoan extends Model
{
    use HasFactory;

    protected $fillable =[
        "lead_id",
        "type_of_property",
        "location",
        "monthly_income",
        "have_any_other_loan",
        "loanAmount",
        "notes",
    ];
}
