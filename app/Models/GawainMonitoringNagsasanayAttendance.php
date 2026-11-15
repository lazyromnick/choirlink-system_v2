<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GawainMonitoringNagsasanayAttendance extends Model
{
    protected $table = 'gawain_monitoring_nagsasanay_attendance';

    protected $fillable = ['week_id', 'nagsasanay_id', 'is_present'];

    protected $casts = ['is_present' => 'boolean'];

    public function week()
    {
        return $this->belongsTo(GawainMonitoringWeek::class, 'week_id');
    }

    public function nagsasanay()
    {
        return $this->belongsTo(Nagsasanay::class, 'nagsasanay_id');
    }
}