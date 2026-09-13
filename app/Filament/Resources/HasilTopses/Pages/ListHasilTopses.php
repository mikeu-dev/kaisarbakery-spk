<?php

namespace App\Filament\Resources\HasilTopses\Pages;

use App\Filament\Resources\HasilTopses\HasilTopsisResource;
use App\Filament\Widgets\TopsisPodiumWidget;
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

    protected function getHeaderWidgets(): array
    {
        return [
            TopsisPodiumWidget::class,
        ];
    }
}
