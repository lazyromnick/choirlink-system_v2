<?php

namespace App\Filament\Resources\NagsasanayResource\Pages;

use App\Filament\Resources\NagsasanayResource;
use App\Models\MangAawit;
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

        $becameCompleted = $record->progress_stage === 'Completed'
            && $this->previousProgressStage !== 'Completed';

        if ($becameCompleted) {
            $this->completeTraining($record);
            return;
        }

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

    protected function completeTraining($record): void
    {
        if ($record->training_track === 'Mang-aawit') {
            $mangAawit = MangAawit::where('mang_aawit_id', $record->mang_aawit_id)->first();

            if (!$mangAawit) {
                Notification::make()
                    ->title('Could not complete training')
                    ->body('No linked Mang-aawit account was found for this trainee.')
                    ->danger()
                    ->send();
                return;
            }

            $mangAawit->currentMembership?->update([
                'designation' => 'Regular',
            ]);

            $record->delete();

            Notification::make()
                ->title('Nagsasanay completed training')
                ->body("{$record->full_name} has graduated and is now a Regular Mang-aawit.")
                ->success()
                ->send();
        } else {
            // Organista track
            $mangAawit = $record->mangAawit;

            if (!$mangAawit) {
                Notification::make()
                    ->title('Could not complete training')
                    ->body('No linked Mang-aawit record was found for this trainee.')
                    ->danger()
                    ->send();
                return;
            }

            $mangAawit->currentMembership?->update([
                'tungkulin' => 'Organista',
            ]);

            $record->delete();

            Notification::make()
                ->title('Nagsasanay completed training')
                ->body("{$mangAawit->full_name} has completed Organista training and their tungkulin has been updated.")
                ->success()
                ->send();
        }

        $this->redirect(NagsasanayResource::getUrl('index'));
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}