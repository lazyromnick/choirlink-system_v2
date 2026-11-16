<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class GawainMonitoringWeek extends Model
{
    protected $table = 'gawain_monitoring_weeks';

    protected $fillable = [
        'week_number',
        'month',
        'year',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function mangAawitAttendance()
    {
        return $this->hasMany(GawainMonitoringMangAawitAttendance::class, 'week_id');
    }

    public function nagsasanayAttendance()
    {
        return $this->hasMany(GawainMonitoringNagsasanayAttendance::class, 'week_id');
    }

    /**
     * Generate (if missing) every week that belongs to the given month,
     * using the "4-or-more-days" majority rule: a Mon–Sun week belongs
     * to whichever month holds at least 4 of its 7 days.
     */
    public static function generateForMonth(int $month, int $year): void
    {
        $firstOfMonth = Carbon::create($year, $month, 1);
        $lastOfMonth  = $firstOfMonth->copy()->endOfMonth();

        $cursor = $firstOfMonth->copy()->startOfWeek(Carbon::MONDAY);

        while ($cursor->lte($lastOfMonth)) {
            $weekStart = $cursor->copy();
            $weekEnd   = $cursor->copy()->addDays(6);

            $daysInMonth = 0;
            for ($d = $weekStart->copy(); $d->lte($weekEnd); $d->addDay()) {
                if ($d->month === $month && $d->year === $year) {
                    $daysInMonth++;
                }
            }

            if ($daysInMonth >= 4) {
                static::firstOrCreate(
                    ['start_date' => $weekStart->toDateString()],
                    [
                        'end_date'    => $weekEnd->toDateString(),
                        'week_number' => $weekStart->isoWeek(),
                        'month'       => $month,
                        'year'        => $year,
                    ]
                );
            }

            $cursor->addWeek();
        }
    }
}