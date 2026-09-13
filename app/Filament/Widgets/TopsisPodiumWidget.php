<?php

namespace App\Filament\Widgets;

use App\Models\HasilTopsis;
use Filament\Widgets\Widget;

class TopsisPodiumWidget extends Widget
{
    protected string $view = 'filament.widgets.topsis-podium-widget';

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $rankings = HasilTopsis::with('karyawan')
            ->orderBy('ranking')
            ->take(3)
            ->get();

        return [
            'rankings' => $rankings,
        ];
    }
}
