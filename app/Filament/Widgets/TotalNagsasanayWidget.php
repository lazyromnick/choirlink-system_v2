<?php

namespace App\Filament\Widgets;

use App\Models\Nagsasanay;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalNagsasanayWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $total = Nagsasanay::whereNull('deleted_at')->count();

        $mangAawitMale = Nagsasanay::whereNull('deleted_at')
            ->where('training_track', 'Mang-aawit')
            ->where('sex', 'Male')
            ->count();

        $mangAawitFemale = Nagsasanay::whereNull('deleted_at')
            ->where('training_track', 'Mang-aawit')
            ->where('sex', 'Female')
            ->count();

        $organistaMale = Nagsasanay::whereNull('deleted_at')
            ->where('training_track', 'Organista')
            ->whereHas('mangAawit', fn ($q) => $q->where('sex', 'Male'))
            ->count();

        $organistaFemale = Nagsasanay::whereNull('deleted_at')
            ->where('training_track', 'Organista')
            ->whereHas('mangAawit', fn ($q) => $q->where('sex', 'Female'))
            ->count();

        return [
            Stat::make('Total Nagsasanay', $total)
                ->description(
                    "Mang-aawit — Male: {$mangAawitMale} · Female: {$mangAawitFemale}" . "\n" .
                    "Organista — Male: {$organistaMale} · Female: {$organistaFemale}"
                )
                ->color('info'),
        ];
    }
}