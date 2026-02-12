<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use DB;
use Exception;
use Setting;
use Storage;
use Session;

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
        $gps = DB::table('gp_list')
                        ->select('gp_list.*', 'districts.name as district_name', 'districts.id as districts_id', 'blocks.name as block_name','zonal_managers.Name as zonal_name','blocks.id as blocks_id')
                        ->leftJoin('districts', 'gp_list.district_id', '=', 'districts.id')
                        ->leftJoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id')
                        ->leftJoin('blocks', 'gp_list.block_id', '=', 'blocks.id')
                        ->where('gp_list.state_id',$state_id)
                        ->get();
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
    {
        $districts = District::get();
        $blocks = Block::get();
        $providers = Provider::get();
        $zonals = DB::table('zonal_managers')->get();
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
    
    // ---------- Dropdown Data ----------
    //$districts = DB::table('districts')->get();
    //$blocks    = DB::table('blocks')->get();
    $zoneIds = DB::table('gp_list')->where('state_id', $state_id)->pluck('zonal_id')->unique();

    $zonals    = DB::table('zonal_managers')->whereIn('id',$zoneIds)->get();

    return view('admin.reports.frtreports', compact('districts', 'blocks', 'zonals'));
}

public function frtreports_details(Request $request)
{

    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    
    // ---------- Dropdown Data ----------
    //$districts = DB::table('districts')->get();
    //$blocks    = DB::table('blocks')->get();
    $zoneIds = DB::table('gp_list')->where('state_id', $state_id)->pluck('zonal_id')->unique();

    $zonals    = DB::table('zonal_managers')->whereIn('id',$zoneIds)->get();

    return view('admin.reports.frtreports_details', compact('districts', 'blocks', 'zonals'));
}


public function get_districts($zone_id)
{

$districtIds = DB::table('gp_list')->where('zonal_id', $zone_id)->where('type', 'GP')->pluck('district_id')->unique();
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

     $user = Session::get('user');
     $company_id = $user->company_id;
     $state_id = $user->state_id;


   $startDate = $request->input('from_date') 
    ? Carbon::parse($request->input('from_date'))->startOfDay() 
    : Carbon::now()->startOfMonth();

    $endDate = $request->input('to_date') 
    ? Carbon::parse($request->input('to_date'))->endOfDay() 
    : Carbon::now()->endOfMonth();    

    $today       = Carbon::today()->toDateString();
    $fromDate    = $request->input('from_date') ? Carbon::parse($request->input('from_date'))->toDateString() : $today;
    $toDate      = $request->input('to_date') ? Carbon::parse($request->input('to_date'))->toDateString() : $today;


     $totalDays = 0;
   for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
    if ($date->dayOfWeek != Carbon::SUNDAY) { // ignore Sundays
        $totalDays++;
    }
}
  
   //dd($totalDays);

    $qb = DB::table('providers as p')
        ->leftJoin('zonal_managers as z', 'z.id', '=', 'p.zone_id')
        ->leftJoin('districts as d', 'd.id', '=', 'p.district_id')->where('p.state_id', $state_id)
        ->select(
            'p.id',
            'p.first_name',
            'p.last_name',
            'p.type as role',
            'p.mobile',
            'z.Name as zone_name',
            'd.Name as district_name'
        );

    // Apply filters
    if ($request->zone_id) {
        $qb->where('p.zone_id', $request->zone_id);
    }
    if ($request->district_id) {
        $qb->where('p.district_id', $request->district_id);
    }
     if ($request->role_id) {
        $qb->where('p.type', $request->role_id);
    }
    if ($request->member_id) {
        $qb->where('p.id', $request->member_id);
    }
   


    $rows = $qb->orderBy('p.first_name')->get();

