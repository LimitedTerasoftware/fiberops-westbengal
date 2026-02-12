<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\DistanceHelper;

use DB;
use Exception;
use Setting;
use Storage;
use Session;
use Auth;

use App\Helpers\Helper;
use Mail;
use DateTime;
use App\District;
use App\Block;
use App\Provider;
use Carbon\Carbon;
use DatePeriod;
use DateInterval;
use App\UserRequests;
use App\MasterTicket;
use App\SubServiceType;
use Illuminate\Support\Facades\Validator;
use App\Leave;

use ZipArchive;
use Illuminate\Support\Facades\File;



class GPResource extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('demo', ['only' => [ 'store', 'update', 'destroy', 'disapprove']]);
        $this->perpage = Setting::get('per_page', '10');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Session::get('user');
	    $company_id = $user->company_id;
	    $state_id = $user->state_id;
        $district_id = $user->district_id;
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth   = Carbon::now()->endOfMonth()->toDateString();
        $type = $request->get('type'); 
        $fromDate = $request->get('from');
        $toDate = $request->get('to');

        $regularlyDownLgdCodes = [];

        if ($type === 'regularly_down' && $fromDate && $toDate) {

            $expectedWeeks = DB::table('master_tickets')
                ->whereBetween('downdate', [$fromDate, $toDate])
                ->select(DB::raw('COUNT(DISTINCT YEARWEEK(downdate, 1)) as weeks'))
                ->value('weeks');

            $regularlyDownLgdCodes = DB::table('master_tickets')
                ->join('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                ->where('user_requests.state_id', $state_id)
                ->whereBetween('master_tickets.downdate', [$fromDate, $toDate])
                ->select(
                    'master_tickets.lgd_code',
                      DB::raw('COUNT(DISTINCT DATE(master_tickets.downdate)) as down_days'),
                    DB::raw('COUNT(DISTINCT YEARWEEK(master_tickets.downdate, 1)) as distinct_weeks')
                )
                ->groupBy('master_tickets.lgd_code')
                ->having('distinct_weeks', '=', $expectedWeeks)
                ->orderBy('down_days', 'desc')
                ->pluck('lgd_code')
                ->toArray();
        }


      $uptimeSubQuery = "
                        SELECT 
                            lgd_code,
                            ROUND(AVG(uptime_percent), 2) AS avg_uptime
                        FROM ont_uptime
                        WHERE record_date BETWEEN '{$startOfMonth}' AND '{$endOfMonth}'
                        GROUP BY lgd_code
                    ";



        $gpsQuery = DB::table('gp_list')
                        ->select('gp_list.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name','zonal_managers.Name as zonal_name','blocks.id as blocks_id',
                                DB::raw('uptime.avg_uptime'))
                        ->leftJoin('districts', 'gp_list.district_id', '=', 'districts.id')
                        ->leftJoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id')
                        ->leftJoin('blocks', 'gp_list.block_id', '=', 'blocks.id')
                        ->leftJoin(
                            DB::raw("({$uptimeSubQuery}) AS uptime"),
                            'gp_list.lgd_code',
                            '=',
                            'uptime.lgd_code'
                        )
                        ->where('gp_list.state_id',$state_id);
                           
                     
                        
         if (!empty($district_id)) {
            $gpsQuery->where('gp_list.district_id', $district_id);
        }
        if ($type === 'regularly_down') {
            $gpsQuery->whereIn('gp_list.lgd_code', $regularlyDownLgdCodes);
        }

        $gps = $gpsQuery->get();
                        // ->paginate($this->perpage);
        // $pagination=(new Helper)->formatPagination($gps);
        // dd($pagination);
        return view('admin.gps.index',compact('gps'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()

    {   $user = Session::get('user');
	    $company_id = $user->company_id;
	    $state_id = $user->state_id;
        $district_id = $user->district_id;

        $zoneIdsQuery = DB::table('gp_list')->where('state_id', $state_id);
        if (!empty($district_id)) {
            $zoneIdsQuery->where('district_id', $district_id);
        }

        $zoneIds = $zoneIdsQuery->pluck('zonal_id')->unique();
        $zonals = DB::table('zonal_managers')->whereIn('id',$zoneIds)->get();

      
        $districtQuery = District::query();
        if (!empty($district_id)) {
            $districtQuery->where('id', $district_id);
        }
        $districts = $districtQuery->get();

       
        $blockQuery= Block::query();
          if (!empty($district_id)) {
            $blockQuery->where('district_id', $district_id);
        }
        $blocks = $blockQuery->get();
        $providersQuery = Provider::query();
          if (!empty($district_id)) {
            $providersQuery->where('district_id', $district_id);
        }
         $providers = $providersQuery->get();
       
        return view('admin.gps.create',compact('districts', 'blocks', 'providers', 'zonals'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $gps
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $gp = DB::table('gp_list')
                        ->select('gp_list.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name', 'blocks.id as blocks_id')
                        ->leftJoin('districts', 'gp_list.district_id', '=', 'districts.id')
                        ->leftJoin('blocks', 'gp_list.block_id', '=', 'blocks.id')
                        ->where('gp_list.id', '=', $id)
                        ->first();
            // dd($gp);
            if($gp == NULL)
                return redirect()
                ->route('admin.gps.index')
                ->with('flash_success', trans('admin.gp_msgs.gp_not_found'));

            return view('admin.gps.show', compact('gp'));

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.gps.index')
                ->with('flash_success', trans('admin.gp_msgs.gp_not_found'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $this->validate($request, [
            'gp_name' => 'required',
            'district' => 'required',
            'block' => 'required',
            'zone' => 'required',
            'provider' => 'required',
            'contact' => 'required'        
        ]);

        try{
            $gp = array();
            $gp['gp_name'] = $request->gp_name;
            $gp['district_id'] = $request->district;
            $gp['block_id'] = $request->block;
            $gp['zonal_id'] = $request->zone;
            $gp['provider'] = $request->provider;
            $gp['contact_no'] = $request->contact;
            $gp['lgd_code'] = $request->lgd_code;
            $gp['phase'] = $request->phase;
            $gp['latitude'] = $request->latitude;
            $gp['longitude'] = $request->longitude;

            $gp = DB::table('gp_list')->insert($gp);

            return redirect()
                ->route('admin.gps.index')
                ->with('flash_success', trans('admin.gp_msgs.gp_saved'));
        } 
        catch (Exception $e) {  
            // dd($e->getMessage());
            return back()->with('flash_error', trans('admin.gp_msgs.gp_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Provider  $district
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $districts = District::get();
            $blocks = Block::get();
            $providers = Provider::select('*',\DB::Raw('concat(first_name," ",last_name) AS name'))->get();
            $zonals = DB::table('zonal_managers')->get();
            $gp = DB::table('gp_list')->find($id);            
            return view('admin.gps.edit',compact('districts', 'blocks', 'providers', 'gp','zonals'));
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.gp_msgs.gp_not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Provider  $district
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'gp_name' => 'required',
            'district' => 'required',
            'block' => 'required',
            'zone' => 'required',
            'provider' => 'required',
            'contact' => 'required'        
        ]);

        try {

            $gp = array();
            $gp['gp_name'] = $request->gp_name;
            $gp['district_id'] = $request->district;
            $gp['block_id'] = $request->block;
            $gp['zonal_id'] = $request->zone;
            $gp['provider'] = $request->provider;
            $gp['contact_no'] = $request->contact;
            $gp['lgd_code'] = $request->lgd_code;
            $gp['phase'] = $request->phase;
            $gp['latitude'] = $request->latitude;
            $gp['longitude'] = $request->longitude;

            $gps = DB::table('gp_list')->where('id',$id)->update($gp);

            return redirect()->route('admin.gps.index')->with('flash_success', trans('admin.gp_msgs.gp_update'));    
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.gp_msgs.gp_not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $district
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::table('gp_list')->delete($id);
            return back()->with('message', trans('admin.gp_msgs.gp_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.gp_msgs.gp_not_found'));
        }
    }

    public function gpreportsnew(Request $request)
{
    $tickets = DB::table('master_tickets')
    ->select(
        'master_tickets.gpname',
        'master_tickets.lgd_code',
        'master_tickets.mandal',
        'master_tickets.district',
        'zonal_managers.Name as zone_name',
        DB::raw("
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(MINUTE, 
            STR_TO_DATE(CONCAT(master_tickets.downdate, ' ', 
                DATE_FORMAT(STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), '%H:%i:%s')
            ), '%Y-%m-%d %H:%i:%s'), 
            COALESCE(user_requests.finished_at, NOW()))
        ) / 60), '.', 
        LPAD(
            MOD(
                SUM(TIMESTAMPDIFF(MINUTE, 
                    STR_TO_DATE(CONCAT(master_tickets.downdate, ' ', 
                        DATE_FORMAT(STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), '%H:%i:%s')
                    ), '%Y-%m-%d %H:%i:%s'), 
                    COALESCE(user_requests.finished_at, NOW()))
                ), 60
            ), 2, '0'
        )
    ) AS total_gps_down_hours
")
    )
    ->leftJoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
    ->leftJoin('providers', 'providers.id', '=', 'user_requests.provider_id')
    ->leftJoin('gp_list', 'master_tickets.lgd_code', '=', 'gp_list.lgd_code')
    ->leftJoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id')
    ->where('user_requests.default_autoclose','Auto');        

    // Apply Filters
    if (!empty($request->district_id)) {
        $tickets->where('master_tickets.district', $request->district_id);
    }
    if (!empty($request->block_id)) {
        $tickets->where('gp_list.block_id', $request->block_id);
    }
    if (!empty($request->zone_id)) {
        $tickets->where('zonal_managers.id', $request->zone_id);
    }
    if (!empty($request->from_date) && !empty($request->to_date)) {
        $tickets->whereBetween('master_tickets.downdate', [$request->from_date, $request->to_date]);
    }

    // Group by required fields
    $downreport = $tickets->groupBy('master_tickets.gpname','master_tickets.lgd_code', 'master_tickets.mandal', 'master_tickets.district', 'zonal_managers.Name')
        ->get();

 //dd($downreport );

        $districts= DB::table('districts')->get();
        $blocks= DB::table('blocks')->get();
        $zonals= DB::table('zonal_managers')->get();


    return view('admin.reports.gpreports', compact('downreport','districts','blocks','zonals'));
}


public function gpreports_old1(Request $request)
{
    // Raw SQL for Working Minutes Calculation
    $workingMinutesSql = "
        CASE 
            WHEN DATE(STR_TO_DATE(CONCAT(master_tickets.downdate, ' ', 
                DATE_FORMAT(STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), '%H:%i:%s')
            ), '%Y-%m-%d %H:%i:%s')) = DATE(COALESCE(user_requests.finished_at, NOW()))
            THEN 
                TIMESTAMPDIFF(
                    MINUTE,
                    GREATEST(
                        STR_TO_DATE(CONCAT(master_tickets.downdate, ' ', 
                            DATE_FORMAT(STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), '%H:%i:%s')
                        ), '%Y-%m-%d %H:%i:%s'),
                        CONCAT(master_tickets.downdate, ' 10:00:00')
                    ),
                    LEAST(
                        COALESCE(user_requests.finished_at, NOW()),
                        CONCAT(master_tickets.downdate, ' 17:00:00')
                    )
                )
            ELSE
                TIMESTAMPDIFF(
                    MINUTE,
                    GREATEST(
                        STR_TO_DATE(CONCAT(master_tickets.downdate, ' ', 
                            DATE_FORMAT(STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), '%H:%i:%s')
                        ), '%Y-%m-%d %H:%i:%s'),
                        CONCAT(master_tickets.downdate, ' 10:00:00')
                    ),
                    CONCAT(master_tickets.downdate, ' 17:00:00')
                )
                +
                (DATEDIFF(
                    DATE(COALESCE(user_requests.finished_at, NOW())),
                    DATE(STR_TO_DATE(CONCAT(master_tickets.downdate, ' ', 
                        DATE_FORMAT(STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), '%H:%i:%s')
                    ), '%Y-%m-%d %H:%i:%s'))
                ) - 1) * 420
                +
                TIMESTAMPDIFF(
                    MINUTE,
                    CONCAT(DATE(COALESCE(user_requests.finished_at, NOW())), ' 10:00:00'),
                    LEAST(
                        COALESCE(user_requests.finished_at, NOW()),
                        CONCAT(DATE(COALESCE(user_requests.finished_at, NOW())), ' 17:00:00')
                    )
                )
        END
    ";

    // Final query
    $tickets = DB::table('master_tickets')
        ->select(
            'master_tickets.gpname',
            'master_tickets.lgd_code',
            'master_tickets.mandal',
            'master_tickets.district',
            'zonal_managers.Name as zone_name',
            DB::raw("
                CONCAT(
                    FLOOR(($workingMinutesSql) / 60), 'h ',
                    LPAD(MOD(($workingMinutesSql), 60), 2, '0'), 'm'
                ) AS total_gps_down_hours
            ") // HH:MM Format
        )
        ->leftJoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
        ->leftJoin('providers', 'providers.id', '=', 'user_requests.provider_id')
        ->leftJoin('gp_list', 'master_tickets.lgd_code', '=', 'gp_list.lgd_code')
        ->leftJoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id')
        ->where('user_requests.default_autoclose', 'Auto');

    // Filters
    if (!empty($request->district_id)) {
        $tickets->where('master_tickets.district', $request->district_id);
    }
    if (!empty($request->block_id)) {
        $tickets->where('gp_list.block_id', $request->block_id);
    }
    if (!empty($request->zone_id)) {
        $tickets->where('zonal_managers.id', $request->zone_id);
    }
    if (!empty($request->from_date) && !empty($request->to_date)) {
        $tickets->whereBetween('master_tickets.downdate', [$request->from_date, $request->to_date]);
    }

    // Group By
    $downreport = $tickets->groupBy(
        'master_tickets.gpname',
        'master_tickets.lgd_code',
        'master_tickets.mandal',
        'master_tickets.district',
        'zonal_managers.Name'
    )
    ->get();

    // Dropdown Data
    $districts = DB::table('districts')->get();
    $blocks = DB::table('blocks')->get();
    $zonals = DB::table('zonal_managers')->get();

    // Return view
    return view('admin.reports.gpreports', compact('downreport', 'districts', 'blocks', 'zonals'));
}

public function gpreports_old2(Request $request)
{
    // 1) Convert downtime into 24-hour format once:
    //    downtime_24h = STR_TO_DATE(CONCAT(mt.downdate, ' ', 24hr_downtime_str), '%Y-%m-%d %H:%i:%s')
    //    We'll inline it in queries to keep it simple.
    // 2) Each partial day is wrapped with GREATEST(0, TIMESTAMPDIFF(...)) to prevent negative values.

    $workingMinutesSql = "
        CASE 
            -- =======================
            -- 1) SAME-DAY SCENARIO
            -- =======================
            WHEN DATE(
                STR_TO_DATE(
                    CONCAT(master_tickets.downdate, ' ', 
                        DATE_FORMAT(
                            STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                            '%H:%i:%s'
                        )
                    ), 
                    '%Y-%m-%d %H:%i:%s'
                )
            ) = DATE(COALESCE(user_requests.finished_at, NOW()))
            THEN 
                -- For same-day, we clamp the start between [10:00, 17:00],
                -- and the end is min(finished_at, 17:00). Then we ensure no negative result.
                GREATEST(
                    0,
                    TIMESTAMPDIFF(
                        MINUTE,
                        GREATEST(
                            LEAST(
                                STR_TO_DATE(
                                    CONCAT(master_tickets.downdate, ' ', 
                                        DATE_FORMAT(
                                            STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                                            '%H:%i:%s'
                                        )
                                    ), 
                                    '%Y-%m-%d %H:%i:%s'
                                ),
                                CONCAT(master_tickets.downdate, ' 17:00:00')
                            ),
                            CONCAT(master_tickets.downdate, ' 10:00:00')
                        ),
                        LEAST(
                            COALESCE(user_requests.finished_at, NOW()),
                            CONCAT(master_tickets.downdate, ' 17:00:00')
                        )
                    )
                )

            -- =======================
            -- 2) MULTI-DAY SCENARIO
            -- =======================
            ELSE
                (
                    -- ---------- (a) First Partial Day ----------
                    GREATEST(
                        0,
                        TIMESTAMPDIFF(
                            MINUTE,
                            GREATEST(
                                LEAST(
                                    STR_TO_DATE(
                                        CONCAT(master_tickets.downdate, ' ', 
                                            DATE_FORMAT(
                                                STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                                                '%H:%i:%s'
                                            )
                                        ), 
                                        '%Y-%m-%d %H:%i:%s'
                                    ),
                                    CONCAT(master_tickets.downdate, ' 17:00:00')
                                ),
                                CONCAT(master_tickets.downdate, ' 10:00:00')
                            ),
                            CONCAT(master_tickets.downdate, ' 17:00:00')
                        )
                    )
                )
                +
                (
                    -- ---------- (b) Middle Full Days ----------
                    -- Each full day is 7 hours (420 minutes).
                    -- If DATEDIFF(...) - 1 is negative, clamp to 0.
                    GREATEST(
                        0,
                        (DATEDIFF(
                            DATE(COALESCE(user_requests.finished_at, NOW())),
                            DATE(
                                STR_TO_DATE(
                                    CONCAT(master_tickets.downdate, ' ', 
                                        DATE_FORMAT(
                                            STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                                            '%H:%i:%s'
                                        )
                                    ), 
                                    '%Y-%m-%d %H:%i:%s'
                                )
                            )
                        ) - 1)
                    ) * 420
                )
                +
                (
                    -- ---------- (c) Last Partial Day ----------
                    -- From 10:00 on the final day up to min(finished_at, 17:00).
                    -- If finished_at < 10:00, we get negative => clamp to 0.
                    GREATEST(
                        0,
                        TIMESTAMPDIFF(
                            MINUTE,
                            CONCAT(
                                DATE(COALESCE(user_requests.finished_at, NOW())), 
                                ' 10:00:00'
                            ),
                            LEAST(
                                COALESCE(user_requests.finished_at, NOW()),
                                CONCAT(
                                    DATE(COALESCE(user_requests.finished_at, NOW())), 
                                    ' 17:00:00'
                                )
                            )
                        )
                    )
                )
        END
    ";

    // ========== Final Query ==========
    $tickets = DB::table('master_tickets')
        ->select(
            'master_tickets.gpname',
            'master_tickets.lgd_code',
            'master_tickets.mandal',
            'master_tickets.district',
            'zonal_managers.Name as zone_name',
            DB::raw("
                CONCAT(
                    FLOOR(($workingMinutesSql) / 60), 'h ',
                    LPAD(MOD(($workingMinutesSql), 60), 2, '0'), 'm'
                ) AS total_gps_down_hours
            ")
        )
        ->leftJoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
        ->leftJoin('providers', 'providers.id', '=', 'user_requests.provider_id')
        ->leftJoin('gp_list', 'master_tickets.lgd_code', '=', 'gp_list.lgd_code')
        ->leftJoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id')
        ->where('user_requests.default_autoclose', 'Auto');

    // ---------- Filters ----------
    if (!empty($request->district_id)) {
        $tickets->where('master_tickets.district', $request->district_id);
    }
    if (!empty($request->block_id)) {
        $tickets->where('gp_list.block_id', $request->block_id);
    }
    if (!empty($request->zone_id)) {
        $tickets->where('zonal_managers.id', $request->zone_id);
    }
    if (!empty($request->from_date) && !empty($request->to_date)) {
        $tickets->whereBetween('master_tickets.downdate', [$request->from_date, $request->to_date]);
    }

    // ---------- Group By ----------
    $downreport = $tickets->groupBy(
        'master_tickets.gpname',
        'master_tickets.lgd_code',
        'master_tickets.mandal',
        'master_tickets.district',
        'zonal_managers.Name'
    )->get();

    // ---------- Dropdown Data ----------
    $districts = DB::table('districts')->get();
    $blocks    = DB::table('blocks')->get();
    $zonals    = DB::table('zonal_managers')->get();

    return view('admin.reports.gpreports', compact('downreport', 'districts', 'blocks', 'zonals'));
}

