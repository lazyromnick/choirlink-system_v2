<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MangAawitResource\Pages;
use App\Models\MangAawit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;

class MangAawitResource extends Resource
{
    protected static ?string $model = MangAawit::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Mang-aawit';
    protected static ?string $modelLabel = 'Mang-aawit';
    protected static ?string $pluralModelLabel = 'Mang-aawit';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
{
    return $form->schema([

        Forms\Components\Section::make('Personal Information')
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('middle_name')
                    ->label('Middle Name')
                    ->maxLength(100),

                Forms\Components\TextInput::make('last_name')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(100),

                Forms\Components\Select::make('purok_grupo_id')
                    ->label('Purok-Grupo')
                    ->options(
                        \App\Models\PurokGrupo::orderBy('name')->pluck('name', 'id')
                    )
                    ->searchable()
                    ->nullable()
                    ->placeholder('— None —'),

                Forms\Components\TextInput::make('suffix')
                    ->label('Suffix')
                    ->maxLength(20),

                Forms\Components\DatePicker::make('birthday')
                    ->label('Birthday')
                    ->required()
                    ->maxDate(now()),

                Forms\Components\Select::make('sex')
                    ->label('Sex')
                    ->options([
                        'Male'   => 'Male',
                        'Female' => 'Female',
                    ])
                    ->required(),


                Forms\Components\DatePicker::make('date_registered')
                    ->label('Date Registered')
                    ->default(now())
                    ->required(),

                Forms\Components\Textarea::make('address')
                    ->label('Address')
                    ->required()
                    ->columnSpanFull(),
            ])
            ->columns(2),

    ]);
}

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            \Filament\Infolists\Components\Section::make('Personal Information')
                ->schema([
                    \Filament\Infolists\Components\TextEntry::make('mang_aawit_id')
                        ->label('Member ID'),

                    \Filament\Infolists\Components\TextEntry::make('full_name')
                        ->label('Full Name'),

                    \Filament\Infolists\Components\TextEntry::make('purokGrupo.name')
                        ->label('Purok-Grupo')
                        ->placeholder('—'),

                    \Filament\Infolists\Components\TextEntry::make('birthday')
                        ->label('Birthday')
                        ->date(),

                    \Filament\Infolists\Components\TextEntry::make('age')
                        ->label('Age')
                        ->suffix(' years old'),

                    \Filament\Infolists\Components\TextEntry::make('sex')
                        ->label('Sex'),

                    \Filament\Infolists\Components\TextEntry::make('address')
                        ->label('Address')
                        ->columnSpanFull(),

                    \Filament\Infolists\Components\TextEntry::make('date_registered')
                        ->label('Date Registered')
                        ->date(),
                ])
                ->columns(2),

            \Filament\Infolists\Components\Section::make('Membership Information')
                ->schema([
                    \Filament\Infolists\Components\TextEntry::make('currentMembership.membership_classification')
                        ->label('Classification'),

                    \Filament\Infolists\Components\TextEntry::make('currentMembership.designation')
                        ->label('Designation'),

                    \Filament\Infolists\Components\TextEntry::make('currentMembership.tungkulin')
                        ->label('Tungkulin'),

                    \Filament\Infolists\Components\TextEntry::make('currentMembership.tungkulin_status')
                        ->label('Tungkulin Status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Active'   => 'success',
                            'Inactive' => 'danger',
                            default    => 'gray',
                        }),

                    \Filament\Infolists\Components\TextEntry::make('currentMembership.membership_status')
                        ->label('Membership Status'),

                    \Filament\Infolists\Components\TextEntry::make('currentMembership.mang_aawit_in')
                        ->label('Worship Service'),

                    \Filament\Infolists\Components\TextEntry::make('currentMembership.previous_locale')
                        ->label('Previous Locale')
                        ->placeholder('—'),

                    \Filament\Infolists\Components\TextEntry::make('currentMembership.destination_locale')
                        ->label('Destination Locale')
                        ->placeholder('—'),

                    \Filament\Infolists\Components\TextEntry::make('currentMembership.transfer_date')
                        ->label('Transfer Date')
                        ->date()
                        ->placeholder('—'),
                ])
                ->columns(2),

            \Filament\Infolists\Components\Section::make('Account')
                ->schema([
                    \Filament\Infolists\Components\TextEntry::make('account.account_status')
                        ->label('Account Status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Active'   => 'success',
                            'Inactive' => 'danger',
                            default    => 'gray',
                        }),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->whereHas('currentMembership', function ($q) {
                $q->where('designation', 'Regular');
            }))
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

                Tables\Columns\TextColumn::make('purokGrupo.name')
                    ->label('Purok-Grupo')
                    ->placeholder('—')
                    ->sortable(),


                Tables\Columns\TextColumn::make('currentMembership.tungkulin')
                    ->label('Tungkulin')
                    ->sortable(),

                Tables\Columns\TextColumn::make('currentMembership.designation')
                    ->label('Designation')
                    ->sortable(),

                Tables\Columns\TextColumn::make('currentMembership.tungkulin_status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'Active'   => 'success',
                        'Inactive' => 'danger',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('account.account_status')
                    ->label('Account')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active'   => 'success',
                        'Inactive' => 'danger',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('date_registered')
                    ->label('Registered')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('last_name', 'asc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index'  => Pages\ListMangAawits::route('/'),
            'create' => Pages\CreateMangAawit::route('/create'),
            'view'   => Pages\ViewMangAawit::route('/{record}'),
            'edit'   => Pages\EditMangAawit::route('/{record}/edit'),
        ];
    }
}