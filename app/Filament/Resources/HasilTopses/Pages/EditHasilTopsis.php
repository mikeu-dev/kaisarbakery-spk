<?php

namespace App\Filament\Resources\HasilTopses\Pages;

use App\Filament\Resources\HasilTopses\HasilTopsisResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHasilTopsis extends EditRecord
{
    protected static string $resource = HasilTopsisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