public function gpreports(Request $request)
{

    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    // 1) Convert downtime into 24-hour format once:
    //    downtime_24h = STR_TO_DATE(CONCAT(mt.downdate, ' ', 24hr_downtime_str), '%Y-%m-%d %H:%i:%s')
    //    We'll inline it in queries to keep it simple.
    // 2) Each partial day is wrapped with GREATEST(0, TIMESTAMPDIFF(...)) to prevent negative values.

    $workingMinutesSql = "
        CASE 
            -- =======================
            -- 1) SAME-DAY SCENARIO
            -- =======================
            WHEN DATE(
                STR_TO_DATE(
                    CONCAT(master_tickets.downdate, ' ', 
                        DATE_FORMAT(
                            STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                            '%H:%i:%s'
                        )
                    ), 
                    '%Y-%m-%d %H:%i:%s'
                )
            ) = DATE(COALESCE(user_requests.finished_at, NOW()))
            THEN 
                -- For same-day, we clamp the start between [10:00, 17:00],
                -- and the end is min(finished_at, 17:00). Then we ensure no negative result.
                GREATEST(
                    0,
                    TIMESTAMPDIFF(
                        MINUTE,
                        GREATEST(
                            LEAST(
                                STR_TO_DATE(
                                    CONCAT(master_tickets.downdate, ' ', 
                                        DATE_FORMAT(
                                            STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                                            '%H:%i:%s'
                                        )
                                    ), 
                                    '%Y-%m-%d %H:%i:%s'
                                ),
                                CONCAT(master_tickets.downdate, ' 17:00:00')
                            ),
                            CONCAT(master_tickets.downdate, ' 10:00:00')
                        ),
                        LEAST(
                            COALESCE(user_requests.finished_at, NOW()),
                            CONCAT(master_tickets.downdate, ' 17:00:00')
                        )
                    )
                )

            -- =======================
            -- 2) MULTI-DAY SCENARIO
            -- =======================
            ELSE
                (
                    -- ---------- (a) First Partial Day ----------
                    GREATEST(
                        0,
                        TIMESTAMPDIFF(
                            MINUTE,
                            GREATEST(
                                LEAST(
                                    STR_TO_DATE(
                                        CONCAT(master_tickets.downdate, ' ', 
                                            DATE_FORMAT(
                                                STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                                                '%H:%i:%s'
                                            )
                                        ), 
                                        '%Y-%m-%d %H:%i:%s'
                                    ),
                                    CONCAT(master_tickets.downdate, ' 17:00:00')
                                ),
                                CONCAT(master_tickets.downdate, ' 10:00:00')
                            ),
                            CONCAT(master_tickets.downdate, ' 17:00:00')
                        )
                    )
                )
                +
                (
                    -- ---------- (b) Middle Full Days ----------
                    -- Each full day is 7 hours (420 minutes).
                    -- If DATEDIFF(...) - 1 is negative, clamp to 0.
                    GREATEST(
                        0,
                        (DATEDIFF(
                            DATE(COALESCE(user_requests.finished_at, NOW())),
                            DATE(
                                STR_TO_DATE(
                                    CONCAT(master_tickets.downdate, ' ', 
                                        DATE_FORMAT(
                                            STR_TO_DATE(master_tickets.downtime, '%h:%i:%s %p'), 
                                            '%H:%i:%s'
                                        )
                                    ), 
                                    '%Y-%m-%d %H:%i:%s'
                                )
                            )
                        ) - 1)
                    ) * 420
                )
                +
                (
                    -- ---------- (c) Last Partial Day ----------
                    -- From 10:00 on the final day up to min(finished_at, 17:00).
                    -- If finished_at < 10:00, we get negative => clamp to 0.
                    GREATEST(
                        0,
                        TIMESTAMPDIFF(
                            MINUTE,
                            CONCAT(
                                DATE(COALESCE(user_requests.finished_at, NOW())), 
                                ' 10:00:00'
                            ),
                            LEAST(
                                COALESCE(user_requests.finished_at, NOW()),
                                CONCAT(
                                    DATE(COALESCE(user_requests.finished_at, NOW())), 
                                    ' 17:00:00'
                                )
                            )
                        )
                    )
                )
        END
    ";

    // ========== Final Query ==========
    $tickets = DB::table('master_tickets')
        ->select(
            'master_tickets.gpname',
            'master_tickets.lgd_code',
            'master_tickets.mandal',
            'master_tickets.district',
            'zonal_managers.Name as zone_name',
            DB::raw("
                CONCAT(
                    FLOOR(SUM($workingMinutesSql) / 60), 'h ',
                    LPAD(MOD(SUM($workingMinutesSql), 60), 2, '0'), 'm'
                ) AS total_gps_down_hours
            ")
        )
        ->leftJoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
        ->leftJoin('providers', 'providers.id', '=', 'user_requests.provider_id')
        ->leftJoin('gp_list', 'master_tickets.lgd_code', '=', 'gp_list.lgd_code')
        ->leftJoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id')
        ->where('user_requests.company_id', $company_id)
        ->where('user_requests.state_id', $state_id)
        ->where('user_requests.default_autoclose', 'Auto');

    // ---------- Filters ----------
    if (!empty($request->district_id)) {
        $tickets->where('master_tickets.district', $request->district_id);
    }
    if (!empty($request->block_id)) {
        $tickets->where('gp_list.block_id', $request->block_id);
    }
    if (!empty($request->zone_id)) {
        $tickets->where('zonal_managers.id', $request->zone_id);
    }
    if (!empty($request->from_date) && !empty($request->to_date)) {
        $tickets->whereBetween('master_tickets.downdate', [$request->from_date, $request->to_date]);
    }

    // ---------- Group By ----------
    $downreport = $tickets->groupBy(
        'master_tickets.gpname',
        'master_tickets.lgd_code',
        'master_tickets.mandal',
        'master_tickets.district',
        'zonal_managers.Name'
    )->get();

    // ---------- Dropdown Data ----------
    $districts = DB::table('districts')->get();
    $blocks    = DB::table('blocks')->get();
    $zonals    = DB::table('zonal_managers')->get();

    return view('admin.reports.gpreports', compact('downreport', 'districts', 'blocks', 'zonals'));
}


