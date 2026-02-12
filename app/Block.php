<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','district_id'
        
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
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
         'created_at','updated_at'
    ];
	
	
}
