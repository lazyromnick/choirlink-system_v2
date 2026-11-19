<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurokGrupoResource\Pages;
use App\Models\PurokGrupo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PurokGrupoResource extends Resource
{
    protected static ?string $model = PurokGrupo::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Purok-Grupo';
    protected static ?string $modelLabel = 'Purok-Grupo';
    protected static ?string $pluralModelLabel = 'Purok-Grupo';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Purok-Grupo';
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Purok-Grupo Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true)
                        ->placeholder('e.g. 1-1, 1-2, 2-1'),

                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(1),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            \Filament\Infolists\Components\Section::make('Purok-Grupo Details')
                ->schema([
                    \Filament\Infolists\Components\TextEntry::make('name')
                        ->label('Name'),

                    \Filament\Infolists\Components\TextEntry::make('description')
                        ->label('Description')
                        ->placeholder('—'),

                    \Filament\Infolists\Components\TextEntry::make('mangAawits_count')
                        ->label('Total Mang-aawit')
                        ->getStateUsing(fn (PurokGrupo $record): int => $record->mangAawits()->count()),

                    \Filament\Infolists\Components\TextEntry::make('nagsasanay_count')
                        ->label('Total Nagsasanay')
                        ->getStateUsing(fn (PurokGrupo $record): int => $record->nagsasanay()->count()),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->placeholder('—')
                    ->limit(50),

                Tables\Columns\TextColumn::make('mang_aawits_count')
                    ->label('Mang-aawit')
                    ->counts('mangAawits')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nagsasanay_count')
                    ->label('Nagsasanay')
                    ->counts('nagsasanay')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('name', 'asc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPurokGrupos::route('/'),
            'create' => Pages\CreatePurokGrupo::route('/create'),
            'view'   => Pages\ViewPurokGrupo::route('/{record}'),
            'edit'   => Pages\EditPurokGrupo::route('/{record}/edit'),
        ];
    }
}