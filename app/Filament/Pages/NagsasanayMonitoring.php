<?php

namespace App\Filament\Pages;

use App\Models\Nagsasanay;
use App\Models\NagsasanayMonitoringAttendance;
use App\Models\NagsasanayMonitoringSession;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NagsasanayMonitoring extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Nagsasanay Monitoring';
    protected static ?string $title = 'Nagsasanay Monitoring';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.nagsasanay-monitoring';

    protected static string $layout = 'filament-panels::components.layout.index';

    public int $month;
    public int $year;
    public ?string $newSessionDate = null;

    // Saved attendance (from database)
    public array $attendanceMap = [];

    // On-screen checkbox state, not saved until Record is clicked
    public array $pending = [];

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year  = now()->year;
        $this->loadAttendance();
    }

    public function getSessions()
    {
        return NagsasanayMonitoringSession::where('month', $this->month)
            ->where('year', $this->year)
            ->orderBy('session_date')
            ->get();
    }

    public function getMangAawitList()
    {
        return Nagsasanay::whereNull('deleted_at')
            ->where('training_track', 'Mang-aawit')
            ->where('progress_stage', '!=', 'Discontinued')
            ->orderBy('last_name')
            ->get();
    }

    public function getOrganistaList()
    {
        return Nagsasanay::whereNull('deleted_at')
            ->where('training_track', 'Organista')
            ->where('progress_stage', '!=', 'Discontinued')
            ->get();
    }

    protected function loadAttendance(): void
    {
        $sessionIds = NagsasanayMonitoringSession::where('month', $this->month)
            ->where('year', $this->year)
            ->pluck('id');

        $records = NagsasanayMonitoringAttendance::whereIn('session_id', $sessionIds)->get();

        $this->attendanceMap = [];
        foreach ($records as $record) {
            $this->attendanceMap[$record->nagsasanay_id][$record->session_id] = (bool) $record->is_present;
        }

        // Reset on-screen checkboxes to match what is saved
        $this->pending = $this->attendanceMap;
    }

    public function isPresent(int $nagsasanayId, int $sessionId): bool
    {
        return $this->attendanceMap[$nagsasanayId][$sessionId] ?? false;
    }

    // Total based on SAVED attendance (updates after Record is clicked)
    public function getTotal(int $nagsasanayId): int
    {
        $totals = $this->attendanceMap[$nagsasanayId] ?? [];
        return count(array_filter($totals, fn ($v) => $v === true));
    }

    public function recordAttendance(): void
    {
        $changes = 0;

        DB::transaction(function () use (&$changes) {
            foreach ($this->pending as $nagsasanayId => $sessions) {
                foreach ($sessions as $sessionId => $value) {
                    $value = (bool) $value;
                    $saved = $this->attendanceMap[$nagsasanayId][$sessionId] ?? false;

                    if ($value === $saved) {
                        continue;
                    }

                    NagsasanayMonitoringAttendance::updateOrCreate(
                        ['nagsasanay_id' => $nagsasanayId, 'session_id' => $sessionId],
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
        $this->loadAttendance();
    }

    public function nextMonth(): void
    {
        $date = \Carbon\Carbon::create($this->year, $this->month)->addMonth();
        $this->month = $date->month;
        $this->year  = $date->year;
        $this->loadAttendance();
    }

    public function goToCurrentMonth(): void
    {
        $this->month = now()->month;
        $this->year  = now()->year;
        $this->loadAttendance();
    }

    public function addSession(): void
    {
        $this->dispatch('open-modal', id: 'add-session-modal');
    }

    public function saveSession(): void
    {
        if (!$this->newSessionDate) {
            Notification::make()->title('Please select a date.')->danger()->send();
            return;
        }

        $date = \Carbon\Carbon::parse($this->newSessionDate);

        $exists = NagsasanayMonitoringSession::where('session_date', $date->toDateString())->exists();

        if ($exists) {
            Notification::make()->title('This date already exists.')->warning()->send();
            return;
        }

        NagsasanayMonitoringSession::create([
            'session_date' => $date->toDateString(),
            'month'        => $date->month,
            'year'         => $date->year,
            'created_by'   => Auth::id(),
        ]);

        $this->newSessionDate = null;
        $this->dispatch('close-modal', id: 'add-session-modal');
        $this->loadAttendance();

        Notification::make()->title('Session date added.')->success()->send();
    }

    public function removeSession(int $sessionId): void
    {
        NagsasanayMonitoringSession::findOrFail($sessionId)->delete();
        $this->loadAttendance();
        Notification::make()->title('Session date removed.')->success()->send();
    }

    public function getFormSchema(): array
    {
        return [];
    }

    protected function getViewData(): array
    {
        return [
            'sessions'      => $this->getSessions(),
            'mangAawitList' => $this->getMangAawitList(),
            'organistaList' => $this->getOrganistaList(),
        ];
    }

    public function getLayout(): string
    {
        return 'filament-panels::components.layout.index';
    }
}