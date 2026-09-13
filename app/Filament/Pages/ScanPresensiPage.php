<?php

namespace App\Filament\Pages;

use App\Models\Karyawan;
use App\Models\Presensi;
use App\Services\DynamicQrService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ScanPresensiPage extends Page
{
    protected string $view = 'filament.pages.scan-presensi-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;

    protected static UnitEnum|string|null $navigationGroup = 'Presensi QR';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Scan Presensi QR';

    protected static ?string $navigationLabel = 'Scan Presensi';

    public ?int $selectedKaryawanId = null;

    public string $manualToken = '';

    public ?array $todayPresensi = null;

    public function mount(): void
    {
        $user = Auth::user();
        if ($user && $user->karyawan) {
            $this->selectedKaryawanId = $user->karyawan->id;
        } else {
            // Admin testing: default to first employee
            $first = Karyawan::first();
            $this->selectedKaryawanId = $first?->id;
        }

        $this->loadTodayPresensi();
    }

    public function loadTodayPresensi(): void
    {
        if (! $this->selectedKaryawanId) {
            $this->todayPresensi = null;

            return;
        }

        $p = Presensi::where('karyawan_id', $this->selectedKaryawanId)
            ->whereDate('tanggal', Carbon::today())
            ->first();

        if ($p) {
            $this->todayPresensi = [
                'tanggal' => $p->tanggal->format('d M Y'),
                'jam_masuk' => $p->jam_masuk ? substr($p->jam_masuk, 0, 5) : '-',
                'jam_keluar' => $p->jam_keluar ? substr($p->jam_keluar, 0, 5) : 'Belum Pulang',
                'status' => $p->status,
                'metode' => $p->metode,
                'catatan' => $p->catatan,
            ];
        } else {
            $this->todayPresensi = null;
        }
    }

    public function processScan(string $token, DynamicQrService $qrService): void
    {
        if (! $this->selectedKaryawanId) {
            Notification::make()
                ->title('Karyawan belum dipilih')
                ->danger()
                ->send();

            return;
        }

        $karyawan = Karyawan::find($this->selectedKaryawanId);
        if (! $karyawan) {
            Notification::make()
                ->title('Data karyawan tidak ditemukan')
                ->danger()
                ->send();

            return;
        }

        $result = $qrService->recordAttendance($karyawan, trim($token));

        if ($result['success']) {
            Notification::make()
                ->title($result['type'] === 'check_in' ? 'Presensi Masuk Berhasil!' : 'Presensi Pulang Berhasil!')
                ->body($result['message'])
                ->success()
                ->send();

            $this->loadTodayPresensi();
            $this->manualToken = '';
        } else {
            Notification::make()
                ->title('Presensi Gagal')
                ->body($result['message'])
                ->danger()
                ->send();
        }
    }

    public function submitManual(DynamicQrService $qrService): void
    {
        $this->validate([
            'manualToken' => 'required|string',
        ]);

        $this->processScan($this->manualToken, $qrService);
    }
}
