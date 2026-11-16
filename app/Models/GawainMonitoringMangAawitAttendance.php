<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GawainMonitoringMangAawitAttendance extends Model
{
    protected $table = 'gawain_monitoring_mang_aawit_attendance';

    protected $fillable = ['week_id', 'mang_aawit_id', 'is_present'];

    protected $casts = ['is_present' => 'boolean'];

    public function week()
    {
        return $this->belongsTo(GawainMonitoringWeek::class, 'week_id');
    }

    public function mangAawit()
    {
        return $this->belongsTo(MangAawit::class, 'mang_aawit_id');
    }
}