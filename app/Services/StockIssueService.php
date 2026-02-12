<?php

namespace App\Services;

use DB;
use Exception;
use Carbon\Carbon;
use App\StockBalance;
use App\StockTransaction;
use App\EmpStockBalance;
use App\EmpStockTransaction;
use App\MaterialSerial;
use App\Material_serial_Allocations;

class StockIssueService
{
    /**
     * Allocate serials during ISSUE
     */
    public function allocateSerials(
        int $materialId,
        int $employeeId,
        array $serialsData,
        int $transactionId
    ) {
        $serialIds = array_keys($serialsData);

        $serials = MaterialSerial::whereIn('id', $serialIds)
            ->where('material_id', $materialId)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($serialsData as $serialId => $qty) {

            if (!isset($serials[$serialId])) {
                throw new Exception("Serial {$serialId} not found");
            }

            $serial = $serials[$serialId];

            if ($qty > $serial->quantity) {
                throw new Exception(
                    "Serial {$serial->serial_number} has insufficient quantity"
                );
            }

            $serial->quantity -= $qty;
            $serial->qtystatus = $serial->quantity > 0
                ? 'PARTIALLY_ISSUED'
                : 'FULLY_ISSUED';

            $serial->status = 'ISSUED';
            $serial->save();

            Material_serial_Allocations::create([
                'stock_transaction_id' => $transactionId,
                'material_serial_id'   => $serialId,
                'employee_id'          => $employeeId,
                'quantity'             => $qty,
                'transaction_type'=>'ISSUE',
                'remarks'              => 'Issued via transaction #' . $transactionId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }

    /**
     * Restore stock + serials when ISSUE is deleted
     */
 

    public function rollbackIssue(StockTransaction $transaction)
{
    if ($transaction->transaction_type !== 'ISSUE') {
        throw new \Exception('Only ISSUE transactions can be rolled back');
    }

    /** Restore district stock */
    StockBalance::where([
        'state_id'    => $transaction->state_id,
        'district_id' => $transaction->district_id,
        'material_id' => $transaction->material_id,
    ])->lockForUpdate()->increment('quantity', $transaction->quantity);

    /** Reduce employee stock */
    EmpStockBalance::where([
        'state_id'    => $transaction->state_id,
        'district_id' => $transaction->district_id,
        'material_id' => $transaction->material_id,
        'employee_id' => $transaction->employee_id,
    ])->lockForUpdate()->decrement('quantity', $transaction->quantity);

    /** Restore serials correctly */
    foreach ($transaction->serialAllocations as $alloc) {

        $serial = $alloc->serial;

        $issuedQty = $serial->allocations()
            ->where('stock_transaction_id', '!=', $transaction->id)
            ->sum('quantity');

        if ($issuedQty > $serial->received_quantity) {
            throw new \Exception(
                "Serial {$serial->serial_number} quantity mismatch"
            );
        }

        $serial->quantity = $serial->received_quantity - $issuedQty;

        if ($issuedQty == 0) {
            $serial->qtystatus = 'AVAILABLE';
            $serial->status    = 'IN_STOCK';
        } elseif ($issuedQty < $serial->received_quantity) {
            $serial->qtystatus = 'PARTIALLY_ISSUED';
            $serial->status    = 'ISSUED';
        } else {
            $serial->qtystatus = 'FULLY_ISSUED';
            $serial->status    = 'ISSUED';
        }

        $serial->save();
    }

    /** Delete serial allocations */
    Material_serial_Allocations::where(
        'stock_transaction_id',
        $transaction->id
    )->delete();

    /** Delete FIFO employee stock links */
    EmpStockTransaction::where(
        'stock_transaction_id',
        $transaction->id
    )->delete();

    /** Delete main transaction */
    $transaction->delete();
}

}
