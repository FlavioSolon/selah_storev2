<?php

namespace App\Filament\Resources\EncomendaResource\Pages;

use App\Filament\Resources\EncomendaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEncomendas extends ListRecords
{
    protected static string $resource = EncomendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