public function frtreports(Request $request)
{

    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    $district_id = $user->district_id;

    
    // ---------- Dropdown Data ----------
    //$districts = DB::table('districts')->get();
    //$blocks    = DB::table('blocks')->get();
    $zoneIdsQuery = DB::table('gp_list')->where('state_id', $state_id);
        if (!empty($district_id)) {
            $zoneIdsQuery->where('district_id', $district_id);
        }

    $zoneIds = $zoneIdsQuery->pluck('zonal_id')->unique();
    $zonals    = DB::table('zonal_managers')->whereIn('id',$zoneIds)->get();

    return view('admin.reports.frtreports', compact('districts', 'blocks', 'zonals'));
}

public function frtreports_details(Request $request)
{

    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    $district_id = $user->district_id;

    // ---------- Dropdown Data ----------
    //$districts = DB::table('districts')->get();
    //$blocks    = DB::table('blocks')->get();
    $zoneIdsQuery = DB::table('gp_list')->where('state_id', $state_id);
      if (!empty($district_id)) {
        $zoneIdsQuery->where('district_id', $district_id);
    }
    $zoneIds = $zoneIdsQuery->pluck('zonal_id')->unique();

    $zonals    = DB::table('zonal_managers')->whereIn('id',$zoneIds)->get();

    return view('admin.reports.frtreports_details', compact('districts', 'blocks', 'zonals'));
}


public function get_districts($zone_id)
{
    Session::put('user', Auth::User());
    $user = Session::get('user');
    
    $district_id = $user->district_id;
   
    $districtQuery = DB::table('gp_list')->where('zonal_id', $zone_id)->where('type', 'GP');
      if (!empty($district_id)) {
                $districtQuery->where('district_id', $district_id);
            }
    $districtIds =  $districtQuery->pluck('district_id')->unique();      
    $districts = DB::table('districts')->whereIn('id', $districtIds)->get(['id', 'name']);
return response()->json($districts);

}

public function get_employees($districtId = null)
{
    $query = DB::table('providers')
        ->select('id', 'first_name', 'last_name', 'type');

    // If district is selected
    if (!empty($districtId)) {
        $query->where('district_id', $districtId);
    }

    // If role is passed (from query ?role_id=2 or 5 etc.)
    if (request()->has('role_id') && request('role_id') !== '') {
        $query->where('type', request('role_id'));
    }

    $employees = $query->get();

    return response()->json($employees);
}

public function getFrtReport(Request $request)
{
    // ================= FORCE LIMITS =================
    ini_set('memory_limit', '1024M');
    set_time_limit(300);

    // ================= USER =================
    $user = Session::get('user');
    $state_id    = $user->state_id;
    $district_id = $user->district_id;

    // ================= DATES =================
    $today = Carbon::today();

    if ($request->input('from_date')) {
        $startDate = Carbon::parse($request->input('from_date'))->startOfDay();
    } else {
        $startDate = $today->copy()->startOfDay();
    }

    if ($request->input('to_date')) {
        $endDate = Carbon::parse($request->input('to_date'))->endOfDay();
    } else {
        $endDate = $today->copy()->endOfDay();
    }

    $fromDate = $startDate->toDateString();
    $toDate   = $endDate->toDateString();

    // ================= SAFETY LIMIT =================
    if ($startDate->diffInDays($endDate) > 45) {
        return response()->json(array(
            'error' => 'Maximum 45 days allowed'
        ), 422);
    }

    // ================= TOTAL DAYS (SUNDAY INCLUDED) =================
    $totalDays = $startDate->diffInDays($endDate) + 1;

    // ================= PROVIDERS =================
    $qb = DB::table('providers as p')
        ->leftJoin('zonal_managers as z', 'z.id', '=', 'p.zone_id')
        ->leftJoin('districts as d', 'd.id', '=', 'p.district_id')
        ->where('p.state_id', $state_id)
        ->where('p.status', 'approved');

    if (!empty($district_id)) {
        $qb->where('p.district_id', $district_id);
    }

    if ($request->zone_id) {
        $qb->where('p.zone_id', $request->zone_id);
    }

    if ($request->role_id) {
        $qb->where('p.type', $request->role_id);
    }

    if ($request->member_id) {
        $qb->where('p.id', $request->member_id);
    }

    $providers = $qb->select(
            'p.id',
            'p.first_name',
            'p.last_name',
            'p.mobile',
            'p.type as role',
            'z.Name as zone_name',
            'd.Name as district_name'
        )
        ->orderBy('p.first_name')
        ->get();

    // ================= ATTENDANCE (ONE QUERY, SUNDAY INCLUDED) =================
    $attendance = DB::table('attendance')
        ->selectRaw('
            provider_id,
            COUNT(*) as present_days,
            SUM(CASE WHEN online_image IS NOT NULL THEN 1 ELSE 0 END) as selfie_count
        ')
        ->whereBetween('created_at', array($startDate, $endDate))
        ->groupBy('provider_id')
        ->get()
        ->keyBy('provider_id');

    // ================= USER REQUESTS (OPTIMIZED, OLD PHP SAFE) =================
    $requests = DB::table('user_requests')
        ->selectRaw('
            provider_id,

            SUM(created_at BETWEEN ? AND ?) as tickets_assigned,
            SUM(default_autoclose="Auto" AND created_at BETWEEN ? AND ?) as tickets_auto_assigned,
            SUM(default_autoclose="Manual" AND created_at BETWEEN ? AND ?) as tickets_manual_assigned,

            SUM(status="INCOMING") as open_tickets,
            SUM(status="INCOMING" AND default_autoclose="Auto") as open_auto_tickets,
            SUM(status="INCOMING" AND default_autoclose="Manual") as open_manual_tickets,

            SUM(status="COMPLETED" AND autoclose="Manual" AND finished_at BETWEEN ? AND ?) as manual_completed,
            SUM(status="COMPLETED" AND autoclose="Auto" AND finished_at BETWEEN ? AND ?) as auto_completed,

            SUM(status="PICKEDUP" AND started_at BETWEEN ? AND ?) as tickets_accepted,
            SUM(status="PICKEDUP" AND default_autoclose="Auto" AND started_at BETWEEN ? AND ?) as tickets_auto_accepted,
            SUM(status="PICKEDUP" AND default_autoclose="Manual" AND started_at BETWEEN ? AND ?) as tickets_manual_accepted,

            SUM(status="ONHOLD" AND started_at BETWEEN ? AND ?) as tickets_onhold,
            SUM(status="ONHOLD" AND default_autoclose="Auto" AND started_at BETWEEN ? AND ?) as tickets_auto_onhold,
            SUM(status="ONHOLD" AND default_autoclose="Manual" AND started_at BETWEEN ? AND ?) as tickets_manual_onhold
        ', array(
            $startDate, $endDate,
            $startDate, $endDate,
            $startDate, $endDate,
            $startDate, $endDate,
            $startDate, $endDate,
            $startDate, $endDate,
            $startDate, $endDate,
            $startDate, $endDate,
            $startDate, $endDate,
            $startDate, $endDate,
            $startDate, $endDate
        ))
        ->where('state_id', $state_id);

    if (!empty($district_id)) {
        $requests->where('district_id', $district_id);
    }

    $requests = $requests->groupBy('provider_id')
        ->get()
        ->keyBy('provider_id');

    // ================= TRACKING (SAFE MODE) =================
    $calculateDistance = $startDate->diffInDays($endDate) <= 31;
    $trackingByProvider = collect();

    if ($calculateDistance) {
        $trackingByProvider = DB::table('provider_tracking_histories')
            ->whereBetween('created_at', array($startDate, $endDate))
            ->select('provider_id', 'latlng', DB::raw('DATE(created_at) as track_date'))
            ->get()
            ->groupBy('provider_id');
    }

    // ================= BUILD RESPONSE =================
    $data = array();

    foreach ($providers as $p) {

        $att   = isset($attendance[$p->id]) ? $attendance[$p->id] : null;
        $stat  = isset($requests[$p->id]) ? $requests[$p->id] : null;

        $attendancePercent = $totalDays > 0
            ? round((($att ? $att->present_days : 0) / $totalDays) * 100) . '%'
            : '0%';

        $distance = 0;
        if ($calculateDistance && isset($trackingByProvider[$p->id])) {
            foreach ($trackingByProvider[$p->id]->groupBy('track_date') as $rows) {
                $distance += $this->calculateDistanceFromLatlngBlobs(
                    $rows->pluck('latlng')->toArray()
                );
            }
        }

        $data[] = array(
            'zone' => $p->zone_name ? $p->zone_name : '-',
            'district' => $p->district_name ? $p->district_name : '-',
            'role' => $p->role,
            'name' => trim($p->first_name . ' ' . $p->last_name),
            'contact' => $p->mobile,

            'attendance' => $attendancePercent,
            'selfie' => ($att && $att->selfie_count > 0) ? 'Y' : 'N',

            'open_tickets' => $stat ? $stat->open_tickets : 0,
            'open_auto_tickets' => $stat ? $stat->open_auto_tickets : 0,
            'open_manual_tickets' => $stat ? $stat->open_manual_tickets : 0,

            'manual_completed' => $stat ? $stat->manual_completed : 0,
            'auto_completed' => $stat ? $stat->auto_completed : 0,

            'tickets_assigned' => $stat ? $stat->tickets_assigned : 0,
            'tickets_auto_assigned' => $stat ? $stat->tickets_auto_assigned : 0,
            'tickets_manual_assigned' => $stat ? $stat->tickets_manual_assigned : 0,

            'tickets_accepted' => $stat ? $stat->tickets_accepted : 0,
            'tickets_auto_accepted' => $stat ? $stat->tickets_auto_accepted : 0,
            'tickets_manual_accepted' => $stat ? $stat->tickets_manual_accepted : 0,

            'tickets_onhold' => $stat ? $stat->tickets_onhold : 0,
            'tickets_auto_onhold' => $stat ? $stat->tickets_auto_onhold : 0,
            'tickets_manual_onhold' => $stat ? $stat->tickets_manual_onhold : 0,

            'distance' => round($distance, 2),
            'provider_id' => $p->id,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        );
    }

    return response()->json(array('data' => $data));
}

