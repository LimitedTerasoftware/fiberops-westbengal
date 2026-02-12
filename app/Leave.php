<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leaves';
    protected $fillable = [
        'provider_id', 'start_date', 'end_date', 'reason', 'status','type'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}

