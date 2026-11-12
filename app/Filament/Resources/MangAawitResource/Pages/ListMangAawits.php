<?php

namespace App\Filament\Resources\MangAawitResource\Pages;

use App\Filament\Resources\MangAawitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMangAawits extends ListRecords
{
    protected static string $resource = MangAawitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
