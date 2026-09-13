<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'karyawan_id',
    'periode',
    'd_plus',
    'd_minus',
    'nilai_preferensi',
    'ranking',
    'keterangan',
])]
class HasilTopsis extends Model
{
    use HasFactory;

    protected $table = 'hasil_topsises';

    protected function casts(): array
    {
        return [
            'd_plus' => 'float',
            'd_minus' => 'float',
            'nilai_preferensi' => 'float',
            'ranking' => 'integer',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
