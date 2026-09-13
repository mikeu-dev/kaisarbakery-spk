<?php

namespace App\Filament\Resources\Penilaians\Tables;

use App\Models\Penilaian;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PenilaiansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('karyawan.nama')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('kriteria.kode')
                    ->label('Kode')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                TextColumn::make('kriteria.nama_kriteria')
                    ->label('Kriteria Penilaian')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('periode')
                    ->label('Periode')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('nilai')
                    ->label('Nilai')
                    ->badge()
                    ->color(fn ($state) => $state >= 85 ? 'success' : ($state >= 75 ? 'warning' : 'danger'))
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('karyawan_id')
            ->filters([
                SelectFilter::make('periode')
                    ->label('Periode')
                    ->options(fn () => Penilaian::distinct()->pluck('periode', 'periode')->toArray()),
                SelectFilter::make('kriteria_id')
                    ->label('Kriteria')
                    ->relationship('kriteria', 'nama_kriteria'),
                SelectFilter::make('karyawan_id')
                    ->label('Karyawan')
                    ->relationship('karyawan', 'nama'),
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
