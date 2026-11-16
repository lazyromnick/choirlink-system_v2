<?php

namespace App\Filament\Widgets;

use App\Models\Nagsasanay;
use Filament\Widgets\Widget;

class TotalNagsasanayWidget extends Widget
{
    protected static string $view = 'filament.widgets.total-nagsasanay-widget';
    protected static ?int $sort = 2;

    protected function getViewData(): array
    {
        return [
            'total' => Nagsasanay::whereNull('deleted_at')->count(),

            'mangAawitMale'   => Nagsasanay::whereNull('deleted_at')->where('training_track', 'Mang-aawit')->where('sex', 'Male')->count(),
            'mangAawitFemale' => Nagsasanay::whereNull('deleted_at')->where('training_track', 'Mang-aawit')->where('sex', 'Female')->count(),

            'organistaMale'   => Nagsasanay::whereNull('deleted_at')->where('training_track', 'Organista')->whereHas('mangAawit', fn ($q) => $q->where('sex', 'Male'))->count(),
            'organistaFemale' => Nagsasanay::whereNull('deleted_at')->where('training_track', 'Organista')->whereHas('mangAawit', fn ($q) => $q->where('sex', 'Female'))->count(),
        ];
    }
}