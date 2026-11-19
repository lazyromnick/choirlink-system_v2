<?php

namespace App\Filament\Pages;

use App\Models\MangAawit;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;

class ViewMangAawitAccount extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.view-mang-aawit-account';

    public MangAawit $record;

    public function mount(MangAawit $record): void
    {
        $this->record = $record;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                Section::make('Account Information')
                    ->schema([
                        TextEntry::make('mang_aawit_id')->label('Member ID'),
                        TextEntry::make('last_name')->label('Last Name'),
                        TextEntry::make('first_name')->label('First Name'),
                        TextEntry::make('currentMembership.tungkulin')->label('Tungkulin'),
                        TextEntry::make('currentMembership.tungkulin_status')
                            ->label('Tungkulin Status')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'Active'   => 'success',
                                'Inactive' => 'danger',
                                default    => 'gray',
                            }),
                        TextEntry::make('account.account_status')
                            ->label('Account Status')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'Active'   => 'success',
                                'Inactive' => 'danger',
                                default    => 'gray',
                            }),
                    ])
                    ->columns(2),

                Section::make('Account History')
                    ->schema([
                        RepeatableEntry::make('account.histories')
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
}