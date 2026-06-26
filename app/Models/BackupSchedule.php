<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupSchedule extends Model
{
    protected $table = 'backup_schedules';

    protected $fillable = [
        'scheduled_date',
        'scheduled_time',
        'frequency',
        'is_active',
        'last_run_at'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime'
    ];
}
