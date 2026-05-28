<?php

namespace App\Http\Controllers\InventoryMng;

use App\Http\Controllers\Controller;
use App\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use DB;
use Excel;
use Carbon\Carbon;
use App\GpRouterUptime;
use App\BlockRouterUptime;
use App\OltUptime;

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
private function applyIntegrationFilter($query, string $table)
{
    return $query->whereNotExists(function ($sub) use ($table) {
        $sub->select(DB::raw(1))
            ->from($table . ' as prev')
            ->whereColumn('prev.lgd_code', $table . '.lgd_code')
            ->whereRaw("prev.record_date >= DATE_SUB(DATE({$table}.record_date), INTERVAL 1 DAY)")
            ->whereRaw("prev.record_date < DATE({$table}.record_date)");
    });
}
private function attachTicketBreakdowns($results, $state_id, $ticket_type = 'router')
{
    $items = method_exists($results, 'getCollection')
        ? $results->getCollection()
        : collect($results);

    if ($items->isEmpty()) {
        return $results;
    }

    $lgdCodes = $items->pluck('lgd_code')->unique()->values();

  $dates = $items->pluck('record_date')
            ->map(function ($date) {
                return Carbon::parse($date)->toDateString();
            })
            ->unique()
            ->values();


    $breakdownRows = DB::table('master_tickets')
        ->join('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
        ->select(
            'master_tickets.lgd_code',
            DB::raw('DATE(master_tickets.downdate) as day'),
            'master_tickets.downreason',
            DB::raw('COUNT(*) as count')
        )
        ->where('user_requests.state_id', $state_id)
        ->whereIn('master_tickets.lgd_code', $lgdCodes)
        ->whereBetween('master_tickets.downdate', [
            $dates->min() . ' 00:00:00',
            $dates->max() . ' 23:59:59',
        ])
        ->whereNotNull('master_tickets.downreason');

    if ($ticket_type === 'router') {
        $breakdownRows->where('user_requests.booking_id', 'like', 'INC%');
    } else {
        $breakdownRows->where('user_requests.booking_id', 'not like', 'INC%');
    }

    $breakdownRows = $breakdownRows
        ->groupBy(
            'master_tickets.lgd_code',
            DB::raw('DATE(master_tickets.downdate)'),
            'master_tickets.downreason'
        )
        ->orderBy('count', 'desc')
        ->get()
       ->groupBy(function ($row) {
                return $row->lgd_code . '|' . $row->day;
            });


    $items->transform(function ($row) use ($breakdownRows) {
        $day = Carbon::parse($row->record_date)->toDateString();
        $key = $row->lgd_code . '|' . $day;

        $row->breakdown = $breakdownRows->get($key, collect());
        $row->ticket_count = $row->breakdown->sum('count');

        return $row;
    });

    if (method_exists($results, 'setCollection')) {
        $results->setCollection($items);
        return $results;
    }

    return $items;
}

private function attachEmptyBreakdowns($results)
{
    $items = method_exists($results, 'getCollection')
        ? $results->getCollection()
        : collect($results);

    $items->transform(function ($row) {
        $row->breakdown = [];
        $row->ticket_count = 0;
        return $row;
    });

    if (method_exists($results, 'setCollection')) {
        $results->setCollection($items);
        return $results;
    }

    return $items;
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
        $uptime_category = $request->get('uptime_category');
        $ticket_type = $request->get('ticket_type', 'ont'); // 'ont',gprouter or 'router'
        $sammriddh = $request->get('samriddh', false);
        $router_category = $request->get('router_category', null);
        $Blockrouter_category = $request->get('Blockrouter_category', null);
        $OLT_category = $request->get('OLT_category', null);
        $districts = DB::table('districts')
            ->where('state_id', $state_id)
            ->get();

        $blocks = $district_id
            ? DB::table('blocks')->where('district_id', $district_id)->get()
            : [];
        if($Blockrouter_category){
              $query = BlockRouterUptime::query()
                        ->join('blocks', 'blocks.routercode', '=', 'block_router_uptime.lgd_code')
                        ->join('districts', 'blocks.district_id', '=', 'districts.id')
                        ->where('districts.state_id', $state_id);
               if ($from_date && $to_date) {
                    $query->whereBetween('block_router_uptime.record_date', [$from_date, $to_date]);
                }

                if ($district_id) {
                    $query->where('blocks.district_id', $district_id);
                }

                if ($block_id) {
                    $query->where('blocks.id', $block_id);
                }

                switch ($Blockrouter_category) {
                    case 'all':
                        // Average Uptime % redirect: show all block-router rows with their own uptime percentages.
                        break;
                    case 'gte98':
                        $query->where('block_router_uptime.uptime_percent', '>=', 98);
                        break;
                    case 'lt98':
                        $query->where('block_router_uptime.uptime_percent', '>', 0)->where('block_router_uptime.uptime_percent', '<', 98);
                        break;
                    case 'zero_availability':
                        $query->where('block_router_uptime.uptime_percent', '=', 0);
                        break;
                   case 'integration':
                    $this->applyIntegrationFilter($query, 'block_router_uptime');
                    break;

                 }
                if ($Blockrouter_category === 'all') {
                    $results = $query->select(
                        'block_router_uptime.lgd_code',
                        'districts.name as district',
                        'blocks.name as mandal',
                        'blocks.name as gpname',
                        DB::raw('ROUND(AVG(block_router_uptime.uptime_percent), 2) as uptime_percent'),
                        DB::raw('MIN(block_router_uptime.record_date) as record_date')
                    )
                    ->groupBy('block_router_uptime.lgd_code', 'districts.name', 'blocks.name')
                    ->orderBy('uptime_percent', 'asc')
                    ->paginate(15);
                    $results = $this->attachEmptyBreakdowns($results);
                } else {
                    $results = $query->select(
                        'block_router_uptime.lgd_code',
                        'districts.name as district',
                        'blocks.name as mandal',
                        'blocks.name as gpname',
                        'block_router_uptime.uptime_percent',
                        'block_router_uptime.record_date'
                    )
                    ->orderBy('block_router_uptime.uptime_percent', 'asc')
                    ->paginate(15);
                }

                $is_uptime_report = true;
                $allIssues = [];

                return view('admin.reports.frequently_down_gps', compact('results', 'districts', 'blocks', 'from_date', 'to_date', 'district_id', 'block_id',  'issue_filter', 'allIssues', 'is_uptime_report', 'Blockrouter_category','ticket_type', 'sammriddh'));


        }
        if($OLT_category){
                $query = OltUptime::query()
                        ->join('olt_locations', 'olt_locations.lgd_code', '=', 'olt_uptime.lgd_code')
                        ->join('districts','olt_locations.district_id','=','districts.id')
                        ->join('blocks','olt_locations.block_id','=','blocks.id')
                        ->where('olt_locations.state_id', $state_id);
                       
               if ($from_date && $to_date) {
                    $query->whereBetween('olt_uptime.record_date', [$from_date, $to_date]);
                }

                if ($district_id) {
                    $query->where('olt_locations.district_id', $district_id);
                }

                if ($block_id) {
                    $query->where('blocks.id', $block_id);
                }

                switch ($OLT_category) {
                    case 'all':
                        // Average Uptime % redirect: show all OLT rows with their own uptime percentages.
                        break;
                       case 'gte98':
                        $query->where('olt_uptime.uptime_percent', '>=', 98);
                        break;
                    case 'gte90':
                        $query->where('olt_uptime.uptime_percent', '>=', 90)->where('olt_uptime.uptime_percent', '<', 98);
                        break;
                    case 'gte75':
                        $query->where('olt_uptime.uptime_percent', '>=', 75)->where('olt_uptime.uptime_percent', '<', 90);
                        break;
                    case 'gte50':
                        $query->where('olt_uptime.uptime_percent', '>=', 50)->where('olt_uptime.uptime_percent', '<', 75);
                        break;
                    case 'gte20':
                        $query->where('olt_uptime.uptime_percent', '>=', 20)->where('olt_uptime.uptime_percent', '<', 50);
                        break;
                    case 'lt20':
                        $query->where('olt_uptime.uptime_percent', '<', 20);
                        break;
                 }
                if ($OLT_category === 'all') {
                    $results = $query->select(
                        'olt_uptime.lgd_code',
                        'olt_locations.olt_location as gpname',
                        'districts.name as district',
                        'blocks.name as mandal',
                        DB::raw('ROUND(AVG(olt_uptime.uptime_percent), 2) as uptime_percent'),
                        DB::raw('MIN(olt_uptime.record_date) as record_date')
                    )
                    ->groupBy('olt_uptime.lgd_code', 'olt_locations.olt_location', 'districts.name', 'blocks.name')
                    ->orderBy('uptime_percent', 'asc')
                    ->paginate(15);
                    $results = $this->attachEmptyBreakdowns($results);
                } else {
                    $results = $query->select(
                        'olt_uptime.lgd_code',
                        'olt_locations.olt_location as gpname',
                        'districts.name as district',
                        'blocks.name as mandal',
                        'olt_uptime.uptime_percent',
                        'olt_uptime.record_date'
                    )
                    ->orderBy('olt_uptime.uptime_percent', 'asc')
                    ->paginate(15);
                }

                $is_uptime_report = true;
                $allIssues = [];

                return view('admin.reports.frequently_down_gps', compact('results', 'districts', 'blocks', 'from_date', 'to_date', 'district_id', 'block_id',  'issue_filter', 'allIssues', 'is_uptime_report', 'OLT_category','ticket_type', 'sammriddh'));


        }

     
          // Filter issues based on ticket type
            $allIssuesQuery = DB::table('master_tickets')
                ->whereNotNull('downreason')
                ->whereBetween('downdate', [$from_date, $to_date]);

            if ($ticket_type === 'ont') {
                $allIssuesQuery->where('ticketid', 'NOT LIKE', 'INC%');
            } else {
                $allIssuesQuery->where('ticketid', 'LIKE', 'INC%');

            }

            $allIssues = $allIssuesQuery->distinct()
                ->orderBy('downreason')
                ->pluck('downreason');
        


        if ($uptime_category) {
                $query = DB::table('ont_uptime')
                    ->join('gp_list', 'ont_uptime.lgd_code', '=', 'gp_list.lgd_code')
                    ->join('districts', 'gp_list.district_id', '=', 'districts.id')
                    ->join('blocks', 'gp_list.block_id', '=', 'blocks.id')
                    ->where('gp_list.state_id', $state_id);
                if ($sammriddh) {
                    $query->where('gp_list.samridh_stat', 1);
                }

                if ($from_date && $to_date) {
                    $query->whereBetween('ont_uptime.record_date', [$from_date, $to_date]);
                }

                if ($district_id) {
                    $query->where('gp_list.district_id', $district_id);
                }

                if ($block_id) {
                    $query->where('gp_list.block_id', $block_id);
                }

                switch ($uptime_category) {
                    case 'all':
                        // Average Uptime % redirect: show all GP rows with their own uptime percentages.
                        break;
                    case 'gte98':
                        $query->where('ont_uptime.uptime_percent', '>=', 98);
                        break;
                    case 'gte90':
                        $query->where('ont_uptime.uptime_percent', '>=', 90)->where('ont_uptime.uptime_percent', '<', 98);
                        break;
                    case 'gte75':
                        $query->where('ont_uptime.uptime_percent', '>=', 75)->where('ont_uptime.uptime_percent', '<', 90);
                        break;
                    case 'gte50':
                        $query->where('ont_uptime.uptime_percent', '>=', 50)->where('ont_uptime.uptime_percent', '<', 75);
                        break;
                    case 'gte20':
                        $query->where('ont_uptime.uptime_percent', '>=', 20)->where('ont_uptime.uptime_percent', '<', 50);
                        break;
                    case 'lt20':
                        $query->where('ont_uptime.uptime_percent', '<', 20);
                        break;
                }
                if ($issue_filter) {
                    $query->whereExists(function ($sub) use ($issue_filter, $state_id, $ticket_type) {
                        $sub->select(DB::raw(1))
                            ->from('master_tickets as mt')
                            ->join('user_requests as ur', 'mt.ticketid', '=', 'ur.booking_id')
                            ->whereColumn('mt.lgd_code', 'gp_list.lgd_code')
                            ->where('mt.downreason', $issue_filter)
                            ->where('ur.state_id', $state_id)
                            ->whereRaw('DATE(mt.downdate) = ont_uptime.record_date');

                        if ($ticket_type === 'router') {
                            $sub->where('ur.booking_id', 'like', 'INC%');
                        } else {
                            $sub->where('ur.booking_id', 'not like', 'INC%');
                        }
                    });
                }


                if ($uptime_category === 'all') {
                    $results = $query->select(
                        'gp_list.lgd_code',
                        'gp_list.gp_name as gpname',
                        'districts.name as district',
                        'blocks.name as mandal',
                        DB::raw('ROUND(AVG(ont_uptime.uptime_percent), 2) as uptime_percent'),
                        DB::raw('MIN(ont_uptime.record_date) as record_date')
                    )
                    ->groupBy('gp_list.lgd_code', 'gp_list.gp_name', 'districts.name', 'blocks.name')
                    ->orderBy('uptime_percent', 'asc')
                    ->paginate(15);
                    $results = $this->attachEmptyBreakdowns($results);
                } else {
                    $results = $query->select(
                        'gp_list.lgd_code',
                        'gp_list.gp_name as gpname',
                        'districts.name as district',
                        'blocks.name as mandal',
                        'ont_uptime.uptime_percent',
                        'ont_uptime.record_date'
                    )
                        ->orderBy('ont_uptime.uptime_percent', 'asc')
                        ->paginate(15);

                   $results = $this->attachTicketBreakdowns($results, $state_id, $ticket_type);
                }

                $is_uptime_report = true;

                return view('admin.reports.frequently_down_gps', compact('results', 'districts', 'blocks', 'from_date', 'to_date', 'district_id', 'block_id',  'issue_filter', 'allIssues', 'is_uptime_report', 'router_category','ticket_type', 'sammriddh'));

        }
        if($router_category){
              $query = GpRouterUptime::query()
                        ->join('gp_list', 'gp_list.lgd_code', '=', 'gp_router_uptime.lgd_code')
                        ->join('districts', 'gp_list.district_id', '=', 'districts.id')
                        ->join('blocks', 'gp_list.block_id', '=', 'blocks.id')
                        ->where('gp_list.state_id', $state_id);
               if ($from_date && $to_date) {
                    $query->whereBetween('gp_router_uptime.record_date', [$from_date, $to_date]);
                }

                if ($district_id) {
                    $query->where('gp_list.district_id', $district_id);
                }

                if ($block_id) {
                    $query->where('gp_list.block_id', $block_id);
                }

                switch ($router_category) {
                    case 'all':
                        // Average Uptime % redirect: show all GP-router rows with their own uptime percentages.
                        break;
                    case 'gte98':
                        $query->where('gp_router_uptime.uptime_percent', '>=', 98);
                        break;
                    case 'lt98':
                        $query->where('gp_router_uptime.uptime_percent', '>', 0)->where('gp_router_uptime.uptime_percent', '<', 98);
                        break;
                    case 'zero_availability':
                        $query->where('gp_router_uptime.uptime_percent', '=', 0);
                        break;
                   case 'integration':
                    $this->applyIntegrationFilter($query, 'gp_router_uptime');
                    break;
                 }
            

                if ($issue_filter) {
                    $query->whereExists(function ($sub) use ($issue_filter, $state_id) {
                        $sub->select(DB::raw(1))
                            ->from('master_tickets as mt')
                            ->join('user_requests as ur', 'mt.ticketid', '=', 'ur.booking_id')
                            ->whereColumn('mt.lgd_code', 'gp_list.lgd_code')
                            ->where('mt.downreason', $issue_filter)
                            ->where('ur.state_id', $state_id)
                            ->whereRaw('DATE(mt.downdate) = gp_router_uptime.record_date')
                            ->where('ur.booking_id', 'like', 'INC%');
                        });
                }


                if ($router_category === 'all') {
                    $results = $query->select(
                        'gp_list.lgd_code',
                        'gp_list.gp_name as gpname',
                        'districts.name as district',
                        'blocks.name as mandal',
                        DB::raw('ROUND(AVG(gp_router_uptime.uptime_percent), 2) as uptime_percent'),
                        DB::raw('MIN(gp_router_uptime.record_date) as record_date')
                    )
                    ->groupBy('gp_list.lgd_code', 'gp_list.gp_name', 'districts.name', 'blocks.name')
                    ->orderBy('uptime_percent', 'asc')
                    ->paginate(15);
                    $results = $this->attachEmptyBreakdowns($results);
                } else {
                    $results = $query->select(
                        'gp_list.lgd_code',
                        'gp_list.gp_name as gpname',
                        'districts.name as district',
                        'blocks.name as mandal',
                        'gp_router_uptime.uptime_percent',
                        'gp_router_uptime.record_date'
                    )
                        ->orderBy('gp_router_uptime.uptime_percent', 'asc')
                        ->paginate(15);

                    $results = $this->attachTicketBreakdowns($results, $state_id, $ticket_type);
                }


                $is_uptime_report = true;

                return view('admin.reports.frequently_down_gps', compact('results', 'districts', 'blocks', 'from_date', 'to_date', 'district_id', 'block_id',  'issue_filter', 'allIssues', 'is_uptime_report', 'Blockrouter_category','ticket_type', 'sammriddh'));


        }
       
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


                    if ($ticket_type === 'router') {
                        $query->where('user_requests.booking_id', 'like', 'INC%');
                    } else {
                        $query->where(function($q) {
                            $q->where('user_requests.booking_id', 'not like', 'INC%')
                              ->orWhereNull('user_requests.booking_id');
                        });
                    }

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

        $is_uptime_report = false;

    

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
                'allIssues',
                'is_uptime_report'
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
            $ticketType = $request->get('ticket_type', 'ont'); // 'ont' or 'router'
            $query = DB::table('master_tickets')
                ->join('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                ->where('user_requests.state_id', $state_id)
                ->whereNotNull('master_tickets.lgd_code');
            
          // Filter by ticket type based on booking_id
            if ($ticketType === 'router') {
                // Router tickets: booking_id LIKE 'INC%'
                $query->where('user_requests.booking_id', 'like', 'INC%');
            } else {
                // ONT tickets: booking_id NOT LIKE 'INC%'
                $query->where(function($q) {
                    $q->where('user_requests.booking_id', 'not like', 'INC%')
                      ->orWhereNull('user_requests.booking_id');
                });
            }
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
    

    public function exportFrequentlyDownGps(Request $request)
    {
        $user = Session::get('user');
        $state_id = $user->state_id ?? 1;

        $from_date   = $request->get('from_date');
        $to_date     = $request->get('to_date');
        $district_id = $request->get('district_id');
        $block_id    = $request->get('block_id');
        $issue_filter = $request->get('issue_filter');
        $uptime_category = $request->get('uptime_category');
        $ticket_type = $request->get('ticket_type', 'ont'); // 'ont' or 'router'
        $sammriddh = $request->get('samriddh', false);
        $router_category = $request->get('router_category', null);
        $Blockrouter_category = $request->get('Blockrouter_category', null);
        $OLT_category = $request->get('OLT_category', null);

        $data = [];

        // --- Logic for Uptime Report Export ---
        if ($uptime_category) {
              $query = DB::table('ont_uptime')
                    ->join('gp_list', 'ont_uptime.lgd_code', '=', 'gp_list.lgd_code')
                    ->join('districts', 'gp_list.district_id', '=', 'districts.id')
                    ->join('blocks', 'gp_list.block_id', '=', 'blocks.id')
                    ->where('gp_list.state_id', $state_id);
                if ($sammriddh) {
                    $query->where('gp_list.samridh_stat', 1);
                }
                if ($from_date && $to_date) {
                    $query->whereBetween('ont_uptime.record_date', [$from_date, $to_date]);
                }

                if ($district_id) {
                    $query->where('gp_list.district_id', $district_id);
                }

                if ($block_id) {
                    $query->where('gp_list.block_id', $block_id);
                }


                switch ($uptime_category) {
                    case 'all':
                        break;
                    case 'gte98':
                        $query->where('ont_uptime.uptime_percent', '>=', 98);
                        break;
                    case 'gte90':
                        $query->where('ont_uptime.uptime_percent', '>=', 90)->where('ont_uptime.uptime_percent', '<', 98);
                        break;
                    case 'gte75':
                        $query->where('ont_uptime.uptime_percent', '>=', 75)->where('ont_uptime.uptime_percent', '<', 90);
                        break;
                    case 'gte50':
                        $query->where('ont_uptime.uptime_percent', '>=', 50)->where('ont_uptime.uptime_percent', '<', 75);
                        break;
                    case 'gte20':
                        $query->where('ont_uptime.uptime_percent', '>=', 20)->where('ont_uptime.uptime_percent', '<', 50);
                        break;
                    case 'lt20':
                        $query->where('ont_uptime.uptime_percent', '<', 20);
                        break;
                }
            if ($issue_filter) {
                $query->whereExists(function ($sub) use ($issue_filter, $state_id, $ticket_type) {
                    $sub->select(DB::raw(1))
                        ->from('master_tickets as mt')
                        ->join('user_requests as ur', 'mt.ticketid', '=', 'ur.booking_id')
                        ->whereColumn('mt.lgd_code', 'gp_list.lgd_code')
                        ->where('mt.downreason', $issue_filter)
                        ->where('ur.state_id', $state_id)
                        ->whereRaw('DATE(mt.downdate) = ont_uptime.record_date');

                    if ($ticket_type === 'router') {
                        $sub->where('ur.booking_id', 'like', 'INC%');
                    } else {
                        $sub->where('ur.booking_id', 'not like', 'INC%');
                    }
                });
            }



            $results = $query->select(
                'gp_list.lgd_code',
                'gp_list.gp_name as gpname',
                'districts.name as district',
                'blocks.name as mandal',
                'ont_uptime.uptime_percent',
                'ont_uptime.record_date'
            )
                ->orderBy('ont_uptime.uptime_percent', 'asc')
                ->get();


            $data[] = ['District', 'Block', 'GP Name', 'LGD Code', 'Uptime %', 'Record Date', 'Total Tickets', 'Issue Breakdown', 'Issue %'];

            foreach ($results as $row) {
                // Fetch Ticket Data for breakdown
                $breakdownQuery = DB::table('master_tickets')
                    ->join('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                    ->select('master_tickets.downreason', DB::raw('COUNT(*) as count'))
                    ->where('master_tickets.lgd_code', $row->lgd_code)
                    ->whereNotNull('master_tickets.downreason')
                    ->where('user_requests.state_id', $state_id);
                           // Filter by ticket type
                    if ($ticket_type === 'router') {
                        $breakdownQuery->where('user_requests.booking_id', 'like', 'INC%');
                    } else {
                        $breakdownQuery->where(function($q) {
                            $q->where('user_requests.booking_id', 'not like', 'INC%')
                              ->orWhereNull('user_requests.booking_id');
                        });
                    }

                if ($row->record_date) {
                    $breakdownQuery->whereDate('master_tickets.downdate', $row->record_date);
                } elseif ($from_date && $to_date) {
                    $breakdownQuery->whereBetween('master_tickets.downdate', [$from_date, $to_date]);
                }
                

                $breakdown = $breakdownQuery->groupBy('master_tickets.downreason')
                    ->orderBy('count', 'desc')
                    ->get();

                $ticket_count = $breakdown->sum('count');

                // Format breakdown string
                $breakdownStr = '';
                $issuePctStr = '';
                if ($ticket_count > 0) {
                    foreach ($breakdown as $bd) {
                        $breakdownStr .= $bd->downreason . ': ' . $bd->count . "\n";
                        $pct = round(($bd->count / $ticket_count) * 100, 1);
                        $issuePctStr .= $bd->downreason . ': ' . $pct . "%\n";
                    }
                } else {
                    $breakdownStr = '-';
                    $issuePctStr = '-';
                }


                $data[] = [
                    $row->district,
                    $row->mandal,
                    $row->gpname,
                    $row->lgd_code,
                    $row->uptime_percent . '%',
                    Carbon::parse($row->record_date)->format('d-m-Y'),
                    $ticket_count,
                    trim($breakdownStr),
                    trim($issuePctStr)
                ];
            }
            $filename = 'Uptime_Report_' . ($uptime_category) . '_' . date('Ymd_His');

        } else if($router_category){
              $query = GpRouterUptime::query()
                        ->join('gp_list', 'gp_list.lgd_code', '=', 'gp_router_uptime.lgd_code')
                        ->join('districts', 'gp_list.district_id', '=', 'districts.id')
                        ->join('blocks', 'gp_list.block_id', '=', 'blocks.id')
                        ->where('gp_list.state_id', $state_id);
               if ($from_date && $to_date) {
                    $query->whereBetween('gp_router_uptime.record_date', [$from_date, $to_date]);
                }

                if ($district_id) {
                    $query->where('gp_list.district_id', $district_id);
                }

                if ($block_id) {
                    $query->where('gp_list.block_id', $block_id);
                }

                switch ($router_category) {
                    case 'all':
                        break;
                    case 'gte98':
                        $query->where('gp_router_uptime.uptime_percent', '>=', 98);
                        break;
                    case 'lt98':
                        $query->where('gp_router_uptime.uptime_percent', '>', 0)->where('gp_router_uptime.uptime_percent', '<', 98);
                        break;
                    case 'zero_availability':
                        $query->where('gp_router_uptime.uptime_percent', '=', 0);
                        break;
                   case 'integration':
                    $this->applyIntegrationFilter($query, 'gp_router_uptime');
                    break;
                 }
            

                if ($issue_filter) {
                    $query->whereExists(function ($sub) use ($issue_filter, $state_id) {
                        $sub->select(DB::raw(1))
                            ->from('master_tickets as mt')
                            ->join('user_requests as ur', 'mt.ticketid', '=', 'ur.booking_id')
                            ->whereColumn('mt.lgd_code', 'gp_list.lgd_code')
                            ->where('mt.downreason', $issue_filter)
                            ->where('ur.state_id', $state_id)
                            ->whereRaw('DATE(mt.downdate) = gp_router_uptime.record_date')
                            ->where('ur.booking_id', 'like', 'INC%');
                        });
                }


                $results = $query->select(
                    'gp_list.lgd_code',
                    'gp_list.gp_name as gpname',
                    'districts.name as district',
                    'blocks.name as mandal',
                    'gp_router_uptime.uptime_percent',
                    'gp_router_uptime.record_date'
                )
                    ->orderBy('gp_router_uptime.uptime_percent', 'asc')
                    ->get();
            $data[] = ['District', 'Block', 'GP Name', 'LGD Code', 'Uptime %', 'Record Date', 'Total Tickets', 'Issue Breakdown', 'Issue %'];


                foreach ($results as $row) {
                    $breakdownQuery = DB::table('master_tickets')
                        ->join('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                        ->select('master_tickets.downreason', DB::raw('COUNT(*) as count'))
                        ->where('master_tickets.lgd_code', $row->lgd_code)
                        ->whereNotNull('master_tickets.downreason')
                        ->where('user_requests.state_id', $state_id)
                         ->where('user_requests.booking_id', 'like', 'INC%');

                    // Use the record_date from the row for filtering tickets
                    if ($row->record_date) {
                        $breakdownQuery->whereDate('master_tickets.downdate', $row->record_date);
                    } elseif ($from_date && $to_date) {
                        $breakdownQuery->whereBetween('master_tickets.downdate', [$from_date, $to_date]);
                    }
                    $breakdown = $breakdownQuery->groupBy('master_tickets.downreason')
                        ->orderBy('count', 'desc')
                        ->get();

                    $row->breakdown = $breakdown;
                    $ticket_count = $breakdown->sum('count');
                     // Format breakdown string
                $breakdownStr = '';
                $issuePctStr = '';
                if ($ticket_count > 0) {
                    foreach ($breakdown as $bd) {
                        $breakdownStr .= $bd->downreason . ': ' . $bd->count . "\n";
                        $pct = round(($bd->count / $ticket_count) * 100, 1);
                        $issuePctStr .= $bd->downreason . ': ' . $pct . "%\n";
                    }
                } else {
                    $breakdownStr = '-';
                    $issuePctStr = '-';
                }


                $data[] = [
                    $row->district,
                    $row->mandal,
                    $row->gpname,
                    $row->lgd_code,
                    $row->uptime_percent . '%',
                    Carbon::parse($row->record_date)->format('d-m-Y'),
                    $ticket_count,
                    trim($breakdownStr),
                    trim($issuePctStr)
                ];
                $filename = 'Router_Uptime_Report_' . ($router_category) . '_' . date('Ymd_His');
                }



        }else if($Blockrouter_category){
              $query = BlockRouterUptime::query()
                        ->join('blocks', 'blocks.routercode', '=', 'block_router_uptime.lgd_code')
                        ->join('districts', 'blocks.district_id', '=', 'districts.id')
                        ->where('districts.state_id', $state_id);
               if ($from_date && $to_date) {
                    $query->whereBetween('block_router_uptime.record_date', [$from_date, $to_date]);
                }

                if ($district_id) {
                    $query->where('blocks.district_id', $district_id);
                }

                if ($block_id) {
                    $query->where('blocks.id', $block_id);
                }

                switch ($Blockrouter_category) {
                    case 'all':
                        break;
                    case 'gte98':
                        $query->where('block_router_uptime.uptime_percent', '>=', 98);
                        break;
                    case 'lt98':
                        $query->where('block_router_uptime.uptime_percent', '>', 0)->where('block_router_uptime.uptime_percent', '<', 98);
                        break;
                    case 'zero_availability':
                        $query->where('block_router_uptime.uptime_percent', '=', 0);
                        break;
                    case 'integration':
                            $this->applyIntegrationFilter($query, 'block_router_uptime');
                            break;
                 }
                $results = $query->select(
                    'block_router_uptime.lgd_code',
                    'districts.name as district',
                    'blocks.name as mandal',
                    'blocks.name as gpname',
                    'block_router_uptime.uptime_percent',
                    'block_router_uptime.record_date'
                )
                ->orderBy('block_router_uptime.uptime_percent', 'asc')
                ->get();

                 $data[] = ['District', 'Block', 'Block Name', 'LGD Code', 'Uptime %', 'Record Date', 'Total Tickets', 'Issue Breakdown', 'Issue %'];

                foreach ($results as $row) {
                    $ticket_count = 0;
                    $breakdownStr = '';
                    $issuePctStr = '';
               

                    $data[] = [
                        $row->district,
                        $row->mandal,
                        $row->gpname,
                        $row->lgd_code,
                        $row->uptime_percent . '%',
                        Carbon::parse($row->record_date)->format('d-m-Y'),
                        $ticket_count,
                        trim($breakdownStr),
                        trim($issuePctStr)
                    ];    
               
                    $filename = 'Block_Router_Uptime_Report_' . ($Blockrouter_category) . '_' . date('Ymd_His');
                }

        }else if($OLT_category){
                $query = OltUptime::query()
                        ->join('olt_locations', 'olt_locations.lgd_code', '=', 'olt_uptime.lgd_code')
                        ->join('districts','olt_locations.district_id','=','districts.id')
                        ->join('blocks','olt_locations.block_id','=','blocks.id')
                        ->where('olt_locations.state_id', $state_id);
                       
               if ($from_date && $to_date) {
                    $query->whereBetween('olt_uptime.record_date', [$from_date, $to_date]);
                }

                if ($district_id) {
                    $query->where('olt_locations.district_id', $district_id);
                }

                if ($block_id) {
                    $query->where('blocks.id', $block_id);
                }

                switch ($OLT_category) {
                    case 'all':
                        break;
                       case 'gte98':
                        $query->where('olt_uptime.uptime_percent', '>=', 98);
                        break;
                    case 'gte90':
                        $query->where('olt_uptime.uptime_percent', '>=', 90)->where('olt_uptime.uptime_percent', '<', 98);
                        break;
                    case 'gte75':
                        $query->where('olt_uptime.uptime_percent', '>=', 75)->where('olt_uptime.uptime_percent', '<', 90);
                        break;
                    case 'gte50':
                        $query->where('olt_uptime.uptime_percent', '>=', 50)->where('olt_uptime.uptime_percent', '<', 75);
                        break;
                    case 'gte20':
                        $query->where('olt_uptime.uptime_percent', '>=', 20)->where('olt_uptime.uptime_percent', '<', 50);
                        break;
                    case 'lt20':
                        $query->where('olt_uptime.uptime_percent', '<', 20);
                        break;
                 }
                $results = $query->select(
                    'olt_uptime.lgd_code',
                    'olt_locations.olt_location as gpname',
                    'districts.name as district',
                    'blocks.name as mandal',
                    'olt_uptime.uptime_percent',
                    'olt_uptime.record_date'
                )
                ->orderBy('olt_uptime.uptime_percent', 'asc')
                ->get();

                 $data[] = ['District', 'Block', 'OLT Name', 'LGD Code', 'Uptime %', 'Record Date', 'Total Tickets', 'Issue Breakdown', 'Issue %'];

                foreach ($results as $row) {
                    $ticket_count = 0;
                    $breakdownStr = '';
                    $issuePctStr = '';
               

                    $data[] = [
                        $row->district,
                        $row->mandal,
                        $row->gpname,
                        $row->lgd_code,
                        $row->uptime_percent . '%',
                        Carbon::parse($row->record_date)->format('d-m-Y'),
                        $ticket_count,
                        trim($breakdownStr),
                        trim($issuePctStr)
                    ];    
               
                    $filename = 'OLT_Uptime_Report_' . ($OLT_category) . '_' . date('Ymd_His');
                }


        }

        else {
            // --- Logic for Standard Frequently Down GPs Export ---
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
            
                    if ($ticket_type === 'router') {
                        $query->where('user_requests.booking_id', 'like', 'INC%');
                    } else {
                        $query->where(function($q) {
                            $q->where('user_requests.booking_id', 'not like', 'INC%')
                              ->orWhereNull('user_requests.booking_id');
                        });
                    }
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
                ->limit(1000)->get();

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

           $data[] = ['District', 'Block', 'GP Name', 'LGD Code', 'Total Tickets', 'Issue Breakdown', 'Issue %'];

            foreach ($results as $row) {
                
            $row->breakdown = $breakdowns[$row->lgd_code] ?? [];

            $row->total_breakdown_count =
                array_sum(array_column($row->breakdown, 'count'));

            $row->top_reason =
            $row->breakdown[0]->downreason ?? 'N/A';

             
                $breakdownStr = '';
                $issuePctStr = '';

                if ($row->ticket_count > 0 && !empty($row->breakdown)) {

                    foreach ($row->breakdown as $bd) {

                        $breakdownStr .= $bd->downreason . ': ' . $bd->count . "\n";

                        $pct = round(($bd->count / $row->ticket_count) * 100, 1);

                        $issuePctStr .= $bd->downreason . ': ' . $pct . "%\n";
                    }

                } else {

                    $breakdownStr = '-';
                    $issuePctStr = '-';

                }

                $data[] = [
                    $row->district,
                    $row->mandal,
                    $row->gpname,
                    $row->lgd_code,
                    $row->ticket_count,
                    trim($breakdownStr),
                    trim($issuePctStr)
                ];

            }
            $filename = 'Frequently_Down_GPs_' . date('Ymd_His');
        }

        return Excel::create($filename, function ($excel) use ($data) {
            $excel->sheet('Sheet1', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

}
