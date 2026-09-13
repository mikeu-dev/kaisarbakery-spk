<?php

namespace App\Filament\Resources\Kriterias\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KriteriaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode')
                    ->label('Kode Kriteria')
                    ->placeholder('Contoh: C1, C2')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('nama_kriteria')
                    ->label('Nama Kriteria Penilaian')
                    ->placeholder('Contoh: Kehadiran, Ketepatan Waktu')
                    ->required(),
                TextInput::make('bobot')
                    ->label('Bobot (0 - 1.0)')
                    ->numeric()
                    ->step(0.01)
                    ->helperText('Contoh: 0.25 (Total seluruh kriteria harus = 1.00)')
                    ->required(),
                Select::make('jenis')
                    ->label('Jenis Kriteria')
                    ->options([
                        'benefit' => 'Benefit (Semakin besar semakin baik)',
                        'cost' => 'Cost (Semakin kecil semakin baik)',
                    ])
                    ->default('benefit')
                    ->required(),
            ]);
    }
}
