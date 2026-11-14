<?php

namespace App\Filament\Resources\NagsasanayResource\Pages;

use App\Filament\Resources\NagsasanayResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditNagsasanay extends EditRecord
{
    protected static string $resource = NagsasanayResource::class;

    protected string $previousProgressStage;
    protected string $previousTrainingStatus;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->previousProgressStage  = $this->record->progress_stage;
        $this->previousTrainingStatus = $this->record->training_status;
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        $becameDiscontinued = $record->progress_stage === 'Discontinued'
            && $this->previousProgressStage !== 'Discontinued';

        $becameInactive = $record->training_status === 'Inactive'
            && $this->previousTrainingStatus !== 'Inactive';

        if ($becameDiscontinued || $becameInactive) {
            $record->delete();

            Notification::make()
                ->title('Nagsasanay archived')
                ->body($record->training_track === 'Mang-aawit'
                    ? 'Training record archived and the linked Mang-aawit account was deactivated.'
                    : 'Training record archived.')
                ->warning()
                ->send();

            $this->redirect(NagsasanayResource::getUrl('index'));
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}