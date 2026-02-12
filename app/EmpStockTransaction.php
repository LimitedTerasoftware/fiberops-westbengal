<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpStockTransaction extends Model
{
    protected $table = 'employee_stock_transactions';

    protected $fillable = [
        'state_id',
        'district_id',
        'material_id',
        'employee_id',
        'transaction_type',
        'stock_transaction_id',
        'in_transaction_id',
        'reference_transaction_id',
        'ticket_id',
        'remarks',
        'quantity'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function allocations()
    {
        return $this->hasMany(
            Material_serial_Allocations::class,
            'stock_transaction_id',
            'stock_transaction_id'
        );
    }

    public function stockTransaction()
    {
        return $this->belongsTo(
            StockTransaction::class,
            'stock_transaction_id'
        );
    }
    public function inTransaction()
    {
        return $this->belongsTo(
            StockTransaction::class,
            'in_transaction_id'
        );
    }
   public function reference()
    {
        return $this->belongsTo(
            EmpStockTransaction::class,
            'reference_transaction_id'
        );
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }
    public function employee()
    {
        return $this->belongsTo(Provider::class, 'employee_id', 'id');
    }

}

