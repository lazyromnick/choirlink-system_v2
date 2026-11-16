<?php

namespace App\Filament\Pages;

use App\Models\HanduganMonitoringWeek;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class HanduganMonitoring extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Handugan Monitoring';
    protected static ?string $title = 'Handugan Monitoring';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.pages.handugan-monitoring';

    protected static string $layout = 'filament-panels::components.layout.index';

    public int $month;
    public int $year;

    public array $pending = [];

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year  = now()->year;
        $this->loadMonth();
    }

    protected function loadMonth(): void
    {
        HanduganMonitoringWeek::generateForMonth($this->month, $this->year);
        $this->loadPending();
    }

    public function getWeeks()
    {
        return HanduganMonitoringWeek::where('month', $this->month)
            ->where('year', $this->year)
            ->orderBy('start_date')
            ->get();
    }

    protected function loadPending(): void
    {
        $this->pending = [];

        foreach ($this->getWeeks() as $week) {
            $this->pending[$week->id] = [
                'th_count'     => $week->th_count,
                'lingap_count' => $week->lingap_count,
                'lagak_count'  => $week->lagak_count,
            ];
        }
    }

    public function recordHandugan(): void
    {
        $changes = 0;

        foreach ($this->pending as $weekId => $counts) {
            $week = HanduganMonitoringWeek::find($weekId);

            if (!$week) {
                continue;
            }

            $th     = $counts['th_count'] !== '' ? $counts['th_count'] : null;
            $lingap = $counts['lingap_count'] !== '' ? $counts['lingap_count'] : null;
            $lagak  = $counts['lagak_count'] !== '' ? $counts['lagak_count'] : null;

            if ($week->th_count == $th && $week->lingap_count == $lingap && $week->lagak_count == $lagak) {
                continue;
            }

            $week->update([
                'th_count'     => $th,
                'lingap_count' => $lingap,
                'lagak_count'  => $lagak,
            ]);

            $changes++;
        }

        $this->loadPending();

        Notification::make()
            ->title($changes > 0 ? "Handugan recorded ({$changes} week(s) updated)." : 'No changes to record.')
            ->success()
            ->send();
    }

    public function getMonthlyAverages(): array
    {
        $weeks = $this->getWeeks();

        if ($weeks->isEmpty()) {
            return ['th' => 0, 'lingap' => 0, 'lagak' => 0];
        }

        return [
            'th'     => (int) round($weeks->avg(fn ($w) => $w->thPercentage())),
            'lingap' => (int) round($weeks->avg(fn ($w) => $w->lingapPercentage())),
            'lagak'  => (int) round($weeks->avg(fn ($w) => $w->lagakPercentage())),
        ];
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
            'weeks'            => $this->getWeeks(),
            'monthlyAverages'  => $this->getMonthlyAverages(),
        ];
    }

    public function getLayout(): string
    {
        return 'filament-panels::components.layout.index';
    }

    public function refreshDenominator(int $weekId): void
    {
        $week = HanduganMonitoringWeek::find($weekId);

        if (!$week) {
            return;
        }

        $oldDenominator = $week->denominator_snapshot;
        $newDenominator = HanduganMonitoringWeek::currentDenominator();

        $week->update(['denominator_snapshot' => $newDenominator]);

        \Filament\Notifications\Notification::make()
            ->title('Denominator refreshed')
            ->body("Week {$week->week_number}: {$oldDenominator} → {$newDenominator}. Percentages for this week have been recalculated.")
            ->success()
            ->send();
    }

    public function refreshAllDenominators(): void
    {
        $newDenominator = HanduganMonitoringWeek::currentDenominator();
        $weeks = $this->getWeeks();

        $changed = 0;

        foreach ($weeks as $week) {
            if ($week->denominator_snapshot !== $newDenominator) {
                $week->update(['denominator_snapshot' => $newDenominator]);
                $changed++;
            }
        }

        \Filament\Notifications\Notification::make()
            ->title($changed > 0
                ? "Refreshed {$changed} week(s) to the current denominator ({$newDenominator})."
                : 'All weeks already match the current denominator.')
            ->success()
            ->send();
    }
}