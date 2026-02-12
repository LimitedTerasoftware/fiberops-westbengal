<?php

namespace App\Http\Controllers\InventoryMng;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

use Exception;
use Session;
use Auth;
use \Carbon\Carbon;
use App\District;
use App\Provider;
use App\Material;
use App\StockBalance;
use App\StockTransaction;
use App\MaterialSerial;
use App\EmpStockBalance;
use App\Material_serial_Allocations;
use App\Services\StockIssueService;
use App\EmpStockTransaction;
use App\EmployeeMaterialLedger;



class StockIssueController extends Controller
{
    /**
     * Display the stock issue form
     */
    public function create()
    {
        try {
            Session::put('user', Auth::User());
            $user = Session::get('user');
            $state_id = $user->state_id;
            $district_id = $user->district_id;
            $materials = Material::all();
            $Query = District::where('state_id',$state_id);
            if (!empty($district_id)) {
                $Query->where('district_id', $district_id);
            }
            $district = $Query->get();
            $materials->each(function($material) use ($state_id, $district_id) {
                    $query = StockBalance::where('material_id', $material->id)
                                        ->where('state_id', $state_id);

                    if (!empty($district_id)) {
                        $query->where('district_id', $district_id);
                    }

                    $material->currentStock = $query->sum('quantity');
                });

            $serialsquery = DB::table('material_serials')
                ->where('qtystatus', '!=','FULLY_ISSUED')
                ->where('state_id', $state_id);
                  if (!empty($district_id)) {
                        $serialsquery->where('district_id', $district_id);
                    }
            $serials=$serialsquery->select('id', 'material_id', 'serial_number', 'quantity', 'status','district_id')->get();

               
            return view('admin.stock-issue.create', compact('district', 'materials', 'serials'));
        } catch (Exception $e) {
            return back()->with('error', 'Error loading stock issue form: ' . $e->getMessage());
        }
    }

