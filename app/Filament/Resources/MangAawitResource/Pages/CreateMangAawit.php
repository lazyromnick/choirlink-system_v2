<?php

namespace App\Filament\Resources\MangAawitResource\Pages;

use App\Filament\Resources\MangAawitResource;
use App\Services\CreateMangAawitService;
use App\Services\MangAawitIdGenerator;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMangAawit extends CreateRecord
{
    protected static string $resource = MangAawitResource::class;

    protected string $initialPassword = '';
    protected string $qrToken = '';

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $service = new CreateMangAawitService(new MangAawitIdGenerator());

        $personalData = [
            'first_name'      => $data['first_name'],
            'middle_name'     => $data['middle_name'] ?? null,
            'last_name'       => $data['last_name'],
            'suffix'          => $data['suffix'] ?? null,
            'birthday'        => $data['birthday'],
            'sex'             => $data['sex'],
            'address'         => $data['address'],
            'date_registered' => $data['date_registered'],
        ];

        $membershipData = [
            'membership_classification' => $data['membership_classification'],
            'designation'               => $data['designation'],
            'tungkulin'                 => $data['tungkulin'],
            'tungkulin_status'          => $data['tungkulin_status'],
            'mang_aawit_in'             => $data['mang_aawit_in'],
            'previous_locale'           => $data['previous_locale'] ?? null,
            'destination_locale'        => $data['destination_locale'] ?? null,
            'transfer_date'             => $data['transfer_date'] ?? null,
        ];

        $result = $service->create($personalData, $membershipData, Auth::id());

        $this->initialPassword = $result['initialPassword'];
        $this->qrToken         = $result['qrToken'];

        return $result['mangAawit'];
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Member created successfully')
            ->body(
                "Member ID: **{$this->record->mang_aawit_id}**\n" .
                "Initial Password: **{$this->initialPassword}**\n" .
                "Give the QR token to the member securely."
            )
            ->success()
            ->persistent()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}