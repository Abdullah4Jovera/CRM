<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotifications extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_id',
        'is_read',
    ];

    /**
     * Get the activity associated with the UserNotifications
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function activity()
    {
        return $this->belongsTo(ActivityLog::class,'activity_id','id');
    }

}
