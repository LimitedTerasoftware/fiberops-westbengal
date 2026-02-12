<?php
namespace App;


use Illuminate\Database\Eloquent\Model;

class SubServiceType extends Model
{
    

    protected $table = 'sub_service_types';

    protected $fillable = ['service_type_id', 'name'];
}
