<?php

namespace App\Filament\Resources\HasilTopses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HasilTopsesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ranking')
                    ->label('Peringkat')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        1 => 'warning',
                        2 => 'gray',
                        3 => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => "#{$state}")
                    ->sortable(),
                TextColumn::make('karyawan.kode_karyawan')
                    ->label('Kode')
                    ->badge()
                    ->color('primary')
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
                TextColumn::make('periode')
                    ->label('Periode')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('d_plus')
                    ->label('Jarak D+')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 5))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('d_minus')
                    ->label('Jarak D-')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 5))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nilai_preferensi')
                    ->label('Nilai Preferensi (Vi)')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 4))
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Kinerja Terbaik' => 'warning',
                        'Baik' => 'success',
                        'Cukup' => 'info',
                        default => 'danger',
                    }),
            ])
            ->defaultSort('ranking', 'asc')
            ->filters([
                //
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
