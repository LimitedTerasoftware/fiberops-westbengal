<?php

namespace App\Http\Controllers\InventoryMng;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
                ->where('status', 'IN_STOCK')
                ->where('state_id', $state_id);
                  if (!empty($district_id)) {
                        $serialsquery->where('district_id', $district_id);
                    }
            $serials=$serialsquery->select('id', 'material_id', 'serial_number', 'quantity', 'status')->get();

               
            return view('admin.stock-issue.create', compact('district', 'materials', 'serials'));
        } catch (Exception $e) {
            return back()->with('error', 'Error loading stock issue form: ' . $e->getMessage());
        }
    }

    /**
     * Store stock issue - Main transaction handler
     */
    public function store(Request $request)
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
                $stockTransaction = DB::table('stock_transactions')->insertGetId([
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

                // Create employee stock transaction
                DB::table('employee_stock_transactions')->insert([
                    'state_id' => $stateId,
                    'district_id' => $districtId,
                    'material_id' => $material->id,
                    'employee_id' => $employeeId,
                    'transaction_type' => 'ISSUE',
                    'stock_transaction_id'=>$stockTransaction->id,
                    // 'ticket_id' => $request->ticket_id,
                    'remarks' => $request->remarks,
                    'quantity' => $quantity,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                    
                // decrease stockblance 
                
                DB::table('stock_balance')
                    ->where('state_id', $stateId)
                    ->where('district_id', $districtId)
                    ->where('material_id', $material->id)
                    ->decrement('quantity', $quantity);

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
                    $this->allocateSerials($stateId, $districtId, $material->id, $employeeId, $serialsData, $stockTransaction);
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

   
    private function allocateSerials($stateId, $districtId, $materialId, $employeeId, $serialsData, $transactionId)
    {
        $serialIds = array_keys($serialsData);
        $serialRecords = DB::table('material_serials')
            ->whereIn('id', $serialIds)
            ->where('material_id', $materialId)
            ->get()
            ->keyBy('id');

        foreach ($serialsData as $serialId => $qty) {
            $serial = $serialRecords->get($serialId);
            if (!$serial) {
                throw new Exception("Serial #$serialId not found for material #$materialId");
            }

            if ($qty > $serial->quantity) {
                throw new Exception("Requested quantity $qty exceeds available quantity {$serial->quantity} for serial #$serialId");
            }

            $newQty = $serial->quantity - $qty;
            $status = $newQty > 0 ? 'PARTIALLY_ISSUED' : 'FULLY_ISSUED';

            DB::table('material_serials')
                ->where('id', $serialId)
                ->update([
                    'quantity' => $newQty,
                    'qtystatus' => $status,
                    'status' => 'ISSUED',
                    'updated_at' => Carbon::now()
                ]);

            DB::table('material_serial_allocations')->insert([
                'stock_transaction_id'=>$transactionId,
                'material_serial_id' => $serialId,
                'employee_id' => $employeeId,
                'quantity' => $qty,
                'remarks' => 'Issued via transaction #' . $transactionId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
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
                'serialAllocations.serial.stockInTransaction'

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
            $transaction = StockTransaction::findOrFail($id);

            // Check authorization
            $this->authorizeTransaction($transaction);

            $validated = $request->validate([
                'quantity' => 'required|numeric|min:0.001',
                'remarks' => 'nullable|string'
            ]);

            DB::beginTransaction();

            $transaction->update([
                'quantity' => $validated['quantity'],
                'remarks' => $validated['remarks'] ?? null,
                'updated_at' => now()
            ]);

            DB::commit();

            return redirect()->route('admin.stock-issue.index')
                ->with('success', 'Transaction updated successfully!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Transaction not found');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating transaction: ' . $e->getMessage());
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

public function destroy($id)
{
    try {
        DB::beginTransaction();

        $transaction = StockTransaction::with([
            'serialAllocations.serial'
        ])->findOrFail($id);

        $stateId    = $transaction->state_id;
        $districtId = $transaction->district_id;
        $materialId = $transaction->material_id;
        $employeeId = $transaction->employee_id;
        $quantity   = $transaction->quantity;

        /** Restore district stock */
        StockBalance::where([
            'state_id'    => $stateId,
            'district_id' => $districtId,
            'material_id' => $materialId
        ])->increment('quantity', $quantity);

        /**  Reduce employee stock */
        EmpStockBalance::where([
            'state_id'    => $stateId,
            'district_id' => $districtId,
            'material_id' => $materialId,
            'employee_id' => $employeeId
        ])->decrement('quantity', $quantity);

        /**  Restore serial quantities */
        foreach ($transaction->serialAllocations as $alloc) {

            $serial = $alloc->serial;

            $serial->quantity += $alloc->quantity;

            $remainingIssued = $serial->allocations()
                ->where('stock_transaction_id', '!=', $transaction->id)
                ->sum('quantity');

            if ($remainingIssued > 0) {
                $serial->qtystatus = 'PARTIALLY_ISSUED';
                $serial->status    = 'IN_STOCK';
            } else {
                $serial->qtystatus = 'AVAILABLE';
                $serial->status    = 'IN_STOCK';
            }

            $serial->save();
        }

        /**  Delete serial allocations */
        Material_serial_Allocations::where('stock_transaction_id', $transaction->id)
            ->delete();

        /**  Delete employee stock transactions (SAFE NOW) */
        EmpStockTransaction::where('stock_transaction_id', $transaction->id)
            ->delete();

        /** Delete main stock transaction */
        $transaction->delete();

        DB::commit();

        return redirect()
            ->route('admin.stock-issue.index')
            ->with('success', 'Stock issue deleted successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}



  
}
