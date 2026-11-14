<?php

namespace App\Filament\Pages;

use App\Models\Nagsasanay;
use App\Models\NagsasanayMonitoringAttendance;
use App\Models\NagsasanayMonitoringSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class NagsasanayMonitoring extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Monitoring';
    protected static ?string $title = 'Nagsasanay Monitoring';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.nagsasanay-monitoring';

    public int $month;
    public int $year;
    public ?string $newSessionDate = null;

    // Attendance cache
    public array $attendanceMap = [];

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year  = now()->year;
        $this->loadAttendance();
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

    #[Computed]
    public function sessions()
    {
        return NagsasanayMonitoringSession::where('month', $this->month)
            ->where('year', $this->year)
            ->orderBy('session_date')
            ->get();
    }

    #[Computed]
    public function mangAawitList()
    {
        return Nagsasanay::whereNull('deleted_at')
            ->where('training_track', 'Mang-aawit')
            ->where('progress_stage', '!=', 'Discontinued')
            ->orderBy('last_name')
            ->get();
    }

    #[Computed]
    public function organistaList()
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
            $this->attendanceMap[$record->nagsasanay_id][$record->session_id] = $record->is_present;
        }
    }

    public function isPresent(int $nagsasanayId, int $sessionId): bool
    {
        return $this->attendanceMap[$nagsasanayId][$sessionId] ?? false;
    }

    public function getTotal(int $nagsasanayId): int
    {
        $totals = $this->attendanceMap[$nagsasanayId] ?? [];
        return count(array_filter($totals, fn ($v) => $v === true));
    }

    public function toggleAttendance(int $nagsasanayId, int $sessionId): void
    {
        $current = $this->attendanceMap[$nagsasanayId][$sessionId] ?? false;

        NagsasanayMonitoringAttendance::updateOrCreate(
            ['nagsasanay_id' => $nagsasanayId, 'session_id' => $sessionId],
            ['is_present' => !$current]
        );

        $this->attendanceMap[$nagsasanayId][$sessionId] = !$current;
    }

    public function addSession(): void
    {
        $this->dispatch('open-modal', id: 'add-session-modal');
    }

    public function addSessionForm(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('newSessionDate')
                ->label('Session Date')
                ->required()
                ->default(now()),
        ]);
    }

    public function saveSession(): void
    {
        if (!$this->newSessionDate) {
            Notification::make()->title('Please select a date.')->danger()->send();
            return;
        }

        $date = \Carbon\Carbon::parse($this->newSessionDate);

        $exists = NagsasanayMonitoringSession::where('session_date', $date->toDateString())
            ->exists();

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
        unset($this->sessions);
    }

    public function removeSession(int $sessionId): void
    {
        NagsasanayMonitoringSession::findOrFail($sessionId)->delete();
        $this->loadAttendance();
        unset($this->sessions);

        Notification::make()->title('Session date removed.')->success()->send();
    }

    public function getFormSchema(): array
    {
        return [];
    }
}