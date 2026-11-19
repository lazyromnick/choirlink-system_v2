<?php

namespace App\Filament\Pages;

use App\Models\Nagsasanay;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;

class ViewNagsasanayAccount extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.view-nagsasanay-account';

    public Nagsasanay $record;

    public function mount(Nagsasanay $record): void
    {
        $this->record = $record;
    }

    protected function linkedMangAawit(): ?\App\Models\MangAawit
    {
        if ($this->record->training_track === 'Organista') {
            return $this->record->mangAawit;
        }

        return \App\Models\MangAawit::withTrashed()
            ->where('mang_aawit_id', $this->record->mang_aawit_id)
            ->first();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $mangAawit = $this->linkedMangAawit();

        return $infolist
            ->record($this->record)
            ->schema([
                Section::make('Account Information')
                    ->schema([
                        TextEntry::make('mang_aawit_id')->label('Member ID'),
                        TextEntry::make('last_name')
                            ->label('Last Name')
                            ->getStateUsing(fn () => $this->record->training_track === 'Organista'
                                ? ($mangAawit?->last_name ?? '—')
                                : ($this->record->last_name ?? '—')),
                        TextEntry::make('first_name')
                            ->label('First Name')
                            ->getStateUsing(fn () => $this->record->training_track === 'Organista'
                                ? ($mangAawit?->first_name ?? '—')
                                : ($this->record->first_name ?? '—')),
                        TextEntry::make('training_track')
                            ->label('Track')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Mang-aawit' => 'info',
                                'Organista'  => 'warning',
                                default      => 'gray',
                            }),
                        TextEntry::make('training_status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Active'   => 'success',
                                'Inactive' => 'danger',
                                default    => 'gray',
                            }),
                    ])
                    ->columns(2),

                Section::make('Account History')
                    ->schema([
                        RepeatableEntry::make('account_history')
                            ->label('')
                            ->schema([
                                TextEntry::make('event_type')->label('Event'),
                                TextEntry::make('created_at')->label('Date')->dateTime(),
                                TextEntry::make('reason')->label('Reason')->placeholder('—'),
                            ])
                            ->columns(3),
                    ])
                    ->collapsible(),
            ]);
    }

    protected function getViewData(): array
    {
        return [
            'history' => $mangAawit = $this->linkedMangAawit()?->account?->histories ?? collect(),
        ];
    }
}