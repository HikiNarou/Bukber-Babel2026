<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lengkap',
        'weeks',
        'days',
        'budget',
        'status',
    ];

    protected $casts = [
        'weeks' => 'array',
        'days'  => 'array',
    ];
}
