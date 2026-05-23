<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laboratorium extends Model
{
    protected $table = 'laboratoriums';

    protected $fillable = [
        'nama',
        'kapasitas',
        'lokasi',
    ];

    /**
     * Relasi jadwal laboratorium
     */
    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }
}