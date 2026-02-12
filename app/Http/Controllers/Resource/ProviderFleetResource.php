<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use DB;
use Exception;
use Auth;
use \Carbon\Carbon;
use Setting;

use App\Provider;
use App\UserRequests;
use App\Helpers\Helper;
use App\Document;
use Mail;
use App\ProviderTrackingHistory;
use App\User;
use App\Fleet;
use App\Admin;
use App\UserPayment;
use App\ServiceType;
use App\ProviderService;
use App\UserRequestRating;
use App\UserRequestPayment;
use App\CustomPush;
use App\AdminWallet;
use App\ProviderWallet;
use App\FleetWallet;
use App\WalletRequests;
use App\ProviderDocument;
use App\MasterTicket;
use ZipArchive;
use Session;
use App\Block;
use App\District;
use Illuminate\Support\Arr;
use Log;


class ProviderFleetResource extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('account');
        $this->middleware('demo', ['only' => ['store', 'update','destroy']]);
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $providers = Provider::with('service','accepted','cancelled')
                    ->where('fleet', Auth::user()->id )
                    ->orderBy('id', 'DESC')
                    ->get();         

        return view('fleet.providers.index', compact('providers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('fleet.providers.create');
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
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|unique:providers,email|email|max:255',
            'mobile' => 'digits_between:6,13',
            'avatar' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
            'password' => 'required|min:6|confirmed',
        ]);

        try{

            $provider = $request->all();

            $provider['password'] = bcrypt($request->password);
            $provider['fleet'] = Auth::user()->id;
            if($request->hasFile('avatar')) {
                $provider['avatar'] = $request->avatar->store('provider/profile');
            }

            $provider = Provider::create($provider);
            $user = $provider; 
            $password = $request->password;
            
            Helper::registermail($user,$password);
            
            return back()->with('flash_success', trans('admin.provider_msgs.provider_saved'));

        } 

        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.provider_msgs.provider_not_found'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $provider = Provider::findOrFail($id);
            return view('fleet.providers.provider-details', compact('provider'));
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $provider = Provider::findOrFail($id);
            return view('fleet.providers.edit',compact('provider'));
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'mobile' => 'digits_between:6,13',
            'avatar' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
        ]);

        try {

            $provider = Provider::findOrFail($id);

            if($request->hasFile('avatar')) {
                if($provider->avatar) {
                    Storage::delete($provider->avatar);
                }
                $provider->avatar = $request->avatar->store('provider/profile');                    
            }

            $provider->first_name = $request->first_name;
            $provider->last_name = $request->last_name;
            $provider->mobile = $request->mobile;
            $provider->save();

            return redirect()->route('fleet.provider.index')->with('flash_success', trans('admin.provider_msgs.provider_update'));    
        } 

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.provider_msgs.provider_not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Provider::find($id)->delete();
            return back()->with('message', trans('admin.provider_msgs.provider_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.provider_msgs.provider_not_found'));
        }
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        try {
            $Provider = Provider::findOrFail($id);           
            $total_documents=Document::count();
            if($Provider->active_documents()==$total_documents && $Provider->service) {
                $Provider->update(['status' => 'approved']);    
                return back()->with('flash_success', trans('admin.provider_msgs.provider_approve'));
            } else {
                if($Provider->active_documents()!=$total_documents){
                    $msg=trans('admin.provider_msgs.document_pending');
                }
                if(!$Provider->service){
                    $msg=trans('admin.provider_msgs.service_type_pending');
                }

                if(!$Provider->service && $Provider->active_documents()!=$total_documents){
                    $msg=trans('admin.provider_msgs.provider_pending');
                }
                return redirect()->route('fleet.provider.document.index', $id)->with('flash_error',$msg);
            }
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.provider_msgs.provider_not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function disapprove($id)
    {
        Provider::where('id',$id)->update(['status' => 'banned']);
        return back()->with('flash_success', trans('admin.provider_msgs.provider_disapprove'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function request($id){

        try{

            $requests = UserRequests::where('user_requests.provider_id',$id)
                    ->RequestHistory()
                    ->get();

            return view('fleet.request.index', compact('requests'));
        } catch (Exception $e) {
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    public function tracking_provider(Request $request){

        try{  
            $tracking = '';
            if($request->all())
            {   
                $tracking = ProviderTrackingHistory::with('provider')->whereprovider_id($request->provider_id)->whereDate('created_at',$request->date_search)->first(); 
                if($tracking)
                {
                    $latlngs = json_decode($tracking->latlng); 
                    $tracking->s_latitude = $latlngs[0]->latitude;
                    $tracking->s_longitude = $latlngs[0]->longitude;
                    $tracking->d_latitude = $latlngs[count($latlngs) - 1]->latitude;
                    $tracking->d_longitude = $latlngs[count($latlngs) - 1]->longitude; 
                } 
            }   

            $providers = Provider::wherefleet(Auth::user()->id)->get();

            return view('fleet.providers.tracking', compact('tracking','providers'));

        } catch (Exception $e) {
            dd($e);
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }
    
 public function teams_status(Request $request){

      $user = Session::get('user');
      $company_id = $user->company_id;
      $state_id = $user->state_id;
      $district_id = $user->district_id;



      $inputFromDate = request()->input('from_date');
      $inputToDate = request()->input('to_date');

      $fromDate = $inputFromDate !== null ? $inputFromDate : date('Y-m-d'); // Default to today's date
      $toDate = $inputToDate !== null ? $inputToDate : date('Y-m-d');       // Default to today's date

 //dd($toDate);
       
      $pendingTicketsQuery = 'COUNT(CASE WHEN user_requests.status = "INCOMING"';
    if ($inputFromDate !== null && $inputToDate !== null) {
        $pendingTicketsQuery .= ' AND DATE(master_tickets.downdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"';
    }
    $pendingTicketsQuery .= ' THEN user_requests.id END) as pending_tickets';

    $pendingTicketsMorethen24 = 'COUNT(CASE WHEN user_requests.status = "INCOMING" AND ';
    $pendingTicketsMorethen24 .= 'STR_TO_DATE(CONCAT(master_tickets.downdate, " ", master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p") < DATE_SUB(NOW(), INTERVAL 24 HOUR)';

   if ($inputFromDate !== null && $inputToDate !== null) {
    $pendingTicketsMorethen24 .= ' AND DATE(master_tickets.downdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"';
   }
   $pendingTicketsMorethen24 .= ' THEN user_requests.id END) as pending_tickets_morethen_24';
    
    $teamsquery = DB::table('providers')->join('zonal_managers','zonal_managers.id','providers.zone_id')
                                   ->join('teams','teams.id','providers.team_id')
                                   ->leftJoin('user_requests','user_requests.provider_id','providers.id')
                                   ->leftJoin('master_tickets','user_requests.booking_id','master_tickets.ticketid')
                                   ->leftjoin('districts','districts.id','providers.district_id')
                                   ->where('providers.zone_id', '!=', 0)
                                   ->where('providers.state_id', $state_id)
                                   ->whereIn('providers.type', [2]);
                                  
                if (!empty($district_id)) {
                    $teamsquery->where('providers.district_id', $district_id);
                }
    $teams =   $teamsquery->groupby('providers.zone_id','providers.team_id')
                                   ->select(
                                        'providers.first_name',
                                        'providers.last_name',
                                        'providers.zone_id',
                                        'districts.name as district',
                                        'zonal_managers.Name as zone_name',
                                        'teams.name as team_name', 
                                        'providers.team_id',
                                        'user_requests.booking_id',
                                        DB::raw('COUNT(CASE WHEN  DATE(master_tickets.downdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as total_tickets'),
                                        DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Auto" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as completed_tickets_auto'),
                                        DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as completed_tickets_manual'),
                                        DB::raw('COUNT(CASE WHEN user_requests.status = "ONHOLD" AND DATE(user_requests.started_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as hold_tickets'),
                                        DB::raw('COUNT(CASE WHEN user_requests.status = "PICKEDUP" AND DATE(user_requests.started_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as pickup_tickets'),
                                       //DB::raw('COUNT(CASE WHEN user_requests.status = "INCOMING"  THEN user_requests.id END) as pending_tickets')
                                        DB::raw($pendingTicketsQuery),
                                        DB::raw($pendingTicketsMorethen24)

                                   )->get();

   

   //dd($teams);    

   return view('admin.reports.index', compact('teams','fromDate','toDate'));

  }

public function dashboard()
    { 
        try{
          
            $startDate = Carbon::create(2025, 1, 1)->toDateString();
           
            Session::put('user', Auth::User());
           
            /*$UserRequest = UserRequests::with('service_type')->with('provider')->with('payment')->findOrFail(83);

            echo "<pre>";
            print_r($UserRequest->toArray());exit;

            return view('emails.invoice',['Email' => $UserRequest]);*/

            $statuses = ['INCOMING', 'ONGOING', 'ONHOLD', 'SCHEDULED', 'COMPLETED', 'CANCELLED','PICKEDUP'];
            $downReasons = ['Electronics', 'Pole Change', 'others'];
            
                // Get ticket counts by status in a single query
             $ticketCounts = UserRequests::select('status', DB::raw('count(*) as total'))->whereDate('user_requests.created_at', '>=', $startDate)
            ->whereIn('status', $statuses)
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

            $cancel_rides = isset($ticketCounts['CANCELLED']) ? $ticketCounts['CANCELLED'] : 0;
            $ongoing_tickets = isset($ticketCounts['INCOMING']) ? $ticketCounts['INCOMING'] : 0;
            $onhold_tickets = isset($ticketCounts['ONHOLD']) ? $ticketCounts['ONHOLD'] : 0;
            $completed_tickets = isset($ticketCounts['COMPLETED']) ? $ticketCounts['COMPLETED'] : 0;
            $scheduled_rides = isset($ticketCounts['SCHEDULED']) ? $ticketCounts['SCHEDULED'] : 0;
            $master_tickets =  UserRequests::with('masterticket')->whereDate('user_requests.created_at', '>=', $startDate)->count();


           // Retrieve tickets grouped by downreason and status in one query
$ticketsByReason = UserRequests::select(
        DB::raw("LOWER(downreason) as downreason"),
        'status',
        DB::raw('COUNT(*) as total')
    )->whereDate('user_requests.created_at', '>=', $startDate)
    ->whereIn('status', $statuses)
    ->where(function ($query) use ($downReasons) {
        foreach ($downReasons as $reason) {
            $query->orWhere('downreason', 'like', "%$reason%");
        }
    })
    ->groupBy(DB::raw("LOWER(downreason)"), 'status')
    ->get()
    ->groupBy('downreason');

//return response()->json($ticketsByReason);

// Helper function to retrieve count values safely
$getCount = function ($reason, $status) use ($ticketsByReason) {
    $key = strtolower($reason);
    if (isset($ticketsByReason[$key])) {
        $filtered = $ticketsByReason[$key]->where('status', $status)->first();
        return $filtered ? $filtered->total : 0;
    }
    return 0;
};

//return response()->json($getCount);

// Building the response data
$data = [];
            $electronics = 0;
            $notstartedelectronics  = 0;
            $ongoingelectronics  = 0;
            $holdelectronics  = 0;
            $completedelectronics = 0;

            $poles = 0;
            $notstartedpoles   = 0;
            $ongoingpoles   = 0;
            $holdpoles   = 0;
            $completedpoles = 0;

            $others= 0;
            $notstartedothers = 0;
            $ongoingothers = 0;
            $holdothers = 0;
            $completedothers = 0;


foreach ($downReasons as $reason) {
    $reasonKey = strtolower($reason);
    $data[$reasonKey] = [
        'total'        => $ticketsByReason[$reasonKey]->sum('total'),
        'not_started'  => $getCount($reason, 'INCOMING'),
        'ongoing'      => $getCount($reason, 'PICKEDUP'),
        'on_hold'      => $getCount($reason, 'ONHOLD'),
        'completed'    => $getCount($reason, 'COMPLETED'),
    ];

     if ($reasonKey == 'electronics') {
        $electronics  = $data[$reasonKey]['total'];
        $notstartedelectronics  = $data[$reasonKey]['not_started'];
        $ongoingelectronics   = $data[$reasonKey]['ongoing'];
        $holdelectronics   = $data[$reasonKey]['on_hold'];
        $completedelectronics = $data[$reasonKey]['completed'];
    }

     if ($reasonKey == 'pole change') {
        $poles = $data[$reasonKey]['total'];
        $notstartedpoles   = $data[$reasonKey]['not_started'];
        $ongoingpoles   = $data[$reasonKey]['ongoing'];
        $holdpoles   = $data[$reasonKey]['on_hold'];
        $completedpoles = $data[$reasonKey]['completed'];
    }

     if ($reasonKey == 'others') {
        $others= $data[$reasonKey]['total'];
        $notstartedothers = $data[$reasonKey]['not_started'];
        $ongoingothers = $data[$reasonKey]['ongoing'];
        $holdothers = $data[$reasonKey]['on_hold'];
        $completedothers = $data[$reasonKey]['completed'];
    }


}

           
$powerCounts = UserRequests::with('masterticket')
    ->where('downreason', 'like', "%power%")->whereDate('user_requests.created_at', '>=', $startDate)
    ->selectRaw("
        COUNT(*) as total,
        SUM(CASE WHEN status = 'INCOMING' THEN 1 ELSE 0 END) as not_started,
        SUM(CASE WHEN status = 'PICKEDUP' THEN 1 ELSE 0 END) as ongoing,
        SUM(CASE WHEN status = 'ONHOLD' THEN 1 ELSE 0 END) as on_hold,
        SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) as completed
    ")
    ->first();         

//return response()->json($upsCounts);  

           $ups = $powerCounts->total;
           $notstartedups = $powerCounts->not_started;
           $ongoingups = $powerCounts->ongoing;
           $holdups = $powerCounts->on_hold;
           $completedups = $powerCounts->completed;

$fiberCounts = UserRequests::with('masterticket')
    ->where('downreason', 'like', "%fiber%")->whereDate('user_requests.created_at', '>=', $startDate)
    ->selectRaw("
        COUNT(*) as total,
        SUM(CASE WHEN status = 'INCOMING' THEN 1 ELSE 0 END) as not_started,
        SUM(CASE WHEN status = 'PICKEDUP' THEN 1 ELSE 0 END) as ongoing,
        SUM(CASE WHEN status = 'ONHOLD' THEN 1 ELSE 0 END) as on_hold,
        SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) as completed
    ")
    ->first();         

//return response()->json($upsCounts);  

           $fiber = $fiberCounts->total;
           $notstartedfiber  = $fiberCounts->not_started;
           $ongoingfiber  = $fiberCounts->ongoing;
           $holdfiber  = $fiberCounts->on_hold;
           $completedfiber = $fiberCounts->completed;



           $ticketCounts = UserRequests::with('masterticket')->whereDate('user_requests.created_at', '>=', $startDate)
    ->selectRaw("
        COUNT(CASE WHEN status = 'COMPLETED' AND DATE(finished_at) = ? THEN 1 END) as yesterdayclosed_tickets,
        COUNT(CASE WHEN status = 'COMPLETED' AND DATE(finished_at) = ? THEN 1 END) as todayclosed_tickets,
        COUNT(CASE WHEN status = 'PICKEDUP' THEN 1 END) as totalongoing_tickets,
        COUNT(CASE WHEN status = 'PICKEDUP' AND DATE(started_at) = ? THEN 1 END) as todayongoing_tickets,
        COUNT(CASE WHEN status = 'PICKEDUP' AND DATE(started_at) = ? THEN 1 END) as yesterdayongoing_tickets,
        COUNT(CASE WHEN status = 'INCOMING' THEN 1 END) as notstarted_tickets,
        COUNT(CASE WHEN status = 'ONHOLD' AND DATE(started_at) = ? THEN 1 END) as yesterdayonhold_tickets,
        COUNT(CASE WHEN status = 'ONHOLD' AND DATE(started_at) = ? THEN 1 END) as todayonhold_tickets
    ", [
        Carbon::yesterday()->toDateString(),
        Carbon::today()->toDateString(),
        Carbon::today()->toDateString(),
        Carbon::yesterday()->toDateString(),
        Carbon::yesterday()->toDateString(),
        Carbon::today()->toDateString()
    ])
    ->first();

// Extracting values into variables
        $yesterdayclosed_tickets = $ticketCounts->yesterdayclosed_tickets;
        $todayclosed_tickets = $ticketCounts->todayclosed_tickets;
        $totalongoing_tickets = $ticketCounts->totalongoing_tickets;
        $todayongoing_tickets = $ticketCounts->todayongoing_tickets;
        $yesterdayongoing_tickets = $ticketCounts->yesterdayongoing_tickets;
        $notstarted_tickets = $ticketCounts->notstarted_tickets;
        $yesterdayonhold_tickets = $ticketCounts->yesterdayonhold_tickets;
        $todayonhold_tickets = $ticketCounts->todayonhold_tickets;


          

            
            $provider = Provider::count();
            
            $teamcount= Provider::where('zone_id','!=',0)->groupBy('providers.team_id')->groupBy('providers.zone_id')->get()->count();
        
            $runningteams = UserRequests::with('masterticket')->join('providers','providers.id','=','user_requests.provider_id')->where('providers.zone_id','!=',0)->where('user_requests.status','=','PICKEDUP')->groupBy('providers.team_id')->groupBy('providers.zone_id')->get()->count();
            

            $completedteams = UserRequests::with('masterticket')->join('providers','providers.id','=','user_requests.provider_id')->where('providers.zone_id','!=',0)->where('user_requests.autoclose','=','Manual')->where('user_requests.status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->groupBy('providers.team_id')->groupBy('providers.zone_id')->get()->count();
           

            $holdteams = UserRequests::with('masterticket')->join('providers','providers.id','=','user_requests.provider_id')->where('providers.zone_id','!=',0)->where('user_requests.status','=','ONHOLD')->whereDate('user_requests.started_at','=',Carbon::today())->groupBy('providers.team_id')->groupBy('providers.zone_id')->get()->count();
          


            $notrunningteams = $teamcount - $runningteams;

            $todaynotRunningTeams = DB::table('providers')
        ->select('providers.zone_id', 'providers.team_id')
        ->where('providers.zone_id', '!=', 0)
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('user_requests')
                ->join('providers AS running_providers', 'running_providers.id', '=', 'user_requests.provider_id')
                ->whereRaw('running_providers.zone_id = providers.zone_id')
                ->whereRaw('running_providers.team_id = providers.team_id')
                ->where('user_requests.status', '=', 'PICKEDUP');
        })
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('user_requests')
                ->join('providers AS completed_providers', 'completed_providers.id', '=', 'user_requests.provider_id')
                ->whereRaw('completed_providers.zone_id = providers.zone_id')
                ->whereRaw('completed_providers.team_id = providers.team_id')
                ->where('user_requests.status', '=', 'COMPLETED')
                ->where('user_requests.autoclose', '=', 'Manual')
                ->whereDate('user_requests.finished_at', '=', DB::raw('CURDATE()'));
        })
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('user_requests')
                ->join('providers AS completed_providers', 'completed_providers.id', '=', 'user_requests.provider_id')
                ->whereRaw('completed_providers.zone_id = providers.zone_id')
                ->whereRaw('completed_providers.team_id = providers.team_id')
                ->where('user_requests.status', '=', 'ONHOLD')
                ->whereDate('user_requests.started_at', '=', DB::raw('CURDATE()'));
        })
        ->groupBy('providers.zone_id', 'providers.team_id')
        ->get();                
       
          $notworkedteamscount = $todaynotRunningTeams->count();


            return view('admin.dashboard',compact('holdteams','completedteams','notrunningteams','runningteams','teamcount','yesterdayongoing_tickets','totalongoing_tickets','yesterdayonhold_tickets','todayonhold_tickets','yesterdayclosed_tickets','todayclosed_tickets','todayongoing_tickets','notstarted_tickets','providers','fleet','provider','scheduled_rides','service','rides','user_cancelled','provider_cancelled','cancel_rides','revenue', 'wallet','master_tickets','completed_tickets','pending_tickets','ongoing_tickets','onhold_tickets','ups','electronics','fiber','poles','others','notstartedups','ongoingups','holdups','completedups','notstartedelectronics','ongoingelectronics','holdelectronics','completedelectronics','notstartedfiber','ongoingfiber','holdfiber','completedfiber','notstartedpoles','ongoingpoles','holdpoles','completedpoles','notstartedothers','ongoingothers','holdothers','completedothers','notworkedteamscount'));
        }
        catch(Exception $e){
            return redirect()->route('admin.user.index')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }

public function all_tickets_status()
{
    $masterQuery = UserRequests::with('masterticket');

    $master_tickets = $masterQuery->count();

    $clonemaster = clone $masterQuery;
    $ongoing_tickets = $clonemaster->where('status', 'PICKEDUP')->count();

    $clonemaster1 = clone $masterQuery;
    $onhold_tickets = $clonemaster1->where('status', 'ONHOLD')->count();

    $clonemaster2 = clone $masterQuery;
    $notstrated_tickets = $clonemaster2->where('status', 'INCOMING')->count();

    $clonemaster3 = clone $masterQuery;
    $completed_tickets = $clonemaster3->where('status', 'COMPLETED')->count();

    $clonemaster4 = clone $masterQuery;
    $completed_yesterday = $clonemaster4->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', Carbon::yesterday())
        ->count();
    $clonemaster5 = clone $masterQuery;
    $onhold_yesterday = $clonemaster5->where('status', 'ONHOLD')
        ->whereDate('user_requests.started_at','=',Carbon::yesterday())
        ->count();
    $clonemaster6 = clone $masterQuery;
    $notstrated_yesterday = $clonemaster6->where('status', 'INCOMING')
        ->whereDate('user_requests.created_at','=',Carbon::yesterday())
        ->count();
   $clonemaster7 = clone $masterQuery;
    $ongoing_yesterday = $clonemaster7->where('status', 'PICKEDUP')
        ->whereDate('user_requests.started_at','=',Carbon::yesterday())
        ->count();




    return response()->json([
        'status' => true,
        'message' => 'Tickets status fetched successfully.',
        'data' => [
            'total_tickets' => $master_tickets,
            'ongoing_tickets' => $ongoing_tickets,
            'ongoing_yesterday' => $ongoing_yesterday,
            'onhold_tickets' => $onhold_tickets,
            'onhold_yesterday' => $onhold_yesterday,
            'completed_tickets' => $completed_tickets,
            'completed_yesterday' => $completed_yesterday,
            'notstrated_tickets' => $notstrated_tickets,
            'notstrated_yesterday' => $notstrated_yesterday,

            

        ]
    ]);
}


}
