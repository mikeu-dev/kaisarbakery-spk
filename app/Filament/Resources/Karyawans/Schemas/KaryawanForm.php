<?php

namespace App\Filament\Resources\Karyawans\Schemas;

use App\Models\User;
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
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('Contoh: Budi Santoso')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('username')
                            ->label('Username')
                            ->placeholder('Contoh: budi')
                            ->unique(User::class, 'username')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->placeholder('Contoh: budi@kaisarbakery.com')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email')
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->placeholder('Minimal 8 karakter')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8),
                        Select::make('role')
                            ->label('Peran (Role)')
                            ->options([
                                User::ROLE_KARYAWAN => 'Karyawan',
                                User::ROLE_ADMIN => 'Admin',
                            ])
                            ->default(User::ROLE_KARYAWAN)
                            ->required(),
                    ])
                    ->createOptionModalHeading('Tambah Akun Pengguna Baru')
                    ->editOptionForm([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('username')
                            ->label('Username')
                            ->unique(User::class, 'username', ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Kata Sandi (Kosongkan jika tidak diubah)')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->minLength(8),
                        Select::make('role')
                            ->label('Peran (Role)')
                            ->options([
                                User::ROLE_KARYAWAN => 'Karyawan',
                                User::ROLE_ADMIN => 'Admin',
                            ])
                            ->required(),
                    ])
                    ->editOptionModalHeading('Ubah Akun Pengguna'),
            ]);
    }
}
