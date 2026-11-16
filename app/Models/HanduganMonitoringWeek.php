<?php

namespace App\Models;

use App\Models\MangAawit;
use App\Models\Nagsasanay;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class HanduganMonitoringWeek extends Model
{
    protected $table = 'handugan_monitoring_weeks';

    protected $fillable = [
        'week_number',
        'month',
        'year',
        'start_date',
        'end_date',
        'denominator_snapshot',
        'th_count',
        'lingap_count',
        'lagak_count',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function thPercentage(): int
    {
        return $this->percentage($this->th_count);
    }

    public function lingapPercentage(): int
    {
        return $this->percentage($this->lingap_count);
    }

    public function lagakPercentage(): int
    {
        return $this->percentage($this->lagak_count);
    }

    protected function percentage(?int $count): int
    {
        if ($count === null || $this->denominator_snapshot === 0) {
            return 0;
        }

        return (int) round(($count / $this->denominator_snapshot) * 100);
    }

    public static function currentDenominator(): int
    {
        $mangAawitCount = MangAawit::whereNull('deleted_at')
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular'))
            ->count();

        $nagsasanayMangAawitCount = Nagsasanay::whereNull('deleted_at')
            ->where('training_track', 'Mang-aawit')
            ->whereNotIn('progress_stage', ['Discontinued', 'Completed'])
            ->count();

        return $mangAawitCount + $nagsasanayMangAawitCount;
    }

    /**
     * Generate (if missing) every week that belongs to the given month,
     * using the same 4-or-more-days majority rule as Gawain Monitoring.
     * The denominator is snapshotted at the moment each week is created.
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
                        'end_date'             => $weekEnd->toDateString(),
                        'week_number'          => $weekStart->isoWeek(),
                        'month'                => $month,
                        'year'                 => $year,
                        'denominator_snapshot' => static::currentDenominator(),
                    ]
                );
            }

            $cursor->addWeek();
        }
    }
}