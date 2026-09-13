<?php

namespace App\Filament\Widgets;

use App\Models\HasilTopsis;
use App\Models\Karyawan;
use App\Models\Presensi;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KaisarOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalKaryawan = Karyawan::count();
        $hadirToday = Presensi::whereDate('tanggal', Carbon::today())
            ->where('status', 'hadir')
            ->count();

        $topKaryawan = HasilTopsis::where('ranking', 1)
            ->with('karyawan')
            ->first();

        $topName = $topKaryawan ? ($topKaryawan->karyawan->nama ?? 'Belum dihitung') : 'Belum dihitung';
        $topScore = $topKaryawan ? ('Skor: '.number_format($topKaryawan->nilai_preferensi, 4)) : 'Silakan hitung TOPSIS';

        return [
            Stat::make('Total Karyawan', $totalKaryawan)
                ->description('Karyawan aktif Kaisar Bakery')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Presensi Hari Ini', "{$hadirToday} / {$totalKaryawan}")
                ->description('Karyawan yang sudah hadir')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success'),

            Stat::make('Karyawan Terbaik', $topName)
                ->description($topScore)
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),
        ];
    }
}
