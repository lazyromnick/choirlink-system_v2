<?php

namespace App\Filament\Widgets;

use App\Models\GawainMonitoringMangAawitAttendance;
use App\Models\GawainMonitoringNagsasanayAttendance;
use App\Models\GawainMonitoringWeek;
use App\Models\MangAawit;
use App\Models\Nagsasanay;
use Filament\Widgets\Widget;

class GawainAttendancePercentageWidget extends Widget
{
    protected static string $view = 'filament.widgets.gawain-attendance-percentage-widget';
    protected static ?int $sort = 3;

    protected function getViewData(): array
    {
        $month = now()->month;
        $year  = now()->year;

        $weekIds   = GawainMonitoringWeek::where('month', $month)->where('year', $year)->pluck('id');
        $weekCount = $weekIds->count();

        $mangAawitTotalCount  = MangAawit::whereNull('deleted_at')->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular'))->count();
        $mangAawitMaleCount   = MangAawit::whereNull('deleted_at')->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular'))->where('sex', 'Male')->count();
        $mangAawitFemaleCount = MangAawit::whereNull('deleted_at')->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular'))->where('sex', 'Female')->count();

        $mangAawitPresentTotal  = GawainMonitoringMangAawitAttendance::whereIn('week_id', $weekIds)->where('is_present', true)->count();
        $mangAawitPresentMale   = GawainMonitoringMangAawitAttendance::whereIn('week_id', $weekIds)->where('is_present', true)->whereHas('mangAawit', fn ($q) => $q->where('sex', 'Male'))->count();
        $mangAawitPresentFemale = GawainMonitoringMangAawitAttendance::whereIn('week_id', $weekIds)->where('is_present', true)->whereHas('mangAawit', fn ($q) => $q->where('sex', 'Female'))->count();

        $nagsasanayTotalCount = Nagsasanay::whereNull('deleted_at')->whereNotIn('progress_stage', ['Discontinued', 'Completed'])->count();
        $nagsasanayPresentTotal = GawainMonitoringNagsasanayAttendance::whereIn('week_id', $weekIds)->where('is_present', true)->count();

        $pct = fn (int $present, int $people) => $weekCount * $people === 0 ? 0 : (int) round(($present / ($weekCount * $people)) * 100);

        return [
            'weekCount'          => $weekCount,
            'monthLabel'         => now()->format('F Y'),

            'mangAawitPct'       => $pct($mangAawitPresentTotal, $mangAawitTotalCount),
            'mangAawitMalePct'   => $pct($mangAawitPresentMale, $mangAawitMaleCount),
            'mangAawitFemalePct' => $pct($mangAawitPresentFemale, $mangAawitFemaleCount),

            'nagsasanayPct'      => $pct($nagsasanayPresentTotal, $nagsasanayTotalCount),
        ];
    }

    public function colorFor(int $percentage): string
    {
        return match (true) {
            $percentage >= 80 => 'success',
            $percentage >= 50 => 'warning',
            default            => 'danger',
        };
    }
}