<?php

namespace App\Filament\Pages;

use App\Services\DynamicQrService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class GenerateQrPage extends Page
{
    protected string $view = 'filament.pages.generate-qr-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static UnitEnum|string|null $navigationGroup = 'Presensi QR';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Generate Dynamic QR Code';

    protected static ?string $navigationLabel = 'Generate QR Dinamis';

    public string $token = '';

    public string $qrSvg = '';

    public int $secondsRemaining = 60;

    public string $lastUpdated = '';

    public function mount(DynamicQrService $qrService): void
    {
        $this->updateQr($qrService);
    }

    public function refreshQr(DynamicQrService $qrService): void
    {
        $this->updateQr($qrService);
    }

    protected function updateQr(DynamicQrService $qrService): void
    {
        $now = time();
        $window = $qrService->getCurrentWindow($now);
        $this->token = $qrService->generateToken($window);
        $this->qrSvg = $qrService->generateQrCodeSvg($this->token);

        $elapsedInWindow = $now % DynamicQrService::WINDOW_SECONDS;
        $this->secondsRemaining = DynamicQrService::WINDOW_SECONDS - $elapsedInWindow;
        $this->lastUpdated = Carbon::now()->format('H:i:s');
    }
}