private function calculateDistanceFromLatlngBlobs($latlngBlobs)
{
    // thresholds (tweak if needed)
    $minMoveKm = 0.01;   // ignore moves < 20 meters (increase to reduce jitter)
    $maxSpeedKmph = 200; // ignore jumps implying > 200 km/h

    $allPoints = array();

    foreach ($latlngBlobs as $json) {
        $arr = @json_decode($json, true);
        if (!is_array($arr)) continue;
        foreach ($arr as $pt) {
            if (!isset($pt['latitude']) || !isset($pt['longitude']) || !isset($pt['datetime'])) continue;
            // normalize & push
            $allPoints[] = array(
                'lat' => (float) $pt['latitude'],
                'lon' => (float) $pt['longitude'],
                'dt'  => $pt['datetime']
            );
        }
    }

    if (count($allPoints) < 2) return 0.0;

    // Sort by datetime ascending (important)
    usort($allPoints, function($a, $b) {
        $ta = strtotime($a['dt']);
        $tb = strtotime($b['dt']);
        if ($ta == $tb) return 0;
        return ($ta < $tb) ? -1 : 1;
    });

    // Remove near-duplicate consecutive points
    $clean = array();
    $prev = null;
    $epsilon = 0.000001; // slightly larger epsilon to collapse near-equal coords
    foreach ($allPoints as $p) {
        if ($prev !== null) {
            if (abs($p['lat'] - $prev['lat']) < $epsilon && abs($p['lon'] - $prev['lon']) < $epsilon) {
                continue;
            }
        }
        $clean[] = $p;
        $prev = $p;
    }

    if (count($clean) < 2) return 0.0;

    // Sum distances with jitter/outlier filters
    $totalKm = 0.0;
    for ($i = 1; $i < count($clean); $i++) {
        $p1 = $clean[$i - 1];
        $p2 = $clean[$i];

        $d = $this->haversine($p1['lat'], $p1['lon'], $p2['lat'], $p2['lon']); // km

        $t1 = strtotime($p1['dt']);
        $t2 = strtotime($p2['dt']);
        $seconds = $t2 - $t1;
        if ($seconds <= 0) $seconds = 1;

        $hours = $seconds / 3600.0;
        $speedKmph = $d / $hours;

        // filters
        if ($d < $minMoveKm) {
            // ignore tiny jitter
            continue;
        }
        if ($speedKmph > $maxSpeedKmph) {
            // unrealistic jump � ignore
            continue;
        }

        $totalKm += $d;
    }

    return round($totalKm, 2);
}
/**
 * Haversine formula � returns distance in kilometers.
 */
private function haversine($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // km

    $lat1 = deg2rad((float) $lat1);
    $lon1 = deg2rad((float) $lon1);
    $lat2 = deg2rad((float) $lat2);
    $lon2 = deg2rad((float) $lon2);

    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;

    $a = sin($dlat / 2) * sin($dlat / 2) +
         cos($lat1) * cos($lat2) *
         sin($dlon / 2) * sin($dlon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c;
}


public function gettodayFrtReport(Request $request)
{
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id   = $user->state_id;
    $district_id = $user->district_id;


    $inputFromDate = request()->input('from_date');
    $inputToDate   = request()->input('to_date');

    $fromDate = $inputFromDate !== null ? $inputFromDate : date('Y-m-d');
    $toDate   = $inputToDate !== null ? $inputToDate : date('Y-m-d');
    // Pending tickets query
    $pendingTicketsQuery = 'COUNT(CASE WHEN user_requests.status = "INCOMING"';
    if ($inputFromDate && $inputToDate) {
        $pendingTicketsQuery .= ' AND DATE(master_tickets.downdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"';
    }
    $pendingTicketsQuery .= ' THEN user_requests.id END) as pending_tickets';

    // Pending > 24 hrs
    $pendingTicketsMorethen24 = 'COUNT(CASE WHEN user_requests.status = "INCOMING" AND ';
    $pendingTicketsMorethen24 .= 'STR_TO_DATE(CONCAT(master_tickets.downdate, " ", master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p") < DATE_SUB(NOW(), INTERVAL 24 HOUR)';
    if ($inputFromDate && $inputToDate) {
        $pendingTicketsMorethen24 .= ' AND DATE(master_tickets.downdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"';
    }
    $pendingTicketsMorethen24 .= ' THEN user_requests.id END) as pending_tickets_morethen_24';

   //dd($pendingTicketsQuery);

    $today = date('Y-m-d');

    // --- Get providers with zone info ---
    $providersquery = DB::table('providers')
        ->leftJoin('user_requests', 'user_requests.provider_id', 'providers.id')
        ->leftJoin('master_tickets', 'user_requests.booking_id', 'master_tickets.ticketid')
        ->leftJoin('zonal_managers', 'zonal_managers.id', 'providers.zone_id')
        ->where('providers.zone_id', '!=', 0)
        ->where('providers.company_id', $company_id)
        ->where('providers.status', 'approved')
        ->where('providers.state_id', $state_id)
        ->whereIn('providers.type', [2,5]); // Only FRT & Patroller
        if (!empty($district_id)) {
            $providersquery->where('providers.district_id', $district_id);
        }
       
        
    $providers = $providersquery->groupBy('providers.id')->select(
            'providers.id as provider_id',
            'providers.type as provider_type', // 2=FRT,5=Patroller
            'providers.zone_id',
            'zonal_managers.Name as zone_name',
            DB::raw('COUNT(CASE WHEN DATE(master_tickets.downdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as total_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as completed_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "ONHOLD" AND DATE(user_requests.started_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as hold_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "PICKEDUP" AND DATE(user_requests.started_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as pickup_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) <= 240 THEN user_requests.id END) as completed_0_4'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) > 240 AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) <= 600 THEN user_requests.id END) as completed_4_10'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) > 600 AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) <= 1440 THEN user_requests.id END) as completed_10_24'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) > 1440 AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) <= 2880 THEN user_requests.id END) as completed_24_48'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) > 2880 THEN user_requests.id END) as completed_gt_48'),
            DB::raw($pendingTicketsQuery),
            DB::raw($pendingTicketsMorethen24)
        )
        ->get();

      //dd($providers);
    // --- Get today's attendance ---
    $attendance = DB::table('attendance')
        ->whereDate('created_at', $today)
        ->whereIn('provider_id', $providers->pluck('provider_id'))
        ->get()
        ->keyBy('provider_id');
    $leaves = Leave::whereIn('provider_id', $providers->pluck('provider_id'))
        ->where('type', 'leave')
        ->whereDate('start_date', '<=', $today)
        ->whereDate('end_date', '>=', $today)
        ->get()
        ->keyBy('provider_id');

    // Function to summarize a provider list
  
    function summarizeProvidersWithAttendance($list, $attendance, $leaves)
    {
        $summary = [
            'total'         => $list->count(),
            'working'       => 0,
            'not_started'   => 0,
            'completed'     => 0,
            'no_ticket'     => 0,
            'only_hold'     => 0,
            'logged_in'     => 0,
            'not_logged_in' => 0,
            'online'        => 0,
            'offline'       => 0,
            'leave'         => 0,
            'completed_0_4' => 0,
            'completed_4_10' => 0,
            'completed_10_24' => 0,
            'completed_24_48' => 0,
            'completed_gt_48' => 0
        ];

        $today = date('Y-m-d');

        foreach ($list as $prov) {

              if (isset($leaves[$prov->provider_id])) {
                $summary['leave']++;
                continue; 
            }


            if (isset($attendance[$prov->provider_id])) {

                // Logged in
                $summary['logged_in']++;

                if ($attendance[$prov->provider_id]->status === 'active') {
                    $summary['online']++;
                } else {
                    $summary['offline']++;
                }

                //  Ticket stats ONLY for present providers
                if ($prov->pickup_tickets > 0) {
                    $summary['working']++;
                }

                if (
                    $prov->pending_tickets > 0 &&
                    $prov->pickup_tickets == 0 &&
                    $prov->hold_tickets == 0 &&
                    $prov->completed_tickets == 0
                ) {
                    $summary['not_started']++;
                }

                if ($prov->completed_tickets > 0 && $prov->pickup_tickets == 0) {
                    $summary['completed']++;
                }

                if (
                    $prov->pending_tickets == 0 &&
                    $prov->pickup_tickets == 0 &&
                    $prov->hold_tickets == 0 &&
                    $prov->completed_tickets == 0
                ) {
                    $summary['no_ticket']++;
                }

                if (
                    $prov->hold_tickets > 0 &&
                    $prov->pickup_tickets == 0 &&
                    $prov->completed_tickets == 0
                ) {
                    $summary['only_hold']++;
                }

            } else {

                //Not in attendance -check leave
                $isOnLeave = Leave::where('provider_id', $prov->provider_id)
                    ->where('type', 'leave')
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today)
                    ->exists();

                if ($isOnLeave) {
                    $summary['leave']++;
                } else {
                    $summary['not_logged_in']++; // absent
                }
            }
        }
        foreach ($list as $prov) {
                $summary['completed_0_4'] += $prov->completed_0_4 ?? 0;
                $summary['completed_4_10'] += $prov->completed_4_10 ?? 0;
                $summary['completed_10_24'] += $prov->completed_10_24 ?? 0;
                $summary['completed_24_48'] += $prov->completed_24_48 ?? 0;
                $summary['completed_gt_48'] += $prov->completed_gt_48 ?? 0;

            }

        return $summary;
    }


    // --- Group providers by zone ---
    $zones = [];
    foreach ($providers as $prov) {
        $zoneId = $prov->zone_id;
        $zoneName = $prov->zone_name;

        if (!isset($zones[$zoneId])) {
            $zones[$zoneId] = [
                'zone_name' => $zoneName,
                'zone_id' => $zoneId,
                'frt' => [],
                'patrollers' => [],
            ];
        }

        if ($prov->provider_type == 2) {
            $zones[$zoneId]['frt'][] = $prov;
        } else if ($prov->provider_type == 5) {
            $zones[$zoneId]['patrollers'][] = $prov;
        }
    }

    // --- Summarize each zone ---
    $zoneReport = [];
    foreach ($zones as $zoneId => $data) {
        $zoneReport[$zoneId] = [
            'zone_name' => $data['zone_name'],
            'zone_id' => $data['zone_id'],
            'frt' => summarizeProvidersWithAttendance(collect($data['frt']), $attendance,$leaves),
            'patrollers' => summarizeProvidersWithAttendance(collect($data['patrollers']), $attendance,$leaves)
        ];
    }

    return response()->json([
        'from_date' => $fromDate,
        'to_date' => $toDate,
        'zones' => $zoneReport
    ]);
}

 // --- Helper: Determine provider stage ---
//  private function getProviderStage($prov, $attendance)
// {
//     if ($prov->pickup_tickets > 0) return 'working';
   
//     if ($prov->pending_tickets > 0 && $prov->pickup_tickets == 0 && $prov->hold_tickets == 0 && $prov->completed_tickets == 0) return 'not_started';
//     if ($prov->completed_tickets > 0 && $prov->pickup_tickets == 0) return 'completed';
//     if ($prov->hold_tickets > 0 && $prov->pickup_tickets == 0 && $prov->completed_tickets == 0) return 'only_hold';
//     if ($prov->pending_tickets == 0 && $prov->pickup_tickets == 0 && $prov->hold_tickets == 0 && $prov->completed_tickets == 0) return 'no_ticket';

//     if ($prov->pending_tickets > 2) return 'open_morethen2';
//     if ($prov->old_ongoing > 0) return 'old_ongoing';


//     if (isset($attendance[$prov->provider_id])) {
//         if ($attendance[$prov->provider_id]->status == 'active') return 'online';
//         if ($attendance[$prov->provider_id]->status == 'offline') return 'offline';
//         return 'logged_in';
//     }

