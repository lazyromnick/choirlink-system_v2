<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NagsasanayMonitoringSession extends Model
{
    use HasFactory;

    protected $table = 'nagsasanay_monitoring_sessions';

    protected $fillable = [
        'session_date',
        'month',
        'year',
        'created_by',
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function attendance()
    {
        return $this->hasMany(NagsasanayMonitoringAttendance::class, 'session_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}