<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NagsasanayMonitoringAttendance extends Model
{
    use HasFactory;

    protected $table = 'nagsasanay_monitoring_attendance';

    protected $fillable = [
        'session_id',
        'nagsasanay_id',
        'is_present',
    ];

    protected $casts = [
        'is_present' => 'boolean',
    ];

    public function session()
    {
        return $this->belongsTo(NagsasanayMonitoringSession::class, 'session_id');
    }

    public function nagsasanay()
    {
        return $this->belongsTo(Nagsasanay::class, 'nagsasanay_id');
    }
}