<?php

namespace App\Filament\Pages;

use App\Services\TopsisService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class PerhitunganTopsisPage extends Page
{
    protected string $view = 'filament.pages.perhitungan-topsis-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static UnitEnum|string|null $navigationGroup = 'SPK TOPSIS';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Kalkulasi & Matriks TOPSIS';

    protected static ?string $navigationLabel = 'Kalkulasi TOPSIS';

    public string $periode = '2026-09';

    public string $activeTab = 'hasil_akhir';

    public array $topsisResult = [];

    public function mount(TopsisService $topsisService): void
    {
        $this->loadCalculation($topsisService);
    }

    public function updatedPeriode(TopsisService $topsisService): void
    {
        $this->loadCalculation($topsisService);
    }

    public function loadCalculation(TopsisService $topsisService): void
    {
        $this->topsisResult = $topsisService->calculate($this->periode);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('hitungUlang')
                ->label('Hitung Ulang & Simpan')
                ->icon('heroicon-m-arrow-path')
                ->color('primary')
                ->action(function (TopsisService $topsisService) {
                    $this->loadCalculation($topsisService);
                    if (! empty($this->topsisResult['rankings'])) {
                        $topsisService->saveResults($this->periode, $this->topsisResult['rankings']);
                        Notification::make()
                            ->title('Perhitungan Berhasil Disimpan!')
                            ->body("Hasil perangkingan periode {$this->periode} telah diperbarui di basis data.")
                            ->success()
                            ->send();
                    }
                }),

            Action::make('cetakLaporan')
                ->label('Cetak Laporan')
                ->icon('heroicon-m-printer')
                ->color('gray')
                ->extraAttributes(['onclick' => 'window.print()']),
        ];
    }
}
