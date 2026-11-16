<?php

namespace App\Filament\Resources\NagsasanayResource\Pages;

use App\Filament\Resources\NagsasanayResource;
use App\Services\CreateNagsasanayMangAawitService;
use App\Services\MangAawitIdGenerator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class CreateNagsasanayMangAawit extends Page
{
    protected static string $resource = NagsasanayResource::class;
    protected static string $view = 'filament.resources.nagsasanay-resource.pages.create-nagsasanay-mang-aawit';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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

                        Forms\Components\TextInput::make('suffix')
                            ->label('Suffix')
                            ->maxLength(20),

                        Forms\Components\DatePicker::make('birthday')
                            ->label('Birthday')
                            ->required()
                            ->maxDate(now()),

                        Forms\Components\Select::make('sex')
                            ->label('Sex')
                            ->options(['Male' => 'Male', 'Female' => 'Female'])
                            ->required(),

                        Forms\Components\Select::make('purok_grupo_id')
                            ->label('Purok-Grupo')
                            ->options(
                                \App\Models\PurokGrupo::orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->nullable()
                            ->placeholder('— None —'),

                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('date_registered')
                            ->label('Date Registered')
                            ->default(now())
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Training Information')
                    ->schema([
                        Forms\Components\DatePicker::make('training_start_date')
                            ->label('Training Start Date')
                            ->required(),

                        Forms\Components\TextInput::make('trainer_name')
                            ->label('Trainer / Responsible Person')
                            ->required()
                            ->maxLength(150),

                        Forms\Components\Select::make('mang_aawit_in')
                            ->label('Mang-aawit in')
                            ->options([
                                'AWS' => 'Katandaan',
                                'CWS' => 'Kabataan',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('remarks')
                            ->label('Remarks')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $service = new CreateNagsasanayMangAawitService(new MangAawitIdGenerator());
        $result  = $service->create($data, Auth::id());

        Notification::make()
            ->title('Nagsasanay created successfully')
            ->body(
                "Member ID: **{$result['nagsasanay']->mang_aawit_id}**\n" .
                "Initial Password: **{$result['initialPassword']}**"
            )
            ->success()
            ->persistent()
            ->send();

        $this->redirect(NagsasanayResource::getUrl('index'));
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Create Nagsasanay')
                ->submit('save'),
        ];
    }
}