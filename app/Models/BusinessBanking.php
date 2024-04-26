<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessBanking extends Model
{
    use HasFactory;

    protected $fillable = [
        "lead_id",
        "business_banking_services",
        "company_name",
        "yearly_turnover",
        "have_any_pos",
        "monthly_amount",
        "have_auto_finance",
        "monthly_emi",
        "lgcs",
        "notes",
    ];
}
