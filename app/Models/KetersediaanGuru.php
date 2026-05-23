<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KetersediaanGuru extends Model
{
    protected $table = 'ketersediaan_gurus';

    protected $fillable = [
        'guru_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }
}