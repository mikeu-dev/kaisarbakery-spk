<?php

namespace App\Filament\Resources\Karyawans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KaryawanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_karyawan')
                    ->label('Kode Alternatif')
                    ->placeholder('Contoh: A1, A2, A3')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('nama')
                    ->label('Nama Lengkap')
                    ->placeholder('Nama karyawan')
                    ->required(),
                TextInput::make('jabatan')
                    ->label('Jabatan')
                    ->placeholder('Contoh: Baker, Kasir, Pastry Chef')
                    ->required(),
                TextInput::make('no_hp')
                    ->label('No. Handphone / WhatsApp')
                    ->placeholder('Contoh: 081234567890')
                    ->tel(),
                Select::make('user_id')
                    ->label('Akun Pengguna (Login)')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }
}
