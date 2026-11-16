<?php

namespace App\Services;

use App\Models\MangAawit;
use App\Models\Nagsasanay;

class MemberReportService
{
    public function getData(): array
    {
        return [
            'mangAawitList'  => $this->getMangAawitList(),
            'nagsasanayList' => $this->getNagsasanayList(),
            'stats'          => [
                'mangAawit'  => $this->getMangAawitStats(),
                'nagsasanay' => $this->getNagsasanayStats(),
            ],
        ];
    }

    protected function getMangAawitList()
    {
        return MangAawit::whereNull('deleted_at')
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular'))
            ->with(['currentMembership', 'purokGrupo'])
            ->orderBy('last_name')
            ->get()
            ->map(fn (MangAawit $m) => [
                'last_name'    => $m->last_name,
                'first_name'   => $m->first_name,
                'birthday'     => $m->birthday?->format('M d, Y'),
                'purok_grupo'  => $m->purokGrupo?->name ?? 'N/A',
                'tungkulin'    => $m->currentMembership?->tungkulin ?? '—',
                'status'       => $m->currentMembership?->tungkulin_status ?? '—',
            ]);
    }

    protected function getNagsasanayList()
    {
        return Nagsasanay::whereNull('deleted_at')
            ->with(['mangAawit.purokGrupo', 'purokGrupo'])
            ->orderBy('last_name')
            ->get()
            ->map(function (Nagsasanay $n) {
                $isOrganista = $n->training_track === 'Organista';

                return [
                    'last_name'      => $isOrganista ? ($n->mangAawit?->last_name ?? '—') : $n->last_name,
                    'first_name'     => $isOrganista ? ($n->mangAawit?->first_name ?? '—') : $n->first_name,
                    'birthday'       => $isOrganista
                        ? $n->mangAawit?->birthday?->format('M d, Y')
                        : $n->birthday?->format('M d, Y'),
                    'purok_grupo'    => $isOrganista
                        ? ($n->mangAawit?->purokGrupo?->name ?? 'N/A')
                        : ($n->purokGrupo?->name ?? 'N/A'),
                    'track'          => $n->training_track,
                    'progress_stage' => $n->progress_stage,
                    'status'         => $n->training_status,
                ];
            });
    }

    protected function getMangAawitStats(): array
    {
        $base = fn () => MangAawit::whereNull('deleted_at')
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular'));

        return [
            'total'          => $base()->count(),
            'totalMale'      => $base()->where('sex', 'Male')->count(),
            'totalFemale'    => $base()->where('sex', 'Female')->count(),
            'active'         => $base()->whereHas('currentMembership', fn ($q) => $q->where('tungkulin_status', 'Active'))->count(),
            'activeMale'     => $base()->whereHas('currentMembership', fn ($q) => $q->where('tungkulin_status', 'Active'))->where('sex', 'Male')->count(),
            'activeFemale'   => $base()->whereHas('currentMembership', fn ($q) => $q->where('tungkulin_status', 'Active'))->where('sex', 'Female')->count(),
            'inactive'       => $base()->whereHas('currentMembership', fn ($q) => $q->where('tungkulin_status', 'Inactive'))->count(),
            'inactiveMale'   => $base()->whereHas('currentMembership', fn ($q) => $q->where('tungkulin_status', 'Inactive'))->where('sex', 'Male')->count(),
            'inactiveFemale' => $base()->whereHas('currentMembership', fn ($q) => $q->where('tungkulin_status', 'Inactive'))->where('sex', 'Female')->count(),
        ];
    }

    protected function getNagsasanayStats(): array
    {
        return [
            'total'           => Nagsasanay::whereNull('deleted_at')->count(),
            'mangAawitMale'   => Nagsasanay::whereNull('deleted_at')->where('training_track', 'Mang-aawit')->where('sex', 'Male')->count(),
            'mangAawitFemale' => Nagsasanay::whereNull('deleted_at')->where('training_track', 'Mang-aawit')->where('sex', 'Female')->count(),
            'organistaMale'   => Nagsasanay::whereNull('deleted_at')->where('training_track', 'Organista')->whereHas('mangAawit', fn ($q) => $q->where('sex', 'Male'))->count(),
            'organistaFemale' => Nagsasanay::whereNull('deleted_at')->where('training_track', 'Organista')->whereHas('mangAawit', fn ($q) => $q->where('sex', 'Female'))->count(),
        ];
    }
}