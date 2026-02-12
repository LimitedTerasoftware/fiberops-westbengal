<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{

    protected $table = 'stock_transactions';

    protected $fillable = [
        'state_id',
        'district_id',
        'material_id',
        'employee_id',
        'transaction_type',
        'reference_type',
        'reference_id',
        'remarks',
        'quantity'
   ];

	public function material() {
    return $this->belongsTo(Material::class);
    }
    
    public function employee()
    {
        return $this->belongsTo(Provider::class, 'employee_id', 'id');
    }



    public function serial() {

        return $this->hasMany(MaterialSerial::class, 'transaction_id');

    }
   public function serialAllocations()
    {
        return $this->hasMany(Material_serial_Allocations::class, 'stock_transaction_id');
    }


    public function district() {
        return $this->belongsTo(District::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }


}
