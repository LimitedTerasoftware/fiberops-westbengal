<?php

namespace App\Http\Controllers\Resource;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserRequests;
use App\Helpers\Helper;
use Auth;
use Setting;
use App\MasterTicket;
use App\SubmitFile;
use DB;
use \Carbon\Carbon;
use Session;


class TripResource extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('demo', ['only' => ['destroy']]);
        $this->perpage = Setting::get('per_page', '10');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {           
            $requests = UserRequests::RequestHistory()->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($requests);
            return view('admin.request.index', compact('requests','pagination'));
        } catch (Exception $e) {
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    public function Fleetindex()
    {
        try {
            $requests = UserRequests::RequestHistory()
                        ->whereHas('provider', function($query) {
                            $query->where('fleet', Auth::user()->id );
                        })->get();
            return view('fleet.request.index', compact('requests'));
        } catch (Exception $e) {
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function scheduled()
    {
        try{
            $requests = UserRequests::where('status' , 'SCHEDULED')
                        ->RequestHistory()
                        ->get();

            return view('admin.request.scheduled', compact('requests'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function pending(Request $request)
    {
		
		if($request->ajax()){
			
			$tickets = DB::table('master_tickets')
                  ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' , 'SEARCHING')
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                ->get();
				
				return response()->json(array('success' => true, 'data'=>$tickets));
			
		}
        try{
            $requests = UserRequests::where('status' , 'SEARCHING')
                        ->RequestHistory()
                        ->get();

            return view('admin.request.noresponsestatus', compact('requests'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function onhold(Request $request)
    {
		
		if($request->ajax()){
			
			$tickets = DB::table('master_tickets')
                  ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' , 'ONHOLD')
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                ->get();
				
				return response()->json(array('success' => true, 'data'=>$tickets));
			
		}
        try{
            $requests = UserRequests::where('status' , 'ONHOLD')
                        ->RequestHistory()
                        ->get();

            return view('admin.request.onholdstatus', compact('requests'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function resolved(Request $request)
    {
        if($request->ajax()){
			
			$tickets = DB::table('master_tickets')
                  ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' , 'COMPLETED')
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                ->get();
				
				return response()->json(array('success' => true, 'data'=>$tickets));
			
		}

        try{
            $requests = UserRequests::where('status' , 'COMPLETED')
                        ->RequestHistory()
                        ->get();

            return view('admin.request.status', compact('requests'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }


     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function ongoing(Request $request)
    {
        Session::put('user', Auth::User());
        $user = Session::get('user');
	    $company_id = $user->company_id;
	    $state_id = $user->state_id;
		if($request->ajax()){
			$search_interval = $request->get('interval', '');
			$tickets = DB::table('master_tickets')
                  ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' , 'INCOMING')
                  ->where('user_requests.company_id', $company_id)
                  ->where('user_requests.state_id', $state_id);
            if($search_interval != ''){
                if($search_interval == 'below_4_hours')
                    $tickets = $tickets->whereRaw('TIMESTAMPDIFF(HOUR, DATE_FORMAT(STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p"), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now()->format("Y-m-d H:i:s").'") < 4');
                else if($search_interval == 'between_4_to_10_hours')
                    $tickets = $tickets->whereRaw('TIMESTAMPDIFF(HOUR, STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now().'") BETWEEN 4 AND 10');
                else if($search_interval == 'between_10_to_24_hours')
                    $tickets = $tickets->whereRaw('TIMESTAMPDIFF(HOUR, STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now().'") BETWEEN 11 AND 24');
                else if($search_interval == 'above_24_hours')
                    $tickets = $tickets->whereRaw('TIMESTAMPDIFF(HOUR, STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now().'") > 24');
            }             
            $tickets = $tickets->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                ->get();
				
				return response()->json(array('success' => true, 'data'=>$tickets));
			
		}
       try{
            $search_interval = $request->get('interval', '');

            $requests = UserRequests::leftjoin('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                          ->where('user_requests.company_id', $company_id)
                          ->where('user_requests.state_id', $state_id)
                          ->where(function ($query) {
                            $query->where('user_requests.status', '=', 'INCOMING');
                        })->select('*','user_requests.id as uid','user_requests.status as ustatus',DB::Raw('TIMESTAMPDIFF(HOUR, DATE_FORMAT(STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p"), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now()->format("Y-m-d H:i:s").'") as hours'));
            if($search_interval != ''){
                if($search_interval == 'below_4_hours')
                    $requests->whereRaw('TIMESTAMPDIFF(HOUR, DATE_FORMAT(STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p"), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now()->format("Y-m-d H:i:s").'") < 4');
                else if($search_interval == 'between_4_to_10_hours')
                    $requests->whereRaw('TIMESTAMPDIFF(HOUR, DATE_FORMAT(STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p"), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now()->format("Y-m-d H:i:s").'") BETWEEN 4 AND 10');
                else if($search_interval == 'between_10_to_24_hours')
                    $requests->whereRaw('TIMESTAMPDIFF(HOUR, DATE_FORMAT(STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p"), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now()->format("Y-m-d H:i:s").'") BETWEEN 11 AND 24');
                else if($search_interval == 'above_24_hours')
                    $requests->whereRaw('TIMESTAMPDIFF(HOUR, DATE_FORMAT(STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p"), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now()->format("Y-m-d H:i:s").'") > 24');
            }             
            $requests = $requests->RequestHistory()->get();

           //dd($requests);

            return view('admin.request.ongoingstatus', compact('requests'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function yesterdayclosed(Request $request)
    {
		if($request->ajax()){
			
			$tickets = DB::table('master_tickets')
                  ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' , 'COMPLETED')
                                 ->whereDate('user_requests.started_at','=',Carbon::yesterday()) 
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                ->get();
				
				return response()->json(array('success' => true, 'data'=>$tickets));
			
		}
       try{
            $requests = UserRequests::leftjoin('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                        ->where(function ($query) {
                            $query->whereDate('user_requests.started_at','=',Carbon::yesterday())
                                  ->where('user_requests.status', '=', 'COMPLETED');
                        })->select('*','user_requests.id as uid','user_requests.status as ustatus',DB::Raw('TIMESTAMPDIFF(HOUR, STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now().'") as hours'))->get();
           
            return view('admin.request.yesterdayclosedtickets', compact('requests'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }



     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function todayclosed(Request $request)
    {
		if($request->ajax()){
			
			$tickets = DB::table('master_tickets')
                  ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' , 'COMPLETED')
                                 ->whereDate('user_requests.started_at','=',Carbon::today()) 
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                ->get();
				
				return response()->json(array('success' => true, 'data'=>$tickets));
			
		}
       try{
            $requests = UserRequests::leftjoin('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                        ->where(function ($query) {
                            $query->whereDate('user_requests.started_at','=',Carbon::today())
                                  ->where('user_requests.status', '=', 'COMPLETED');
                        })->select('*','user_requests.id as uid','user_requests.status as ustatus',DB::Raw('TIMESTAMPDIFF(HOUR, STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now().'") as hours'))->get();
           
            return view('admin.request.todayclosedtickets', compact('requests'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }




     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function todayongoing(Request $request)
    {
		if($request->ajax()){
			
			$tickets = DB::table('master_tickets')
                  ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status','!=','COMPLETED')
                                 ->whereDate('user_requests.started_at','=',Carbon::today()) 
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                ->get();
				
				return response()->json(array('success' => true, 'data'=>$tickets));
			
		}
       try{
            $requests = UserRequests::leftjoin('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                        ->where(function ($query) {
                            $query->whereDate('user_requests.started_at','=',Carbon::today())
                                  ->where('user_requests.status', '!=', 'COMPLETED');
                        })->select('*','user_requests.id as uid','user_requests.status as ustatus',DB::Raw('TIMESTAMPDIFF(HOUR, STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now().'") as hours'))->get();
           
            return view('admin.request.todayongoingtickets', compact('requests'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }


      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function notstarted(Request $request)
    {
		if($request->ajax()){
			
			$tickets = DB::table('master_tickets')
                  ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status','!=','COMPLETED')
                                 ->where('user_requests.status','!=','ONHOLD')
                                 ->whereNull('user_requests.started_at') 
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                ->get();
				
				return response()->json(array('success' => true, 'data'=>$tickets));
			
		}
       try{
            $requests = UserRequests::leftjoin('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                        ->where(function ($query) {
                            $query->whereNull('user_requests.started_at')
                                  ->where('user_requests.status', '!=', 'COMPLETED')
                                  ->where('user_requests.status','!=','ONHOLD');
                        })->select('*','user_requests.id as uid','user_requests.status as ustatus',DB::Raw('TIMESTAMPDIFF(HOUR, STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now().'") as hours'))->get();
           
            return view('admin.request.notstartedtickets', compact('requests'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ups()
    {
        try{
          
           $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.gpname','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                 ->where('master_tickets.ticketid','!=','')
                 ->where('master_tickets.downreason', 'like', '%POWER SHUTDOWN%')->orWhere('master_tickets.downreason', 'like', '%Power Shutdown%')->orWhere('master_tickets.downreasonindetailed', 'like', '%Power%')->paginate($this->perpage);
             $pagination=(new Helper)->formatPagination($tickets);
             $districts= DB::table('districts')->get();
             $blocks= DB::table('blocks')->get();
             return view('admin.tickets', compact('tickets','districts','blocks','pagination'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fiber()
    {
        try{
            $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','user_requests.gpname','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                 ->where('master_tickets.ticketid','!=','')
                 ->where('master_tickets.downreason', 'like', '%FIBER CUT%')->orWhere('master_tickets.downreason', 'like', '%Fiber%')->orWhere('master_tickets.downreasonindetailed', 'like', '%Fiber%')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($tickets);
            $districts= DB::table('districts')->get();
             $blocks= DB::table('blocks')->get();
             return view('admin.tickets', compact('tickets','districts','blocks','pagination')); 
              } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function poles()
    {
        try{
            $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.gpname','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                 ->where('master_tickets.ticketid','!=','')
                 ->where('master_tickets.downreason', 'like', '%POLE CHANGE%')->orWhere('master_tickets.downreason', 'like', '%Pole Change%')->orWhere('master_tickets.downreasonindetailed', 'like', '%Pole Change%')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($tickets);
             $districts= DB::table('districts')->get();
             $blocks= DB::table('blocks')->get();
             return view('admin.tickets', compact('tickets','districts','blocks','pagination'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function electronics()
    {
        try{
            $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.gpname','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                 ->where('master_tickets.ticketid','!=','')
                 ->where('master_tickets.downreason', 'like', '%ELECTRONICS%')->orWhere('master_tickets.downreason', 'like', '%Electronics%')->orWhere('master_tickets.downreasonindetailed', 'like', '%Electronics%')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($tickets);
             $districts= DB::table('districts')->get();
             $blocks= DB::table('blocks')->get();
             return view('admin.tickets', compact('tickets','districts','blocks','pagination'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function Fleetscheduled()
    {
        try{
            $requests = UserRequests::where('status' , 'SCHEDULED')
                         ->whereHas('provider', function($query) {
                            $query->where('fleet', Auth::user()->id );
                        })
                        ->get();

            return view('fleet.request.scheduled', compact('requests'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $request = UserRequests::findOrFail($id);
            $documents = SubmitFile::where('request_id',$id)->first();
            $maptrackdata = DB::table('master_coordinates')->select('id' ,'ticket_id', 'latitude' ,'longitude')->where('request_id', $id)->orderBy('id','DESC')->skip(0)->take(20)->get();
            $ticket = MasterTicket::where('ticketid',$request->booking_id)->first();
            return view('admin.request.show', compact('request','ticket','maptrackdata','documents'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    public function Fleetshow($id)
    {
        try {
            $request = UserRequests::findOrFail($id);
            return view('fleet.request.show', compact('request'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    public function Accountshow($id)
    {
        try {
            $request = UserRequests::findOrFail($id);
            return view('account.request.show', compact('request'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $Request = UserRequests::findOrFail($id);
            $Request->delete();
            return back()->with('flash_success', trans('admin.request_delete'));
        } catch (Exception $e) {
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    public function Fleetdestroy($id)
    {
        try {
            $Request = UserRequests::findOrFail($id);
            $Request->delete();
            return back()->with('flash_success', trans('admin.request_delete'));
        } catch (Exception $e) {
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ongoing_intervals()
    {


         try{
             $user = Session::get('user');
   
            $company_id = $user->company_id;
            $state_id = $user->state_id;
            $district_id = $user->district_id;

            $requestsQuery = UserRequests::leftjoin('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                        ->where('user_requests.status' , 'INCOMING') 
                         ->where('user_requests.company_id', $company_id)
                         ->where('user_requests.state_id', $state_id);
                      
            if (!empty($district_id)) {
                $requestsQuery->where('user_requests.district_id', $district_id);
            }
            $requests=   $requestsQuery->select(
                            DB::raw('COUNT(CASE WHEN TIMESTAMPDIFF(HOUR, DATE_FORMAT(STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p"), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now()->format("Y-m-d H:i:s").'") < 4 THEN 1 END) AS below_4_hours'),
                            DB::raw('COUNT(CASE WHEN TIMESTAMPDIFF(HOUR,  DATE_FORMAT(STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p"), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now()->format("Y-m-d H:i:s").'") BETWEEN 4 AND 10 THEN 1 END) AS between_4_to_10_hours'),
                            DB::raw('COUNT(CASE WHEN TIMESTAMPDIFF(HOUR,  DATE_FORMAT(STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p"), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now()->format("Y-m-d H:i:s").'") BETWEEN 11 AND 24 THEN 1 END) AS between_10_to_24_hours'),
                            DB::raw('COUNT(CASE WHEN TIMESTAMPDIFF(HOUR,  DATE_FORMAT(STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p"), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now()->format("Y-m-d H:i:s").'") > 24 THEN 1 END) AS above_24_hours')
                        )
                        ->RequestHistory()
                        ->get();
            
            $descriptions = array();
            $descriptions[0] = "Gp's Below 4 Hrs";
            $descriptions[1] = "Gp's Between 4 to 10 Hrs";
            $descriptions[2] = "Gp's Between 10 to 24 Hrs";
            $descriptions[3] = "Gp's above 24 Hrs";
           
            $ongoing_ticketsQuery = UserRequests::with('masterticket')->Where('status','INCOMING')->where('user_requests.company_id', $company_id)
                         ->where('user_requests.state_id', $state_id);
              if (!empty($district_id)) {
                $ongoing_ticketsQuery->where('user_requests.district_id', $district_id);
            }
            $ongoing_tickets =$ongoing_ticketsQuery->count();

            return view('admin.request.ongoing_intervals', compact('requests', 'descriptions','ongoing_tickets'));
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }
}
