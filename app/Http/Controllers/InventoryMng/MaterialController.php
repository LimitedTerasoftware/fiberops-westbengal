<?php

namespace App\Http\Controllers\InventoryMng;

use App\Http\Controllers\Controller;
use App\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use DB;
class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('unit_filter') && !empty($request->unit_filter)) {
            $query->where('purchase_unit', $request->unit_filter);
        }

        if ($request->has('serial_filter') && $request->serial_filter !== '') {
            $query->where('has_serial', $request->serial_filter);
        }

        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $materials = $query->paginate(15);

        $units = Material::distinct()->pluck('purchase_unit')->filter()->sort();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.materials.table', compact('materials'))->render(),
                'pagination' => (string) $materials->links()

            ]);
        }

        return view('admin.materials.index', compact('materials', 'units'));
    }

    
    public function create()
    {
        return view('admin.materials.create');
    }

   
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:materials,code',
            'name' => 'required|string|max:255',
            'purchase_unit' => 'required|string|max:50',
            'base_unit' => 'required|string|max:50',
            'qty_per_purchase_unit' => 'required|numeric|min:0.001|max:999999.999',
            'has_serial' => 'boolean',
            'description' => 'nullable|string|max:1000'
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

        try {
            $material = Material::create([
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'purchase_unit' => $request->purchase_unit,
                'base_unit' => $request->base_unit,
                'qty_per_purchase_unit' => $request->qty_per_purchase_unit,
                'has_serial' => $request->has('has_serial') ? true : false,
                'description' => $request->description
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Material created successfully',
                    'material' => $material
                ]);
            }

            return redirect()->route('admin.materials.index')
                           ->with('success', 'Material created successfully');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating material: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error creating material')->withInput();
        }
    }

   
    public function show(Material $material)
    {
        return view('admin.materials.show', compact('material'));
    }

   
    public function edit(Material $material)
    {
        
                return response()->json([
                    'success' => true,
                    'material' => $material
                ]);
          
        // return view('admin.materials.edit', compact('material'));
    }

   
    public function update(Request $request, Material $material)
    {
        
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:materials,code,' . $material->id,
            'name' => 'required|string|max:255',
            'purchase_unit' => 'required|string|max:50',
            'base_unit' => 'required|string|max:50',
            'qty_per_purchase_unit' => 'required|numeric|min:0.001|max:999999.999',
            'has_serial' => 'boolean',
            'description' => 'nullable|string|max:1000'
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

        try {
            $material->update([
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'purchase_unit' => $request->purchase_unit,
                'base_unit' => $request->base_unit,
                'qty_per_purchase_unit' => $request->qty_per_purchase_unit,
                'has_serial' => $request->has('has_serial') ? true : false,
                'description' => $request->description
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Material updated successfully',
                    'material' => $material
                ]);
            }

            return redirect()->route('admin.materials.index')
                           ->with('success', 'Material updated successfully');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating material: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error updating material')->withInput();
        }
    }

 
    public function destroy(Material $material)
    {
        try {
            $material->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Material deleted successfully'
                ]);
            }

            return redirect()->route('admin.materials.index')
                           ->with('success', 'Material deleted successfully');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting material: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error deleting material');
        }
    }

  
    public function getMaterial(Material $material)
    {
        return response()->json([
            'success' => true,
            'material' => $material
        ]);
    }

   
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:materials,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid material IDs'
            ], 422);
        }

        try {
            Material::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' materials deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting materials: ' . $e->getMessage()
            ], 500);
        }
    }

 public function getFrequentlyDownGps(Request $request)
{
    try {

        $user = Session::get('user');
        $state_id = $user->state_id ?? 1;

        $from_date   = $request->get('from_date');
        $to_date     = $request->get('to_date');
        $district_id = $request->get('district_id');
        $block_id    = $request->get('block_id');
        $issue_filter = $request->get('issue_filter');


        $expectedWeeks = DB::table('master_tickets')
            ->join('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
            ->where('user_requests.state_id', $state_id)
            ->whereBetween('master_tickets.downdate', [$from_date, $to_date])
            ->select(DB::raw('COUNT(DISTINCT YEARWEEK(master_tickets.downdate,1)) as weeks'))
            ->value('weeks');


        $query = DB::table('master_tickets')
            ->join('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
            ->select(
                'master_tickets.lgd_code',
                'master_tickets.gpname',
                'master_tickets.district',
                'master_tickets.mandal',
                DB::raw('COUNT(*) as ticket_count'),
                DB::raw('COUNT(DISTINCT YEARWEEK(master_tickets.downdate,1)) as week_count')
            )
            ->where('user_requests.state_id', $state_id)
            ->whereNotNull('master_tickets.lgd_code')
            ->whereBetween('master_tickets.downdate', [$from_date, $to_date]);

  

        if ($district_id) {

            $query->where('user_requests.district_id', $district_id);

        }


        if ($block_id) {

            $blockName = DB::table('blocks')
                ->where('id', $block_id)
                ->value('name');

            if ($blockName) {

                $query->where('master_tickets.mandal', $blockName);

            }
        }

   

        if ($issue_filter) {

            $query->whereExists(function ($sub) use (
                $issue_filter,
                $state_id,
                $from_date,
                $to_date
            ) {

                $sub->select(DB::raw(1))
                    ->from('master_tickets as mt2')
                    ->join('user_requests as ur2', 'mt2.ticketid', '=', 'ur2.booking_id')
                    ->whereRaw('mt2.lgd_code = master_tickets.lgd_code')
                    ->where('mt2.downreason', $issue_filter)
                    ->where('ur2.state_id', $state_id)
                    ->whereBetween('mt2.downdate', [$from_date, $to_date]);

            });
        }

   

        $results = $query
            ->groupBy(
                'master_tickets.lgd_code',
                'master_tickets.gpname',
                'master_tickets.district',
                'master_tickets.mandal'
            )
            ->havingRaw(
                "COUNT(DISTINCT YEARWEEK(master_tickets.downdate,1)) = ?",
                [$expectedWeeks]
            )
            ->orderBy('ticket_count', 'desc')
            ->paginate(15);

      

        $lgdCodes = $results->pluck('lgd_code')->toArray();

        $breakdowns = [];

        if (!empty($lgdCodes)) {

            $breakdownRows = DB::table('master_tickets')
                ->join('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                ->select(
                    'master_tickets.lgd_code',
                    'master_tickets.downreason',
                    DB::raw('COUNT(*) as count')
                )
                ->where('user_requests.state_id', $state_id)
                ->whereBetween('master_tickets.downdate', [$from_date, $to_date])
                ->whereIn('master_tickets.lgd_code', $lgdCodes)
                ->whereNotNull('master_tickets.downreason')
                ->groupBy(
                    'master_tickets.lgd_code',
                    'master_tickets.downreason'
                )
                ->orderBy('count', 'desc')
                ->get();

            foreach ($breakdownRows as $row) {

                $breakdowns[$row->lgd_code][] = $row;

            }
        }

      

        foreach ($results as $row) {

            $row->breakdown = $breakdowns[$row->lgd_code] ?? [];

            $row->total_breakdown_count =
                array_sum(array_column($row->breakdown, 'count'));

            $row->top_reason =
                $row->breakdown[0]->downreason ?? 'N/A';
        }


        $districts = DB::table('districts')
            ->where('state_id', $state_id)
            ->get();

        $blocks = $district_id
            ? DB::table('blocks')->where('district_id', $district_id)->get()
            : [];

        $allIssues = DB::table('master_tickets')
            ->whereNotNull('downreason')
            ->where('ticketid', 'NOT LIKE', 'INC%')
             ->whereBetween('downdate', [$from_date, $to_date])
            ->distinct()
            ->orderBy('downreason')
            ->pluck('downreason');

        return view(
            'admin.reports.frequently_down_gps',
            compact(
                'results',
                'districts',
                'blocks',
                'from_date',
                'to_date',
                'district_id',
                'block_id',
                'issue_filter',
                'allIssues'
            )
        );

    }
    catch (Exception $e) {

        return back()->with(
            'flash_error',
            'Error: ' . $e->getMessage()
        );
    }
}


    
    public function getRecurringGpTrends(Request $request)
    {
        try {
            $user = Session::get('user');
            $state_id = isset($user->state_id) ? $user->state_id : 1;

            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $query = DB::table('master_tickets')
                ->join('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                ->where('user_requests.state_id', $state_id)
                ->whereNotNull('master_tickets.lgd_code');

            if ($fromDate && $toDate) {
                $query->whereBetween('master_tickets.downdate', [$fromDate, $toDate]);
            }
               
                $expectedWeeks = DB::table('master_tickets')
                ->whereBetween('downdate', [$fromDate, $toDate])
                ->select(DB::raw('COUNT(DISTINCT YEARWEEK(downdate, 1)) as weeks'))
                ->value('weeks');

                $recurringGps = $query->select(
                        'master_tickets.lgd_code',
                        'master_tickets.gpname',
                         DB::raw('COUNT(*) as ticket_count'),
                        DB::raw('COUNT(DISTINCT DATE(master_tickets.downdate)) as down_days'),
                        DB::raw('COUNT(DISTINCT YEARWEEK(master_tickets.downdate, 1)) as distinct_weeks')
                    )
                    ->groupBy('master_tickets.lgd_code', 'master_tickets.gpname')
                    ->having('distinct_weeks', '=', $expectedWeeks)
                    ->orderBy('ticket_count', 'desc')
                     ->get();


            $topGps = $recurringGps; // Re-assign for clarity

            $gpLabels = [];
            $reasonDatasets = [];
            $allReasons = [];
            $gpReasonCounts = [];

            // Collect GPs first to ensure order matching
            foreach ($topGps as $gp) {
                $gpLabels[] = $gp->gpname;
            }

            // Optimize Breakdown Query: Instead of query-per-GP, query all at once
            $targetLgdCodes = $topGps->pluck('lgd_code')->toArray();

            if (empty($targetLgdCodes)) {
                return response()->json([
                    'labels' => [],
                    'datasets' => []
                ]);
            }

            $breakdownQuery = DB::table('master_tickets')
                ->join('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                ->select('master_tickets.lgd_code', 'master_tickets.gpname', 'master_tickets.downreason', DB::raw('COUNT(*) as count'))
                ->whereIn('master_tickets.lgd_code', $targetLgdCodes)
                ->where('user_requests.state_id', $state_id);

            if ($fromDate && $toDate) {
                $breakdownQuery->whereBetween('master_tickets.downdate', [$fromDate, $toDate]);
            }

            $breakdownResults = $breakdownQuery->groupBy('master_tickets.lgd_code', 'master_tickets.gpname', 'master_tickets.downreason')->orderBy('count', 'desc')->get();

            // Process results
            foreach ($breakdownResults as $row) {
                $reason = $row->downreason ?? 'Unknown';
                if (!in_array($reason, $allReasons)) {
                    $allReasons[] = $reason;
                }
                $gpReasonCounts[$row->gpname][$reason] = $row->count;
            }

            // Build Datasets
            $colors = [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40',
                '#C9CBCF',
                '#E7E9ED',
                '#71B37C',
                '#EC932F'
            ];

            foreach ($allReasons as $index => $reason) {
                $data = [];
                foreach ($gpLabels as $gpName) {
                    $data[] = isset($gpReasonCounts[$gpName][$reason]) ? $gpReasonCounts[$gpName][$reason] : 0;
                }

                $reasonDatasets[] = [
                    'label' => $reason,
                    'data' => $data,
                    'backgroundColor' => isset($colors[$index]) ? $colors[$index] : '#' . substr(md5($reason), 0, 6),
                ];
            }

            return response()->json([
                'labels' => $gpLabels,
                'datasets' => $reasonDatasets
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}