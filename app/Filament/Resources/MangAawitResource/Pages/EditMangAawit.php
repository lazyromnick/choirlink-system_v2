<?php

namespace App\Filament\Resources\MangAawitResource\Pages;

use App\Filament\Resources\MangAawitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditMangAawit extends EditRecord
{
    protected static string $resource = MangAawitResource::class;

    protected array $membershipData = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $membership = $this->record->currentMembership;

        if ($membership) {
            $data['membership_classification'] = $membership->membership_classification;
            $data['designation']               = $membership->designation;
            $data['tungkulin']                 = $membership->tungkulin;
            $data['tungkulin_status']          = $membership->tungkulin_status;
            $data['mang_aawit_in']             = $membership->mang_aawit_in;
            $data['previous_locale']           = $membership->previous_locale;
            $data['destination_locale']        = $membership->destination_locale;
            $data['transfer_date']             = $membership->transfer_date;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->membershipData = [
            'membership_classification' => $data['membership_classification'],
            'designation'               => $data['designation'],
            'tungkulin'                 => $data['tungkulin'],
            'tungkulin_status'          => $data['tungkulin_status'],
            'mang_aawit_in'             => $data['mang_aawit_in'],
            'previous_locale'           => $data['previous_locale'] ?? null,
            'destination_locale'        => $data['destination_locale'] ?? null,
            'transfer_date'             => $data['transfer_date'] ?? null,
        ];

        unset(
            $data['membership_classification'],
            $data['designation'],
            $data['tungkulin'],
            $data['tungkulin_status'],
            $data['mang_aawit_in'],
            $data['previous_locale'],
            $data['destination_locale'],
            $data['transfer_date'],
        );

        return $data;
    }

    protected function afterSave(): void
    {
        $membership = $this->record->currentMembership;

        if (!$membership) {
            return;
        }

        $wasTransferOut = $membership->membership_classification === 'Transfer-Out';

        $membership->update($this->membershipData);

        $becameTransferOut = $this->membershipData['membership_classification'] === 'Transfer-Out';

        if ($becameTransferOut && !$wasTransferOut) {
            $this->archiveMember();
        }
    }

    protected function archiveMember(): void
    {
        $record     = $this->record;
        $membership = $record->currentMembership;
        $account    = $record->account;

        $membership?->update([
            'membership_status' => 'Transfer-Out',
        ]);

        if ($account && $account->account_status === 'Active') {
            $account->update(['account_status' => 'Inactive']);

            $account->histories()->create([
                'event_type'      => 'Deactivation',
                'previous_status' => 'Active',
                'new_status'      => 'Inactive',
                'reason'          => 'Automatically deactivated — member transferred out.',
                'changed_by'      => Auth::id(),
                'actor_type'      => 'Admin',
            ]);
        }

        $record->delete();

        \Filament\Notifications\Notification::make()
            ->title('Member archived')
            ->body('Classification set to Transfer-Out — account deactivated and record archived.')
            ->warning()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}