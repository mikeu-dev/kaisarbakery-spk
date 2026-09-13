<?php

namespace App\Filament\Resources\Presensis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class PresensiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('karyawan_id')
                    ->label('Karyawan')
                    ->relationship('karyawan', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('tanggal')
                    ->label('Tanggal Presensi')
                    ->default(now())
                    ->required(),
                TimePicker::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->seconds(false),
                TimePicker::make('jam_keluar')
                    ->label('Jam Keluar')
                    ->seconds(false),
                Select::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpha (Tanpa Keterangan)',
                    ])
                    ->default('hadir')
                    ->required(),
                Select::make('metode')
                    ->label('Metode Presensi')
                    ->options([
                        'qr_dinamis' => 'QR Code Dinamis',
                        'manual' => 'Manual (Admin)',
                    ])
                    ->default('qr_dinamis')
                    ->required(),
                Textarea::make('catatan')
                    ->label('Keterangan / Catatan')
                    ->columnSpanFull(),
            ]);
    }
}
