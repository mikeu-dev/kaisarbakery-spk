<?php

namespace App\Filament\Resources\HasilTopses;

use App\Filament\Resources\HasilTopses\Pages\CreateHasilTopsis;
use App\Filament\Resources\HasilTopses\Pages\EditHasilTopsis;
use App\Filament\Resources\HasilTopses\Pages\ListHasilTopses;
use App\Filament\Resources\HasilTopses\Schemas\HasilTopsisForm;
use App\Filament\Resources\HasilTopses\Tables\HasilTopsesTable;
use App\Models\HasilTopsis;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HasilTopsisResource extends Resource
{
    protected static ?string $model = HasilTopsis::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static UnitEnum|string|null $navigationGroup = 'SPK TOPSIS';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Hasil Perangkingan';

    protected static ?string $pluralModelLabel = 'Hasil Akhir TOPSIS';

    public static function form(Schema $schema): Schema
    {
        return HasilTopsisForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HasilTopsesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHasilTopses::route('/'),
            'create' => CreateHasilTopsis::route('/create'),
            'edit' => EditHasilTopsis::route('/{record}/edit'),
        ];
    }
}
