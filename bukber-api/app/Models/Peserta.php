<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Peserta extends Model
{
    use HasFactory;

    protected $table = 'peserta';

    protected $fillable = [
        'uuid',
        'nama_lengkap',
        'minggu',
        'budget_per_orang',
        'catatan',
        'device_fingerprint',
        'ip_address',
    ];

    protected $casts = [
        'minggu' => 'integer',
        'budget_per_orang' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function hari(): HasMany
    {
        return $this->hasMany(PesertaHari::class, 'peserta_id');
    }

    public function lokasi(): HasOne
    {
        return $this->hasOne(Lokasi::class, 'peserta_id');
    }
}
