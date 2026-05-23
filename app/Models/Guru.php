<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'no_hp',
        'alamat',
    ];

    /**
     * Relasi ke user login
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi mapel yang dikuasai guru
     */
    public function mataPelajarans(): BelongsToMany
    {
        return $this->belongsToMany(
            MataPelajaran::class,
            'guru_mata_pelajaran'
        );
    }

    /**
     * Relasi ketersediaan guru
     */
    public function ketersediaans(): HasMany
    {
        return $this->hasMany(KetersediaanGuru::class);
    }

    /**
     * Relasi jadwal guru
     */
    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }
}