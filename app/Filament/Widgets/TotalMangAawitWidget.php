<?php

namespace App\Filament\Widgets;

use App\Models\MangAawit;
use Filament\Widgets\Widget;

class TotalMangAawitWidget extends Widget
{
    protected static string $view = 'filament.widgets.total-mang-aawit-widget';
    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        $base = fn () => MangAawit::whereNull('deleted_at')
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular'));

        return [
            'total'         => $base()->count(),
            'totalMale'     => $base()->where('sex', 'Male')->count(),
            'totalFemale'   => $base()->where('sex', 'Female')->count(),

            'active'        => $base()->whereHas('currentMembership', fn ($q) => $q->where('tungkulin_status', 'Active'))->count(),
            'activeMale'    => $base()->whereHas('currentMembership', fn ($q) => $q->where('tungkulin_status', 'Active'))->where('sex', 'Male')->count(),
            'activeFemale'  => $base()->whereHas('currentMembership', fn ($q) => $q->where('tungkulin_status', 'Active'))->where('sex', 'Female')->count(),

            'inactive'       => $base()->whereHas('currentMembership', fn ($q) => $q->where('tungkulin_status', 'Inactive'))->count(),
            'inactiveMale'   => $base()->whereHas('currentMembership', fn ($q) => $q->where('tungkulin_status', 'Inactive'))->where('sex', 'Male')->count(),
            'inactiveFemale' => $base()->whereHas('currentMembership', fn ($q) => $q->where('tungkulin_status', 'Inactive'))->where('sex', 'Female')->count(),
        ];
    }
}