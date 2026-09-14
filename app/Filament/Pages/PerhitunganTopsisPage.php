<?php

namespace App\Filament\Pages;

use App\Services\TopsisService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;
use UnitEnum;

class PerhitunganTopsisPage extends Page
{
    protected string $view = 'filament.pages.perhitungan-topsis-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static UnitEnum|string|null $navigationGroup = 'SPK TOPSIS';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Kalkulasi & Matriks TOPSIS';

    protected static ?string $navigationLabel = 'Kalkulasi TOPSIS';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public string $periode = '2026-09';

    #[Url]
    public string $activeTab = 'hasil_akhir';

    public array $topsisResult = [];

    /**
     * @return array<NavigationItem>
     */
    public function getSubNavigation(): array
    {
        return [
            NavigationItem::make('1. Perangkingan Akhir (Vi)')
                ->icon('heroicon-m-trophy')
                ->isActiveWhen(fn (): bool => $this->activeTab === 'hasil_akhir')
                ->extraAttributes(['wire:click' => "\$set('activeTab', 'hasil_akhir')"]),

            NavigationItem::make('2. Matriks Keputusan (X)')
                ->icon('heroicon-m-table-cells')
                ->isActiveWhen(fn (): bool => $this->activeTab === 'matriks_x')
                ->extraAttributes(['wire:click' => "\$set('activeTab', 'matriks_x')"]),

            NavigationItem::make('3. Normalisasi Matriks (R)')
                ->icon('heroicon-m-calculator')
                ->isActiveWhen(fn (): bool => $this->activeTab === 'matriks_r')
                ->extraAttributes(['wire:click' => "\$set('activeTab', 'matriks_r')"]),

            NavigationItem::make('4. Matriks Terbobot (Y)')
                ->icon('heroicon-m-scale')
                ->isActiveWhen(fn (): bool => $this->activeTab === 'matriks_y')
                ->extraAttributes(['wire:click' => "\$set('activeTab', 'matriks_y')"]),

            NavigationItem::make('5. Solusi Ideal (A+ / A-)')
                ->icon('heroicon-m-sparkles')
                ->isActiveWhen(fn (): bool => $this->activeTab === 'solusi_ideal')
                ->extraAttributes(['wire:click' => "\$set('activeTab', 'solusi_ideal')"]),

            NavigationItem::make('6. Jarak Euclidean (D+ / D-)')
                ->icon('heroicon-m-arrows-right-left')
                ->isActiveWhen(fn (): bool => $this->activeTab === 'jarak_ideal')
                ->extraAttributes(['wire:click' => "\$set('activeTab', 'jarak_ideal')"]),
        ];
    }

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
