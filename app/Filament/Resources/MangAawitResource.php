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

            Forms\Components\Section::make('Membership Information')
    ->schema([
        Forms\Components\Select::make('membership_classification')
    ->label('Membership Classification')
    ->options([
        'Taga-lokal'   => 'Taga-lokal',
        'Transfer-In'  => 'Transfer-In',
        'Transfer-Out' => 'Transfer-Out',
    ])
    ->required()
    ->live()
    ->afterStateUpdated(function (Forms\Set $set, $state) {
        if ($state === 'Taga-lokal') {
            $set('previous_locale', null);
            $set('destination_locale', null);
            $set('transfer_date', null);
        } elseif ($state === 'Transfer-In') {
            $set('destination_locale', null);
        } elseif ($state === 'Transfer-Out') {
            $set('previous_locale', null);
        }
    }),

        Forms\Components\Select::make('designation')
            ->label('Designation')
            ->options([
                'Regular'    => 'Regular',
                'Nagsasanay' => 'Nagsasanay',
            ])
            ->required(),

        Forms\Components\Select::make('tungkulin')
            ->label('Tungkulin')
            ->options([
                'Mang-aawit' => 'Mang-aawit',
                'Organista'  => 'Organista',
            ])
            ->required(),

        Forms\Components\Select::make('tungkulin_status')
            ->label('Tungkulin Status')
            ->options([
                'Active'   => 'Active',
                'Inactive' => 'Inactive',
            ])
            ->required(),

        Forms\Components\Select::make('mang_aawit_in')
            ->label('Worship Service')
            ->options([
                'AWS' => 'AWS',
                'CWS' => 'CWS',
            ])
            ->required(),

        Forms\Components\TextInput::make('previous_locale')
            ->label('Previous Locale')
            ->maxLength(255)
            ->visible(fn (Forms\Get $get) => $get('membership_classification') === 'Transfer-In')
            ->required(fn (Forms\Get $get) => $get('membership_classification') === 'Transfer-In'),

        Forms\Components\TextInput::make('destination_locale')
            ->label('Destination Locale')
            ->maxLength(255)
            ->visible(fn (Forms\Get $get) => $get('membership_classification') === 'Transfer-Out')
            ->required(fn (Forms\Get $get) => $get('membership_classification') === 'Transfer-Out'),

        Forms\Components\DatePicker::make('transfer_date')
            ->label('Transfer Date')
            ->visible(fn (Forms\Get $get) => in_array($get('membership_classification'), ['Transfer-In', 'Transfer-Out']))
            ->required(fn (Forms\Get $get) => in_array($get('membership_classification'), ['Transfer-In', 'Transfer-Out'])),
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

            \Filament\Infolists\Components\Section::make('Account History')
                ->schema([
                    \Filament\Infolists\Components\RepeatableEntry::make('account.histories')
                        ->label('')
                        ->schema([
                            \Filament\Infolists\Components\TextEntry::make('event_type')
                                ->label('Event'),
                            \Filament\Infolists\Components\TextEntry::make('created_at')
                                ->label('Date')
                                ->dateTime(),
                            \Filament\Infolists\Components\TextEntry::make('reason')
                                ->label('Reason')
                                ->placeholder('—'),
                        ])
                        ->columns(3),
                ])
                ->collapsible(),
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

                Tables\Actions\Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (MangAawit $record): bool => $record->account?->account_status === 'Active')
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
                            'changed_by'      => \Illuminate\Support\Facades\Auth::id(),
                            'actor_type'      => 'Admin',
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Account deactivated')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),

                        Tables\Actions\Action::make('activate')
                            ->label('Activate')
                            ->icon('heroicon-o-check-circle')
                            ->color('success')
                            ->visible(fn (MangAawit $record): bool => $record->account?->account_status === 'Inactive')
                            ->action(function (MangAawit $record) {
                                $account = $record->account;
                                if (!$account) return;

                                $account->update(['account_status' => 'Active']);

                                $account->histories()->create([
                                    'event_type'      => 'Activation',
                                    'previous_status' => 'Inactive',
                                    'new_status'      => 'Active',
                                    'reason'          => null,
                                    'changed_by'      => \Illuminate\Support\Facades\Auth::id(),
                                    'actor_type'      => 'Admin',
                                ]);

                                \Filament\Notifications\Notification::make()
                                    ->title('Account activated')
                                    ->success()
                                    ->send();
                            })
                            ->requiresConfirmation(),

                        Tables\Actions\Action::make('resetPassword')
                            ->label('Reset Password')
                            ->icon('heroicon-o-key')
                            ->color('warning')
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
                                    'changed_by'      => \Illuminate\Support\Facades\Auth::id(),
                                    'actor_type'      => 'Admin',
                                ]);

                                \Filament\Notifications\Notification::make()
                                    ->title('Password reset to birthday-based temporary password')
                                    ->success()
                                    ->send();
                            })
                            ->requiresConfirmation(),

                            Tables\Actions\RestoreAction::make(),
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
            'index'    => Pages\ListMangAawits::route('/'),
            'create'   => Pages\CreateMangAawit::route('/create'),
            'archived' => Pages\ListArchivedMangAawits::route('/archived'),
            'view'     => Pages\ViewMangAawit::route('/{record}'),
            'edit'     => Pages\EditMangAawit::route('/{record}/edit'),
        ];
    }
}