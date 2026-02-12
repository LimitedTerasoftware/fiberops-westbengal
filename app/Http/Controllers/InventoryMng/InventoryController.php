<?php

namespace App\Http\Controllers\InventoryMng;

use App\Http\Controllers\Controller;
use App\Material;
use App\StockBalance;
use App\StockTransaction;
use App\MaterialSerial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\District;
use App\Provider;
use App\EmpStockTransaction;
use App\EmpStockBalance;
use App\Material_serial_Allocations;
use App\Services\StockIssueService;
use Session;
use Auth;
use \Carbon\Carbon;
use Exception;


class InventoryController extends Controller
{
     const TRANSACTION_TYPES = [
        'OPENING' => 'Opening Stock',
        'IN' => 'In Stock',
        // 'ISSUE' => 'Issue',
        // 'RETURN' => 'FRT Return',
        // 'ADJUSTMENT' => 'Adjustment',
        // 'REPLACE_IN' => 'Material Replace'
    ];

    

    public function index(Request $request)
    {
        Session::put('user', Auth::User());
        $user = Session::get('user');
	    $state_id = $user->state_id;
        $district_id = $user->district_id;
        $query = StockTransaction::with([
            'material',
            'district',
            'employee',
            'serial' 
        ]);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('material', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('type_filter') && !empty($request->type_filter)) {
            $query->where('transaction_type', $request->type_filter);
        }
         if (!empty($district_id)) {
                $query->where('district_id', $district_id);
            }
        
        $transactions = $query->where('state_id',$state_id)->whereIn('transaction_type', ['OPENING', 'IN'])->orderBy('created_at', 'desc')->paginate(10);
        $transactionTypes = self::TRANSACTION_TYPES;

        return view('admin.stock-entry.index', compact('transactions','transactionTypes'));
    }

    public function create()
    {
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
        $transactionTypes = self::TRANSACTION_TYPES;

        return view('admin.stock-entry.create', compact('materials', 'district', 'transactionTypes'));
    }
    public function show($id)
    {
        $transaction = StockTransaction::with([
        'material',
        'district',
        'employee',
        'serial'
    ])->findOrFail($id);
    
    return view('admin.stock-entry.show', compact('transaction'));

    }
    public function getEmployees(Request $request)
    {
        $districtId = $request->district_id;
        if(!$districtId){
            return response()->json([
                'success'=>false,
                'message'=>'Please select district'
            ],400);
        }

        $employees = Provider::where('district_id', $districtId)->get();

        return response()->json([
            'success' => true,
            'employees' => $employees
        ]);
    }


