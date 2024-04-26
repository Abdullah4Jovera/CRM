<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        "lead_id" ,
        "company_name" ,
        "monthly_salary" ,
        "load_amount" ,
        "have_any_loan" ,
        "taken_loan_amount" ,
        "notes" ,
    ];

    /**
     * Get the user that owns the PersonalLoan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
