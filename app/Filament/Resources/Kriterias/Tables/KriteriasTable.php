<?php

namespace App\Filament\Resources\Kriterias\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KriteriasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode')
                    ->label('Kode')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_kriteria')
                    ->label('Nama Kriteria')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('bobot')
                    ->label('Bobot')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2).' ('.((float) $state * 100).'%)')
                    ->sortable(),
                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'benefit' => 'success',
                        'cost' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
            ])
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
