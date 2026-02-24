<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSetting extends Model
{
    use HasFactory;

    protected $table = 'event_settings';

    protected $fillable = [
        'nama_event',
        'deadline_registrasi',
        'deadline_voting',
        'is_registration_open',
        'is_voting_open',
    ];

    protected $casts = [
        'deadline_registrasi' => 'datetime',
        'deadline_voting' => 'datetime',
        'is_registration_open' => 'boolean',
        'is_voting_open' => 'boolean',
    ];

    public static function singleton(): self
    {
        return self::query()->firstOrCreate(
            ['id' => 1],
            [
                'nama_event' => 'Bukber Magang BSB',
                'is_registration_open' => true,
                'is_voting_open' => false,
            ]
        );
    }
}
