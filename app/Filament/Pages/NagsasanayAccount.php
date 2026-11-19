<?php

namespace App\Filament\Pages;

use App\Models\Nagsasanay;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;

class NagsasanayAccount extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Nagsasanay Account';
    protected static ?string $title = 'Nagsasanay Account';
    protected static ?string $navigationGroup = 'Accounts';
    protected static string $view = 'filament.pages.nagsasanay-account';

    public bool $showArchived = false;

    public function toggleArchived(): void
    {
        $this->showArchived = !$this->showArchived;
        $this->resetTable();
    }

    protected function getTableQuery()
    {
        return $this->showArchived ? Nagsasanay::onlyTrashed() : Nagsasanay::whereNull('deleted_at');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('mang_aawit_id')
                    ->label('Member ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn (Nagsasanay $record): string =>
                        $record->training_track === 'Organista'
                            ? ($record->mangAawit?->last_name ?? '—')
                            : ($record->last_name ?? '—')
                    ),

                Tables\Columns\TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn (Nagsasanay $record): string =>
                        $record->training_track === 'Organista'
                            ? ($record->mangAawit?->first_name ?? '—')
                            : ($record->first_name ?? '—')
                    ),

                Tables\Columns\TextColumn::make('training_track')
                    ->label('Track')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Mang-aawit' => 'info',
                        'Organista'  => 'warning',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('training_status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'Active'   => 'success',
                        'Inactive' => 'danger',
                        default    => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('viewHistory')
                    ->label('View History')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Nagsasanay $record): string => route('filament.admin.pages.view-nagsasanay-account', ['record' => $record->id])),


                Tables\Actions\Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Nagsasanay $record): bool =>
                        !$this->showArchived && $this->resolveLinkedMangAawit($record)?->account?->account_status === 'Active'
                    )
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason for deactivation')
                            ->required(),
                    ])
                    ->action(function (Nagsasanay $record, array $data) {
                        $account = $this->resolveLinkedMangAawit($record)?->account;
                        if (!$account) return;

                        $account->update(['account_status' => 'Inactive']);

                        $account->histories()->create([
                            'event_type'      => 'Deactivation',
                            'previous_status' => 'Active',
                            'new_status'      => 'Inactive',
                            'reason'          => $data['reason'],
                            'changed_by'      => Auth::id(),
                            'actor_type'      => 'Admin',
                        ]);

                        Notification::make()->title('Account deactivated')->success()->send();
                    })
                    ->requiresConfirmation(),

                Tables\Actions\Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Nagsasanay $record): bool =>
                        !$this->showArchived && $this->resolveLinkedMangAawit($record)?->account?->account_status === 'Inactive'
                    )
                    ->action(function (Nagsasanay $record) {
                        $account = $this->resolveLinkedMangAawit($record)?->account;
                        if (!$account) return;

                        $account->update(['account_status' => 'Active']);

                        $account->histories()->create([
                            'event_type'      => 'Activation',
                            'previous_status' => 'Inactive',
                            'new_status'      => 'Active',
                            'reason'          => null,
                            'changed_by'      => Auth::id(),
                            'actor_type'      => 'Admin',
                        ]);

                        Notification::make()->title('Account activated')->success()->send();
                    })
                    ->requiresConfirmation(),

                Tables\Actions\Action::make('resetPassword')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->visible(fn (): bool => !$this->showArchived)
                    ->action(function (Nagsasanay $record) {
                        $mangAawit = $this->resolveLinkedMangAawit($record);
                        $account = $mangAawit?->account;
                        if (!$account || !$mangAawit) return;

                        $newPassword = $mangAawit->birthday->format('Ymd');
                        $account->update([
                            'password'             => $newPassword,
                            'must_change_password' => true,
                        ]);

                        $account->histories()->create([
                            'event_type'      => 'Password Reset',
                            'previous_status' => null,
                            'new_status'      => null,
                            'reason'          => null,
                            'changed_by'      => Auth::id(),
                            'actor_type'      => 'Admin',
                        ]);

                        Notification::make()->title('Password reset to birthday-based temporary password')->success()->send();
                    })
                    ->requiresConfirmation(),


                Tables\Actions\Action::make('restore')
                    ->label('Restore')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn (): bool => $this->showArchived)
                    ->requiresConfirmation()
                    ->action(function (Nagsasanay $record) {
                        $record->restore();

                        $body = 'Training record restored.';

                        if ($record->training_track === 'Mang-aawit') {
                            $mangAawit = \App\Models\MangAawit::withTrashed()
                                ->where('mang_aawit_id', $record->mang_aawit_id)
                                ->first();

                            if ($mangAawit) {
                                if ($mangAawit->trashed()) {
                                    $mangAawit->restore();
                                }

                                $account = $mangAawit->account;

                                if ($account && $account->account_status !== 'Active') {
                                    $account->update(['account_status' => 'Active']);

                                    $account->histories()->create([
                                        'event_type'      => 'Activation',
                                        'previous_status' => 'Inactive',
                                        'new_status'      => 'Active',
                                        'reason'          => 'Automatically reactivated — Nagsasanay training record restored.',
                                        'changed_by'      => \Illuminate\Support\Facades\Auth::id(),
                                        'actor_type'      => 'Admin',
                                    ]);

                                    $body = 'Training record restored and the linked Mang-aawit account was reactivated.';
                                }
                            }
                        }

                        Notification::make()->title('Nagsasanay restored')->body($body)->success()->send();
                        $this->resetTable();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (): bool => !$this->showArchived),
            ]);
    }

    protected function resolveLinkedMangAawit(Nagsasanay $record): ?\App\Models\MangAawit
    {
        if ($record->training_track === 'Organista') {
            return $record->mangAawit;
        }

        return \App\Models\MangAawit::where('mang_aawit_id', $record->mang_aawit_id)->first();
    }
}