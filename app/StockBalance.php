<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockBalance extends Model
{

    protected $table = 'stock_balance';

    protected $fillable = [
        'state_id',
        'district_id',
        'material_id',
        'quantity'
        ];

	public function material() {
        return $this->belongsTo(Material::class);
    }

    public function district() {
        return $this->belongsTo(District::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }




   
}
