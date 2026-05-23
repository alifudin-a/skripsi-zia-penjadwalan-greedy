<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    protected $table = 'jadwals';

    protected $fillable = [
        'guru_id',
        'mata_pelajaran_id',
        'laboratorium_id',
        'kelas',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'status',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function laboratorium(): BelongsTo
    {
        return $this->belongsTo(Laboratorium::class);
    }
}