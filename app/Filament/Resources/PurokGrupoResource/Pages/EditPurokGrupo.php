<?php

namespace App\Filament\Resources\PurokGrupoResource\Pages;

use App\Filament\Resources\PurokGrupoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPurokGrupo extends EditRecord
{
    protected static string $resource = PurokGrupoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
