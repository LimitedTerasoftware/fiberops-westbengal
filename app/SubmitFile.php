<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubmitFile extends Model
{
	/**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $table = 'submitfiles';
    
    protected $fillable = [
        'provider_id', 'ticket_id','request_id','category', 'subcategory', 'description', 'before_image', 'after_image','materials',
        'otdr_img',
        'joint_enclouser_beforeimg',
        'joint_enclouser_afterimg',
        'issue_type',
        'construction_type',
        'fiber_type',
        'consttype_restoration',
        'joint_enclosurebefore_latlong',
        'joint_enclosureafter_latlong',
        'issues',
        'before_img_latlong','after_img_latlong','otdr_img_latlong','video',

    ];

  

   

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at'
    ];

}
