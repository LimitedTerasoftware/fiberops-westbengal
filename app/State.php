<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $primaryKey = 'state_id';
    
    protected $fillable = [
        'state_name'
    ];

    public function districts()
    {
        return $this->hasMany(District::class, 'state_id', 'state_id');
    }

    public function oltLocations()
    {
        return $this->hasMany(OltLocation::class, 'state_id', 'state_id');
    }
}
