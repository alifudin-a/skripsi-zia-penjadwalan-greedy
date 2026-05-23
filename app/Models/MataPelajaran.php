<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataPelajaran extends Model
{
    protected $fillable = [
        'kode',
        'nama',
        'total_jp',
        'maksimal_jp_per_sesi',
    ];

    /**
     * Relasi guru yang mengajar mapel ini
     */
    public function gurus(): BelongsToMany
    {
        return $this->belongsToMany(
            Guru::class,
            'guru_mata_pelajaran'
        );
    }

    /**
     * Relasi jadwal
     */
    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }
}