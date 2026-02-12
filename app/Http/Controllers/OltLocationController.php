<?php

namespace App\Http\Controllers;

use App\OltLocation;
use App\State;
use App\District;
use App\Block;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;
use Excel;
use DB;
use Setting;
use App\OntUptime;
use App\OltUptime;
use \Carbon\Carbon;


class OltLocationController extends Controller
{
     public function index(Request $request)
    {
        Session::put('user', Auth::user());
        $user = Session::get('user');
        $company_id = $user->company_id;
        $state_id = $user->state_id;
        $district_id = $user->district_id;

        $query = OltLocation::with(['state', 'district', 'block'])->where('state_id', $state_id);
         if (!empty($district_id)) {
            $query->where('district_id', $district_id);
        }

        if ($request->has('search') && !empty($request->search) ) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('olt_location', 'like', "%{$search}%")
                  ->orWhere('olt_location_code', 'like', "%{$search}%")
                  ->orWhere('lgd_code', 'like', "%{$search}%")
                  ->orWhere('olt_ip', 'like', "%{$search}%")
                  ->orWhereHas('district', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('block', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

      

        $oltLocations = $query->orderBy('created_at', 'desc')->paginate(10);
      

        return view('admin.olt-locations.index', compact('oltLocations'));
    }

    public function create()
    {
        Session::put('user', Auth::user());
        $user = Session::get('user');
        $company_id = $user->company_id;
        $state_id = $user->state_id;
        $district_id = $user->district_id;
        $query = District::where('state_id', $state_id);
           if (!empty($district_id)) {
            $query->where('id', $district_id);
        }
        $districts = $query->get();
        return view('admin.olt-locations.create', compact('districts'));
    }

    public function store(Request $request)
    {
        Session::put('user', Auth::user());
        $user = Session::get('user');
        $state_id = $user->state_id;

        // Validation
        $validator = Validator::make($request->all(), [
            'district_id' => 'required|exists:districts,id',
            'block_id' => 'required|exists:blocks,id',
            'olt_location' => 'required|string|max:255',
            'olt_location_code' => 'required|string|max:255|unique:olt_locations,olt_location_code',
            'lgd_code' => 'required|string|max:255|unique:olt_locations,lgd_code',
            'olt_ip' => 'required|ip|unique:olt_locations,olt_ip', 
            'no_of_gps' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        $data = $request->all();
        $data['state_id'] = $state_id;

        OltLocation::create($data);

        return redirect()->route('admin.olt-locations.index')
                        ->with('success', 'OLT location created successfully.');
    }


    public function show($id)
    {
        $location = OltLocation::with(['state', 'district', 'block'])->findOrFail($id);

        return view('admin.olt-locations.show', compact('location'));
    }

   
    public function edit($id)
    {
        Session::put('user', Auth::user());
        $user = Session::get('user');
        $company_id = $user->company_id;
        $state_id = $user->state_id;
        $district_id = $user->district_id;
       
        $location = OltLocation::with(['state', 'district', 'block'])->findOrFail($id);

        $states = State::all();
        $query = District::where('state_id', $location->state_id);
       
           if (!empty($district_id)) {
            $query->where('id', $district_id);
        }
        $districts = $query->get();
        $blocksQuery = Block::where('district_id', $location->district_id);
          if (!empty($district_id)) {
            $blocksQuery->where('district_id', $district_id);
        }
        $blocks = $blocksQuery->get();

        return view('admin.olt-locations.edit', compact('location', 'states', 'districts', 'blocks'));
    }


  
    public function update($id,Request $request)
    {
        $oltLocation = OltLocation::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'district_id' => 'required|exists:districts,id',
            'block_id' => 'required|exists:blocks,id',
            'olt_location' => 'required|string|max:255',
            'olt_location_code' => 'required|string|max:255|unique:olt_locations,olt_location_code,'.$oltLocation->id,
            'lgd_code' => 'required|string|max:255|unique:olt_locations,lgd_code,'.$oltLocation->id,
            'olt_ip' => 'required|ip|unique:olt_locations,olt_ip,'.$oltLocation->id,
            'no_of_gps' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Session::put('user', Auth::user());
        $user = Session::get('user');
        $state_id = $user->state_id;
        $oltLocation->update([
            'state_id' => $state_id,
            'district_id' => $request->district_id,
            'block_id' => $request->block_id,
            'olt_location' => $request->olt_location,
            'olt_location_code' => $request->olt_location_code,
            'lgd_code' => $request->lgd_code,
            'olt_ip' => $request->olt_ip,
            'no_of_gps' => $request->no_of_gps,
        ]);


        return redirect()->route('admin.olt-locations.index')
            ->with('success', 'OLT location updated successfully.');
    }


    public function destroy($id)
    {
        $deleted = OltLocation::where('id', $id)->delete();

        return response()->json([
            'success' => (bool) $deleted,
            'message' => $deleted ? 'OLT location deleted successfully.' : 'Failed to delete OLT location.'
        ]);
    }


    public function getDistricts(Request $request)
    {
        $stateId = $request->state_id;
        $districts = District::where('state_id', $stateId)->get(['id', 'name']);
        
        return response()->json($districts);
    }

    public function getBlocks(Request $request)
    {
        $districtId = $request->district_id;
        $blocks = Block::where('district_id', $districtId)->get(['id', 'name']);
        
        return response()->json($blocks);
    }
    public function ExportOlt(Request $request){
        Session::put('user', Auth::user());
        $user = Session::get('user');
        $company_id = $user->company_id;
        $state_id = $user->state_id;
        $district_id = $user->district_id;

        $query = OltLocation::with(['state', 'district', 'block'])->where('state_id', $state_id);
       
           if (!empty($district_id)) {
            $query->where('district_id', $district_id);
        }
       

        if ($request->has('search') && !empty($request->search) ) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('olt_location', 'like', "%{$search}%")
                  ->orWhere('olt_location_code', 'like', "%{$search}%")
                  ->orWhere('lgd_code', 'like', "%{$search}%")
                  ->orWhere('olt_ip', 'like', "%{$search}%")
                  ->orWhereHas('district', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('block', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

    $oltLocations = $query->orderBy('created_at', 'desc')->get();
    $fileName = 'olt.xlsx';
  
    $excelContent = Excel::create('olt', function($excel) use ($oltLocations) {
        $excel->sheet('Sheet1', function($sheet) use ($oltLocations) {
            $data = [];
            // Headings
            $data[] = [
                'State','District','Block','Olt Location','Olt Location Code',
                'LGD Code','Olt IP','No Of Gps'
            ];

            foreach($oltLocations as $location){
                $data[] = [
                    $location->state->state_name,
                    $location->district->name, 
                    $location->block->name,
                    $location->olt_location,
                    $location->olt_location_code,
                     $location->lgd_code,
                     $location->olt_ip,
                    $location->no_of_gps
                  
                ];
            }

            $sheet->fromArray($data, null, 'A1', false, false);
        });
    })->string('xlsx');

    return response($excelContent, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
    ]);


    }
    public function UptimeMng (){
        return view('admin.olt-locations.uptime');
    }

public function OntUpload(Request $request)
{
    $this->validate($request, [
        'csv_file' => 'required|mimes:csv,txt|max:2048',
    ]);

    $path = $request->file('csv_file')->getRealPath();
    $content = file_get_contents($path);
    $content = preg_replace("/\r\n|\r/", "\n", $content);
    file_put_contents($path, $content);
    $file = fopen($path, 'r');
    $header = fgetcsv($file);
    $records = [];

    try {
        while (($row = fgetcsv($file)) !== false) {
            if ($row === null || empty(array_filter($row))) continue;

            $date = null;
            if (!empty($row[2])) {
                try {
                    $date = \Carbon\Carbon::parse($row[2])->format('Y-m-d');
                } catch (\Exception $e) {
                    throw new \Exception("Invalid date format in CSV.");
                }
            }

            $data = [
                'lgd_code' => $row[0],
                'uptime_percent' => $row[1],
                'record_date' => $date,
            ];

            OntUptime::create($data);
            $records[] = $data;
        }
    } catch (\Exception $e) {
        fclose($file);
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
    }

    fclose($file);

    return response()->json([
        'status' => 'success',
        'message' => 'CSV uploaded successfully!',
        'records' => $records
    ]);
}
public function OltUpload(Request $request)
{
    $this->validate($request, [
        'csv_file' => 'required|mimes:csv,txt|max:2048',
    ]);

    $path = $request->file('csv_file')->getRealPath();
    $content = file_get_contents($path);
    $content = preg_replace("/\r\n|\r/", "\n", $content);
    file_put_contents($path, $content);
    $file = fopen($path, 'r');
    $header = fgetcsv($file);
    $records = [];

    try {
        while (($row = fgetcsv($file)) !== false) {
            if ($row === null || empty(array_filter($row))) continue;

            $date = null;
            if (!empty($row[2])) {
                try {
                    $date = \Carbon\Carbon::parse($row[2])->format('Y-m-d');
                } catch (\Exception $e) {
                    throw new \Exception("Invalid date format in CSV.");
                }
            }

            $data = [
                'lgd_code' => $row[0],
                'uptime_percent' => $row[1],
                'record_date' => $date,
            ];

            OltUptime::create($data);
            $records[] = $data;
        }
    } catch (\Exception $e) {
        fclose($file);
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
    }

    fclose($file);

    return response()->json([
        'status' => 'success',
        'message' => 'CSV uploaded successfully!',
        'records' => $records
    ]);
}



public function OntData(Request $request)
{
    $month    = $request->get('month');
    $fromDate = $request->get('fromDate');
    $toDate   = $request->get('toDate');

    Session::put('user', Auth::User());
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    $district_id = $user->district_id;


    $query = OntUptime::query()
             ->join('gp_list', 'gp_list.lgd_code', '=', 'ont_uptime.lgd_code')
        ->where('gp_list.company_id', $company_id)
        ->where('gp_list.state_id', $state_id);
        if (!empty($district_id)) {
            $query->where('gp_list.district_id', $district_id);
        }

    if (!empty($month)) {
        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
            $query->whereBetween('record_date', [$start, $end]);
        } catch (\Exception $e) {
            $query->whereBetween('record_date', [
                Carbon::now()->subDays(6)->toDateString(),
                Carbon::now()->toDateString()
            ]);
        }
    } elseif (!empty($fromDate) && !empty($toDate)) {
        $query->whereBetween('record_date', [$fromDate, $toDate]);
    } else {
        $query->whereBetween('record_date', [
            Carbon::now()->subDays(6)->toDateString(),
            Carbon::now()->toDateString()
        ]);
    }

    $data = $query
        ->selectRaw('DATE(record_date) as day')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 98 THEN 1 ELSE 0 END) as gte98')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 90 AND uptime_percent < 98 THEN 1 ELSE 0 END) as gte90')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 75 AND uptime_percent < 90 THEN 1 ELSE 0 END) as gte75')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 50 AND uptime_percent < 75 THEN 1 ELSE 0 END) as gte50')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 20 AND uptime_percent < 50 THEN 1 ELSE 0 END) as gte20')
        ->selectRaw('SUM(CASE WHEN uptime_percent < 20 THEN 1 ELSE 0 END) as lt20')
        ->selectRaw('COUNT(*) as total')
        ->selectRaw('ROUND(SUM(CASE WHEN uptime_percent >= 98 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as pct_gte98')
        ->groupBy('day')
        ->orderBy('day', 'asc')
        ->get();

