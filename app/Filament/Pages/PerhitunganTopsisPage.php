<?php

namespace App\Filament\Pages;

use App\Services\TopsisService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
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

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->persistTab()
                    ->tabs([
                        Tab::make('1. Perangkingan Akhir (Vi)')
                            ->icon(Heroicon::Trophy)
                            ->schema([
                                View::make('filament.pages.topsis.hasil-akhir')
                                    ->viewData(fn (): array => ['topsisResult' => $this->topsisResult]),
                            ]),
                        Tab::make('2. Matriks Keputusan (X)')
                            ->icon(Heroicon::TableCells)
                            ->schema([
                                View::make('filament.pages.topsis.matriks-x')
                                    ->viewData(fn (): array => ['topsisResult' => $this->topsisResult]),
                            ]),
                        Tab::make('3. Normalisasi Matriks (R)')
                            ->icon(Heroicon::Calculator)
                            ->schema([
                                View::make('filament.pages.topsis.matriks-r')
                                    ->viewData(fn (): array => ['topsisResult' => $this->topsisResult]),
                            ]),
                        Tab::make('4. Matriks Terbobot (Y)')
                            ->icon(Heroicon::Scale)
                            ->schema([
                                View::make('filament.pages.topsis.matriks-y')
                                    ->viewData(fn (): array => ['topsisResult' => $this->topsisResult]),
                            ]),
                        Tab::make('5. Solusi Ideal (A+ / A-)')
                            ->icon(Heroicon::Sparkles)
                            ->schema([
                                View::make('filament.pages.topsis.solusi-ideal')
                                    ->viewData(fn (): array => ['topsisResult' => $this->topsisResult]),
                            ]),
                        Tab::make('6. Jarak Euclidean (D+ / D-)')
                            ->icon(Heroicon::ArrowsRightLeft)
                            ->schema([
                                View::make('filament.pages.topsis.jarak-ideal')
                                    ->viewData(fn (): array => ['topsisResult' => $this->topsisResult]),
                            ]),
                    ]),
            ]);
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
