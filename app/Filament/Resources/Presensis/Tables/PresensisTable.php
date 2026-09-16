<?php

namespace App\Filament\Resources\Presensis\Tables;

use App\Models\Karyawan;
use App\Services\PresensiExportService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PresensisTable
{
    public static function configure(Table $table): Table
    {
        $currentYear = (int) now()->format('Y');
        $years = array_combine(
            range($currentYear - 3, $currentYear + 2),
            range($currentYear - 3, $currentYear + 2)
        );

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('karyawan.nama')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('karyawan.jabatan')
                    ->label('Jabatan')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('jam_keluar')
                    ->label('Jam Pulang')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Belum Pulang')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'hadir' => 'success',
                        'izin' => 'info',
                        'sakit' => 'warning',
                        'alpha' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('metode')
                    ->label('Metode')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn (string $state): string => $state === 'qr_dinamis' ? 'QR Dinamis' : 'Manual'),
            ])
            ->defaultSort('tanggal', 'desc')
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Export Excel')
                    ->icon(Heroicon::ArrowDownTray)
                    ->color('success')
                    ->modalHeading('Export Data Presensi ke Excel')
                    ->modalDescription('Pilih periode dan filter data presensi yang ingin diexport ke format Excel (.xlsx).')
                    ->modalSubmitActionLabel('Download Excel')
                    ->schema([
                        Select::make('tipe_periode')
                            ->label('Pilihan Periode')
                            ->options([
                                'harian' => 'Per Hari (Harian)',
                                'mingguan' => 'Per Minggu (7 Hari)',
                                'bulanan' => 'Per Bulan (Bulanan)',
                                'rentang_bulan' => 'Rentang Bulan',
                                'tahunan' => 'Per Tahun (Tahunan)',
                            ])
                            ->default('bulanan')
                            ->live()
                            ->required(),

                        // Per Hari
                        DatePicker::make('tanggal')
                            ->label('Pilih Tanggal')
                            ->default(now()->format('Y-m-d'))
                            ->visible(fn (Get $get): bool => $get('tipe_periode') === 'harian')
                            ->required(fn (Get $get): bool => $get('tipe_periode') === 'harian'),

                        // Per Minggu
                        DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai (Awal Minggu)')
                            ->default(now()->startOfWeek()->format('Y-m-d'))
                            ->visible(fn (Get $get): bool => $get('tipe_periode') === 'mingguan')
                            ->required(fn (Get $get): bool => $get('tipe_periode') === 'mingguan'),
                        DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai (Akhir Minggu)')
                            ->default(now()->endOfWeek()->format('Y-m-d'))
                            ->visible(fn (Get $get): bool => $get('tipe_periode') === 'mingguan')
                            ->required(fn (Get $get): bool => $get('tipe_periode') === 'mingguan'),

                        // Per Bulan
                        Select::make('bulan')
                            ->label('Pilih Bulan')
                            ->options($months)
                            ->default((int) now()->format('n'))
                            ->visible(fn (Get $get): bool => $get('tipe_periode') === 'bulanan')
                            ->required(fn (Get $get): bool => $get('tipe_periode') === 'bulanan'),
                        Select::make('tahun')
                            ->label('Pilih Tahun')
                            ->options($years)
                            ->default($currentYear)
                            ->visible(fn (Get $get): bool => $get('tipe_periode') === 'bulanan')
                            ->required(fn (Get $get): bool => $get('tipe_periode') === 'bulanan'),

                        // Rentang Bulan
                        Select::make('bulan_mulai')
                            ->label('Dari Bulan')
                            ->options($months)
                            ->default(1)
                            ->visible(fn (Get $get): bool => $get('tipe_periode') === 'rentang_bulan')
                            ->required(fn (Get $get): bool => $get('tipe_periode') === 'rentang_bulan'),
                        Select::make('bulan_selesai')
                            ->label('Sampai Bulan')
                            ->options($months)
                            ->default((int) now()->format('n'))
                            ->visible(fn (Get $get): bool => $get('tipe_periode') === 'rentang_bulan')
                            ->required(fn (Get $get): bool => $get('tipe_periode') === 'rentang_bulan'),
                        Select::make('tahun_rentang')
                            ->label('Tahun')
                            ->options($years)
                            ->default($currentYear)
                            ->visible(fn (Get $get): bool => $get('tipe_periode') === 'rentang_bulan')
                            ->required(fn (Get $get): bool => $get('tipe_periode') === 'rentang_bulan'),

                        // Per Tahun
                        Select::make('tahun_tunggal')
                            ->label('Pilih Tahun')
                            ->options($years)
                            ->default($currentYear)
                            ->visible(fn (Get $get): bool => $get('tipe_periode') === 'tahunan')
                            ->required(fn (Get $get): bool => $get('tipe_periode') === 'tahunan'),

                        // Filter Tambahan
                        Select::make('karyawan_id')
                            ->label('Filter Karyawan (Opsional)')
                            ->placeholder('Semua Karyawan')
                            ->options(fn (): array => Karyawan::orderBy('nama')->pluck('nama', 'id')->toArray())
                            ->searchable(),
                        Select::make('status')
                            ->label('Filter Status Kehadiran (Opsional)')
                            ->placeholder('Semua Status')
                            ->options([
                                'hadir' => 'Hadir',
                                'izin' => 'Izin',
                                'sakit' => 'Sakit',
                                'alpha' => 'Alpha',
                            ]),
                    ])
                    ->action(function (array $data, PresensiExportService $exportService) {
                        $records = $exportService->queryRecords($data);

                        if ($records->isEmpty()) {
                            Notification::make()
                                ->warning()
                                ->title('Data Tidak Ditemukan')
                                ->body('Tidak ada data presensi yang sesuai dengan periode dan filter yang dipilih.')
                                ->send();

                            return null;
                        }

                        return $exportService->downloadExcel($records, $data);
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpha',
                    ]),
                SelectFilter::make('karyawan_id')
                    ->label('Karyawan')
                    ->relationship('karyawan', 'nama')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