    $averages = [
    'gte98'     => round($data->avg('gte98'), 2),
    'gte90'     => round($data->avg('gte90'), 2),
    'gte75'     => round($data->avg('gte75'), 2),
    'gte50'     => round($data->avg('gte50'), 2),
    'gte20'     => round($data->avg('gte20'), 2),
    'lt20'      => round($data->avg('lt20'), 2),
    'total'     => round($data->avg('total'), 2),
    'pct_gte98' => round($data->avg('pct_gte98'), 2),
    ];


   return response()->json([
    'data' => $data,
    'averages' => $averages
]);

}

  public function OntDataList(Request $request)
{
    Session::put('user', Auth::User());
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id   = $user->state_id;
    $district_id = $user->district_id;
    $month    = $request->get('month');
    $fromDate = $request->get('fromDate');
    $toDate   = $request->get('toDate');
    $query = OntUptime::query()
        ->join('gp_list', 'gp_list.lgd_code', '=', 'ont_uptime.lgd_code')
        ->join('districts','gp_list.district_id','=','districts.id')
        ->join('blocks','gp_list.block_id','=','blocks.id')
        ->leftJoin('zonal_managers','gp_list.zonal_id','=','zonal_managers.id')
        ->where('gp_list.company_id', $company_id)
        ->where('gp_list.state_id', $state_id);
        if (!empty($district_id)) {
            $query->where('gp_list.district_id', $district_id);
        }
        if (!empty($month)) {
        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
            $query->whereBetween('record_date', [$start, $end]);
        } catch (\Exception $e) {
            $query->whereBetween('record_date', [
                Carbon::now()->subDays(6)->toDateString(),
                Carbon::now()->toDateString()
            ]);
        }
        } elseif (!empty($fromDate) && !empty($toDate)) {
            $query->whereBetween('record_date', [$fromDate, $toDate]);
        } else {
            $query->whereBetween('record_date', [
                Carbon::now()->subDays(6)->toDateString(),
                Carbon::now()->toDateString()
            ]);
        }
       
    $records = $query->orderBy('ont_uptime.id', 'des')
                ->select(
                    'ont_uptime.*',
                    'districts.name as district_name',
                    'blocks.name as block_name',
                    'zonal_managers.name as zone_name',
                    'gp_list.phase',
                    'gp_list.gp_name'
                )->paginate(10);

    return response()->json([
        'data' => $records,
    ]);

}
public function OltData(Request $request)
{
    $month    = $request->get('month');
    $fromDate = $request->get('fromDate');
    $toDate   = $request->get('toDate');
    

    Session::put('user', Auth::User());
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    $district_id = $user->district_id;


    $query = OltUptime::query()
             ->join('olt_locations', 'olt_locations.lgd_code', '=', 'olt_uptime.lgd_code')
        ->where('olt_locations.state_id', $state_id);
          if (!empty($district_id)) {
            $query->where('olt_locations.district_id', $district_id);
        }

    if (!empty($month)) {
        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
            $query->whereBetween('record_date', [$start, $end]);
        } catch (\Exception $e) {
            $query->whereBetween('record_date', [
                Carbon::now()->subDays(6)->toDateString(),
                Carbon::now()->toDateString()
            ]);
        }
    } elseif (!empty($fromDate) && !empty($toDate)) {
        $query->whereBetween('record_date', [$fromDate, $toDate]);
    } else {
        $query->whereBetween('record_date', [
            Carbon::now()->subDays(6)->toDateString(),
            Carbon::now()->toDateString()
        ]);
    }

    $data = $query
        ->selectRaw('DATE(record_date) as day')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 98 THEN 1 ELSE 0 END) as gte98')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 90 AND uptime_percent < 98 THEN 1 ELSE 0 END) as gte90')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 75 AND uptime_percent < 90 THEN 1 ELSE 0 END) as gte75')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 50 AND uptime_percent < 75 THEN 1 ELSE 0 END) as gte50')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 20 AND uptime_percent < 50 THEN 1 ELSE 0 END) as gte20')
        ->selectRaw('SUM(CASE WHEN uptime_percent < 20 THEN 1 ELSE 0 END) as lt20')
        ->selectRaw('COUNT(*) as total')
        ->selectRaw('ROUND(SUM(CASE WHEN uptime_percent >= 98 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as pct_gte98')
        ->groupBy('day')
        ->orderBy('day', 'asc')
        ->get();

    $averages = [
    'gte98'     => round($data->avg('gte98'), 2),
    'gte90'     => round($data->avg('gte90'), 2),
    'gte75'     => round($data->avg('gte75'), 2),
    'gte50'     => round($data->avg('gte50'), 2),
    'gte20'     => round($data->avg('gte20'), 2),
    'lt20'      => round($data->avg('lt20'), 2),
    'total'     => round($data->avg('total'), 2),
    'pct_gte98' => round($data->avg('pct_gte98'), 2),
    ];


   return response()->json([
    'data' => $data,
    'averages' => $averages
]);

}
  public function OltDataList()
{
    Session::put('user', Auth::User());
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id   = $user->state_id;
    $district_id = $user->district_id;
    $query = OltUptime::query()
        ->join('olt_locations', 'olt_locations.lgd_code', '=', 'olt_uptime.lgd_code')
        ->join('districts','olt_locations.district_id','=','districts.id')
        ->join('blocks','olt_locations.block_id','=','blocks.id')
        ->where('olt_locations.state_id', $state_id);
        if (!empty($district_id)) {
            $query->where('olt_locations.district_id', $district_id);
        }
      

    $records = $query->orderBy('olt_uptime.id', 'asc')
                ->select(
                    'olt_uptime.*',
                    'districts.name as district_name',
                    'blocks.name as block_name',
                    'olt_locations.olt_location',
                    'olt_locations.olt_ip',
                    'olt_locations.no_of_gps'
                    
                )->paginate(10);

    return response()->json([
        'data' => $records,
    ]);

}
public function SamriddhData(Request $request)
{
    $month    = $request->get('month');
    $fromDate = $request->get('fromDate');
    $toDate   = $request->get('toDate');

    Session::put('user', Auth::User());
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    $district_id = $user->district_id;


    $query = OntUptime::query()
             ->join('gp_list', 'gp_list.lgd_code', '=', 'ont_uptime.lgd_code')
        ->where('gp_list.company_id', $company_id)
        ->where('gp_list.state_id', $state_id)
        ->where('gp_list.samridh_stat',1);
        if (!empty($district_id)) {
            $query->where('gp_list.district_id', $district_id);
        }

    if (!empty($month)) {
        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
            $query->whereBetween('record_date', [$start, $end]);
        } catch (\Exception $e) {
            $query->whereBetween('record_date', [
                Carbon::now()->subDays(6)->toDateString(),
                Carbon::now()->toDateString()
            ]);
        }
    } elseif (!empty($fromDate) && !empty($toDate)) {
        $query->whereBetween('record_date', [$fromDate, $toDate]);
    } else {
        $query->whereBetween('record_date', [
            Carbon::now()->subDays(6)->toDateString(),
            Carbon::now()->toDateString()
        ]);
    }

    $data = $query
        ->selectRaw('DATE(record_date) as day')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 98 THEN 1 ELSE 0 END) as gte98')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 90 AND uptime_percent < 98 THEN 1 ELSE 0 END) as gte90')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 75 AND uptime_percent < 90 THEN 1 ELSE 0 END) as gte75')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 50 AND uptime_percent < 75 THEN 1 ELSE 0 END) as gte50')
        ->selectRaw('SUM(CASE WHEN uptime_percent >= 20 AND uptime_percent < 50 THEN 1 ELSE 0 END) as gte20')
        ->selectRaw('SUM(CASE WHEN uptime_percent < 20 THEN 1 ELSE 0 END) as lt20')
        ->selectRaw('COUNT(*) as total')
        ->selectRaw('ROUND(SUM(CASE WHEN uptime_percent >= 98 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as pct_gte98')
        ->groupBy('day')
        ->orderBy('day', 'asc')
        ->get();

    $averages = [
    'gte98'     => round($data->avg('gte98'), 2),
    'gte90'     => round($data->avg('gte90'), 2),
    'gte75'     => round($data->avg('gte75'), 2),
    'gte50'     => round($data->avg('gte50'), 2),
    'gte20'     => round($data->avg('gte20'), 2),
    'lt20'      => round($data->avg('lt20'), 2),
    'total'     => round($data->avg('total'), 2),
    'pct_gte98' => round($data->avg('pct_gte98'), 2),
    ];


   return response()->json([
    'data' => $data,
    'averages' => $averages
]);

}

  public function SamriddhDataList()
{
    Session::put('user', Auth::User());
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id   = $user->state_id;
    $district_id = $user->district_id;
    $query = OntUptime::query()
        ->join('gp_list', 'gp_list.lgd_code', '=', 'ont_uptime.lgd_code')
        ->join('districts','gp_list.district_id','=','districts.id')
        ->join('blocks','gp_list.block_id','=','blocks.id')
        ->leftJoin('zonal_managers','gp_list.zonal_id','=','zonal_managers.id')
        ->where('gp_list.company_id', $company_id)
        ->where('gp_list.state_id', $state_id)
        ->where('gp_list.samridh_stat',1);
       
     if (!empty($district_id)) {
            $query->where('gp_list.district_id', $district_id);
        }
    $records = $query->orderBy('ont_uptime.id', 'asc')
                ->select(
                    'ont_uptime.*',
                    'districts.name as district_name',
                    'blocks.name as block_name',
                    'zonal_managers.name as zone_name',
                    'gp_list.phase',
                    'gp_list.gp_name'
                )->paginate(10);

    return response()->json([
        'data' => $records,
    ]);

}

}