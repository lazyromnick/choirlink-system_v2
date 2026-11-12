<?php

namespace App\Filament\Resources\NagsasanayResource\Pages;

use App\Filament\Resources\NagsasanayResource;
use Filament\Resources\Pages\EditRecord;

class EditNagsasanay extends EditRecord
{
    protected static string $resource = NagsasanayResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}