    /**
     * Store stock issue - Main transaction handler
     */
    public function store(Request $request,StockIssueService $service)
    { 
      
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|integer|exists:providers,id',
                'district_id' => 'nullable|integer|exists:districts,id',
                'transaction_type' => 'required|in:ISSUE',
                'ticket_id' => 'nullable|string|max:100',
                'remarks' => 'nullable|string|max:500',
                'items' => 'required|array|min:1',
                'items.*.material_id' => 'required|integer|exists:materials,id',
                'items.*.quantity' => 'required|numeric|min:0.001',
                'items.*.serials' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $user = Auth::user();
            $stateId = $user->state_id ?? null;
            $districtId = $request->district_id;
            $employeeId = $request->employee_id;
            $employeeRecord = DB::table('providers')->find($employeeId);

            if (!$employeeRecord) {
                throw new Exception("Employee not found");
            }

            if (!$districtId) {
                $districtId = $employeeRecord->district_id;
            }

            $materialIds = array_column($request->items, 'material_id');

            $materials = DB::table('materials')
                ->whereIn('id', $materialIds)
                ->get()
                ->keyBy('id');

            $processedTransactions = [];

            foreach ($request->items as $itemIndex => $item) {
                $material = $materials->get($item['material_id']);

                if (!$material) {
                    throw new Exception("Material #" . ($itemIndex + 1) . " not found");
                }

                $quantity = (float) $item['quantity'];

         
               
                $serialsData = [];
                if ($material->has_serial) {
                    if (empty($item['serials'])) {
                        throw new Exception("Material #" . ($itemIndex + 1) . " serial number(s) required");
                    }
                    $pairs = explode(',', $item['serials']);
                    $totalQty = 0;
                     foreach ($pairs as $pair) {

                        if (trim($pair) === '' || !str_contains($pair, ':')) {
                            continue;
                        }

                        [$serialId, $qty] = explode(':', $pair);

                        $serialId = (int) $serialId;
                        $qty      = (int) $qty;

                        if ($qty < 1) {
                            return back()->with('error', "Invalid quantity for serial {$serialId}");
                        }
                         $serialsData[(int)$serialId] = (float)$qty;

                        $totalQty += $qty;
                    }
                     if ($totalQty != (int) $item['quantity']) {
                        return back()->with('error', "Serial quantities do not match given quantity for material {$item['material_id']}.");
                    }


                  
                }

                $currentBalance = DB::table('stock_balance')
                            ->where('state_id', $stateId)
                            ->where('district_id', $districtId)
                            ->where('material_id', $material->id)
                            ->sum('quantity');
                           


                if ($currentBalance < $quantity) {
                    throw new Exception(
                        "Material #" . ($itemIndex + 1) . " (" . $material->name . "): Insufficient stock. " .
                        "Available: " . $currentBalance . ", Requested: " . $quantity
                    );
                }

                //  Create main stock transaction
                $stockTransaction = StockTransaction::insertGetId([
                    'state_id' => $stateId,
                    'district_id' => $districtId,
                    'material_id' => $material->id,
                    'employee_id' => $employeeId,
                    'transaction_type' => 'ISSUE',
                    // 'reference_type' => null,
                    // 'reference_id' => $request->ticket_id,
                    'remarks' => $request->remarks,
                    'quantity' => $quantity,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                $inTransactions = StockTransaction::where([
                    'state_id' => $stateId,
                    'district_id' => $districtId,
                    'material_id' => $material->id,
                ])
                ->whereIn('transaction_type', ['IN', 'OPENING'])
                ->orderBy('created_at') // FIFO
                ->get();

                $remainingQty = $quantity;
                
                foreach ($inTransactions as $inTxn) {

                if ($remainingQty <= 0) {
                    break;
                }

                // how much already issued from this IN txn
                $alreadyIssued = EmpStockTransaction::where('in_transaction_id', $inTxn->id)
                    ->sum('quantity');

                $availableFromThis = $inTxn->quantity - $alreadyIssued;

                if ($availableFromThis <= 0) {
                    continue;
                }

               $consumeQty = min($availableFromThis, $remainingQty);
            



                // Create employee stock transaction
                DB::table('employee_stock_transactions')->insert([
                    'state_id' => $stateId,
                    'district_id' => $districtId,
                    'material_id' => $material->id,
                    'employee_id' => $employeeId,
                    'transaction_type' => 'ISSUE',
                    'stock_transaction_id'=>$stockTransaction,
                    'in_transaction_id'=>$inTxn->id, 
                    // 'ticket_id' => $request->ticket_id,
                    'remarks' => $request->remarks,
                    // 'quantity' => $quantity,
                    'quantity' => $consumeQty, 
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                $remainingQty -= $consumeQty;
            }
            if ($remainingQty > 0) {
                throw new Exception(
                    "FIFO mismatch: Not enough IN stock for material {$material->name}"
                );
            }

                    
                // decrease stockblance 
                
               StockBalance::where([
                    'state_id' => $stateId,
                    'district_id' => $districtId,
                    'material_id' => $material->id
                ])->decrement('quantity', $quantity);

                //  Increase employee stock balance
                $empBalance = DB::table('employee_stock_balance')
                    ->where('state_id', $stateId)
                    ->where('district_id', $districtId)
                    ->where('material_id', $material->id)
                    ->where('employee_id', $employeeId)
                    ->first();

                if ($empBalance) {
                    DB::table('employee_stock_balance')
                        ->where('id', $empBalance->id)
                        ->increment('quantity', $quantity);
                } else {
                    DB::table('employee_stock_balance')->insert([
                        'state_id' => $stateId,
                        'district_id' => $districtId,
                        'material_id' => $material->id,
                        'employee_id' => $employeeId,
                        'quantity' => $quantity,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }

                if ($material->has_serial && !empty($serialsData)) {
                    $service->allocateSerials( $material->id, $employeeId, $serialsData, $stockTransaction);
                }

                $processedTransactions[] = [
                    'id' => $stockTransaction,
                    'material' => $material->name,
                    'quantity' => $quantity,
                    'has_serial' => $material->has_serial
                ];
            }

            DB::commit();

            return redirect()->route('admin.stock-issue.index')
                           ->with('success', count($processedTransactions) . ' material(s) issued successfully to ' . $employeeRecord->first_name . ' ' . $employeeRecord->last_name);

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Stock issue failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get employee stock balance
     */
    public function getEmployeeBalance(Request $request)
    {
         $employeeId = $request->query('employeeId'); 
        try {
            $balance = EmpStockBalance::where('employee_id', $employeeId)
                      ->sum('quantity');

            return response()->json([
                'success' => true,
                'balance' => $balance
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

     public function getMatBalance(Request $request){
        $dis_id = $request->query('district_id');
        $mat_id = $request->query('mat_id');
        try{
            $bal = StockBalance::where(['district_id'=>$dis_id,'material_id'=> $mat_id])->sum('quantity');
            return response()->json([
                'success'=>true,
                'balance'=>$bal
            ]);

        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }

       
    }


    /**
     * Get available serials for material
     */
    public function getMaterialSerials($materialId)
    {
        try {
            $serials = DB::table('material_serials')
                ->where('material_id', $materialId)
                ->where('status', 'IN_STOCK')
                ->select('id', 'serial_number', 'quantity', 'status')
                ->get();

            return response()->json([
                'success' => true,
                'serials' => $serials
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

  
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $state_id = $user->state_id;
            $district_id = $user->district_id;

            $query = StockTransaction::with([
                'material',
                'district',
                'employee',
                'serialAllocations.serial.stockInTransaction'
            ]);
                
            if ($request->has('employee') && !empty($request->employee)) {
                $employeeId = $request->employee;
                $query->where('employee_id', $employeeId);
            }
            if ($request->has('district') && !empty($request->district)) {
                    $districtId = $request->district;
                    $query->where('district_id', $districtId);
            }
            if ($request->has('material') && !empty($request->material)) {
                    $materialId = $request->material;
                    $query->where('material_id', $materialId);
            }
            if ($request->has('serial')) {
                $serial = $request->serial;

                $query->whereHas('serialAllocations.serial', function($q) use ($serial) {
                    $q->where('serial_number', 'LIKE', "%{$serial}%");
                });
            }
            if ($request->has('date_from') && !empty($request->date_from)) {
                    $dateFrom = $request->date_from;
                    $query->whereDate('created_at', '>=', $dateFrom);
                }

                if ($request->has('date_to') && !empty($request->date_to)) {
                    $dateTo = $request->date_to;
                    $query->whereDate('created_at', '<=', $dateTo);
                }

            if ($request->has('search')) {
                $search = $request->search;
                $query->whereHas('material', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%");
                });
            }
        


            if (!empty($district_id)) {
                $query->where('district_id', $district_id);
            }

            $transactions = $query->where('state_id', $state_id)
                ->where('transaction_type', 'ISSUE')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

                $employees = Provider::where('state_id', $state_id)
                    ->when($district_id, function ($q) use ($district_id) {
                        return $q->where('district_id', $district_id);
                    })
                    ->select('id', 'first_name', 'last_name')
                    ->get();

                $districts = District::where('state_id', $state_id)
                ->when($district_id, function ($q) use ($district_id) {
                        return $q->where('id', $district_id);
                    })
                    ->select('id', 'name')
                    ->get();

                $materials = Material::select('id', 'name')
                            ->get();
                        

            return view('admin.stock-issue.index', compact(
                'transactions',
                'employees',
                'districts',
                'materials'
            ));

        } catch (\Exception $e) {
            return back()->with('error', 'Error loading transactions: ' . $e->getMessage());
        }
    }

   public function show($id)
    {
        try {
            $transaction = StockTransaction::with([
                'material',
                'district',
                'employee',
               'serialAllocations.serial.stockInTransaction'
            ])->findOrFail($id);

            // Check authorization
            $this->authorizeTransaction($transaction);

            return view('admin.stock-issue.show', compact('transaction'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Transaction not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading transaction'], 500);
        }
    }


    public function edit($id)
    {
        try {
            $transaction = StockTransaction::with([
                'material',
                'district',
                'employee',
                'serialAllocations.serial'

            ])->findOrFail($id);

            // Check authorization
            $this->authorizeTransaction($transaction);
          
            return view('admin.stock-issue.edit', compact('transaction'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Transaction not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading transaction'], 500);
        }
    }
  



public function update(Request $request, $id)
{
    try {
        DB::beginTransaction();

        $transaction = StockTransaction::with([
            'material',
            'serialAllocations.serial'
        ])->findOrFail($id);

        if ($transaction->transaction_type !== 'ISSUE') {
            throw new Exception('Invalid transaction type');
        }

        // -------------------
        // Validation
        // -------------------
        $rules = [
            'quantity' => 'required|numeric|min:0.001',
            'remarks'  => 'nullable|string|max:500',
        ];

        if ($transaction->serialAllocations->count()) {
            $rules['serials'] = 'required|array';
            $rules['serials.*.quantity'] = 'required|numeric|min:0.001';
        }

        Validator::make($request->all(), $rules)->validate();

        $oldQty = (float) $transaction->quantity;
        $newQty = (float) $request->quantity;

        // =====================================================
        // ROLLBACK OLD ISSUE (balances + FIFO)
        // =====================================================
        $oldEmpTxns = EmpStockTransaction::where(
            'stock_transaction_id',
            $transaction->id
        )->get();

        foreach ($oldEmpTxns as $txn) {

            // restore district stock
            StockBalance::where([
                'state_id'    => $transaction->state_id,
                'district_id' => $transaction->district_id,
                'material_id' => $transaction->material_id
            ])->increment('quantity', $txn->quantity);

            // restore employee stock
            DB::table('employee_stock_balance')
                ->where([
                    'state_id'    => $transaction->state_id,
                    'district_id' => $transaction->district_id,
                    'material_id' => $transaction->material_id,
                    'employee_id' => $transaction->employee_id
                ])
                ->decrement('quantity', $txn->quantity);
        }

        // remove old FIFO links
        EmpStockTransaction::where(
            'stock_transaction_id',
            $transaction->id
        )->delete();

        // =====================================================
        // CHECK STOCK AFTER ROLLBACK
        // =====================================================
        $available = StockBalance::where([
            'state_id'    => $transaction->state_id,
            'district_id' => $transaction->district_id,
            'material_id' => $transaction->material_id
        ])->value('quantity');

        if ($available < $newQty) {
            throw new Exception(
                "Insufficient district stock. Available: {$available}"
            );
        }

        // =====================================================
        //  RE-ISSUE USING FIFO (IN / OPENING)
        // =====================================================
        $remainingQty = $newQty;

        $inTransactions = StockTransaction::where([
            'state_id'    => $transaction->state_id,
            'district_id' => $transaction->district_id,
            'material_id' => $transaction->material_id,
        ])
        ->whereIn('transaction_type', ['IN', 'OPENING'])
        ->orderBy('created_at') // FIFO
        ->get();

        foreach ($inTransactions as $inTxn) {

            if ($remainingQty <= 0) break;

            $alreadyIssued = EmpStockTransaction::where(
                'in_transaction_id',
                $inTxn->id
            )->sum('quantity');

            $availableFromThis = $inTxn->quantity - $alreadyIssued;

            if ($availableFromThis <= 0) continue;

            $consume = min($availableFromThis, $remainingQty);

            EmpStockTransaction::create([
                'state_id'             => $transaction->state_id,
                'district_id'          => $transaction->district_id,
                'material_id'          => $transaction->material_id,
                'employee_id'          => $transaction->employee_id,
                'transaction_type'     => 'ISSUE',
                'stock_transaction_id' => $transaction->id,
                'in_transaction_id'    => $inTxn->id,
                'quantity'             => $consume,
            ]);

            $remainingQty -= $consume;
        }

        // =====================================================
        //  APPLY FINAL BALANCES
        // =====================================================
        StockBalance::where([
            'state_id'    => $transaction->state_id,
            'district_id' => $transaction->district_id,
            'material_id' => $transaction->material_id
        ])->decrement('quantity', $newQty);

        DB::table('employee_stock_balance')
            ->where([
                'state_id'    => $transaction->state_id,
                'district_id' => $transaction->district_id,
                'material_id' => $transaction->material_id,
                'employee_id' => $transaction->employee_id
            ])
            ->increment('quantity', $newQty);

        // =====================================================
        //  SERIAL RECALC (NO NEGATIVE EVER)
        // =====================================================
        if ($transaction->serialAllocations->count()) {

            // $totalSerialQty = collect($request->serials)
            //     ->sum(fn ($s) => (float)$s['quantity']);
               $totalSerialQty = 0;
            foreach ($request->serials as $data) {
                $totalSerialQty += (float) $data['quantity'];
            }

            if ($totalSerialQty != $newQty) {
                throw new Exception(
                    'Serial quantities must match total quantity'
                );
            }

            foreach ($transaction->serialAllocations as $alloc) {

                $alloc->quantity =
                    (float) $request->serials[$alloc->material_serial_id]['quantity'];
                $alloc->save();

                $serial = $alloc->serial;

                $issuedQty = $serial->allocations()->sum('quantity');

                if ($issuedQty > $serial->received_quantity) {
                    throw new Exception(
                        "Insufficient stock for serial {$serial->serial_number}"
                    );
                }

                $serial->quantity =
                    $serial->received_quantity - $issuedQty;

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
        }

        // =====================================================
        // UPDATE MAIN TRANSACTION
        // =====================================================
        $transaction->update([
            'quantity' => $newQty,
            'remarks'  => $request->remarks
        ]);

        DB::commit();

        return redirect()
            ->route('admin.stock-issue.index')
            ->with('success', 'Stock issue updated successfully');

    } catch (Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', $e->getMessage());
    }
}







    /**
     * Get employee stock details
     */
    public function getEmployeeStock($employeeId)
    {
        try {
            $stock = DB::table('employee_stock_balance as esb')
                ->join('materials as m', 'esb.material_id', '=', 'm.id')
                ->where('esb.employee_id', $employeeId)
                ->select(
                    'esb.id',
                    'm.name as material_name',
                    'm.code as material_code',
                    'esb.quantity',
                    'm.base_unit'
                )
                ->get();

            return response()->json([
                'success' => true,
                'stock' => $stock
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

   
    /**
     * Update issued quantity (for partial usage/return)
     */
    public function updateIssue(Request $request, $transactionId)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'quantity_used' => 'required|numeric|min:0',
                'transaction_type' => 'required|in:USAGE,RETURN'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $original = DB::table('stock_transactions')
                ->where('id', $transactionId)
                ->where('transaction_type', 'ISSUE')
                ->first();

            if (!$original) {
                throw new Exception("Transaction not found");
            }

            $user = Auth::user();
            $type = $request->transaction_type;

            // Record the usage/return
            DB::table('employee_stock_transactions')->insert([
                'state_id' => $original->state_id,
                'district_id' => $original->district_id,
                'material_id' => $original->material_id,
                'employee_id' => $original->employee_id,
                'transaction_type' => $type,
                'ticket_id' => $request->ticket_id,
                'remarks' => $request->remarks,
                'quantity' => $request->quantity_used,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update employee balance if return
            if ($type === 'RETURN') {
                DB::table('employee_stock_balance')
                    ->where('employee_id', $original->employee_id)
                    ->where('material_id', $original->material_id)
                    ->decrement('quantity', $request->quantity_used);

                // Return to district stock
                DB::table('stock_balance')
                    ->where('state_id', $original->state_id)
                    ->where('district_id', $original->district_id)
                    ->where('material_id', $original->material_id)
                    ->increment('quantity', $request->quantity_used);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    private function authorizeTransaction($transaction)
    {
        $user = Auth::user();

        if ($user->state_id !== $transaction->state_id) {
            abort(403, 'Unauthorized');
        }

        if (!empty($user->district_id) && $user->district_id !== $transaction->district_id) {
            abort(403, 'Unauthorized');
        }
    }



public function destroy($id, StockIssueService $service)
{
    try {
        DB::beginTransaction();

        $transaction = StockTransaction::with([
            'serialAllocations.serial'
        ])->findOrFail($id);

        $this->authorizeTransaction($transaction);

        $service->rollbackIssue($transaction);

        DB::commit();

        return redirect()
            ->route('admin.stock-issue.index')
            ->with('success', 'Stock issue deleted successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}



public function employeeStockReport(Request $request)
{
    $perPage = $request->get('per_page', 15);
    $page    = Paginator::resolveCurrentPage() ?: 1;

    $user = Auth::user();

        $ledgerQuery = EmployeeMaterialLedger::with(['material', 'employee'])
        ->where('state_id', $user->state_id);

    if (!empty($user->district_id)) {
        $ledgerQuery->where('district_id', $user->district_id);
    }

    if ($request->district) {
        $ledgerQuery->where('district_id', $request->district);
    }

    if ($request->employee_id) {
        $ledgerQuery->where('employee_id', $request->employee_id);
    }

    if ($request->material_id) {
        $ledgerQuery->where('material_id', $request->material_id);
    }

    if ($request->from_date && $request->to_date) {
        $ledgerQuery->whereBetween('issue_date', [
            $request->from_date . ' 00:00:00',
            $request->to_date . ' 23:59:59'
        ]);
    }

    if ($request->search) {
        $search = $request->search;
        $ledgerQuery->whereHas('material', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });
    }

    $ledgerRows = $ledgerQuery->get();

    $report = [];

    /* ====BUILD REPORT=============================== */
    foreach ($ledgerRows as $row) {

        $key = $row->employee_id . '_' . $row->material_id;

        if (!isset($report[$key])) {
            $report[$key] = [
                'employee_id' => $row->employee_id,
                'material_id' => $row->material_id,
                'employee'    => $row->employee->first_name . ' ' . $row->employee->last_name,
                'material'    => $row->material->name,
                'baseunit'    =>$row->material->base_unit,
                'is_serial'   => (bool) $row->has_serial,
                'issued'      => 0,
                'used'        => 0,
                'balance'     => 0,
                'issued_indents' => [],  
                'serials'     => [],
                'tickets'     => []
            ];
        }

        /* =====NON-SERIAL MATERIAL=============================== */
        if (!$row->has_serial) {

            if ($row->transaction_type === 'ISSUE') {
                $report[$key]['issued'] += $row->quantity;
                $indent = $row->indent_no ? : '-';
                $report[$key]['issued_indents'][$indent]=
                 ($report[$key]['issued_indents'][$indent] ?? 0)  + $row->quantity;
            }

            if ($row->transaction_type === 'USED') {
                $report[$key]['used'] += $row->quantity;

                if ($row->ticket_id) {
                    $report[$key]['tickets'][$row->ticket_id] =
                        ($report[$key]['tickets'][$row->ticket_id] ?? 0) + $row->quantity;
                }
            }
        }

        /* ===========SERIAL MATERIAL=============================== */
        if ($row->has_serial && $row->serial_number) {

            $serialKey = $row->serial_number;

            if (!isset($report[$key]['serials'][$serialKey])) {
                $report[$key]['serials'][$serialKey] = [
                    'serial_number' => $serialKey,
                    'issued'        => 0,
                    'used'          => 0,
                    'balance'       => 0,
                     'issued_indents'  => [],
                    'tickets'       => []
                ];
            }

            if ($row->transaction_type === 'ISSUE') {
                $report[$key]['serials'][$serialKey]['issued'] += $row->quantity;
                $report[$key]['issued'] += $row->quantity;
                $indent = $row->indent_no ?: 'N/A';

                $report[$key]['serials'][$serialKey]['issued_indents'][$indent] =
                    ($report[$key]['serials'][$serialKey]['issued_indents'][$indent] ?? 0)
                    + $row->quantity;

            }

            if ($row->transaction_type === 'USED') {
                $report[$key]['serials'][$serialKey]['used'] += $row->quantity;
                $report[$key]['used'] += $row->quantity;

                if ($row->ticket_id) {
                    $report[$key]['serials'][$serialKey]['tickets'][$row->ticket_id] =
                        ($report[$key]['serials'][$serialKey]['tickets'][$row->ticket_id] ?? 0)
                        + $row->quantity;
                }
            }

            $report[$key]['serials'][$serialKey]['balance'] =
                $report[$key]['serials'][$serialKey]['issued']
                - $report[$key]['serials'][$serialKey]['used'];
        }
    }

    /* ============FINAL BALANCE =============================== */
    foreach ($report as &$row) {
        // ---- issued_indents (non-serial)
        $issuedIndents = [];
        foreach ($row['issued_indents'] as $indent => $qty) {
            $issuedIndents[] = [
                'indent_no' => $indent,
                'qty'       => $qty
            ];
        }
        $row['issued_indents'] = $issuedIndents;
        // ---- serials normalize
        foreach ($row['serials'] as &$s) {

            $indents = [];
            foreach ($s['issued_indents'] as $indent => $qty) {
                $indents[] = [
                    'indent_no' => $indent,
                    'qty'       => $qty
                ];
            }
            $s['issued_indents'] = $indents;

            // tickets normalize (already grouped)
            $tickets = [];
            foreach ($s['tickets'] as $ticketId => $qty) {
                $tickets[] = [
                    'ticket_id' => $ticketId,
                    'used'      => $qty
                ];
            }
            $s['tickets'] = $tickets;
        }



        $row['balance'] = $row['issued'] - $row['used'];

        // tickets ,indexed array
        $tickets = [];
        foreach ($row['tickets'] as $ticketId => $qty) {
            $tickets[] = [
                'ticket_id' => $ticketId,
                'used'      => $qty
            ];
        }
        $row['tickets'] = $tickets;

        // serials,indexed
        $row['serials'] = array_values($row['serials']);
    }

    /* ============ PAGINATION =============================== */
    $reportArray = array_values($report);
    $total       = count($reportArray);
    $items       = array_slice($reportArray, ($page - 1) * $perPage, $perPage);

    $paginated = new LengthAwarePaginator(
        $items,
        $total,
        $perPage,
        $page,
        [
            'path'  => $request->url(),
            'query' => $request->query(),
        ]
    );

    /* === DISTRICTS =============================== */
    $districtQuery = DB::table('districts')->where('state_id', $user->state_id);

    if (!empty($user->district_id)) {
        $districtQuery->where('id', $user->district_id);
    }

    if ($request->district) {
        $districtQuery->where('id', $request->district);
    }

    $districts = $districtQuery->get();

    return view('admin.stock-issue.stockreport', [
        'report'    => $paginated,
        'employees' => Provider::all(),
        'materials' => Material::all(),
        'districts' => $districts
    ]);
}







  
}
