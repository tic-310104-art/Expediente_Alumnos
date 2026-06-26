<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    protected $table = 'log_activities';

    protected $fillable = [
        'subject', 'details', 'url', 'method', 'ip', 'agent', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
