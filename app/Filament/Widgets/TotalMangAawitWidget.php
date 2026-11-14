<?php

namespace App\Filament\Widgets;

use App\Models\MangAawit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalMangAawitWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = MangAawit::whereNull('deleted_at')
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular'))
            ->count();

        $totalMale = MangAawit::whereNull('deleted_at')
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular'))
            ->where('sex', 'Male')
            ->count();

        $totalFemale = MangAawit::whereNull('deleted_at')
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular'))
            ->where('sex', 'Female')
            ->count();

        $active = MangAawit::whereNull('deleted_at')
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular')->where('tungkulin_status', 'Active'))
            ->count();

        $activeMale = MangAawit::whereNull('deleted_at')
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular')->where('tungkulin_status', 'Active'))
            ->where('sex', 'Male')
            ->count();

        $activeFemale = MangAawit::whereNull('deleted_at')
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular')->where('tungkulin_status', 'Active'))
            ->where('sex', 'Female')
            ->count();

        $inactive = MangAawit::whereNull('deleted_at')
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular')->where('tungkulin_status', 'Inactive'))
            ->count();

        $inactiveMale = MangAawit::whereNull('deleted_at')
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular')->where('tungkulin_status', 'Inactive'))
            ->where('sex', 'Male')
            ->count();

        $inactiveFemale = MangAawit::whereNull('deleted_at')
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular')->where('tungkulin_status', 'Inactive'))
            ->where('sex', 'Female')
            ->count();

        return [
            Stat::make('Total Mang-aawit', $total)
                ->description("Male: {$totalMale} · Female: {$totalFemale}")
                ->color('primary'),

            Stat::make('Active Mang-aawit', $active)
                ->description("Male: {$activeMale} · Female: {$activeFemale}")
                ->color('success'),

            Stat::make('Inactive Mang-aawit', $inactive)
                ->description("Male: {$inactiveMale} · Female: {$inactiveFemale}")
                ->color('danger'),
        ];
    }
}