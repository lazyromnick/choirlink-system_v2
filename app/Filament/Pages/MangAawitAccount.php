<?php

namespace App\Filament\Pages;

use App\Models\MangAawit;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MangAawitAccount extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Mang-aawit Account';
    protected static ?string $title = 'Mang-aawit Account';
    protected static ?string $navigationGroup = 'Accounts';
    protected static string $view = 'filament.pages.mang-aawit-account';

    public bool $showArchived = false;

    public function toggleArchived(): void
    {
        $this->showArchived = !$this->showArchived;
        $this->resetTable();
    }

    protected function getTableQuery()
    {
        $query = MangAawit::query()
            ->whereHas('currentMembership', fn ($q) => $q->where('designation', 'Regular'));

        return $this->showArchived ? $query->onlyTrashed() : $query->whereNull('deleted_at');
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('currentMembership.tungkulin')
                    ->label('Tungkulin')
                    ->sortable(),

                Tables\Columns\TextColumn::make('currentMembership.tungkulin_status')
                    ->label('Tungkulin Status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'Active'   => 'success',
                        'Inactive' => 'danger',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('account.account_status')
                    ->label('Account Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
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
                    ->url(fn (MangAawit $record): string => route('filament.admin.pages.view-mang-aawit-account', ['record' => $record->id])),

                Tables\Actions\Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (MangAawit $record): bool => !$this->showArchived && $record->account?->account_status === 'Active')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason for deactivation')
                            ->required(),
                    ])
                    ->action(function (MangAawit $record, array $data) {
                        $account = $record->account;
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
                    ->visible(fn (MangAawit $record): bool => !$this->showArchived && $record->account?->account_status === 'Inactive')
                    ->action(function (MangAawit $record) {
                        $account = $record->account;
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
                    ->action(function (MangAawit $record) {
                        $account = $record->account;
                        if (!$account) return;

                        $newPassword = $record->birthday->format('Ymd');
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
                    ->action(function (MangAawit $record) {
                        $record->restore();
                        Notification::make()->title('Member restored')->success()->send();
                        $this->resetTable();
                    }),

                Tables\Actions\Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn (): bool => !$this->showArchived)
                    ->requiresConfirmation()
                    ->action(function (MangAawit $record) {
                        $record->delete();
                        Notification::make()->title('Member archived')->success()->send();
                        $this->resetTable();
                    }),
            ]);
    }
}