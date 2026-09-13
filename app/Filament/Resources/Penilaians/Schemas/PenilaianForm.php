<?php

namespace App\Filament\Resources\Penilaians\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PenilaianForm
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
                Select::make('kriteria_id')
                    ->label('Kriteria Penilaian')
                    ->relationship('kriteria', 'nama_kriteria')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('periode')
                    ->label('Periode Penilaian')
                    ->placeholder('Contoh: 2026-09')
                    ->default('2026-09')
                    ->required(),
                TextInput::make('nilai')
                    ->label('Nilai Kinerja (0 - 100)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->placeholder('Contoh: 85')
                    ->required(),
            ]);
    }
}
