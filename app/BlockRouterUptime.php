<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlockRouterUptime extends Model
{
    protected $table = 'block_router_uptime';

    protected $fillable = [
        'lgd_code',
        'uptime_percent',
        'record_date',
        'reason',
        'rca',
    ];

    protected $casts = [
        'record_date' => 'date',
        'uptime_percent' => 'decimal:2',
    ];
}