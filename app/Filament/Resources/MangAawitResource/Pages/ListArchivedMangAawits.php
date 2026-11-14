<?php

namespace App\Filament\Resources\MangAawitResource\Pages;

use App\Filament\Resources\MangAawitResource;
use App\Models\MangAawit;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListArchivedMangAawits extends ListRecords
{
    protected static string $resource = MangAawitResource::class;

    protected static ?string $title = 'Archived Mang-aawit';

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(MangAawit::onlyTrashed())
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

                Tables\Columns\TextColumn::make('currentMembership.membership_status')
                    ->label('Membership Status'),

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
                    ->action(function (MangAawit $record) {
                        $record->restore();

                        \Filament\Notifications\Notification::make()
                            ->title('Member restored')
                            ->body('Remember to update their membership classification and reactivate their account if needed.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }
}