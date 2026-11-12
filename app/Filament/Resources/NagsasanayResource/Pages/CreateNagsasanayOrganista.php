<?php

namespace App\Filament\Resources\NagsasanayResource\Pages;

use App\Filament\Resources\NagsasanayResource;
use App\Models\MangAawit;
use App\Services\CreateNagsasanayOrganistaService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class CreateNagsasanayOrganista extends Page
{
    protected static string $resource = NagsasanayResource::class;
    protected static string $view = 'filament.resources.nagsasanay-resource.pages.create-nagsasanay-organista';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('mang_aawit_record_id')
                    ->label('Mang-aawit')
                    ->options(
                        MangAawit::whereNull('deleted_at')
                            ->whereHas('currentMembership', function ($query) {
                                $query->where('designation', 'Regular')
                                    ->where('tungkulin', 'Mang-aawit');
                            })
                            ->get()
                            ->mapWithKeys(fn ($m) => [$m->id => $m->full_name . ' — ' . $m->mang_aawit_id])
                    )
                    ->searchable()
                    ->required(),
            

                Forms\Components\Section::make('Training Information')
                    ->schema([
                        Forms\Components\DatePicker::make('training_start_date')
                            ->label('Training Start Date')
                            ->required(),

                        Forms\Components\TextInput::make('trainer_name')
                            ->label('Trainer / Responsible Person')
                            ->required()
                            ->maxLength(150),

                        Forms\Components\Select::make('progress_stage')
                            ->label('Progress Stage')
                            ->options([
                                'Newly Registered' => 'Newly Registered',
                                'On Going'         => 'On Going',
                                'For Evaluation'   => 'For Evaluation',
                                'Completed'        => 'Completed',
                                'Discontinued'     => 'Discontinued',
                            ])
                            ->default('Newly Registered')
                            ->required(),

                        Forms\Components\Select::make('training_status')
                            ->label('Training Status')
                            ->options([
                                'Active'   => 'Active',
                                'Inactive' => 'Inactive',
                            ])
                            ->default('Active')
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

        $service = new CreateNagsasanayOrganistaService();
        $service->create($data, Auth::id());

        Notification::make()
            ->title('Nagsasanay Organista created successfully')
            ->success()
            ->send();

        $this->redirect(NagsasanayResource::getUrl('index'));
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Create Nagsasanay Organista')
                ->submit('save'),
        ];
    }
}