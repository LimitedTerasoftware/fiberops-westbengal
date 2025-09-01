<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OntUptime extends Model
{
    protected $table = 'ont_uptime';

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
