<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialSerial extends Model
{

    protected $table = 'material_serials';

    protected $fillable = [
        'state_id',
        'district_id',
        'material_id',
        'transaction_id',
        'serial_number',
        'received_quantity',
        'quantity',
        'qtystatus',
        'lifecycle_status',
        'remarks',
        'status'
        ];

	public function material() {
        return $this->belongsTo(Material::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }
    public function district() {
        return $this->belongsTo(District::class);
    }
    public function allocations()
    {
        return $this->hasMany(Material_serial_Allocations::class, 'material_serial_id');
    }
    public function stockInTransaction()
    {
        return $this->belongsTo(StockTransaction::class, 'transaction_id');
    }




}
