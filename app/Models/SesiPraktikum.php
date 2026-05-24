<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiPraktikum extends Model
{
    /**
     * Nama tabel
     */
    protected $table = 'sesi_praktikums';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'nama_sesi',
        'jam_mulai',
        'jam_selesai',
        'jumlah_jp',
    ];

    /**
     * Relasi ke jadwal
     *
     * 1 sesi praktikum
     * bisa dipakai banyak jadwal
     */
    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }
}