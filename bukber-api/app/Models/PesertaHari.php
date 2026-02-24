<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesertaHari extends Model
{
    use HasFactory;

    public const MINGGU_LIST = [1, 2, 3, 4];
    public const HARI_LIST = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

    protected $table = 'peserta_hari';

    public $timestamps = false;

    protected $fillable = [
        'peserta_id',
        'minggu',
        'hari',
    ];

    protected $casts = [
        'minggu' => 'integer',
    ];

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }
}