$requestsByProvider = DB::table('user_requests')
    ->selectRaw('
        provider_id,

        -- Tickets assigned (based on created_at range)
        SUM(
            CASE 
                WHEN DATE(created_at) BETWEEN ? AND ? 
                THEN 1 ELSE 0 
            END
        ) as tickets_assigned,


        -- Tickets Auto assigned (based on created_at range)
        SUM(
            CASE 
                WHEN autoclose = "Auto" AND DATE(created_at) BETWEEN ? AND ? 
                THEN 1 ELSE 0 
            END
        ) as tickets_auto_assigned,


        -- Tickets Manual assigned (based on created_at range)
        SUM(
            CASE 
                WHEN autoclose = "Manual" AND DATE(created_at) BETWEEN ? AND ? 
                THEN 1 ELSE 0 
            END
        ) as tickets_manual_assigned,


        -- Not visited (all INCOMING, no date filter)
        SUM(
            CASE 
                WHEN status = "INCOMING" 
                THEN 1 ELSE 0 
            END
        ) as open_tickets,

        -- Not Auto visited (all INCOMING, no date filter)
        SUM(
            CASE 
                WHEN status = "INCOMING" AND autoclose = "Auto"
                THEN 1 ELSE 0 
            END
        ) as open_auto_tickets,
    
        -- Not Manual visited (all INCOMING, no date filter)
        SUM(
            CASE 
                WHEN status = "INCOMING" AND autoclose = "Manual"
                THEN 1 ELSE 0 
            END
        ) as open_manual_tickets,


        -- Completed manually (finished_at range)
        SUM(
            CASE 
                WHEN status = "COMPLETED" 
                     AND autoclose = "Manual" 
                     AND DATE(finished_at) BETWEEN ? AND ? 
                THEN 1 ELSE 0 
            END
        ) as manual_completed,
 
         -- Completed Auto (finished_at range)
        SUM(
            CASE 
                WHEN status = "COMPLETED" 
                     AND autoclose = "Auto" 
                     AND DATE(finished_at) BETWEEN ? AND ? 
                THEN 1 ELSE 0 
            END
        ) as auto_completed,


        -- Accepted (different fields depending on status)
        SUM(
            CASE 
                WHEN status = "PICKEDUP" 
                     AND DATE(started_at) BETWEEN ? AND ? 
                THEN 1
                ELSE 0
            END
        ) as tickets_accepted,

        -- Accepted Auto (different fields depending on status)
        SUM(
            CASE 
                WHEN status = "PICKEDUP" AND autoclose = "Auto" 
                     AND DATE(started_at) BETWEEN ? AND ? 
                THEN 1
                ELSE 0
            END
        ) as tickets_auto_accepted,

         -- Accepted Manual  (different fields depending on status)
        SUM(
            CASE 
                WHEN status = "PICKEDUP" AND autoclose = "Manual" 
                     AND DATE(started_at) BETWEEN ? AND ? 
                THEN 1
                ELSE 0
            END
        ) as tickets_manual_accepted,
 
        -- On hold Auto only (started_at range)
        SUM(
            CASE 
                WHEN status = "ONHOLD" AND autoclose = "Auto"
                     AND DATE(started_at) BETWEEN ? AND ? 
                THEN 1 ELSE 0 
            END
        ) as tickets_auto_onhold,

        -- On hold Manual only (started_at range)
        SUM(
            CASE 
                WHEN status = "ONHOLD" AND autoclose = "Manual"
                     AND DATE(started_at) BETWEEN ? AND ? 
                THEN 1 ELSE 0 
            END
        ) as tickets_manual_onhold,


        -- On hold only (started_at range)
        SUM(
            CASE 
                WHEN status = "ONHOLD" 
                     AND DATE(started_at) BETWEEN ? AND ? 
                THEN 1 ELSE 0 
            END
        ) as tickets_onhold
    ', [
        $fromDate, $toDate,   // tickets_assigned
        $fromDate, $toDate,   // tickets_assigned Auto
        $fromDate, $toDate,   // tickets_assigned Manual
        $fromDate, $toDate,   // gps_visited
        $fromDate, $toDate,   // gps_visited
        $fromDate, $toDate,   // tickets_accepted -> PICKEDUP
        $fromDate, $toDate,   // tickets_accepted -> PICKEDUP AUTO
        $fromDate, $toDate,   // tickets_accepted -> PICKEDUP MANUAL
        $fromDate, $toDate,    // tickets_onhold auto
        $fromDate, $toDate,    // tickets_onhold manual
        $fromDate, $toDate    // tickets_onhold
    ])
    ->where('state_id', $state_id)
    ->groupBy('provider_id')
    ->get()
    ->keyBy('provider_id');

   //dd($requestsByProvider);

    // fetch tracking rows in the date range (we'll group by provider then by date per-provider)
    $trackingData = DB::table('provider_tracking_histories')
        ->whereBetween(DB::raw('DATE(created_at)'), [$fromDate, $toDate])
        ->select('provider_id', 'latlng', DB::raw('DATE(created_at) as track_date'))
        ->get();

    // group by provider_id only -> per-provider collection of rows
    $trackingByProvider = $trackingData->groupBy('provider_id');

    $data = array();
    foreach ($rows as $r) {
        $zone      = $r->zone_name ? $r->zone_name : '-';
        $district  = $r->district_name ? $r->district_name : '-';
        $role      = $r->role ? $r->role : '-';
        $name      = trim(($r->first_name ? $r->first_name : '') . ' ' . ($r->last_name ? $r->last_name : ''));
        $contact   = $r->mobile? $r->mobile: '-';

        $attendanceCount = DB::table('attendance')
            ->where('provider_id', $r->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->filter(function ($a) {
                return Carbon::parse($a->created_at)->dayOfWeek != Carbon::SUNDAY;
            })
            ->count();

        $attendancePercent = $totalDays > 0 ? round(($attendanceCount / $totalDays) * 100) . '%' : '0%';
  
        $selfieToday = DB::table('attendance')->where('provider_id', $r->id)->whereDate('created_at', $today)->whereNotNull('online_image')->first();
        $selfie = $selfieToday ? 'Y' : 'N';

       
       
$stats = isset($requestsByProvider[$r->id]) ? $requestsByProvider[$r->id] : null;


$open_tickets  = isset($stats->open_tickets)  ? $stats->open_tickets  : 0;
$open_auto_tickets  = isset($stats->open_auto_tickets)  ? $stats->open_auto_tickets  : 0;
$open_manual_tickets  = isset($stats->open_manual_tickets)  ? $stats->open_manual_tickets  : 0;

$manual_completed = isset($stats->manual_completed) ? $stats->manual_completed : 0;
$auto_completed = isset($stats->auto_completed) ? $stats->auto_completed : 0;

$tickets_assigned = isset($stats->tickets_assigned) ? $stats->tickets_assigned : 0;
$tickets_auto_assigned = isset($stats->tickets_auto_assigned) ? $stats->tickets_auto_assigned : 0;
$tickets_manual_assigned = isset($stats->tickets_manual_assigned) ? $stats->tickets_manual_assigned : 0;

$tickets_accepted = isset($stats->tickets_accepted) ? $stats->tickets_accepted : 0;
$tickets_auto_accepted = isset($stats->tickets_auto_accepted) ? $stats->tickets_auto_accepted : 0;
$tickets_manual_accepted = isset($stats->tickets_manual_accepted) ? $stats->tickets_manual_accepted : 0;

$tickets_onhold   = isset($stats->tickets_onhold)   ? $stats->tickets_onhold   : 0;
$tickets_auto_onhold   = isset($stats->tickets_auto_onhold)   ? $stats->tickets_auto_onhold   : 0;
$tickets_manual_onhold   = isset($stats->tickets_manual_onhold)   ? $stats->tickets_manual_onhold   : 0;
       
       $totalDistance = 0.0;
        $providerTracking = $trackingByProvider->get($r->id, collect()); // collection of rows for this provider

        if ($providerTracking && $providerTracking->count() > 0) {
            // group provider rows by date (track_date) so day-by-day distance is computed separately
            $byDate = $providerTracking->groupBy('track_date');

            foreach ($byDate as $trackDate => $rowsForDay) {
                // assemble JSON blobs for this day
                $latlngArray = array();
                foreach ($rowsForDay as $tr) {
                    if (isset($tr->latlng) && $tr->latlng) {
                        $latlngArray[] = $tr->latlng;
                    }
                }

                if (count($latlngArray) > 0) {
                    $dayDistance = $this->calculateDistanceFromLatlngBlobs($latlngArray);
                    $totalDistance += $dayDistance;
                }
            }
        }

        $distance = round($totalDistance, 2);

        $data[] = array(
            'zone'            => $zone,
            'district'        => $district,
            'role'            => $role,
            'name'            => $name,
            'contact'         => $contact,
            'attendance'      => $attendancePercent,
            'selfie'          => $selfie,
            'open_tickets'    => $open_tickets,
            'open_auto_tickets'    => $open_auto_tickets,
            'open_manual_tickets'    => $open_manual_tickets,

            'manual_completed'=> $manual_completed,
            'auto_completed'  => $auto_completed,
            'distance'        => $distance,
            'tickets_assigned'=> $tickets_assigned,
            'tickets_auto_assigned'=> $tickets_auto_assigned,
            'tickets_manual_assigned'=> $tickets_manual_assigned,

            'tickets_accepted'=> $tickets_accepted,
            'tickets_auto_accepted'=> $tickets_auto_accepted,
            'tickets_manual_accepted'=> $tickets_manual_accepted,

            'tickets_onhold'  => $tickets_onhold,
            'tickets_auto_onhold'  => $tickets_auto_onhold,
            'tickets_manual_onhold'  => $tickets_manual_onhold,

            'avg_tat'         => 0,
            'sla_breaches'    => 0,
            'provider_id'     => $r->id,
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
            // unrealistic jump — ignore
            continue;
        }

        $totalKm += $d;
    }

    return round($totalKm, 2);
}
/**
 * Haversine formula — returns distance in kilometers.
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

    $today = date('Y-m-d');

    // --- Get providers with zone info ---
    $providers = DB::table('providers')
        ->leftJoin('user_requests', 'user_requests.provider_id', 'providers.id')
        ->leftJoin('master_tickets', 'user_requests.booking_id', 'master_tickets.ticketid')
        ->leftJoin('zonal_managers', 'zonal_managers.id', 'providers.zone_id')
        ->where('providers.zone_id', '!=', 0)
        ->where('providers.company_id', $company_id)
        ->where('providers.state_id', $state_id)
        ->whereIn('providers.type', [2,5]) // Only FRT & Patroller
        ->groupBy('providers.id')
        ->select(
            'providers.id as provider_id',
            'providers.type as provider_type', // 2=FRT,5=Patroller
            'providers.zone_id',
            'zonal_managers.Name as zone_name',
            DB::raw('COUNT(CASE WHEN DATE(master_tickets.downdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as total_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as completed_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "ONHOLD" AND DATE(user_requests.started_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as hold_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "PICKEDUP" AND DATE(user_requests.started_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as pickup_tickets'),
            DB::raw($pendingTicketsQuery),
            DB::raw($pendingTicketsMorethen24)
        )
        ->get();

    // --- Get today's attendance ---
    $attendance = DB::table('attendance')
        ->whereDate('created_at', $today)
        ->whereIn('provider_id', $providers->pluck('provider_id'))
        ->get()
        ->keyBy('provider_id');

    // Function to summarize a provider list
    function summarizeProvidersWithAttendance($list, $attendance) {
        $summary = [
            'total'        => $list->count(),
            'working'      => 0,
            'not_started'  => 0,
            'completed'    => 0,
            'no_ticket'    => 0,
            'only_hold'    => 0,
            'logged_in'    => 0,
            'not_logged_in'=> 0,
            'online'       => 0,
            'offline'      => 0,
        ];

        foreach ($list as $prov) {
            // Ticket stats
            if ($prov->pickup_tickets > 0) $summary['working']++;
            if ($prov->pending_tickets > 0 && $prov->pickup_tickets == 0 && $prov->hold_tickets == 0 && $prov->completed_tickets == 0) $summary['not_started']++;
            if ($prov->completed_tickets > 0 && $prov->pickup_tickets == 0 ) $summary['completed']++;
            if ($prov->pending_tickets == 0 && $prov->pickup_tickets == 0 && $prov->hold_tickets == 0 && $prov->completed_tickets == 0) $summary['no_ticket']++;
            if ($prov->hold_tickets > 0  && $prov->pickup_tickets == 0 && $prov->completed_tickets == 0) $summary['only_hold']++;

            // Attendance stats
            if (isset($attendance[$prov->provider_id])) {
                $summary['logged_in']++;
                if ($attendance[$prov->provider_id]->status == 'active') {
                    $summary['online']++;
                } else {
                    $summary['offline']++;
                }
            } else {
                $summary['not_logged_in']++;
            }
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
            'frt' => summarizeProvidersWithAttendance(collect($data['frt']), $attendance),
            'patrollers' => summarizeProvidersWithAttendance(collect($data['patrollers']), $attendance)
        ];
    }

    return response()->json([
        'from_date' => $fromDate,
        'to_date' => $toDate,
        'zones' => $zoneReport
    ]);
}

 // --- Helper: Determine provider stage ---
    private function getProviderStage($prov, $attendance)
    {
        if ($prov->pickup_tickets > 0) return 'working';
        if ($prov->pending_tickets > 0 && $prov->pickup_tickets == 0 && $prov->hold_tickets == 0 && $prov->completed_tickets == 0) return 'not_started';
        if ($prov->completed_tickets > 0 && $prov->pickup_tickets == 0) return 'completed';
        if ($prov->pending_tickets == 0 && $prov->pickup_tickets == 0 && $prov->hold_tickets == 0 && $prov->completed_tickets == 0) return 'no_ticket';
        if ($prov->hold_tickets > 0 && $prov->pickup_tickets == 0 && $prov->completed_tickets == 0) return 'only_hold';

        if (isset($attendance[$prov->provider_id])) {
            if ($attendance[$prov->provider_id]->status == 'active') return 'online';
            if ($attendance[$prov->provider_id]->status == 'offline') return 'offline';
            return 'logged_in';
        }

        return 'not_logged_in';
    }

    // --- Helper: Filter providers by stage ---
    private function filterProvidersByStage($list, $attendance, $stage)
    {
        $filtered = [];
        foreach ($list as $prov) {
            $provStage = $this->getProviderStage($prov, $attendance);
            if ($provStage === $stage) {
                $filtered[] = $prov;
            }
        }
        return collect($filtered);
    }

    // --- Helper: Summarize counts ---
    private function summarizeProvidersWithAttendance($list, $attendance)
    {
        $summary = [
            'total'        => $list->count(),
            'working'      => 0,
            'not_started'  => 0,
            'completed'    => 0,
            'no_ticket'    => 0,
            'only_hold'    => 0,
            'logged_in'    => 0,
            'not_logged_in'=> 0,
            'online'       => 0,
            'offline'      => 0,
        ];

        foreach ($list as $prov) {
            $stage = $this->getProviderStage($prov, $attendance);
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


    // same provider query as before (without filters)
    $providers = DB::table('providers')
        ->leftJoin('user_requests', 'user_requests.provider_id', 'providers.id')
        ->leftJoin('master_tickets', 'user_requests.booking_id', 'master_tickets.ticketid')
        ->leftJoin('zonal_managers', 'zonal_managers.id', 'providers.zone_id')
        ->where('providers.company_id', $company_id)
        ->where('providers.state_id', $state_id)
        ->whereIn('providers.type', [2, 5])
        ->when($zone_id, function ($q) use ($zone_id) {
    return $q->where('providers.zone_id', $zone_id);
})
->when($type, function ($q) use ($type) {
    return $q->where('providers.type', $type == 'frt' ? 2 : 5);
})
        ->select(
            'providers.id as provider_id',
            'providers.first_name',
            'providers.last_name',
            'providers.mobile',
            'providers.type',
            'providers.zone_id',
            'zonal_managers.Name as zone_name',
            DB::raw('COUNT(CASE WHEN DATE(master_tickets.downdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as total_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as completed_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "ONHOLD" AND DATE(user_requests.started_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as hold_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "PICKEDUP" AND DATE(user_requests.started_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as pickup_tickets'),
            DB::raw($pendingTicketsQuery)
                )
        ->groupBy('providers.id')
        ->get();

    // get attendance for the same date
    $attendance = DB::table('attendance')
        ->whereDate('created_at', $fromDate)
        ->whereIn('provider_id', $providers->pluck('provider_id'))
        ->get()
        ->keyBy('provider_id');

//dd($attendance);

    // Filter by clicked stage
    $filtered = $this->filterProvidersByStage($providers, $attendance, $stage);

    return response()->json([
        'count' => $filtered->count(),
        'list' => $filtered->values(),
    ]);
}





}