<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'state_id',
        'name'
        
    ];
    
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }

    public function oltLocations()
    {
        return $this->hasMany(OltLocation::class);
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
         'created_at'
    ];
	
	
}
