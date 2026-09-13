<?php

namespace App\Filament\Resources\HasilTopses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HasilTopsisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('karyawan_id')
                    ->relationship('karyawan', 'id')
                    ->required(),
                TextInput::make('periode')
                    ->required()
                    ->default('2026-09'),
                TextInput::make('d_plus')
                    ->numeric(),
                TextInput::make('d_minus')
                    ->numeric(),
                TextInput::make('nilai_preferensi')
                    ->required()
                    ->numeric(),
                TextInput::make('ranking')
                    ->required()
                    ->numeric(),
                TextInput::make('keterangan'),
            ]);
    }
}
