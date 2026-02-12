<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{

    protected $table = 'materials';

    protected $fillable = [
        'code',
        'name',
        'purchase_unit',
        'base_unit',
        'qty_per_purchase_unit',
        'has_serial',
        'description'
    ];

    protected $casts = [
        'has_serial' => 'boolean',
        'qty_per_purchase_unit' => 'decimal:3',
    ];

    //  helper to convert base quantity to display quantity
    public function convertToPurchaseUnit(float $baseQty)
    {
        $full = floor($baseQty / $this->qty_per_purchase_unit);
        $left = $baseQty - ($full * $this->qty_per_purchase_unit);

        return [
            'full_units' => $full,
            'leftover' => $left
        ];
    }

    public function serials() {
        return $this->hasMany(MaterialSerial::class);
    }

    public function stockBalance() {
        return $this->hasMany(StockBalance::class);
    }

    public function transactions() {
        return $this->hasMany(StockTransaction::class);
    }

}
