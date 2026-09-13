<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kode', 'nama_kriteria', 'bobot', 'jenis'])]
class Kriteria extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'bobot' => 'float',
        ];
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'kriteria_id');
    }
}
