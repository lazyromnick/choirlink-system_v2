<?php

namespace App\Filament\Resources\NagsasanayResource\Pages;

use App\Filament\Resources\NagsasanayResource;
use App\Models\Nagsasanay;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListArchivedNagsasanays extends ListRecords
{
    protected static string $resource = NagsasanayResource::class;

    protected static ?string $title = 'Archived Nagsasanay';

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Nagsasanay::onlyTrashed())
            ->columns([
                Tables\Columns\TextColumn::make('mang_aawit_id')
                    ->label('Member ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name'),

                Tables\Columns\TextColumn::make('training_track')
                    ->label('Track')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Mang-aawit' => 'info',
                        'Organista'  => 'warning',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('progress_stage')
                    ->label('Progress Stage'),

                Tables\Columns\TextColumn::make('training_status')
                    ->label('Training Status'),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Archived On')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('restore')
                    ->label('Restore')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
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
                                } elseif ($account) {
                                    $body = 'Training record restored. The linked Mang-aawit account was already active.';
                                } else {
                                    $body = 'Training record restored, but no linked Mang-aawit account was found.';
                                }
                            }
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Nagsasanay restored')
                            ->body($body)
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }
}