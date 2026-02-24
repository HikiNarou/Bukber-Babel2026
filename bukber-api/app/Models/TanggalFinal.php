<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TanggalFinal extends Model
{
    use HasFactory;

    protected $table = 'tanggal_final';

    protected $fillable = [
        'tanggal',
        'jam',
        'lokasi_id',
        'catatan',
        'is_locked',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'jam' => 'string',
        'is_locked' => 'boolean',
    ];

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }
}
