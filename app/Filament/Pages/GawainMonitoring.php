<?php

namespace App\Filament\Pages;

use App\Models\GawainMonitoringMangAawitAttendance;
use App\Models\GawainMonitoringNagsasanayAttendance;
use App\Models\GawainMonitoringWeek;
use App\Models\MangAawit;
use App\Models\Nagsasanay;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class GawainMonitoring extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Gawain Monitoring';
    protected static ?string $title = 'Gawain Monitoring';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.gawain-monitoring';

    protected static string $layout = 'filament-panels::components.layout.index';

    public int $month;
    public int $year;

    public array $mangAawitAttendanceMap = [];
    public array $nagsasanayAttendanceMap = [];

    public array $pendingMangAawit = [];
    public array $pendingNagsasanay = [];

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year  = now()->year;
        $this->loadMonth();
    }

    protected function loadMonth(): void
    {
        GawainMonitoringWeek::generateForMonth($this->month, $this->year);
        $this->loadAttendance();
    }

    public function getWeeks()
    {
        return GawainMonitoringWeek::where('month', $this->month)
            ->where('year', $this->year)
            ->orderBy('start_date')
            ->get();
    }

    public function getMangAawitList()
    {
        return MangAawit::whereNull('deleted_at')
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular'))
            ->orderBy('last_name')
            ->get();
    }

    public function getNagsasanayList()
    {
        return Nagsasanay::whereNull('deleted_at')
            ->whereNotIn('progress_stage', ['Discontinued', 'Completed'])
            ->get();
    }

    protected function loadAttendance(): void
    {
        $weekIds = GawainMonitoringWeek::where('month', $this->month)
            ->where('year', $this->year)
            ->pluck('id');

        $mangAawitRecords = GawainMonitoringMangAawitAttendance::whereIn('week_id', $weekIds)->get();
        $this->mangAawitAttendanceMap = [];
        foreach ($mangAawitRecords as $record) {
            $this->mangAawitAttendanceMap[$record->mang_aawit_id][$record->week_id] = (bool) $record->is_present;
        }
        $this->pendingMangAawit = $this->mangAawitAttendanceMap;

        $nagsasanayRecords = GawainMonitoringNagsasanayAttendance::whereIn('week_id', $weekIds)->get();
        $this->nagsasanayAttendanceMap = [];
        foreach ($nagsasanayRecords as $record) {
            $this->nagsasanayAttendanceMap[$record->nagsasanay_id][$record->week_id] = (bool) $record->is_present;
        }
        $this->pendingNagsasanay = $this->nagsasanayAttendanceMap;
    }

    public function isMangAawitPresent(int $mangAawitId, int $weekId): bool
    {
        return $this->mangAawitAttendanceMap[$mangAawitId][$weekId] ?? false;
    }

    public function isNagsasanayPresent(int $nagsasanayId, int $weekId): bool
    {
        return $this->nagsasanayAttendanceMap[$nagsasanayId][$weekId] ?? false;
    }

    public function getMangAawitTotal(int $mangAawitId): int
    {
        $totals = $this->mangAawitAttendanceMap[$mangAawitId] ?? [];
        return count(array_filter($totals, fn ($v) => $v === true));
    }

    public function getNagsasanayTotal(int $nagsasanayId): int
    {
        $totals = $this->nagsasanayAttendanceMap[$nagsasanayId] ?? [];
        return count(array_filter($totals, fn ($v) => $v === true));
    }

    public function recordAttendance(): void
    {
        $changes = 0;

        DB::transaction(function () use (&$changes) {
            foreach ($this->pendingMangAawit as $mangAawitId => $weeks) {
                foreach ($weeks as $weekId => $value) {
                    $value = (bool) $value;
                    $saved = $this->mangAawitAttendanceMap[$mangAawitId][$weekId] ?? false;

                    if ($value === $saved) {
                        continue;
                    }

                    GawainMonitoringMangAawitAttendance::updateOrCreate(
                        ['mang_aawit_id' => $mangAawitId, 'week_id' => $weekId],
                        ['is_present' => $value]
                    );
                    $changes++;
                }
            }

            foreach ($this->pendingNagsasanay as $nagsasanayId => $weeks) {
                foreach ($weeks as $weekId => $value) {
                    $value = (bool) $value;
                    $saved = $this->nagsasanayAttendanceMap[$nagsasanayId][$weekId] ?? false;

                    if ($value === $saved) {
                        continue;
                    }

                    GawainMonitoringNagsasanayAttendance::updateOrCreate(
                        ['nagsasanay_id' => $nagsasanayId, 'week_id' => $weekId],
                        ['is_present' => $value]
                    );
                    $changes++;
                }
            }
        });

        $this->loadAttendance();

        Notification::make()
            ->title($changes > 0 ? "Attendance recorded ({$changes} change/s)." : 'No changes to record.')
            ->success()
            ->send();
    }

    public function previousMonth(): void
    {
        $date = \Carbon\Carbon::create($this->year, $this->month)->subMonth();
        $this->month = $date->month;
        $this->year  = $date->year;
        $this->loadMonth();
    }

    public function nextMonth(): void
    {
        $date = \Carbon\Carbon::create($this->year, $this->month)->addMonth();
        $this->month = $date->month;
        $this->year  = $date->year;
        $this->loadMonth();
    }

    public function goToCurrentMonth(): void
    {
        $this->month = now()->month;
        $this->year  = now()->year;
        $this->loadMonth();
    }

    protected function getViewData(): array
    {
        return [
            'weeks'          => $this->getWeeks(),
            'mangAawitList'  => $this->getMangAawitList(),
            'nagsasanayList' => $this->getNagsasanayList(),
        ];
    }

    public function getLayout(): string
    {
        return 'filament-panels::components.layout.index';
    }
}