<?php

namespace App\Filament\Resources\NagsasanayResource\Pages;

use App\Filament\Resources\NagsasanayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNagsasanays extends ListRecords
{
    protected static string $resource = NagsasanayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create_mang_awit')
                ->label('Add Nagsasanay (Mang-aawit)')
                ->icon('heroicon-o-user-plus')
                ->url(NagsasanayResource::getUrl('create-trainee')),

            Actions\Action::make('create_organista')
                ->label('Add Nagsasanay (Organista)')
                ->icon('heroicon-o-musical-note')
                ->color('warning')
                ->url(NagsasanayResource::getUrl('create-organista')),

            Actions\Action::make('viewArchived')
                ->label('View Archived')
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->url(fn () => NagsasanayResource::getUrl('archived')),
        ];
    }
}