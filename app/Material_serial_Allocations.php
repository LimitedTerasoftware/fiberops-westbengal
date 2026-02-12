<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material_serial_Allocations extends Model
{

    protected $table = 'material_serial_allocations';

    protected $fillable = [
        'stock_transaction_id',
        'material_serial_id',
        'employee_id',
        'quantity',
        'remarks',
        'transaction_type',
        'ticket_id',
        'replacement_of_serial_id'
       ];

    public function serial()
    {
        return $this->belongsTo(MaterialSerial::class, 'material_serial_id');
    }
    public function transaction()
    {
        return $this->belongsTo(
            EmpStockTransaction::class,
            'stock_transaction_id'
        );
    }

    public function replacedSerial()
    {
        return $this->belongsTo(
            MaterialSerial::class,
            'replacement_of_serial_id'
        );
    }
	

}