//     return 'not_logged_in';
// }
private function getProviderStage($prov, $attendance,$leaves)
{
    $today = date('Y-m-d');

    if (isset($leaves[$prov->provider_id])) {
            return 'leave';
    }

    if (!isset($attendance[$prov->provider_id])) {

        // $isOnLeave = Leave::where('provider_id', $prov->provider_id)
        //     ->where('type', 'leave')
        //     ->whereDate('start_date', '<=', $today)
        //     ->whereDate('end_date', '>=', $today)
        //     ->exists();

        // return $isOnLeave ? 'leave' : 'not_logged_in';
         return 'not_logged_in';
    }

    /* -------- Attendance status -------- */
    if ($attendance[$prov->provider_id]->status === 'active') {
        $attendanceStage = 'online';
    } elseif ($attendance[$prov->provider_id]->status === 'offline') {
        $attendanceStage = 'offline';
    } else {
        $attendanceStage = 'logged_in';
    }

    /* -------- Ticket based stages (ONLY for present providers) -------- */
    if ($prov->pickup_tickets > 0) return 'working';

    if (
        $prov->pending_tickets > 0 &&
        $prov->pickup_tickets == 0 &&
        $prov->hold_tickets == 0 &&
        $prov->completed_tickets == 0
    ) return 'not_started';

    if ($prov->completed_tickets > 0 && $prov->pickup_tickets == 0) return 'completed';

    if (
        $prov->hold_tickets > 0 &&
        $prov->pickup_tickets == 0 &&
        $prov->completed_tickets == 0
    ) return 'only_hold';

    if (
        $prov->pending_tickets == 0 &&
        $prov->pickup_tickets == 0 &&
        $prov->hold_tickets == 0 &&
        $prov->completed_tickets == 0
    ) return 'no_ticket';

    if ($prov->pending_tickets > 2) return 'open_morethen2';

    if ($prov->old_ongoing > 0) return 'old_ongoing';

    return $attendanceStage;
}


    // --- Helper: Filter providers by stage ---
    // private function filterProvidersByStage($list, $attendance,$leaves, $stage)
    // {
    //     $filtered = [];
    //     foreach ($list as $prov) {
    //         $provStage = $this->getProviderStage($prov, $attendance,$leaves);
    //         if ($provStage === $stage) {
    //             $filtered[] = $prov;
                
    //         }
    //     }
    //     return collect($filtered);
    // }
  private function filterProvidersByStage($list, $attendance, $leaves, $stage)
    {
        $filtered = [];
        // Duration buckets check
              $durationStages = ['completed_0_4', 'completed_4_10', 'completed_10_24', 'completed_24_48', 'completed_gt_48'];

        
        if (in_array($stage, $durationStages)) {
            
            foreach ($list as $prov) {
                // If the provider has > 0 tickets in this bucket, include them
                if (isset($prov->$stage) && $prov->$stage > 0) {
                     $filtered[] = $prov;
                }
            }
        } else {
             // Default priority-based logic
            foreach ($list as $prov) {
                $provStage = $this->getProviderStage($prov, $attendance, $leaves);
                if ($provStage === $stage) {
                    $filtered[] = $prov;
                }
            }
        }
        return collect($filtered);
    }

    // --- Helper: Summarize counts ---
    private function summarizeProvidersWithAttendance($list, $attendance,$leaves)
    {
        $summary = [
            'total'        => $list->count(),
            'working'      => 0,
            'old_ongoing'      => 0,
            'open_morethen2' =>0,
            'not_started'  => 0,
            'completed'    => 0,
            'no_ticket'    => 0,
            'only_hold'    => 0,
            'logged_in'    => 0,
            'not_logged_in'=> 0,
            'online'       => 0,
            'offline'      => 0,
            'leave'=>0,
            
        ];

        foreach ($list as $prov) {
            $stage = $this->getProviderStage($prov, $attendance,$leaves);
            if (isset($summary[$stage])) {
                $summary[$stage]++;
            }
        }

        return $summary;
    }


public function getTodayFrtDetails(Request $request)
{
    $zone_id = $request->input('zone_id');
    $type = $request->input('type'); // 'frt' or 'patroller'
    $stage = $request->input('stage');
    $inputFromDate  = $request->input('from_date', date('Y-m-d'));
    $inputToDate   = $request->input('to_date', date('Y-m-d'));


    $fromDate = $inputFromDate !== null ? $inputFromDate : date('Y-m-d');
    $toDate   = $inputToDate !== null ? $inputToDate : date('Y-m-d');


    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    $district_id = $user->district_id;

   $pendingTicketsQuery = 'COUNT(CASE WHEN user_requests.status = "INCOMING" THEN user_requests.id END) as pending_tickets';



    // same provider query as before (without filters)
    $providers = DB::table('providers')
        ->leftJoin('user_requests', 'user_requests.provider_id', 'providers.id')
        ->leftJoin('master_tickets', 'user_requests.booking_id', 'master_tickets.ticketid')
        ->leftJoin('zonal_managers', 'zonal_managers.id', 'providers.zone_id')
        ->where('providers.company_id', $company_id)
        ->where('providers.state_id', $state_id)
        ->where('providers.zone_id', '!=', 0) 
        ->whereIn('providers.type', [2, 5])
        ->where('providers.status', 'approved')
        ->when($zone_id, function ($q) use ($zone_id) {
    return $q->where('providers.zone_id', $zone_id);
})
->when($type, function ($q) use ($type) {
    return $q->where('providers.type', $type == 'frt' ? 2 : 5);
})
->when($district_id, function ($q) use ($district_id) {
        return $q->where('providers.district_id', $district_id);
    })
        ->select(
            'providers.id as provider_id',
            'providers.first_name',
            'providers.last_name',
            'providers.mobile',
            'providers.type',
            'providers.zone_id',
            'zonal_managers.Name as zone_name',
            DB::raw('COUNT(CASE WHEN DATE(master_tickets.created_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as total_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as completed_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "ONHOLD" AND DATE(user_requests.started_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as hold_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "PICKEDUP" AND DATE(user_requests.started_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as pickup_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) <= 240 THEN user_requests.id END) as completed_0_4'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) > 240 AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) <= 600 THEN user_requests.id END) as completed_4_10'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) > 600 AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) <= 1440 THEN user_requests.id END) as completed_10_24'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) > 1440 AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) <= 2880 THEN user_requests.id END) as completed_24_48'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) > 2880 THEN user_requests.id END) as completed_gt_48'),

            DB::raw($pendingTicketsQuery),
            DB::raw('COUNT(CASE WHEN user_requests.status = "PICKEDUP" AND DATE(user_requests.started_at) < "' . $fromDate . '" AND (user_requests.finished_at IS NULL OR user_requests.status != "COMPLETED") THEN user_requests.id END) as old_ongoing')


                  
    )
    ->groupBy('providers.id')
    ->get();

 //dd($providers);

    // get attendance for the same date
    $attendance = DB::table('attendance')
        ->whereDate('created_at', $fromDate)
        ->whereIn('provider_id', $providers->pluck('provider_id'))
        ->get()
        ->keyBy('provider_id');

    $leaves = Leave::whereIn('provider_id', $providers->pluck('provider_id'))
            ->where('type', 'leave')
            ->whereDate('start_date', '<=', $fromDate) 
            ->whereDate('end_date', '>=', $fromDate)
            ->get()
            ->keyBy('provider_id');

//dd($attendance);

    // Filter by clicked stage
    $filtered = $this->filterProvidersByStage($providers, $attendance,$leaves, $stage);
  
    return response()->json([
        'count' => $filtered->count(),
        // 'list' => $filtered->values(),
        'list' => $filtered->values()->map(function ($prov) use ($stage) {
                // If stage is a specific duration bucket, override counts
                $durationStages = ['completed_0_4', 'completed_4_10', 'completed_10_24', 'completed_24_48', 'completed_gt_48'];
                if (in_array($stage, $durationStages)) {
                    // Set completed_tickets to the specific bucket value
                    $prov->completed_tickets = $prov->$stage ?? 0;

                    // Zero out the individual buckets so they don't show up redundantly or confusingly
                    $prov->completed_0_4 = 0;
                    $prov->completed_4_10 = 0;
                    $prov->completed_10_24 = 0;
                    $prov->completed_24_48 = 0;
                    $prov->completed_gt_48 = 0;
                }
                return $prov;
            }),
        'fromDate'=>$fromDate, 'toDate'=>$toDate
    ]);
}

public function getSubCategories($categoryId)
    {
        try {
            // Fetch subcategories where the parent category matches
            $subCategories = SubServiceType::where('service_type_id', $categoryId)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            // Return JSON response
            return response()->json($subCategories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load subcategories.'], 500);
        }
    }

 public function gp_mapping()
    {
        
    return view('admin.gps.mapping');

    }

public function gp_mapping_update(Request $request)
    {

  
        // ? Use Validator instead of $request->validate()
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:1,2',
            'import_file' => 'required|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validation failed. Please check your inputs.');
        }
