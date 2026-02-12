<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpStockBalance extends Model
{

    protected $table = 'employee_stock_balance';

    protected $fillable = [
        'state_id',
        'district_id',
        'material_id',
        'employee_id',
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
