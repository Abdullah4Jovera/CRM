<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Client;

class UniquePhone implements Rule
{
    public function passes($attribute, $value)
    {
        // Check if the phone number is unique in the database
        return !Client::where('phone', $value)->exists();
    }

    public function message()
    {
        return 'The phone number has already been taken.';
    }
}
