<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OltUptime extends Model
{
    protected $table = 'olt_uptime';

    protected $fillable = [
        'lgd_code',
        'uptime_percent',
        'record_date',
    ];

    protected $casts = [
        'record_date' => 'date',
        'uptime_percent' => 'decimal:2',
    ];
}