    public function getMaterialDetails(Request $request)
    {
       
        $materialId = $request->get('material_id');
        $district_id = $request->get('district_id');
 
        if (!$materialId) {
            return response()->json([
                'success' => false,
                'message' => 'Material ID required'
            ], 400);
        }

        $material = Material::find($materialId);

        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material not found'
            ], 404);
        }

        $currentStock = StockBalance::where('material_id', $material->id)->where('district_id',$district_id)
            ->sum('quantity');
          

        return response()->json([
            'success' => true,
            'material' => [
                'id' => $material->id,
                'code' => $material->code,
                'name' => $material->name,
                'purchase_unit' => $material->purchase_unit,
                'base_unit' => $material->base_unit,
                'qty_per_purchase_unit' => $material->qty_per_purchase_unit,
                'has_serial' => $material->has_serial,
                'current_stock' => $currentStock
            ]
        ]);
    }

  
      public function store(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
           
            'district_id' => 'required|integer',
            'transaction_type' => 'required|in:' . implode(',', array_keys(self::TRANSACTION_TYPES)),
            'reference_type' => 'nullable|string|max:50',
            'reference_id' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|integer|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:0.001|max:999999.999',
            'items.*.serials' => 'nullable|array',
            'items.*.serials.*.serial' => 'nullable|string|max:100',
            'items.*.serials.*.qty' => 'nullable|numeric|min:0.001',

        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }
        Session::put('user', Auth::User());
        $user = Session::get('user');
	    $state_id = $user->state_id;

        DB::beginTransaction();

        try {
            $createdTransactions = [];
            $materials = Material::whereIn('id', array_column($request->items, 'material_id'))->get()->keyBy('id');

            foreach ($request->items as $itemIndex => $item) {
                $material = $materials->get($item['material_id']);

                if (!$material) {
                  throw new \Exception("Material #" . ($itemIndex + 1) . " not found");
                }

                $quantity = $item['quantity'];
               

                $serialsData = $item['serials'] ?? null;
                $baseQuantity = $serialsData 
                    ? array_sum(array_map(function($s) { return floatval($s['qty']); }, $serialsData))
                    : $quantity;



                if ($material->has_serial) {
                    if (empty($serialsData)) {
                        throw new \Exception("Material #" . ($itemIndex + 1) . " (" . $material->name . ") requires serial numbers");
                    }
                      
                    $serialNumbers = [];
                    $serialQtyTotal = 0;
                    foreach ($serialsData as $s) {
                        if (empty($s['serial']) || empty($s['qty'])) {
                            throw new \Exception("Each serial must have serial + qty");
                        }

                        $serialNumbers[] = $s['serial'];
                       
                        $serialQtyTotal += floatval($s['qty']);
                    }
                   if (count($serialNumbers) != $item['quantity']) {

                        throw new \Exception("Total qty from serials does not match entered no of items");
                    }
                    if(count($serialNumbers) != count(array_unique($serialNumbers))) {
                        throw new \Exception("Duplicate serial numbers found");
                    }

                   // Check if serials already exist
                    $existingSerials = MaterialSerial::where('material_id', $material->id)
                        ->whereIn('serial_number', $serialNumbers)
                        ->pluck('serial_number')
                        ->toArray();

                    if (!empty($existingSerials)) {
                        throw new \Exception("Material #".($itemIndex + 1).": Serials already exist: " . implode(', ', $existingSerials));
                    }
                }

                // Update or create stock balance
                $stockBalance = StockBalance::where('state_id', $state_id)
                    ->where('district_id', $request->district_id)
                    ->where('material_id', $material->id)
                    ->first();

                $quantityChange = $request->transaction_type === 'ISSUE' ? -$baseQuantity : $baseQuantity;

                if ($stockBalance) {
                    $newQuantity = $stockBalance->quantity + $quantityChange;

                    if ($newQuantity < 0 && $request->transaction_type === 'ISSUE') {
                        throw new \Exception(
                            "Material #" . ($itemIndex + 1) . " (" . $material->name . "): Insufficient stock. Available: " . $stockBalance->quantity
                        );
                    }

                    $stockBalance->quantity = max(0, $newQuantity);
                    $stockBalance->save();
                } else {
                    if ($quantityChange < 0) {
                        throw new \Exception("Material #".($itemIndex + 1). "(".$material->name."): No stock available");
                    }

                    $stockBalance = StockBalance::create([
                        'state_id' => $state_id,
                        'district_id' => $request->district_id,
                        'material_id' => $material->id,
                        'quantity' => $quantityChange
                    ]);
                }

                // Create transaction record
                $transaction = StockTransaction::create([
                    'state_id' => $state_id,
                    'district_id' => $request->district_id,
                    'material_id' => $material->id,
                    // 'employee_id' => auth()->id() ?? 1,
                    'transaction_type' => $request->transaction_type,
                    'reference_type' => $request->reference_type ?? null,
                    'reference_id' => $request->reference_id ?? null,
                    'remarks' => $request->remarks ?? '',
                    'quantity' => $baseQuantity
                ]);

                // Create serial records if required
                if ($material->has_serial) {
                    
                    $serialRecords = [];
                    foreach ($serialsData as $serial) {
                        $serialRecords[] = [
                            'transaction_id'=>$transaction->id,
                            'state_id' => $state_id,
                            'district_id' => $request->district_id,
                            'material_id' => $material->id,
                            'serial_number' =>$serial['serial'],
                            'received_quantity' => $serial['qty'],
                            'quantity'=>$serial['qty'],
                            'status' => $request->transaction_type === 'ISSUE' ? 'ISSUED' : 'IN_STOCK',
                            'qtystatus'=>'AVAILABLE',
                            'lifecycle_status'=>'IN_STOCK',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }

                    if (!empty($serialRecords)) {
                        MaterialSerial::insert($serialRecords);
                    }
                }

                $createdTransactions[] = [
                    'id' => $transaction->id,
                    'material' => $material->name,
                    'quantity' => $baseQuantity
                ];
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => count($createdTransactions) . ' material(s) added successfully',
                    'transactions' => $createdTransactions
                ]);
            }

            return redirect()->route('admin.stock-entry.index')
                ->with('success', count($createdTransactions) . ' material(s) stock entry created successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }


    
    public function edit($id)
    {
        try {
            $transaction = StockTransaction::with([
                'material',
                'district',
                'serial'

            ])->findOrFail($id);

            // Check authorization
            $this->authorizeTransaction($transaction);
         
          
            return view('admin.stock-entry.edit', compact('transaction'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Transaction not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading transaction'], 500);
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
      public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $transaction = StockTransaction::with([
                'material',
                'serial'
            ])->lockForUpdate()->findOrFail($id);

            // Allow only IN / OPENING
            if (!in_array($transaction->transaction_type, ['IN', 'OPENING'])) {
                throw new Exception('Only IN / OPENING entries can be updated');
            } 
          

            /** BLOCK UPDATE IF ALREADY ISSUED */
            if ($transaction->serial->count()) {

                foreach ($transaction->serial as $serial) {
                    
                    if ($serial->allocations()->sum('quantity') > 0) {
                        throw new Exception(
                            "Serial {$serial->serial_number} already issued. Update not allowed."
                        );
                    }
                }

            } else {

                $issuedQty = EmpStockTransaction::where(
                    'in_transaction_id',
                    $transaction->id
                )->sum('quantity');

                if ($issuedQty > 0) {
                    throw new Exception('This stock entry is already issued.');
                }
            }

            /** VALIDATION */
            $rules = [
                'quantity' => 'required|numeric|min:0.001',
                'remarks'  => 'nullable|string|max:500',
            ];

            if ($transaction->serial->count()) {
                $rules['serials'] = 'required|array';
                $rules['serials.*.received_quantity'] = 'required|numeric|min:0.001';
            }

            Validator::make($request->all(), $rules)->validate();


            $oldQty = $transaction->quantity;
            $newQty = (float) $request->quantity;
            $diff   = $newQty - $oldQty;

            /** UPDATE DISTRICT STOCK */
            if ($diff != 0) {
                StockBalance::where([
                    'state_id'    => $transaction->state_id,
                    'district_id' => $transaction->district_id,
                    'material_id' => $transaction->material_id,
                ])->increment('quantity', $diff);
            }

            /** SERIAL UPDATE */
            if ($transaction->serial->count()) {

                $totalSerialQty = 0;

                foreach ($transaction->serial as $serial) {

                    $newSerialQty = (float)
                        $request->serials[$serial->id]['received_quantity'];

                    $serial->received_quantity = $newSerialQty;
                    $serial->quantity          = $newSerialQty;
                    $serial->qtystatus         = 'AVAILABLE';
                    $serial->status            = 'IN_STOCK';
                    $serial->save();

                    $totalSerialQty += $newSerialQty;
                }

                if ($totalSerialQty != $newQty) {
                    throw new Exception('Serial total must match main quantity');
                }
            }

            /** UPDATE MAIN TRANSACTION */
            $transaction->update([
                'quantity' => $newQty,
                'remarks'  => $request->remarks,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.stock-entry.index')
                ->with('success', 'Stock entry updated successfully');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

public function destroy($id)
{
    try {
        DB::beginTransaction();

        $transaction = StockTransaction::with([
            'serial'
        ])->lockForUpdate()->findOrFail($id);

        if (!in_array($transaction->transaction_type, ['IN', 'OPENING'])) {
            throw new Exception('Only IN / OPENING entries can be deleted');
        }

      
        if ($transaction->serial->count()) {

            foreach ($transaction->serial as $serial) {
                if ($serial->allocations()->sum('quantity') > 0) {
                    throw new Exception(
                        "Serial {$serial->serial_number} already issued. Delete not allowed."
                    );
                }
            }

        } else {

            $issuedQty = EmpStockTransaction::where(
                'in_transaction_id',
                $transaction->id
            )->sum('quantity');

            if ($issuedQty > 0) {
                throw new Exception('This stock entry is already issued. Delete not allowed.');
            }
        }

     
        StockBalance::where([
            'state_id'    => $transaction->state_id,
            'district_id' => $transaction->district_id,
            'material_id' => $transaction->material_id,
        ])->decrement('quantity', $transaction->quantity);

     
        if ($transaction->serial->count()) {
            foreach ($transaction->serial as $serial) {
                $serial->delete();
            }
        }

    
        $transaction->delete();

        DB::commit();

        return redirect()
            ->route('admin.stock-entry.index')
            ->with('success', 'Stock entry deleted successfully');

    } catch (Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}

   

}
