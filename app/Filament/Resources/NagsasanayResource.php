<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NagsasanayResource\Pages;
use App\Models\MangAawit;
use App\Models\Nagsasanay;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NagsasanayResource extends Resource
{
    protected static ?string $model = Nagsasanay::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Nagsasanay';
    protected static ?string $modelLabel = 'Nagsasanay';
    protected static ?string $pluralModelLabel = 'Nagsasanay';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Nagsasanay';

    public static function form(Form $form): Form
    {
        return $form->schema([
        Forms\Components\Section::make('Personal Information')
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label('First Name')
                    ->maxLength(100)
                    ->visible(fn ($record) => $record?->training_track === 'Mang-aawit'),

                Forms\Components\TextInput::make('middle_name')
                    ->label('Middle Name')
                    ->maxLength(100)
                    ->visible(fn ($record) => $record?->training_track === 'Mang-aawit'),

                Forms\Components\TextInput::make('last_name')
                    ->label('Last Name')
                    ->maxLength(100)
                    ->visible(fn ($record) => $record?->training_track === 'Mang-aawit'),

                Forms\Components\TextInput::make('suffix')
                    ->label('Suffix')
                    ->maxLength(20)
                    ->visible(fn ($record) => $record?->training_track === 'Mang-aawit'),

                Forms\Components\DatePicker::make('birthday')
                    ->label('Birthday')
                    ->maxDate(now())
                    ->visible(fn ($record) => $record?->training_track === 'Mang-aawit'),

                Forms\Components\Select::make('sex')
                    ->label('Sex')
                    ->options(['Male' => 'Male', 'Female' => 'Female'])
                    ->visible(fn ($record) => $record?->training_track === 'Mang-aawit'),

                Forms\Components\Select::make('purok_grupo_id')
                    ->label('Purok-Grupo')
                    ->options(
                        \App\Models\PurokGrupo::orderBy('name')->pluck('name', 'id')
                    )
                    ->searchable()
                    ->nullable()
                    ->placeholder('— None —')
                    ->visible(fn ($record) => $record?->training_track === 'Mang-aawit'),

                Forms\Components\Textarea::make('address')
                    ->label('Address')
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record?->training_track === 'Mang-aawit'),
            ])
            ->columns(2),

        Forms\Components\Section::make('Training Information')
            ->schema([
                Forms\Components\DatePicker::make('training_start_date')
                    ->label('Training Start Date'),

                Forms\Components\TextInput::make('trainer_name')
                    ->label('Nag-recruit')
                    ->maxLength(150),

                Forms\Components\Select::make('progress_stage')
                    ->label('Progress Stage')
                    ->options([
                        'Newly Registered' => 'Newly Registered',
                        'On Going'         => 'On Going',
                        'For Evaluation'   => 'For Evaluation',
                        'Completed'        => 'Completed',
                        'Discontinued'     => 'Discontinued',
                    ]),

                Forms\Components\Select::make('training_status')
                    ->label('Training Status')
                    ->options([
                        'Active'   => 'Active',
                        'Inactive' => 'Inactive',
                    ]),

                Forms\Components\Textarea::make('remarks')
                    ->label('Remarks')
                    ->columnSpanFull(),
            ])
            ->columns(2),

        Forms\Components\Section::make('Membership Details')
            ->description('Fill in when training is completed.')
            ->schema([
                Forms\Components\Select::make('membership_classification')
                    ->label('Classification')
                    ->options([
                        'Taga-lokal'   => 'Taga-lokal',
                        'Transfer-In'  => 'Transfer-In',
                        'Transfer-Out' => 'Transfer-Out',
                    ])
                    ->live(),

                Forms\Components\Select::make('mang_aawit_in')
                    ->label('Mang-aawit in')
                    ->options([
                        'AWS' => 'Katandaan',
                        'CWS' => 'Kabataan',
                    ]),

                Forms\Components\DatePicker::make('transfer_date')
                    ->label('Transfer Date')
                    ->visible(fn (Forms\Get $get) => in_array($get('membership_classification'), ['Transfer-In', 'Transfer-Out'])),

                Forms\Components\TextInput::make('previous_locale')
                    ->label('Previous Locale')
                    ->maxLength(150)
                    ->visible(fn (Forms\Get $get) => $get('membership_classification') === 'Transfer-In'),

                Forms\Components\TextInput::make('destination_locale')
                    ->label('Destination Locale')
                    ->maxLength(150)
                    ->visible(fn (Forms\Get $get) => $get('membership_classification') === 'Transfer-Out'),
            ])
            ->columns(2)
            ->visible(fn ($record) => $record?->training_track === 'Mang-aawit'),

        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            \Filament\Infolists\Components\Section::make('Training Information')
                ->schema([
                    \Filament\Infolists\Components\TextEntry::make('mang_aawit_id')
                        ->label('Member ID'),

                    \Filament\Infolists\Components\TextEntry::make('training_track')
                        ->label('Training Track')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Mang-aawit' => 'info',
                            'Organista'  => 'warning',
                            default      => 'gray',
                        }),

                    \Filament\Infolists\Components\TextEntry::make('full_name')
                        ->label('Full Name'),

                    \Filament\Infolists\Components\TextEntry::make('purok_grupo_display')
                        ->label('Purok-Grupo')
                        ->getStateUsing(fn (Nagsasanay $record): string =>
                            $record->training_track === 'Organista'
                                ? ($record->mangAawit?->purokGrupo?->name ?? '—')
                                : ($record->purokGrupo?->name ?? '—')
                        ),

                    \Filament\Infolists\Components\TextEntry::make('training_category')
                        ->label('Training Category'),

                    \Filament\Infolists\Components\TextEntry::make('training_start_date')
                        ->label('Training Start Date')
                        ->date(),

                    \Filament\Infolists\Components\TextEntry::make('trainer_name')
                        ->label('Nag-recruit'),

                    \Filament\Infolists\Components\TextEntry::make('progress_stage')
                        ->label('Progress Stage')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Newly Registered' => 'gray',
                            'On Going'         => 'info',
                            'For Evaluation'   => 'warning',
                            'Completed'        => 'success',
                            'Discontinued'     => 'danger',
                            default            => 'gray',
                        }),

                    \Filament\Infolists\Components\TextEntry::make('training_status')
                        ->label('Training Status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Active'   => 'success',
                            'Inactive' => 'danger',
                            default    => 'gray',
                        }),

                    \Filament\Infolists\Components\TextEntry::make('remarks')
                        ->label('Remarks')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            \Filament\Infolists\Components\Section::make('Membership Details')
                ->schema([
                    \Filament\Infolists\Components\TextEntry::make('membership_classification')
                        ->label('Classification')
                        ->placeholder('Not yet set'),

                    \Filament\Infolists\Components\TextEntry::make('mang_aawit_in')
                        ->label('Mang-aawit in')
                        ->placeholder('Not yet set'),

                    \Filament\Infolists\Components\TextEntry::make('previous_locale')
                        ->label('Previous Locale')
                        ->placeholder('—'),

                    \Filament\Infolists\Components\TextEntry::make('destination_locale')
                        ->label('Destination Locale')
                        ->placeholder('—'),

                    \Filament\Infolists\Components\TextEntry::make('transfer_date')
                        ->label('Transfer Date')
                        ->date()
                        ->placeholder('—'),
                ])
                ->columns(2)
                ->visible(fn (Nagsasanay $record): bool => $record->training_track === 'Mang-aawit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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

                Tables\Columns\TextColumn::make('purok_grupo_display')
                    ->label('Purok-Grupo')
                    ->getStateUsing(fn (Nagsasanay $record): string =>
                        $record->training_track === 'Organista'
                            ? ($record->mangAawit?->purokGrupo?->name ?? '—')
                            : ($record->purokGrupo?->name ?? '—')
                    ),

                Tables\Columns\TextColumn::make('training_track')
                    ->label('Track')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Mang-aawit' => 'info',
                        'Organista'  => 'warning',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('trainer_name')
                    ->label('Nag-recruit')
                    ->searchable(),

                Tables\Columns\TextColumn::make('progress_stage')
                    ->label('Progress')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'Newly Registered' => 'gray',
                        'On Going'         => 'info',
                        'For Evaluation'   => 'warning',
                        'Completed'        => 'success',
                        'Discontinued'     => 'danger',
                        default            => 'gray',
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

                Tables\Columns\TextColumn::make('training_start_date')
                    ->label('Started')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('last_name', 'asc')
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
            'index'             => Pages\ListNagsasanays::route('/'),
            'create-trainee'    => Pages\CreateNagsasanayMangAawit::route('/create-trainee'),
            'create-organista'  => Pages\CreateNagsasanayOrganista::route('/create-organista'),
            'archived'          => Pages\ListArchivedNagsasanays::route('/archived'),
            'view'              => Pages\ViewNagsasanay::route('/{record}'),
            'edit'              => Pages\EditNagsasanay::route('/{record}/edit'),
        ];
    }
}