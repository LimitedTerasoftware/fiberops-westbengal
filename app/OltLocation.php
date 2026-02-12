<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OltLocation extends Model
{
    protected $table = 'olt_locations';

     protected $fillable = [
         'state_id',
        'district_id',
        'block_id',
        'olt_location',
        'olt_location_code',
        'lgd_code',
        'olt_ip',
        'no_of_gps'
    ];
  

    protected $casts = [
        'no_of_gps' => 'integer',
    ];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

}
