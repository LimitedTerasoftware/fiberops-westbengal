<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeMaterialLedger extends Model
{
    protected $table = 'employee_material_ledger';

    protected $fillable = [
        // External references
        'request_id',
        'issued_item_id',
        'indent_no',

        // Employee & location
        'employee_id',
        'state_id',
        'district_id',

        // Material
        'material_id',
        'material_code',

        // Serial handling
        'has_serial',
        'serial_number',
        'replaced_serial_number',

        // Transaction
        'transaction_type',
        'quantity',

        // Ticket / usage
        'ticket_id',

        // Dates
        'issue_date',
    ];

    protected $casts = [
        'has_serial'  => 'boolean',
        'quantity'    => 'decimal:3',
        'issue_date'  => 'datetime',
    ];


     // Employee (Provider)
     
    public function employee()
    {
        return $this->belongsTo(
            \App\Provider::class,
            'employee_id'
        );
    }

    
    // Material master
    
    public function material()
    {
        return $this->belongsTo(
            \App\Material::class,
            'material_id'
        );
    }

  
    public function district()
    {
        return $this->belongsTo(
            \App\District::class,
            'district_id'
        );
    }

  
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');

    }

    
    public function scopeIssue($query)
    {
        return $query->where('transaction_type', 'ISSUE');
    }


    public function scopeUsed($query)
    {
        return $query->where('transaction_type', 'USED');
    }

   
    public function scopeSerial($query)
    {
        return $query->where('has_serial', 1);
    }

 
    public function scopeNonSerial($query)
    {
        return $query->where('has_serial', 0);
    }
}
