<?php

namespace App\Filament\Resources\HasilTopses\Pages;

use App\Filament\Resources\HasilTopses\HasilTopsisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHasilTopses extends ListRecords
{
    protected static string $resource = HasilTopsisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