//dd('rahul');
        $file = $request->file('import_file');
        if (!$file->isValid()) {
            return back()->with('error', 'Invalid file uploaded.');
        }

        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return back()->with('error', 'Unable to open the CSV file.');
        }

        $updated = 0;
        $notFound = 0;
        $rowCount = 0;
        $headerSkipped = false;

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($data) < 3) continue;

            // Skip header row
            if (!$headerSkipped) {
                $headerSkipped = true;
                if (!is_numeric($data[0])) continue;
            }

            $lgdCode = trim($data[0]);
            $name = trim($data[1]);
            $phone = trim($data[2]);
            $rowCount++;

            if (!$lgdCode) continue;

            switch ($request->input('type')) {
                case '1': // FRT
                    $updateData = [
                        'provider' => $name,
                        'contact_no' => $phone,
                    ];
                    break;
                case '2': // Patroller
                    $updateData = [
                        'petroller' => $name,
                        'petroller_contact_no' => $phone,
                    ];
                    break;
                default:
                    $updateData = [];
                    break;
            }

            $affected = DB::table('gp_list')
                ->where('lgd_code', $lgdCode)
                ->update($updateData);

            if ($affected) {
                $updated++;
            } else {
                $notFound++;
            }
        }

        fclose($handle);

        if ($updated > 0) {
            return redirect()->back()->with('success', "Import completed successfully! Updated: {$updated}, Not Found: {$notFound} (Total Rows: {$rowCount})");
        } else {
            return redirect()->back()->with('error', "No records updated. Please check your LGD codes in the CSV file.");
        }
    }


  public function tracking1(Request $request)
{
    $provider_id = $request->provider_id;

    // Get the ticket the provider is working on
    $ticket = DB::table('user_requests')
        ->join('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
        ->select(
            'user_requests.booking_id',
            'master_tickets.lgd_code',
            'master_tickets.gpname',
            'master_tickets.mandal',
            'master_tickets.district',
            'master_tickets.lat as gp_lat',
            'master_tickets.log as gp_lng',
            'user_requests.started_at'
        )
        ->where('user_requests.provider_id', $provider_id)
        ->where('user_requests.status', 'PICKEDUP')
        ->first();

    if (!$ticket) {
        return response()->json([
            'status' => false,
            'message' => 'No active ticket found for this provider'
        ]);
    }

    $lgd_code = $ticket->lgd_code;

    $startedAt = $ticket->started_at;

   $dayStart = \Carbon\Carbon::parse($startedAt)->startOfDay();
   $dayEnd   = \Carbon\Carbon::parse($startedAt)->endOfDay();

    // Provider details
    $provider = DB::table('providers')
        ->select('id','first_name', 'last_name', 'mobile')
        ->where('id', $provider_id)
        ->first();

    // Get provider movement (tracking history >= started_at)
    $trackingRows = DB::table('provider_tracking_histories')
    ->where('provider_id', $provider_id)
    ->whereBetween('created_at', [$dayStart, $dayEnd])
    ->orderBy('created_at', 'asc')
    ->get(['latlng', 'created_at']);

    // Decode all JSON latlng arrays & merge into one
    $travelPath = [];

    foreach ($trackingRows as $row) {
        $points = json_decode($row->latlng, true);

        if (is_array($points)) {
            foreach ($points as $p) {
                // ensure record is >= started_at
                if ($p['datetime'] >= $startedAt) {
                    $travelPath[] = [
                        'lat' => $p['latitude'],
                        'lng' => $p['longitude'],
                        'datetime' => $p['datetime']
                    ];
                }
            }
        }
    }

    // Sort by datetime
    usort($travelPath, function ($a, $b) {
        return strtotime($a['datetime']) - strtotime($b['datetime']);
    });

    // Final output  
   

return view('admin.reports.tracking', [
    'data' => [
        'provider' => $provider,
        'ticket' => $ticket,
        'travel_path' => $travelPath,
        'start_point' => $travelPath[0] ?? null,
        'last_point' => end($travelPath) ?: null,
        'gp_location' => [
            'lat' => $ticket->gp_lat,
            'lng' => $ticket->gp_lng
        ]
    ]
]);

}

public function getTrackingData($id, Request $request)
{
    $ticket = DB::table('user_requests')
        ->select('started_at')
        ->where('provider_id', $id)
        ->where('status', 'PICKEDUP')
        ->first();

    $startedAt = $ticket ? $ticket->started_at : null;

    $fromDate = $startedAt ? date('Y-m-d', strtotime($startedAt)) : date('Y-m-d');
    $toDate = date('Y-m-d');

    // Get tracking data for the date range
    $trackingHistory = DB::table('provider_tracking_histories')
        ->where('provider_id', $id)
        ->whereBetween('created_at', [
            $fromDate . ' 00:00:00',
            $toDate . ' 23:59:59'
        ])
        ->orderBy('created_at', 'asc')
        ->get();

    $trackingData = [];
    $dailyDistances = [];
    $dailyIdleTimes = [];
    $totalDistance = 0;

    // --- Flatten all points ---
    foreach ($trackingHistory as $history) {
        if ($history->latlng) {
            $points = json_decode($history->latlng, true);

            if (is_array($points)) {

                $historyDate = date('Y-m-d', strtotime($history->created_at));

                foreach ($points as $point) {

                    // ? FILTER POINTS older than startedAt
                    $pointDatetime = $point['datetime'] ?? $history->created_at;

                    if ($startedAt && strtotime($pointDatetime) < strtotime($startedAt)) {
                        continue; // skip old points
                    }

                    if (isset($point['latitude'], $point['longitude'])) {
                        $trackingData[] = [
                            'latitude' => (float)$point['latitude'],
                            'longitude' => (float)$point['longitude'],
                            'datetime' => $pointDatetime,
                            'date' => date('Y-m-d', strtotime($pointDatetime)),
                            'address' => $this->getAddressFromCoordinates($point['latitude'], $point['longitude'])
                        ];
                    }
                }
            }
        }
    }

    // --- Group points by date ---
    $groupedByDate = [];
    foreach ($trackingData as $p) {
        $groupedByDate[$p['date']][] = $p;
    }

    // --- Process each day ---
    foreach ($groupedByDate as $date => $points) {

        $dayDistance = 0;
        $dayIdleTime = 0;

        // Calculate total distance using helper
        $dayDistance = DistanceHelper::calculateAccurateDistance($points);
        $totalDistance += $dayDistance;

        // Sort by datetime
        usort($points, function($a, $b) {
            return strtotime($a['datetime']) <=> strtotime($b['datetime']);
        });

        $prevPoint = $points[0];

        foreach (array_slice($points, 1) as $currPoint) {

            $timeDiff = strtotime($currPoint['datetime']) - strtotime($prevPoint['datetime']);

            $threshold = 0.0005; // ~50m
            if (
                abs($currPoint['latitude'] - $prevPoint['latitude']) < $threshold &&
                abs($currPoint['longitude'] - $prevPoint['longitude']) < $threshold
            ) {
                if ($timeDiff > 60) $dayIdleTime += $timeDiff;
            }

            $prevPoint = $currPoint;
        }

        $dailyDistances[$date] = $dayDistance;
        $dailyIdleTimes[$date] = $dayIdleTime;
    }

    return response()->json([
        'tracking' => $trackingData,
        'total_distance' => $totalDistance,
        'daily_distances' => $dailyDistances,
        'daily_idle_time' => $dailyIdleTimes
    ]);
}
 
 public function tracking(Request $request)
{

       $provider_id = $request->provider_id;

        $provider = DB::table('providers')
                    ->leftJoin('attendance', function ($join) {
                        $join->on('providers.id', '=', 'attendance.provider_id')
                            ->whereDate('attendance.created_at', Carbon::today());
                    })
                    ->where('providers.id', $provider_id)
                    ->select('providers.*', 'attendance.online_image')
                    ->first();

        //$column = $provider->type === 2 ? 'contact_no' : 'petroller_contact_no';

       $ticket = DB::table('user_requests')
        ->join('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
        ->select(
            'user_requests.booking_id',
            'master_tickets.lgd_code',
            'master_tickets.gpname',
            'master_tickets.mandal',
            'master_tickets.district',
            'master_tickets.lat as gp_lat',
            'master_tickets.log as gp_lng',
            'user_requests.subcategory',
            'user_requests.downreason',
            'master_tickets.downdate',
            'master_tickets.downtime',
            'user_requests.autoclose',
            'user_requests.started_at'
        )
        ->where('user_requests.provider_id', $provider_id)
        ->where('user_requests.status', 'PICKEDUP')
        ->first();

    if (!$ticket) {
        return response()->json([
            'status' => false,
            'message' => 'No active ticket found for this provider'
        ]);
    }

    $lgd_code = $ticket->lgd_code;


        $GP = DB::table('gp_list')
            ->where('type', 'GP')
            ->where('lgd_code', $lgd_code)->get();

         

        
        if (!$provider) {
            abort(404, 'Employee not found');
        }

        return view('admin.reports.live_track', compact('provider','GP','ticket'));

}

 private function getAddressFromCoordinates($lat, $lng)
    {
        return "Location: {$lat}, {$lng}";
    }



public function ongoingTicketData(Request $request)
{

    $filter = $request->input('filter', 'all');

    $user = Session::get('user');
    $company_id  = $user->company_id;
    $state_id    = $user->state_id;
    $district_id = $user->district_id;

    // --- Get only PICKEDUP Tickets with provider & zone info ---
    $query = DB::table('user_requests')
        ->join('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
        ->join('providers', 'providers.id', 'user_requests.provider_id')
        ->leftJoin('zonal_managers', 'zonal_managers.id', 'providers.zone_id')
        ->where('providers.company_id', $company_id)
        ->where('providers.state_id', $state_id)
        ->where('providers.status', 'approved')
        ->whereIn('providers.type', [2, 5]) // 2=FRT,5=Patroller
        ->where('user_requests.status', "PICKEDUP")
        ->select(
            'providers.id as provider_id',
            'providers.type as provider_type',
            'providers.zone_id',
            'zonal_managers.Name as zone_name',
            'user_requests.started_at'
        );

    if (!empty($district_id)) {
        $query->where('providers.district_id', $district_id);
    }

    // APPLY FILTER
    if ($filter == "auto") {
        $query->where('user_requests.default_autoclose', "Auto");
    } 
    elseif ($filter == "manual") {
        $query->where('user_requests.default_autoclose', "Manual");
    }

    $tickets = $query->groupBy('user_requests.provider_id')->get();


    // -------------------------------
    // Zone ? FRT & Patroller Buckets
    // -------------------------------
    $zones = [];

    foreach ($tickets as $t) {

        if (!isset($zones[$t->zone_id])) {
            $zones[$t->zone_id] = [
                'zone_name' => $t->zone_name,
                'zone_id' => $t->zone_id,
                'frt' => [
                    '0_4' => 0,
                    '4_10' => 0,
                    '10_24' => 0,
                    '24_48' => 0,
                    'above_48' => 0,
                ],
                'patrollers' => [
                    '0_4' => 0,
                    '4_10' => 0,
                    '10_24' => 0,
                    '24_48' => 0,
                    'above_48' => 0,
                ]
            ];
        }

        // Calculate age in hours
       $ageHours = Carbon::now()->diffInHours(Carbon::parse($t->started_at));

        // Decide bucket
        if ($ageHours < 4) $bucket = '0_4';
        else if ($ageHours < 10) $bucket = '4_10';
        else if ($ageHours < 24) $bucket = '10_24';
        else if ($ageHours < 48) $bucket = '24_48';
        else $bucket = 'above_48';

        // Provider type bucket
        if ($t->provider_type == 2) {
            $zones[$t->zone_id]['frt'][$bucket]++;
        } else {
            $zones[$t->zone_id]['patrollers'][$bucket]++;
        }
    }

    return response()->json([
        'zones' => $zones
    ]);
}


public function getRangPickupTickets(Request $request)
{
    $zone_id = $request->input('zone_id');
    $type    = $request->input('type');      
    $range   = $request->input('range'); 
    $filter  = $request->input('filter', 'all');     

    // DATE FILTERS
    $fromDate = $request->input('from_date', date('Y-m-d'));
    $toDate   = $request->input('to_date', date('Y-m-d'));

    list($minH, $maxH) = $this->getRangeHours($range);

    $user = Session::get('user');
    $company_id  = $user->company_id;
    $state_id    = $user->state_id;
    $district_id = $user->district_id;

    // =============================================
    // 1. FETCH PICKEDUP TICKETS
    // =============================================
    $tickets = DB::table('user_requests')
        ->join('providers', 'providers.id', 'user_requests.provider_id')
        ->leftJoin('zonal_managers', 'zonal_managers.id', 'providers.zone_id')
        ->join('master_tickets', 'master_tickets.ticketid', 'user_requests.booking_id')
        ->where('providers.company_id', $company_id)
        ->where('providers.state_id', $state_id)
        ->where('providers.status', 'approved')
        ->where('user_requests.status', 'PICKEDUP')
        ->where('providers.type', $type == 'frt' ? 2 : 5)
        ->when($zone_id, function ($q) use ($zone_id) {
            return $q->where('providers.zone_id', $zone_id);
        })
        ->when($district_id, function ($q) use ($district_id) {
            return $q->where('providers.district_id', $district_id);
        })
         ->when($filter === 'auto', function ($q) {
            return $q->where('user_requests.default_autoclose', 'Auto');
        })
        ->when($filter === 'manual', function ($q) {
            return $q->where('user_requests.default_autoclose', 'Manual');
        })
        ->select(
            'providers.id as provider_id',
            'providers.first_name',
            'providers.last_name',
            'providers.mobile',
            'providers.type',
            'providers.zone_id',
            'zonal_managers.Name as zone_name',
            'user_requests.id as urid',
            'user_requests.started_at',
            'user_requests.status',
            'user_requests.finished_at',
            'master_tickets.created_at as ticket_created'
        )->groupBy('user_requests.provider_id')
        ->get();

    // =============================================
    // 2. RANGE FILTER
    // =============================================
    $filtered = $tickets->filter(function ($row) use ($minH, $maxH) {
        $ageHours = Carbon::now()->diffInHours(Carbon::parse($row->started_at));
        return $ageHours >= $minH && $ageHours < $maxH;
    });

    $providerIds = $filtered->pluck('provider_id')->unique();

    if ($providerIds->isEmpty()) {
        return response()->json([
            'range' => $range,
            'count' => 0,
            'summary' => [],
            'list' => []
        ]);
    }

    // =============================================
    // 3. FETCH ALL COUNTS FOR ALL PROVIDERS (ONE QUERY)
    // =============================================

    $sql =
        "user_requests.provider_id, " .
        "COUNT(CASE WHEN DATE(master_tickets.created_at) BETWEEN ? AND ? THEN 1 END) AS total_tickets, " .
        "COUNT(CASE WHEN user_requests.status='COMPLETED' AND DATE(user_requests.finished_at) BETWEEN ? AND ? THEN 1 END) AS completed_tickets, " .
        "COUNT(CASE WHEN user_requests.status='ONHOLD' AND DATE(user_requests.started_at) BETWEEN ? AND ? THEN 1 END) AS hold_tickets, " .
        "COUNT(CASE WHEN user_requests.status='PICKEDUP' AND DATE(user_requests.started_at) BETWEEN ? AND ? THEN 1 END) AS pickup_tickets, " .
        "COUNT(CASE WHEN user_requests.status='INCOMING' THEN 1 END) AS pending_tickets, " .
        "COUNT(CASE WHEN user_requests.status='PICKEDUP' AND DATE(user_requests.started_at) < ? AND user_requests.finished_at IS NULL THEN 1 END) AS old_ongoing";

    $counts = DB::table('user_requests')
        ->leftJoin('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
        ->whereIn('user_requests.provider_id', $providerIds)
        ->selectRaw(
            $sql,
            array(
                $fromDate, $toDate,
                $fromDate, $toDate,
                $fromDate, $toDate,
                $fromDate, $toDate,
                $fromDate
            )
        )
        ->groupBy('user_requests.provider_id')
        ->get()
        ->keyBy('provider_id');

    // =============================================
    // 4. BUILD FINAL RESULT
    // =============================================
    $summary = array(
        'total_tickets'     => 0,
        'completed_tickets' => 0,
        'hold_tickets'      => 0,
        'pickup_tickets'    => 0,
        'pending_tickets'   => 0,
        'old_ongoing'       => 0
    );

    $finalList = array();

    foreach ($filtered as $t) {

        $prov = (array)$t;

        if (isset($counts[$t->provider_id])) {
            $c = $counts[$t->provider_id];

            $prov['total_tickets']     = $c->total_tickets;
            $prov['completed_tickets'] = $c->completed_tickets;
            $prov['hold_tickets']      = $c->hold_tickets;
            $prov['pickup_tickets']    = $c->pickup_tickets;
            $prov['pending_tickets']   = $c->pending_tickets;
            $prov['old_ongoing']       = $c->old_ongoing;

            $summary['total_tickets']     += $c->total_tickets;
            $summary['completed_tickets'] += $c->completed_tickets;
            $summary['hold_tickets']      += $c->hold_tickets;
            $summary['pickup_tickets']    += $c->pickup_tickets;
            $summary['pending_tickets']   += $c->pending_tickets;
            $summary['old_ongoing']       += $c->old_ongoing;
        }

        $finalList[] = $prov;
    }

    return response()->json(array(
        'range'   => $range,
        'count'   => count($finalList),
        'summary' => $summary,
        'list'    => array_values($finalList)
    ));
}

private function getRangeHours($range)
{
    switch ($range) {
        case '0_4': return array(0, 4);
        case '4_10': return array(4, 10);
        case '10_24': return array(10, 24);
        case '24_48': return array(24, 48);
        case 'above_48': return array(48, 9999);
    }
    return array(0, 9999);
}



public function ongoing_details(Request $request)
{

    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    
    // ---------- Dropdown Data ----------
    //$districts = DB::table('districts')->get();
    //$blocks    = DB::table('blocks')->get();
    $zoneIds = DB::table('gp_list')->where('state_id', $state_id)->pluck('zonal_id')->unique();

    $zonals    = DB::table('zonal_managers')->whereIn('id',$zoneIds)->get();

    return view('admin.reports.ongoingTT_details', compact('districts', 'blocks', 'zonals'));
}

public function dashboard_workforce_test(Request $request)
{
return view('admin.reports.dashboard-test');


}

public function joint_enclouser_reports(Request $request)
{

   $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    $district_id = $user->district_id;

    // ---------- Dropdown Data ----------
    //$districts = DB::table('districts')->get();
    //$blocks    = DB::table('blocks')->get();
    $zoneIdsQuery = DB::table('gp_list')->where('state_id', $state_id);
      if (!empty($district_id)) {
        $zoneIdsQuery->where('district_id', $district_id);
    }
    $zoneIds = $zoneIdsQuery->pluck('zonal_id')->unique();

    $zonals    = DB::table('zonal_managers')->whereIn('id',$zoneIds)->get();

      return view('admin.dashboard.joint_enclouser', compact('districts', 'blocks', 'zonals'));


}

public function joint_enclouser_tickets(Request $request)
{
    $user = Session::get('user');

    $company_id = $user->company_id;
    $state_id   = $user->state_id;

    $zone_id     = $request->zone_id;
    $district_id = $request->district_id ?: $user->district_id;
    $role_id     = $request->role_id;
    $member_id   = $request->member_id;
    $generated   = $request->generated_type;
    $purpose     = $request->purpose;

    // ================= DATE =================
    $startDate = $request->from_date
        ? Carbon::parse($request->from_date)->startOfDay()
        : Carbon::today()->startOfDay();

    $endDate = $request->to_date
        ? Carbon::parse($request->to_date)->endOfDay()
        : Carbon::today()->endOfDay();

    // ================= SUBQUERY =================
    $submitFileSubQuery = "
        (
            SELECT ticket_id, joint_enclouser_beforeimg,joint_enclouser_afterimg,materials,joint_enclosurebefore_latlong,
            joint_enclosureafter_latlong
            FROM submitfiles
            WHERE joint_enclouser_beforeimg IS NOT NULL
              AND joint_enclouser_beforeimg != '[]'
            GROUP BY ticket_id
        ) sf
    ";

    // ================= BASE QUERY =================
    $query = DB::table('user_requests')
        ->join('providers', 'providers.id', '=', 'user_requests.provider_id')
        ->leftJoin('zonal_managers', 'zonal_managers.id', '=', 'providers.zone_id')
        ->leftJoin('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
        ->leftJoin(DB::raw($submitFileSubQuery), function ($join) {
            $join->on('sf.ticket_id', '=', 'user_requests.booking_id');
        })
        ->where('providers.company_id', $company_id)
        ->where('providers.state_id', $state_id)
        ->where('providers.status', 'approved')
        ->where('user_requests.status', 'COMPLETED')
        ->whereNotNull('sf.joint_enclouser_beforeimg')
        ->whereBetween('user_requests.finished_at', array($startDate, $endDate));

    // ================= MANUAL FILTERS =================
    if ($zone_id) {
        $query->where('providers.zone_id', $zone_id);
    }

    if ($district_id) {
        $query->where('providers.district_id', $district_id);
    }

    if ($role_id) {
        $query->where('providers.type', $role_id);
    }

    if ($member_id) {
        $query->where('providers.id', $member_id);
    }

    if ($generated) {
        $query->where('user_requests.default_autoclose', $generated);
    }

    if ($purpose) {
        $query->where('user_requests.purpose', $purpose);
    }

    // ================= SELECT =================
    $tickets = $query
        ->select(
            'providers.id as provider_id',
            'providers.first_name',
            'providers.last_name',
            'providers.mobile',
            'providers.type',
            'zonal_managers.Name as zone_name',
            'master_tickets.district',
            'master_tickets.mandal',
            'master_tickets.gpname',
            'master_tickets.lgd_code',
            'user_requests.id as urid',
            'user_requests.booking_id',
            'master_tickets.downdate',
            'master_tickets.downtime',
            'user_requests.started_at',
            'user_requests.finished_at',
            'user_requests.downreason',
            'user_requests.status',
            'user_requests.downreasonindetailed',
            'user_requests.default_autoclose',
            'user_requests.autoclose', 
            'user_requests.subcategory',
            'user_requests.purpose',
            'sf.materials',
            'sf.joint_enclouser_beforeimg',
            'sf.joint_enclouser_afterimg',
            'sf.joint_enclosurebefore_latlong',
            'sf.joint_enclosureafter_latlong'
            )
        ->orderBy('user_requests.finished_at', 'desc')
        ->get();

    return response()->json(array(
        'data' => $tickets
    ));
}

public function joint_enclosure_download(Request $request)
{

   $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    $district_id = $user->district_id;

    // ---------- Dropdown Data ----------
    //$districts = DB::table('districts')->get();
    //$blocks    = DB::table('blocks')->get();
    $zoneIdsQuery = DB::table('gp_list')->where('state_id', $state_id);
      if (!empty($district_id)) {
        $zoneIdsQuery->where('district_id', $district_id);
    }
    $zoneIds = $zoneIdsQuery->pluck('zonal_id')->unique();

    $zonals    = DB::table('zonal_managers')->whereIn('id',$zoneIds)->get();

      return view('admin.dashboard.joint_enclosure_download', compact('districts', 'blocks', 'zonals'));


}


public function jointEnclosureDownload_old(Request $request)
{
    $user = Session::get('user');

    $company_id  = $user->company_id;
    $state_id    = $user->state_id;
    $district_id = $request->district_id ?: $user->district_id;
    $block_id    = $request->block_id;

    $startDate = $request->from_date
        ? Carbon::parse($request->from_date)->startOfDay()
        : Carbon::today()->startOfDay();

    $endDate = $request->to_date
        ? Carbon::parse($request->to_date)->endOfDay()
        : Carbon::today()->endOfDay();

    /* ===== SAME SUBQUERY ===== */
    $submitFileSubQuery = "
        (
            SELECT ticket_id, joint_enclouser_beforeimg, joint_enclouser_afterimg
            FROM submitfiles
            WHERE joint_enclouser_beforeimg IS NOT NULL
            GROUP BY ticket_id
        ) sf
    ";

    /* ===== SAME QUERY ===== */
    $query = DB::table('user_requests')
        ->join('providers', 'providers.id', '=', 'user_requests.provider_id')
        ->leftJoin('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
        ->leftJoin('gp_list', 'master_tickets.lgd_code', '=', 'gp_list.lgd_code')
        ->leftJoin(DB::raw($submitFileSubQuery), function ($join) {
            $join->on('sf.ticket_id', '=', 'user_requests.booking_id');
        })
        ->where('providers.company_id', $company_id)
        ->where('providers.state_id', $state_id)
        ->where('user_requests.status', 'COMPLETED')
        ->whereBetween('user_requests.finished_at', [$startDate, $endDate]);
     if ($district_id) {
        $query->where('user_requests.district_id', $district_id);
    }

    if ($block_id) {
        $query->where('gp_list.block_id', $block_id);
    }
        $tickets = $query->select(
            'master_tickets.district',
            'master_tickets.mandal as block',
            'master_tickets.gpname',
            'sf.joint_enclouser_beforeimg',
            'sf.joint_enclouser_afterimg'
        )
        ->get();

     /* ===== ? HANDLE NO DATA ===== */
     if ($tickets->isEmpty()) {
      return redirect()->back()
        ->with('error', 'No joint enclosure images found for the selected block/district.');
    }

    /* ===== ZIP SETUP ===== */
    $zipName = 'joint_enclosure_images_' . time() . '.zip';
    $zipPath = storage_path('app/' . $zipName);

    $zip = new ZipArchive;
    $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    foreach ($tickets as $t) {

        $district = preg_replace('/[^A-Za-z0-9\-]/', '_', $t->district);
        $block    = preg_replace('/[^A-Za-z0-9\-]/', '_', $t->block);
        $gp       = preg_replace('/[^A-Za-z0-9\-]/', '_', $t->gpname);

        $basePath = "{$district}/{$block}/{$gp}/";

        $beforeImgs = json_decode($t->joint_enclouser_beforeimg, true) ?? [];
        $afterImgs  = json_decode($t->joint_enclouser_afterimg, true) ?? [];

        foreach ($beforeImgs as $img) {
            $filePath = public_path('uploads/SubmitFiles/' . $img);
            if (File::exists($filePath)) {
                $zip->addFile($filePath, $basePath . 'before_' . basename($img));
            }
        }

        foreach ($afterImgs as $img) {
            $filePath = public_path('uploads/SubmitFiles/' . $img);
            if (File::exists($filePath)) {
                $zip->addFile($filePath, $basePath . 'after_' . basename($img));
            }
        }
    }

    $zip->close();

    return response()->download($zipPath)->deleteFileAfterSend(true);
}


public function jointEnclosureDownload_old2(Request $request)
{
    $user = Session::get('user');

    $company_id  = $user->company_id;
    $state_id    = $user->state_id;
    $district_id = $request->district_id ?: $user->district_id;
    $block_id    = $request->block_id;

    // ================= DATE FILTER =================
    $startDate = $request->from_date
        ? Carbon::parse($request->from_date)->startOfDay()
        : Carbon::today()->startOfDay();

    $endDate = $request->to_date
        ? Carbon::parse($request->to_date)->endOfDay()
        : Carbon::today()->endOfDay();

    // ================= SUBQUERY =================
    $submitFileSubQuery = "
        (
            SELECT ticket_id,
                   joint_enclouser_beforeimg,
                   joint_enclouser_afterimg
            FROM submitfiles
            WHERE joint_enclouser_beforeimg IS NOT NULL
            GROUP BY ticket_id
        ) sf
    ";

    // ================= BASE QUERY =================
    $query = DB::table('user_requests')
        ->join('providers', 'providers.id', '=', 'user_requests.provider_id')
        ->leftJoin('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
        ->leftJoin('gp_list', 'master_tickets.lgd_code', '=', 'gp_list.lgd_code')
        ->leftJoin(DB::raw($submitFileSubQuery), function ($join) {
            $join->on('sf.ticket_id', '=', 'user_requests.booking_id');
        })
        ->where('providers.company_id', $company_id)
        ->where('providers.state_id', $state_id)
        ->where('user_requests.status', 'COMPLETED')
        ->whereBetween('user_requests.finished_at', [$startDate, $endDate]);

    // ================= FILTERS =================
    if ($district_id) {
        $query->where('user_requests.district_id', $district_id);
    }

    if ($block_id) {
        $query->where('gp_list.block_id', $block_id);
    }

    // ================= FETCH DATA =================
    $tickets = $query->select(
            'master_tickets.district',
            'master_tickets.mandal as block',
            'master_tickets.gpname',
            'sf.joint_enclouser_beforeimg',
            'sf.joint_enclouser_afterimg'
        )
        ->get();

    // ================= NO DATA HANDLING =================
    if ($tickets->isEmpty()) {
        return redirect()->back()
            ->with('error', 'No joint enclosure images found for the selected block/district.');
    }

    // ================= ZIP SETUP =================
    $zipName = 'joint_enclosure_images_' . time() . '.zip';
    $zipPath = storage_path('app/' . $zipName);

    $zip = new ZipArchive;

    // ? ZIP OPEN SAFETY CHECK
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return redirect()->back()
            ->with('error', 'Unable to create ZIP file. Please try again.');
    }

    // ================= ADD FILES =================
    foreach ($tickets as $t) {

        $beforeImgs = json_decode($t->joint_enclouser_beforeimg, true) ?? [];
        $afterImgs  = json_decode($t->joint_enclouser_afterimg, true) ?? [];

        // Skip tickets with no images
        if (empty($beforeImgs) && empty($afterImgs)) {
            continue;
        }

        $district = preg_replace('/[^A-Za-z0-9\-]/', '_', $t->district);
        $block    = preg_replace('/[^A-Za-z0-9\-]/', '_', $t->block);
        $gp       = preg_replace('/[^A-Za-z0-9\-]/', '_', $t->gpname);

        $basePath = "{$district}/{$block}/{$gp}/";

        // ===== BEFORE IMAGES =====
        foreach ($beforeImgs as $img) {
            $filePath = public_path('uploads/SubmitFiles/' . $img);
            if (File::exists($filePath)) {
                $zip->addFile(
                    $filePath,
                    $basePath . 'before_' . uniqid() . '_' . basename($img)
                );
            }
        }

        // ===== AFTER IMAGES =====
        foreach ($afterImgs as $img) {
            $filePath = public_path('uploads/SubmitFiles/' . $img);
            if (File::exists($filePath)) {
                $zip->addFile(
                    $filePath,
                    $basePath . 'after_' . uniqid() . '_' . basename($img)
                );
            }
        }
    }

    $zip->close();

    // ================= DOWNLOAD =================
    return response()
        ->download($zipPath)
        ->deleteFileAfterSend(true);
}
    public function jointEnclosureDownload(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $user = Session::get('user');

        $company_id = $user->company_id;
        $state_id = $user->state_id;
        $district_id = $request->district_id ?: $user->district_id;
        $block_id = $request->block_id;

        // ================= DATE FILTER =================
        $startDate = $request->from_date
            ? Carbon::parse($request->from_date)->startOfDay()
            : Carbon::today()->startOfDay();

        $endDate = $request->to_date
            ? Carbon::parse($request->to_date)->endOfDay()
            : Carbon::today()->endOfDay();

        // =================  FETCH JOINT ENCLOSURE IMAGES (SubmitFiles) =================
        $JEQuery = DB::table('user_requests')
            ->join('providers', 'providers.id', '=', 'user_requests.provider_id')
            ->join('submitfiles', 'submitfiles.ticket_id', '=', 'user_requests.booking_id')
            ->join('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
            ->where('providers.company_id', $company_id)
            ->where('providers.state_id', $state_id)
            ->where('user_requests.status', 'COMPLETED')
            ->whereBetween('user_requests.finished_at', [$startDate, $endDate])
            ->whereNotNull('submitfiles.joint_enclouser_beforeimg');

        if ($district_id) {
            $JEQuery->where('user_requests.district_id', $district_id);
        }
        if ($block_id) {
            $JEQuery->join('gp_list', 'master_tickets.lgd_code', '=', 'gp_list.lgd_code')
                ->where('gp_list.block_id', $block_id);
        }

        $JEResults = $JEQuery->select(
            'master_tickets.district',
            'master_tickets.mandal as block',
            'master_tickets.gpname',
            'submitfiles.joint_enclouser_beforeimg',
            'submitfiles.joint_enclouser_afterimg'
        )->get();


        // =================  FETCH RAISE TICIKET IMAGES (Patroller) =================
        $RTQuery = DB::table('raise_tickets')
            ->join('gp_list', 'gp_list.gp_name', '=', 'raise_tickets.gp_name')
            ->join('blocks', 'blocks.id', '=', 'gp_list.block_id')
            ->join('districts', 'districts.id', '=', 'gp_list.district_id')
            ->whereBetween('raise_tickets.created_at', [$startDate, $endDate])
            ->whereNotNull('raise_tickets.attachments');

        if ($district_id) {
            $RTQuery->where('gp_list.district_id', $district_id);
        }
        if ($block_id) {
            $RTQuery->where('gp_list.block_id', $block_id);
        }

        $RTResults = $RTQuery->select(
            'districts.name as district',
            'blocks.name as block',
            'raise_tickets.gp_name as gpname',
            'raise_tickets.attachments'
        )->get();


        // ================= MERGE CHECK =================
        if ($JEResults->isEmpty() && $RTResults->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No images found for the selected criteria.');
        }

        // ================= ZIP SETUP =================
        $zipName = 'JointEnclosure_Image' . time() . '.zip';
        $zipPath = storage_path('app/' . $zipName);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return redirect()->back()->with('error', 'Unable to create ZIP file.');
        }

        $filesAdded = 0;

        // Process Joint Enclosure
        foreach ($JEResults as $rec) {
            $basePath = $this->sanitizePath($rec->district) . '/' .
                $this->sanitizePath($rec->block) . '/' .
                $this->sanitizePath($rec->gpname) . '/';

            $before = json_decode($rec->joint_enclouser_beforeimg, true);
            if (!is_array($before) && !empty($rec->joint_enclouser_beforeimg))
                $before = [$rec->joint_enclouser_beforeimg];
            $before = $before ?: [];

            $after = json_decode($rec->joint_enclouser_afterimg, true);
            if (!is_array($after) && !empty($rec->joint_enclouser_afterimg))
                $after = [$rec->joint_enclouser_afterimg];
            $after = $after ?: [];

            $this->addImagesToZip($zip, $before, $basePath, 'before_', $filesAdded);
            $this->addImagesToZip($zip, $after, $basePath, 'after_', $filesAdded);
        }

        // Process Raise Tickets
        foreach ($RTResults as $rec) {
            $basePath = $this->sanitizePath($rec->district) . '/' .
                $this->sanitizePath($rec->block) . '/' .
                $this->sanitizePath($rec->gpname) . '/';

            $imgs = json_decode($rec->attachments, true);
            if (!is_array($imgs) && !empty($rec->attachments))
                $imgs = [$rec->attachments];
            $imgs = $imgs ?: [];

            $this->addImagesToZip($zip, $imgs, $basePath, 'patroller_', $filesAdded);
        }

        $zip->close();

        if ($filesAdded === 0) {
            if (File::exists($zipPath))
                unlink($zipPath);
            return redirect()->back()->with('error', 'Files not found on server.');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function sanitizePath($name)
    {
        return preg_replace('/[^A-Za-z0-9\-]/', '_', $name);
    }

    private function addImagesToZip($zip, $images, $basePath, $prefix, &$counter)
    {
        if (!is_array($images))
            return;

        foreach ($images as $img) {

            $path = '';
            if ($prefix == 'patroller_') {
                $path = public_path($img);
                if (!file_exists($path)) {
                    $path = public_path('uploads/' . $img);
                }
            } else {
                $path = public_path('uploads/SubmitFiles/' . $img);
            }

            if (File::exists($path)) {
                $zip->addFile($path, $basePath . basename($img));
                $counter++;
            }
        }
    }
  public function getCompletionTrend(Request $request) {
        $user = Session::get('user');
        $company_id = $user->company_id;
        $state_id = $user->state_id;
        $district_id = $user->district_id;
        $today = Carbon::today();
        // Default last 7 days including today
        $startDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : $today->copy()->subDays(6);
        $endDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : $today->copy();
        $fromDate = $startDate->toDateString();
        $toDate = $endDate->toDateString();
        
        // Generate Date Range Array
        $period = new DatePeriod(
             new DateTime($fromDate),
             new DateInterval('P1D'),
             new DateTime($toDate . ' 23:59:59')
        );
        
        $dates = [];
        foreach ($period as $date) {
            $dates[$date->format("Y-m-d")] = [
                'date' => $date->format("Y-m-d"),
                'frt_assigned' => 0,
                'frt_completed' => 0,
                'pat_assigned' => 0,
                'pat_completed' => 0
            ];
        }
        // ASSIGNED TICKETS (Based on user_requests.assigned_at)
        // Group by Date, Provider Type
        $assignedQuery = DB::table('master_tickets')
            ->join('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
            ->join('providers', 'providers.id', '=', 'user_requests.provider_id')
            ->where('providers.company_id', $company_id)
            ->where('providers.state_id', $state_id)
            ->where('providers.status', 'approved')
            ->where('user_requests.autoclose', "Manual")
           ->whereDate('user_requests.assigned_at', '>=', $fromDate)
           ->whereDate('user_requests.assigned_at', '<=', $toDate)
            ->whereIn('providers.type', [2, 5]); // 2=FRT, 5=Patroller
        if (!empty($district_id)) {
            $assignedQuery->where('providers.district_id', $district_id);
        }
       $assignedData = $assignedQuery->select(
                        DB::raw('DATE(user_requests.assigned_at) as date'),
                        'providers.type',
                        DB::raw('COUNT(*) as count')
                    )
                    ->groupBy(DB::raw('DATE(user_requests.assigned_at)'), 'providers.type')
                    ->get();
        foreach ($assignedData as $row) {
            $d = $row->date;
            if (isset($dates[$d])) {
                if ($row->type == 2) {
                    $dates[$d]['frt_assigned'] += $row->count;
                } elseif ($row->type == 5) {
                    $dates[$d]['pat_assigned'] += $row->count;
                }
            }
        }
        // 2. COMPLETED TICKETS (Based on user_requests.finished_at)
        $completedQuery = DB::table('user_requests')
            ->join('providers', 'providers.id', '=', 'user_requests.provider_id')
            ->where('providers.company_id', $company_id)
            ->where('providers.state_id', $state_id)
            ->where('providers.status', 'approved')
             ->where('user_requests.autoclose', "Manual")
            ->where('user_requests.status', 'COMPLETED')
            ->whereDate('user_requests.finished_at', '>=', $fromDate)
            ->whereDate('user_requests.finished_at', '<=', $toDate)
            ->whereIn('providers.type', [2, 5]);
        if (!empty($district_id)) {
            $completedQuery->where('providers.district_id', $district_id);
        }
        $completedData = $completedQuery->select(
                DB::raw('DATE(user_requests.finished_at) as date'),
                'providers.type',
                DB::raw('count(*) as count')
            )
            ->groupBy(DB::raw('DATE(user_requests.finished_at)'), 'providers.type')
            ->get();
        foreach ($completedData as $row) {
             $d = $row->date;
             if (isset($dates[$d])) {
                 if ($row->type == 2) {
                     $dates[$d]['frt_completed'] += $row->count;
                 } elseif ($row->type == 5) {
                     $dates[$d]['pat_completed'] += $row->count;
                 }
             }
        }
        return response()->json(array_values($dates));
    }

public function getBlocks($districtId)
{
    return DB::table('blocks')
        ->where('district_id', $districtId)
        ->select('id', 'name')
        ->get();
}

 






}