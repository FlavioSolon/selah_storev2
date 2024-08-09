<?php

namespace App\Filament\Resources\EncomendaResource\Pages;

use App\Filament\Resources\EncomendaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEncomenda extends EditRecord
{
    protected static string $resource = EncomendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
