<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['user_id', 'kode_karyawan', 'nama', 'jabatan', 'no_hp'])]
class Karyawan extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class, 'karyawan_id');
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'karyawan_id');
    }

    public function hasilTopsis(): HasOne
    {
        return $this->hasOne(HasilTopsis::class, 'karyawan_id');
    }

    public function hasilTopsises(): HasMany
    {
        return $this->hasMany(HasilTopsis::class, 'karyawan_id');
    }
}
