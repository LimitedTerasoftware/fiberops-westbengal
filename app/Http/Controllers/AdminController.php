<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Helpers\Helper;

use Auth;
use Setting;
use Exception;
use \Carbon\Carbon;
use App\Http\Controllers\SendPushNotification;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProviderResources\TripController;
use Illuminate\Support\Facades\Validator;
use App\OntUptime;

use App\User;
use App\Fleet;
use App\Admin;
use App\Provider;
use App\UserPayment;
use App\ServiceType;
use App\UserRequests;
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
use DB;
use Session;
use App\Block;
use App\Document;
use App\District;
use Illuminate\Support\Arr;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\OntUptimeImport;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware('demo', ['only' => [
                'settings_store', 
                'settings_payment_store',
                'profile_update',
                'password_update',
                'send_push',
            ]]);
        $this->perpage = Setting::get('per_page', '10');
    }


    /**
     * Dashboard.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    { 
        try{
           
            Session::put('user', Auth::User());
           
            /*$UserRequest = UserRequests::with('service_type')->with('provider')->with('payment')->findOrFail(83);

            echo "<pre>";
            print_r($UserRequest->toArray());exit;

            return view('emails.invoice',['Email' => $UserRequest]);*/

            $masterQuery = UserRequests::with('masterticket');
            $master_tickets =  $masterQuery->count();
            $clonemaster = clone $masterQuery;
            $ongoing_tickets = $clonemaster->Where('status','INCOMING')->count();
            $clonemaster1 = clone $masterQuery;
            $onhold_tickets = $clonemaster1->where('status','ONHOLD')->count();
            $clonemaster2 = clone $masterQuery;
            $scheduled_rides =$clonemaster2->where('status','SCHEDULED')->count();
            $clonemaster3 = clone $masterQuery;
            $completed_tickets = $clonemaster3->where('status','COMPLETED')->count();
            
            $uniqueLgdCount = DB::table('user_requests as ur')
                            ->leftJoin('master_tickets as mt', 'mt.ticketid', '=', 'ur.booking_id')
                            ->whereIn('ur.status', ['INCOMING', 'ONHOLD', 'SCHEDULED','PICKEDUP'])
                            ->whereNotNull('mt.lgd_code')->where('ur.autoclose','Auto')->distinct('mt.lgd_code')
                            ->count('mt.lgd_code');


         
            $totalGp = DB::table('gp_list')->count();

            
            $powerQuery = UserRequests::with('masterticket')->where('downreason', 'like', '%Power%');

            $ups = $powerQuery->count();
            $clonepower = clone $powerQuery;
            $notstartedups = $clonepower->where('status','=','INCOMING')->count();
            $clonepower1 = clone $powerQuery;
            $ongoingups = $clonepower1->where('status','=','PICKEDUP')->count();
            $clonepower2 = clone $powerQuery;
            $holdups = $clonepower2->where('status','=','ONHOLD')->count();
            $clonepower3 = clone $powerQuery;
            $completedups = $clonepower3->where('status','=','COMPLETED')->count();
            $clonepower4 = clone $powerQuery;
            $completedups_yesterday = $clonepower4->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            $clonepower5 = clone $powerQuery;
            $completedups_today = $clonepower5->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->count();

           
            $electronicsQuery = UserRequests::with('masterticket')->where('downreason', 'regexp', 'ONT|Software/Hardware');

            $electronics = $electronicsQuery->count();
            $cloneelectronics = clone $electronicsQuery;
            $notstartedelectronics  = $cloneelectronics->where('status','=','INCOMING')->count();
            $cloneelectronics1 = clone $electronicsQuery;
            $ongoingelectronics  = $cloneelectronics1->where('status','=','PICKEDUP')->count();
            $cloneelectronics2 = clone $electronicsQuery;
            $holdelectronics  = $cloneelectronics2->where('status','=','ONHOLD')->count();
            $cloneelectronics3 = clone $electronicsQuery;
            $completedelectronics = $cloneelectronics3->where('status','=','COMPLETED')->count();
            $cloneelectronics4 = clone $electronicsQuery;
            $completedelectronics_yesterday = $cloneelectronics4 ->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            $cloneelectronics5 = clone $electronicsQuery;
            $completedelectronics_today = $cloneelectronics5->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->count();



            $solorQuery = UserRequests::with('masterticket')->where('downreason', 'regexp', 'SOLAR|SPV|SLA');

            $solar = $solorQuery->count();
            $clonesolor= clone $solorQuery;
            $notstartedsolar = $clonesolor->where('status', 'INCOMING')->count();
            $clonesolor1= clone $solorQuery;
            $ongoingsolar = $clonesolor1->where('status', 'PICKEDUP')->count();
            $clonesolor2= clone $solorQuery;
            $holdsolar = $clonesolor2->where('status', 'ONHOLD')->count();
            $clonesolor3= clone $solorQuery;
            $completedsolar = $clonesolor3->where('status', 'COMPLETED')->count();
            $clonesolor4= clone $solorQuery;
            $completedsolar_yesterday = $clonesolor4->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            $clonesolor5= clone $solorQuery;
            $completedsolar_today = $clonesolor5->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->count();



            $oltQuery = UserRequests::with('masterticket')->where('downreason', 'regexp', 'OLT');

            $olt = $oltQuery->count();
            $cloneolt = clone $oltQuery;
            $notstartedolt = $cloneolt->where('status', 'INCOMING')->count();
            $cloneolt1 = clone $oltQuery;
            $ongoingolt = $cloneolt1->where('status', 'PICKEDUP')->count();
            $cloneolt2 = clone $oltQuery;
            $holdolt = $cloneolt2->where('status', 'ONHOLD')->count();
            $cloneolt3 = clone $oltQuery;
            $completedolt = $cloneolt3->where('status', 'COMPLETED')->count();
            $cloneolt4 = clone $oltQuery;
            $completedolt_yesterday = $cloneolt4->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            $cloneolt5 = clone $oltQuery;
            $completedolt_today = $cloneolt5->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->count();


            $ccuQuery = UserRequests::with('masterticket')->where('downreason', 'regexp', 'CCU|Battery');

            $ccu = $ccuQuery->count();
            $cloneccu = clone $ccuQuery;
            $notstartedccu = $cloneccu->where('status', 'INCOMING')->count();
            $cloneccu1 = clone $ccuQuery;
            $ongoingccu = $cloneccu1->where('status', 'PICKEDUP')->count();
            $cloneccu2 = clone $ccuQuery;
            $holdccu = $cloneccu2->where('status', 'ONHOLD')->count();
            $cloneccu3 = clone $ccuQuery;
            $completedccu = $cloneccu3->where('status', 'COMPLETED')->count();
            $cloneccu4 = clone $ccuQuery;
            $completedccu_yesterday = $cloneccu4->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            $cloneccu5 = clone $ccuQuery;
            $completedccu_today = $cloneccu5->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->count();


            $fiberQuery = UserRequests::with('masterticket')->where('downreason', 'regexp', 'FIBER');

            $fiber = $fiberQuery->count();
            $clonefiber = clone $fiberQuery;
            $notstartedfiber  = $clonefiber->where('status','=','INCOMING')->count();
            $clonefiber1 = clone $fiberQuery;
            $ongoingfiber  = $clonefiber1->where('status','=','PICKEDUP')->count();
            $clonefiber2 = clone $fiberQuery;
            $holdfiber  = $clonefiber2->where('status','=','ONHOLD')->count();
            $clonefiber3 = clone $fiberQuery;
            $completedfiber = $clonefiber3->where('status','=','COMPLETED')->count();
            $clonefiber4 = clone $fiberQuery;
            $completedfiber_yesterday = $clonefiber4->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            $clonefiber5 = clone $fiberQuery;
            $completedfiber_today = $clonefiber5->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->count();



  
            $otherQuery = UserRequests::with('masterticket')->where('downreason', 'regexp', 'Others|No Bin Type|GP Shifting|PP Extension|Other');
           
            $others= $otherQuery->count();
            $clonepower = clone $otherQuery;
            $notstartedothers = $clonepower->where('status','=','INCOMING')->count();
            $clonepower1 = clone $otherQuery;
            $ongoingothers = $clonepower1->where('status','=','PICKEDUP')->count();
            $clonepower2 = clone $otherQuery;
            $holdothers = $clonepower2->where('status','=','ONHOLD')->count();
            $clonepower3 = clone $otherQuery;
            $completedothers =  $clonepower3->where('status','=','COMPLETED')->count();
            $clonepower4 = clone $otherQuery;
            $completedothers_yesterday = $clonepower4 ->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            $clonepower5 = clone $otherQuery;
            $completedothers_today= $clonepower5->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->count();




            $service = ServiceType::count();
            $fleet = Fleet::count();

            $masterbase = UserRequests::with('masterticket');
            $clonemasterbase = clone $masterbase;
            $yesterdayclosed_tickets = $clonemasterbase->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            $clonemasterbase1 = clone $masterbase;
            $todayclosed_tickets = $clonemasterbase1->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->count();
            $clonemasterbase2 = clone $masterbase;
            $totalongoing_tickets = $clonemasterbase2->where('status','=','PICKEDUP')->count();
            $clonemasterbase3 = clone $masterbase;
            $todayongoing_tickets = $clonemasterbase3->where('status','=','PICKEDUP')->whereDate('user_requests.started_at','=',Carbon::today())->count();
            $clonemasterbase4 = clone $masterbase;
            $yesterdayongoing_tickets = $clonemasterbase4->where('status','=','PICKEDUP')->whereDate('user_requests.started_at','=',Carbon::Yesterday())->count();
            $clonemasterbase5 = clone $masterbase;
            $notstarted_tickets = $clonemasterbase5->where('status','=','INCOMING')->count();
            $clonemasterbase6 = clone $masterbase;
            $yesterdayonhold_tickets = $clonemasterbase6->where('status','=','ONHOLD')->whereDate('user_requests.started_at','=',Carbon::yesterday())->count();
            $clonemasterbase7 = clone $masterbase;
            $todayonhold_tickets = $clonemasterbase7->where('status','=','ONHOLD')->whereDate('user_requests.started_at','=',Carbon::today())->count();

            


            
            $provider = Provider::count();
            // $teamcount= Provider::where('zone_id','!=',0)->groupBy('providers.team_id')->groupBy('providers.zone_id')->get()->count();
            $teamcount = Provider::join('zonal_managers','providers.zone_id','=','zonal_managers.id')
                ->where('providers.zone_id', '!=', 0)
                ->where('providers.team_id', '!=', 0)
                ->whereNotNull('providers.team_id')
                ->where('providers.type',2)
                ->count();
            $runningteamcount = UserRequests::with('masterticket')->join('providers','providers.id','=','user_requests.provider_id')->where('providers.zone_id','!=',0)->where('user_requests.status','=','PICKEDUP')->groupBy('providers.team_id')->groupBy('providers.zone_id')->get();
            $runningteams = $runningteamcount->count();

            $completedteamcount = UserRequests::with('masterticket')->join('providers','providers.id','=','user_requests.provider_id')->where('providers.zone_id','!=',0)->where('user_requests.autoclose','=','Manual')->where('user_requests.status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->groupBy('providers.team_id')->groupBy('providers.zone_id')->get();
            $completedteams = $completedteamcount->count();

             $holdteamcount = UserRequests::with('masterticket')->join('providers','providers.id','=','user_requests.provider_id')->where('providers.zone_id','!=',0)->where('user_requests.status','=','ONHOLD')->whereDate('user_requests.started_at','=',Carbon::today())->groupBy('providers.team_id')->groupBy('providers.zone_id')->get();
            $holdteams = $holdteamcount->count();


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


            //dd("asdasdsad");
            return view('admin.dashboard',compact('holdteams','completedteams','notrunningteams','runningteams','teamcount','yesterdayongoing_tickets','totalongoing_tickets','yesterdayonhold_tickets','todayonhold_tickets','yesterdayclosed_tickets','todayclosed_tickets','todayongoing_tickets','notstarted_tickets','providers','fleet','provider','scheduled_rides','service','rides','user_cancelled','provider_cancelled','cancel_rides','revenue', 'wallet','master_tickets','completed_tickets','pending_tickets','ongoing_tickets','onhold_tickets','ups','electronics','fiber','poles','others','notstartedups','ongoingups','holdups','completedups','notstartedelectronics','ongoingelectronics','holdelectronics','completedelectronics','notstartedfiber','ongoingfiber','holdfiber','completedfiber','notstartedpoles','ongoingpoles','holdpoles','completedpoles','notstartedothers','ongoingothers','holdothers','completedothers','notworkedteamscount','solar','notstartedsolar','ongoingsolar','holdsolar',
                         'completedsolar','olt','notstartedolt','ongoingolt','holdolt','completedolt','ccu','notstartedccu','ongoingccu','holdccu','completedccu','completedothers_yesterday','completedfiber_yesterday','completedccu_yesterday','completedolt_yesterday','completedsolar_yesterday','completedelectronics_yesterday','completedups_yesterday',
                          'completedothers_today','completedfiber_today','completedccu_today','completedolt_today','completedsolar_today','completedelectronics_today','completedups_today','uniqueLgdCount','totalGp'));
        }
        catch(Exception $e){
            return redirect()->route('admin.user.index')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }
 
    public function DownGp(){
        $DownGpList = DB::table('user_requests as ur')
                            ->leftJoin('master_tickets as mt', 'mt.ticketid', '=', 'ur.booking_id')
                            ->whereIn('ur.status', ['INCOMING', 'ONHOLD', 'SCHEDULED'])
                            ->whereNotNull('mt.lgd_code') 
                            ->distinct('mt.lgd_code')
                            ->get();
                            dd($DownGpList);
    }
    /**
     * Ongoing History.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function ticketongoinghistory()
    {
        try{
            $totalongoing_tickets = UserRequests::with('masterticket')->where('status','=','PICKEDUP')->where('autoclose','!=','')->count();
            $todayongoing_tickets = UserRequests::with('masterticket')->where('status','=','PICKEDUP')->where('autoclose','!=','')->whereDate('user_requests.started_at','=',Carbon::today())->count();
            $yesterdayongoing_tickets = UserRequests::with('masterticket')->where('status','=','PICKEDUP')->where('autoclose','!=','')->whereDate('user_requests.started_at','=',Carbon::Yesterday())->count();
            $manual_tickets =  UserRequests::with('masterticket')->where('status','=','PICKEDUP')->where('autoclose','=','Manual')->count();
            $regular_tickets =  UserRequests::with('masterticket')->where('status','=','PICKEDUP')->where('autoclose','=','Auto')->count();
            $todaymanual_tickets = UserRequests::with('masterticket')->where('status','=','PICKEDUP')->where('autoclose','=','Manual')->whereDate('user_requests.started_at','=',Carbon::today())->count();
            $yesterdaymanual_tickets = UserRequests::with('masterticket')->where('status','=','PICKEDUP')->where('autoclose','=','Manual')->whereDate('user_requests.started_at','=',Carbon::Yesterday())->count();
            $todayregular_tickets = UserRequests::with('masterticket')->where('status','=','PICKEDUP')->where('autoclose','=','Auto')->whereDate('user_requests.started_at','=',Carbon::today())->count();
            $yesterdayregular_tickets = UserRequests::with('masterticket')->where('status','=','PICKEDUP')->where('autoclose','=','Auto')->whereDate('user_requests.started_at','=',Carbon::Yesterday())->count();
            
            $manual_manual_tickets = UserRequests::with('masterticket')->where('status','PICKEDUP')->where('default_autoclose','=','Manual')->where('autoclose','=','Manual')->count();
            $manual_auto_tickets = UserRequests::with('masterticket')->where('status','PICKEDUP')->where('default_autoclose','=','Manual')->where('autoclose','=','Auto')->count();
            $auto_manual_tickets = UserRequests::with('masterticket')->where('status','PICKEDUP')->where('default_autoclose','=','Auto')->where('autoclose','=','Manual')->count();
            $auto_auto_tickets = UserRequests::with('masterticket')->where('status','PICKEDUP')->where('default_autoclose','=','Auto')->where('autoclose','=','Auto')->count();
                
             
            $today_manual_manual_tickets = UserRequests::with('masterticket')->where('status','PICKEDUP')->where('default_autoclose','=','Manual')->where('autoclose','=','Manual')->whereDate('user_requests.started_at','=',Carbon::today())->count();
            $today_manual_auto_tickets = UserRequests::with('masterticket')->where('status','PICKEDUP')->where('default_autoclose','=','Manual')->where('autoclose','=','Auto')->whereDate('user_requests.started_at','=',Carbon::today())->count();
            $today_auto_manual_tickets = UserRequests::with('masterticket')->where('status','PICKEDUP')->where('default_autoclose','=','Auto')->where('autoclose','=','Manual')->whereDate('user_requests.started_at','=',Carbon::today())->count();
            $today_auto_auto_tickets = UserRequests::with('masterticket')->where('status','PICKEDUP')->where('default_autoclose','=','Auto')->where('autoclose','=','Auto')->whereDate('user_requests.started_at','=',Carbon::today())->count();
           
            $yesterday_manual_manual_tickets = UserRequests::with('masterticket')->where('status','PICKEDUP')->where('default_autoclose','=','Manual')->where('autoclose','=','Manual')->whereDate('user_requests.started_at','=',Carbon::yesterday())->count();
            $yesterday_manual_auto_tickets = UserRequests::with('masterticket')->where('status','PICKEDUP')->where('default_autoclose','=','Manual')->where('autoclose','=','Auto')->whereDate('user_requests.started_at','=',Carbon::yesterday())->count();
            $yesterday_auto_manual_tickets = UserRequests::with('masterticket')->where('status','PICKEDUP')->where('default_autoclose','=','Auto')->where('autoclose','=','Manual')->whereDate('user_requests.started_at','=',Carbon::yesterday())->count();
            $yesterday_auto_auto_tickets = UserRequests::with('masterticket')->where('status','PICKEDUP')->where('default_autoclose','=','Auto')->where('autoclose','=','Auto')->whereDate('user_requests.started_at','=',Carbon::yesterday())->count();
           

      
           return view('admin.ticketongoinghistory',compact('yesterday_auto_auto_tickets','yesterday_auto_manual_tickets','yesterday_manual_auto_tickets','yesterday_manual_manual_tickets','today_auto_auto_tickets','today_auto_manual_tickets','today_manual_auto_tickets','today_manual_manual_tickets','auto_auto_tickets','auto_manual_tickets','manual_auto_tickets','manual_manual_tickets','totalongoing_tickets','todayongoing_tickets','yesterdayongoing_tickets','manual_tickets','regular_tickets','todaymanual_tickets','yesterdaymanual_tickets','todayregular_tickets','yesterdayregular_tickets'));
        }
        catch(Exception $e){
            return redirect()->route('admin.user.index')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }


   
   /**
     * Ongoing History.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function ticketcompletedhistory()
    {
        try{
            $completed_tickets = UserRequests::with('masterticket')->where('status','COMPLETED')->count();
            $manual_manual_tickets = UserRequests::with('masterticket')->where('status','COMPLETED')->where('default_autoclose','=','Manual')->where('autoclose','=','Manual')->count();
            $manual_auto_tickets = UserRequests::with('masterticket')->where('status','COMPLETED')->where('default_autoclose','=','Manual')->where('autoclose','=','Auto')->count();
            $auto_manual_tickets = UserRequests::with('masterticket')->where('status','COMPLETED')->where('default_autoclose','=','Auto')->where('autoclose','=','Manual')->count();
            $auto_auto_tickets = UserRequests::with('masterticket')->where('status','COMPLETED')->where('default_autoclose','=','Auto')->where('autoclose','=','Auto')->count();
            $yesterdayclosed_tickets = UserRequests::with('masterticket')->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            $todayclosed_tickets = UserRequests::with('masterticket')->where('status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->count();
            
            $today_manual_manual_tickets = UserRequests::with('masterticket')->where('status','COMPLETED')->where('default_autoclose','=','Manual')->where('autoclose','=','Manual')->whereDate('user_requests.finished_at','=',Carbon::today())->count();
            $today_manual_auto_tickets = UserRequests::with('masterticket')->where('status','COMPLETED')->where('default_autoclose','=','Manual')->where('autoclose','=','Auto')->whereDate('user_requests.finished_at','=',Carbon::today())->count();
            $today_auto_manual_tickets = UserRequests::with('masterticket')->where('status','COMPLETED')->where('default_autoclose','=','Auto')->where('autoclose','=','Manual')->whereDate('user_requests.finished_at','=',Carbon::today())->count();
            $today_auto_auto_tickets = UserRequests::with('masterticket')->where('status','COMPLETED')->where('default_autoclose','=','Auto')->where('autoclose','=','Auto')->whereDate('user_requests.finished_at','=',Carbon::today())->count();
           
            $yesterday_manual_manual_tickets = UserRequests::with('masterticket')->where('status','COMPLETED')->where('default_autoclose','=','Manual')->where('autoclose','=','Manual')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            $yesterday_manual_auto_tickets = UserRequests::with('masterticket')->where('status','COMPLETED')->where('default_autoclose','=','Manual')->where('autoclose','=','Auto')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            $yesterday_auto_manual_tickets = UserRequests::with('masterticket')->where('status','COMPLETED')->where('default_autoclose','=','Auto')->where('autoclose','=','Manual')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            $yesterday_auto_auto_tickets = UserRequests::with('masterticket')->where('status','COMPLETED')->where('default_autoclose','=','Auto')->where('autoclose','=','Auto')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
           

            $todayongoing_tickets = UserRequests::with('masterticket')->where('status','=','PICKEDUP')->where('autoclose','!=','')->whereDate('user_requests.started_at','=',Carbon::today())->count();
            $todaymanualclosed_tickets = UserRequests::with('masterticket')->where('status','=','COMPLETED')->where('autoclose','=','Manual')->whereDate('user_requests.finished_at','=',Carbon::today())->count();
            $todayregularclosed_tickets = UserRequests::with('masterticket')->where('status','=','COMPLETED')->where('autoclose','=','Auto')->whereDate('user_requests.finished_at','=',Carbon::today())->count();
            $yesterdaymanualclosed_tickets = UserRequests::with('masterticket')->where('status','=','COMPLETED')->where('autoclose','=','Manual')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            $yesterdayregularclosed_tickets = UserRequests::with('masterticket')->where('status','=','COMPLETED')->where('autoclose','=','Auto')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
            
           return view('admin.ticketcompletedhistory',compact('yesterday_auto_auto_tickets','yesterday_auto_manual_tickets','yesterday_manual_auto_tickets','yesterday_manual_manual_tickets','today_auto_auto_tickets','today_auto_manual_tickets','today_manual_auto_tickets','today_manual_manual_tickets','auto_auto_tickets','auto_manual_tickets','manual_auto_tickets','manual_manual_tickets','yesterdaymanualclosed_tickets','yesterdayregularclosed_tickets','todaymanualclosed_tickets','todayregularclosed_tickets','completed_tickets','yesterdayclosed_tickets','todayclosed_tickets','manual_tickets'));
        }
        catch(Exception $e){
            return redirect()->route('admin.user.index')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }




    /**
     * Heat Map.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function heatmap()
    {
        try{
           $rides = DB::table('user_requests')->join('users','user_requests.user_id','=','users.id')->orderBy('user_requests.id','desc')->get();
            $providers = Provider::take(100)->orderBy('rating','desc')->get();
            return view('admin.heatmap',compact('providers','rides'));
        }
        catch(Exception $e){
            return redirect()->route('admin.user.index')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }


    	/**
     * Attendance.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function attendance(Request $request)
    {
		
        try{
             if(!empty($request->district_id) && !empty($request->from_date) && !empty($request->to_date)){
            $providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','providers.version','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
            ->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')->where('providers.district_id', $request->district_id )->whereDate('attendance.created_at','>=',$request->from_date)->whereDate('attendance.created_at','<=',$request->to_date)->orderBy('attendance.created_at','desc')->get();
              } else if(!empty($request->from_date) && !empty($request->to_date)){
            $providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','providers.version','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
            ->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')->whereDate('attendance.created_at','>=',$request->from_date)->whereDate('attendance.created_at','<=',$request->to_date)->orderBy('attendance.created_at','desc')->get();
              }
              else{
              $providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','providers.version','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
            ->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')->where('attendance.created_at','>=',Carbon::today())->orderBy('attendance.created_at','desc')->get();
              }

			$districts= DB::table('districts')->get();
                        $blocks= DB::table('blocks')->get();
	
            return view('admin.attendance',compact('providers','districts','blocks'));
        }
        catch(Exception $e){
            return redirect()->route('admin.attendance')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }
	

     	/**
     * Attendance.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function userattendance(Request $request)
    {

          try{
                $totalusers = DB::table('providers')->get()->count();
                //print_r($totalusers);exit;

                return view('admin.userattendance',compact('providers','districts'));
        }
        catch(Exception $e){
            return redirect()->route('admin.userattendance')->with('flash_error','Something Went Wrong with Dashboard!');
        }

    }

   	/**
     * Attendance.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function todayattendancereport(Request $request)
    {		
        try{
         $totalusers = DB::table('providers')->get()->count();
         $loggedinusers=DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
            ->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')->where('attendance.created_at','>=',Carbon::today())->orderBy('attendance.created_at','desc')->count();
         $notloggedinusers = 0;
         return view('admin.todayattendancereport',compact('totalusers','loggedinusers','notloggedinusers'));
        }
        catch(Exception $e){
            return redirect()->route('admin.todayattendancereport')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }





	/**
     * Attendance.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function attendancereport(Request $request)
    {
		
		
        try{

            //dd($request->from_date);
			
             if(!empty($request->district_id) && !empty($request->from_date) && !empty($request->to_date)){
             $providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('count(attendance.created_at) as present '),DB::raw('group_concat(attendance.created_at) as presentdates'),DB::raw('group_concat(date_format(attendance.created_at,"%Y-%m-%d")) as origindate'))
            ->join('attendance','providers.id','=','attendance.provider_id')
			->join('districts','providers.district_id','=','districts.id')
			->where('providers.district_id', $request->district_id )->whereDate('attendance.created_at','>=',$request->from_date)->whereDate('attendance.created_at','<=',$request->to_date)->groupBy('providers.id')->orderBy('attendance.created_at','desc')->get();
            //dd($providers);
              } else if(!empty($request->district_id)){
                $providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('count(attendance.created_at) as present '),DB::raw('group_concat(attendance.created_at) as presentdates'),DB::raw('group_concat(date_format(attendance.created_at,"%Y-%m-%d")) as origindate'))
            ->join('attendance','providers.id','=','attendance.provider_id')
            ->join('districts','providers.district_id','=','districts.id')
            ->where('providers.district_id', $request->district_id )
            ->groupBy('providers.id')
            ->orderBy('attendance.created_at','desc')->get();
            // echo $providers;
              } else if(!empty($request->from_date) && !empty($request->to_date)){
                $providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('count(attendance.created_at) as present '),DB::raw('group_concat(attendance.created_at) as presentdates'),DB::raw('group_concat(date_format(attendance.created_at,"%Y-%m-%d")) as origindate'))
            ->join('attendance','providers.id','=','attendance.provider_id')
            ->join('districts','providers.district_id','=','districts.id')->whereDate('attendance.created_at','>=',$request->from_date)->whereDate('attendance.created_at','<=',$request->to_date)->groupBy('providers.id')->orderBy('attendance.created_at','desc')->get();
              }
              else{
				  
				  
              $providers =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('count(attendance.created_at) as present '),DB::raw('group_concat(attendance.created_at) as presentdates'),DB::raw('group_concat(date_format(attendance.created_at,"%Y-%m-%d")) as origindate'))
            ->join('attendance','providers.id','=','attendance.provider_id')
			->join('districts','providers.district_id','=','districts.id')
			->whereDate('attendance.created_at','>=', DB::raw('DATE_FORMAT(NOW(),"%Y/%m/01")'))
			->whereDate('attendance.created_at','<=', DB::raw('DATE_FORMAT(last_day(NOW()), "%Y/%m/%d")'))
            ->groupBy('providers.id')
			->orderBy('attendance.created_at','desc')->get();

             //dd($providers);
              }
			  

			 //dd($providers);

			$districts= DB::table('districts')->get();
			
            return view('admin.attendancereport1',compact('providers','districts'));
        }
        catch(Exception $e){
            return redirect()->route('admin.reportattendance')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }




     /**
     * Display a listing of the occ resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function occ(Request $request)
    {   

                     
        if(!empty($request->page) && $request->page=='all'){
            $users =  DB::table('users')
            ->select('users.id','users.first_name','users.last_name','users.email','users.mobile','users.rating','users.district_id','users.created_at','users.type','districts.name' )
            ->join('districts', 'users.district_id', '=', 'districts.id')->where('type',2)->orderBy('id' , 'asc')->get();
            return response()->json(array('success' => true, 'data'=>$users));
        }
        else{

             $users =  DB::table('users')
            ->select('users.id','users.first_name','users.last_name','users.email','users.mobile','users.rating','users.district_id','users.created_at','users.type','districts.name' )
            ->join('districts', 'users.district_id', '=', 'districts.id')->where('type',2)->orderBy('created_at' , 'desc')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($users);
            return view('admin.users.index', compact('users','pagination'));
        }

        
    }


    /**
     * Display a listing of the occ resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function frt(Request $request)
    {   

                    
        if(!empty($request->page) && $request->page=='all'){
            $providers = Provider::where('type',2)->orderBy('id' , 'asc')->get();
            return response()->json(array('success' => true, 'data'=>$providers));
        }
        else{

            $providers = Provider::where('type',2)->orderBy('created_at' , 'desc')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($providers);
            return view('admin.providers.index', compact('providers','pagination'));
        }

        
    }

    /**
     * Display a listing of the occ resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function zonalincharge(Request $request)
    {   

                    
        if(!empty($request->page) && $request->page=='all'){
            $providers = Provider::where('type',3)->orderBy('id' , 'asc')->get();
            return response()->json(array('success' => true, 'data'=>$providers));
        }
        else{

            $providers = Provider::where('type',3)->orderBy('created_at' , 'desc')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($providers);
            return view('admin.providers.index', compact('providers','pagination'));
        }

        
    }


   /**
     * Display a listing of the occ resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function districtincharge(Request $request)
    {   

                    
        if(!empty($request->page) && $request->page=='all'){
            $providers = Provider::where('type',4)->orderBy('id' , 'asc')->get();
            return response()->json(array('success' => true, 'data'=>$providers));
        }
        else{

            $providers = Provider::where('type',4)->orderBy('created_at' , 'desc')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($providers);
            return view('admin.providers.index', compact('providers','pagination'));
        }

        
    }


    /**
     * Map of all Users and Drivers.
     *
     * @return \Illuminate\Http\Response
     */
    public function map_index(Request $request)
    {
        $districts= DB::table('districts')->get();
        $district_id = $request->district_id;
		
        //dd($district_id);
        return view('admin.map.index',compact('districts','district_id'));
    }
	
	 /**
     * Map of all Users and Drivers.
     *
     * @return \Illuminate\Http\Response
     */
    public function trackattendance(Request $request)
    {
        $districts= DB::table('districts')->get();
        $blocks= DB::table('blocks')->get(); 
		$providers= DB::table('providers')->get();
        $district_id = $request->district_id;
        $block_id = $request->block_id;
		$provider_id = $request->provider_id;
		$from_date = $request->from_date;
		$to_date = $request->to_date;
        //dd($district_id);
        return view('admin.attendancemap.index',compact('districts','district_id','providers','provider_id','from_date','to_date','blocks','block_id'));
    }


    /**
     * Map of all  gps.
     *
     * @return \Illuminate\Http\Response
     */
    public function tracklocations(Request $request)
    {
        $districts= DB::table('districts')->get();
		$providers= DB::table('providers')->get();
        $district_id = $request->district_id;
		$provider_id = $request->provider_id;
		$from_date = $request->from_date;
		$to_date = $request->to_date;
        //dd($district_id);
        return view('admin.allmaps.index',compact('districts','district_id','providers','provider_id','from_date','to_date'));
    }


    /**
     * Map of all Users and Drivers.
     *
     * @return \Illuminate\Http\Response
     */
    public function currentlocation($id)
    {

          $Providers = Provider::where('id', '=', $id)
                    ->with('service')
                    ->first();

        return view('admin.map.current',compact('Providers'));
    }

    /**
     * Map of all Users and Drivers.
     *
     * @return \Illuminate\Http\Response
     */
    public function map_ajax(Request $request)
    {
        try {
           if(!empty($request->district_id)){
            $Providers = Provider::where('latitude', '!=', 0)
                    ->where('longitude', '!=', 0)
                    ->where('district_id', '=', $request->district_id)
                     ->where('block_id', '=', $request->block_id)

                    ->with('service')
                    ->get();
             }else{
              $Providers = Provider::where('latitude', '!=', 0)
                    ->where('longitude', '!=', 0)
                    ->with('service')
                    ->get();
             }
             //dd($Providers);
            // $Users = User::where('latitude', '!=', 0)
            //         ->where('longitude', '!=', 0)
            //         ->get();

            for ($i=0; $i < sizeof($Providers); $i++) { 
                $Providers[$i]->status = 'user';
            }

            // $All = $Users->merge($Providers);
            $All =$Providers;

            return $All;

        } catch (Exception $e) {
            return [];
        }
    }
	
	
	
    /**
     * Map of all Users and Drivers.
     *
     * @return \Illuminate\Http\Response
     */
    public function trackmap_ajax(Request $request)
    {
        try {
            //dd($request->all());
           if(!empty($request->district_id)){
					$Providers =DB::table('providers')
					->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.status as astatus','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
					->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')
                    ->where('providers.district_id', '=', $request->district_id)
					->where('providers.id', '=', $request->provider_id)
					->where('attendance.created_at','>=',$request->from_date)
					->where('attendance.created_at','<=',$request->to_date)
                    ->get();
             }else{
                $Providers = DB::table('providers')
    ->select(
        'providers.first_name',
        'providers.last_name',
        'providers.mobile',
        'providers.latitude',
        'providers.longitude',
        'providers.district_id',
        'providers.type',
        'districts.name as district_name',
        'attendance.status',
        'attendance.address',
        'attendance.offaddress',
        'attendance.created_at',
        'attendance.updated_at',
        DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration')
    )
    ->leftJoin('attendance', 'providers.id', '=', 'attendance.provider_id')
    ->leftJoin('districts', 'providers.district_id', '=', 'districts.id')
    ->get();   

             }
             //dd($Providers);
            // $Users = User::where('latitude', '!=', 0)
            //         ->where('longitude', '!=', 0)
            //         ->get();

            for ($i=0; $i < sizeof($Providers); $i++) { 
                $Providers[$i]->status = 'user';
            }

            // $All = $Users->merge($Providers);
            $All =$Providers;

            return $All;

        } catch (Exception $e) {
            return [];
        }
    }



     /**
     * Map of all Users and Drivers.
     *
     * @return \Illuminate\Http\Response
     */
    public function alltrackmap_ajax(Request $request)
    {
        try {
            //dd($request->all());
           if(!empty($request->district_id)){
					$Providers =DB::table('providers')
					->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.status as astatus','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
					->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')
                    ->where('providers.district_id', '=', $request->district_id)
					->where('providers.id', '=', $request->provider_id)
					->where('attendance.created_at','>=',$request->from_date)
					->where('attendance.created_at','<=',$request->to_date)
                    ->get();
             }else{
               $Providers =DB::table('gp_list')
            ->select('gp_list.gp_name','gp_list.provider','gp_list.latitude','gp_list.longitude','gp_list.district_id','gp_list.lgd_code','districts.name as district_name','blocks.name as block_name','gp_list.status','gp_list.contact_no')
            ->join('districts','gp_list.district_id','=','districts.id')->join('blocks','blocks.id','=','gp_list.block_id')->get();

             }
             //dd($Providers);
            // $Users = User::where('latitude', '!=', 0)
            //         ->where('longitude', '!=', 0)
            //         ->get();

            for ($i=0; $i < sizeof($Providers); $i++) { 
                $Providers[$i]->status = 'location';
            }

            // $All = $Users->merge($Providers);
            $All =$Providers;

            return $All;

        } catch (Exception $e) {
            return [];
        }
    }

	
	
	
	

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        return view('admin.settings.application');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function settings_store(Request $request)
    {
        $this->validate($request,[
                'site_title' => 'required',
                'site_icon' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
                'site_logo' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
            ]);

        if($request->hasFile('site_icon')) {
            $site_icon = Helper::upload_picture($request->file('site_icon'));
            Setting::set('site_icon', $site_icon);
        }

        if($request->hasFile('site_logo')) {
            $site_logo = Helper::upload_picture($request->file('site_logo'));
            Setting::set('site_logo', $site_logo);
        }

        if($request->hasFile('site_email_logo')) {
            $site_email_logo = Helper::upload_picture($request->file('site_email_logo'));
            Setting::set('site_email_logo', $site_email_logo);
        }

        Setting::set('site_title', $request->site_title);
        Setting::set('store_link_android_user', $request->store_link_android_user);
        Setting::set('store_link_android_provider', $request->store_link_android_provider);
        Setting::set('store_link_ios_user', $request->store_link_ios_user);
        Setting::set('store_link_ios_provider', $request->store_link_ios_provider);
        Setting::set('store_facebook_link', $request->store_facebook_link);
        Setting::set('store_twitter_link', $request->store_twitter_link);
        Setting::set('provider_select_timeout', $request->provider_select_timeout);
        Setting::set('provider_search_radius', $request->provider_search_radius);
        Setting::set('sos_number', $request->sos_number);
        Setting::set('contact_number', $request->contact_number);
        Setting::set('contact_email', $request->contact_email);
        Setting::set('site_copyright', $request->site_copyright);        
        Setting::set('social_login', $request->social_login);
        Setting::set('map_key', $request->map_key);
        Setting::set('fb_app_version', $request->fb_app_version);
        Setting::set('fb_app_id', $request->fb_app_id);
        Setting::set('fb_app_secret', $request->fb_app_secret);
        Setting::set('manual_request', $request->manual_request == 'on' ? 1 : 0 );
        Setting::set('broadcast_request', $request->broadcast_request == 'on' ? 1 : 0 );
        Setting::set('track_distance', $request->track_distance == 'on' ? 1 : 0 );
        Setting::set('distance', $request->distance);
        Setting::save();
        
        return back()->with('flash_success','Settings Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function settings_payment()
    {
        return view('admin.payment.settings');
    }

    /**
     * Save payment related settings.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function settings_payment_store(Request $request)
    {

        $this->validate($request, [
                'CARD' => 'in:on',
                'CASH' => 'in:on',
                'stripe_secret_key' => 'required_if:CARD,on|max:255',
                'stripe_publishable_key' => 'required_if:CARD,on|max:255',                
                'daily_target' => 'required|integer|min:0',
                'tax_percentage' => 'required|numeric|min:0|max:100',
                'surge_percentage' => 'required|numeric|min:0|max:100',
                'commission_percentage' => 'required|numeric|min:0|max:100',
                'fleet_commission_percentage' => 'sometimes|nullable|numeric|min:0|max:100',
                'surge_trigger' => 'required|integer|min:0',
                'currency' => 'required'
            ]);

        if($request->has('CARD')==0 && $request->has('CASH')==0){
            return back()->with('flash_error','Atleast one payment mode must be enable.');
        }

        Setting::set('CARD', $request->has('CARD') ? 1 : 0 );
        Setting::set('CASH', $request->has('CASH') ? 1 : 0 );
        Setting::set('stripe_secret_key', $request->stripe_secret_key);
        Setting::set('stripe_publishable_key', $request->stripe_publishable_key);
        //Setting::set('stripe_oauth_url', $request->stripe_oauth_url);
        Setting::set('daily_target', $request->daily_target);
        Setting::set('tax_percentage', $request->tax_percentage);
        Setting::set('surge_percentage', $request->surge_percentage);
        Setting::set('commission_percentage', $request->commission_percentage);
        Setting::set('provider_commission_percentage', 0);
        Setting::set('fleet_commission_percentage', $request->has('fleet_commission_percentage')?$request->fleet_commission_percentage : 0);
        Setting::set('surge_trigger', $request->surge_trigger);
        Setting::set('currency', $request->currency);
        Setting::set('booking_prefix', $request->booking_prefix);
        Setting::save();

        return back()->with('flash_success','Settings Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        return view('admin.account.profile');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function profile_update(Request $request)
    {
        //print_r($request->all()); exit;
        $this->validate($request,[
            'name' => 'required|max:255',
            'email' => 'required|max:255|email|unique:admins,email,'.Auth::guard('admin')->user()->id.',id',
            'picture' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
        ]);

        try{
            $admin = Auth::guard('admin')->user();
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->language = $request->language;
            
            if($request->hasFile('picture')){
                $admin->picture = $request->picture->store('admin/profile');             }
            $admin->save();

            Session::put('user', Auth::User());

            return redirect()->back()->with('flash_success','Profile Updated');
        }

        catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function password()
    {
        return view('admin.account.change-password');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function password_update(Request $request)
    {

        $this->validate($request,[
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        try {

           $Admin = Admin::find(Auth::guard('admin')->user()->id);

            if(password_verify($request->old_password, $Admin->password))
            {
                $Admin->password = bcrypt($request->password);
                $Admin->save();

                return redirect()->back()->with('flash_success','Password Updated');
            }
        } catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function payment()
    {
        try {
             $payments = UserRequests::where('paid', 1)
                    ->has('user')
                    ->has('provider')
                    ->has('payment')
                    ->orderBy('user_requests.created_at','desc')
                    ->paginate($this->perpage);

             $pagination=(new Helper)->formatPagination($payments);       
            
            return view('admin.payment.payment-history', compact('payments','pagination'));
        } catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function help()
    {
        try {
            $str = file_get_contents('http://appoets.com/help.json');
            $Data = json_decode($str, true);
            return view('admin.help', compact('Data'));
        } catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }

    /**
     * User Rating.
     *
     * @return \Illuminate\Http\Response
     */
    public function user_review()
    {
        try {
            $Reviews = UserRequestRating::where('user_id', '!=', 0)->with('user', 'provider')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($Reviews);
            return view('admin.review.user_review',compact('Reviews','pagination'));

        } catch(Exception $e) {
            return redirect()->route('admin.setting')->with('flash_error','Something Went Wrong!');
        }
    }

    /**
     * Provider Rating.
     *
     * @return \Illuminate\Http\Response
     */
    public function provider_review()
    {
        try {
            $Reviews = UserRequestRating::where('provider_id','!=',0)->with('user','provider')->paginate($this->perpage);
            $pagination=(new Helper)->formatPagination($Reviews);
            return view('admin.review.provider_review',compact('Reviews','pagination'));
        } catch(Exception $e) {
            return redirect()->route('admin.setting')->with('flash_error','Something Went Wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProviderService
     * @return \Illuminate\Http\Response
     */
    public function destory_provider_service($id){
        try {
            ProviderService::find($id)->delete();
            return back()->with('message', 'Service deleted successfully');
        } catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }

    /**
     * Testing page for push notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function push_index()
    {

        $data = \PushNotification::app('AndroidUser')
            ->to('c2CHAz2meU8:APA91bHrRIMEu9gioDLIpo5ez-9exHgRlL5tlFfyXZ28me0dkQIUDdGzHXCI6mTyI9gZh4IEnPXSqrekavC22KcQYxbo5ql0Uahfh4mfKEL0ziAsDQwnv4ySQPDNnW5Wfcuc1C4GYipp')
            ->send('Hello World, i`m a push message');
        dd($data);
    }

    /**
     * Testing page for push notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function push_store(Request $request)
    {
        try {
            ProviderService::find($id)->delete();
            return back()->with('message', 'Service deleted successfully');
        } catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }

    /**
     * privacy.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */

    public function cmspages(){
        return view('admin.pages.static');
    }

    /**
     * pages.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function pages(Request $request){
        $this->validate($request, [
                'types' => 'required|not_in:select',
            ]);

        Setting::set($request->types, $request->content);
        Setting::save();

        return back()->with('flash_success', 'Content Updated!');
    }

    public function pagesearch($request){
        $value = Setting::get($request);        
        return $value;        
    }

    /**
     * account statements.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement($type = '', $request = null){

        try{

            $page = trans('admin.include.overall_ride_statments');
            $listname = trans('admin.include.overall_ride_earnings');
            if($type == 'individual'){
                $page = trans('admin.include.provider_statement');
                $listname = trans('admin.include.provider_earnings');
            }elseif($type == 'today'){
                $page = trans('admin.include.today_statement').' - '. date('d M Y');
                $listname = trans('admin.include.today_earnings');
            }elseif($type == 'monthly'){
                $page = trans('admin.include.monthly_statement').' - '. date('F');
                $listname = trans('admin.include.monthly_earnings');
            }elseif($type == 'yearly'){
                $page = trans('admin.include.yearly_statement').' - '. date('Y');
                $listname = trans('admin.include.yearly_earnings');
            }elseif($type == 'range'){
                $page = trans('admin.include.statement_from').' '.Carbon::createFromFormat('Y-m-d', $request->from_date)->format('d M Y').'  '.trans('admin.include.statement_to').' '.Carbon::createFromFormat('Y-m-d', $request->to_date)->format('d M Y');
            }
            
            $rides = UserRequests::with('payment')->orderBy('id','desc');

            $cancel_rides = UserRequests::where('status','CANCELLED');
            $revenue = UserRequestPayment::select(\DB::raw(
                           'SUM(ROUND(fixed) + ROUND(distance)) as overall, SUM(ROUND(commision)) as commission' 
                       ));

            if($type == 'today'){

                $rides->where('created_at', '>=', Carbon::today());
                $cancel_rides->where('created_at', '>=', Carbon::today());
                $revenue->where('created_at', '>=', Carbon::today());

            }elseif($type == 'monthly'){

                $rides->where('created_at', '>=', Carbon::now()->month);
                $cancel_rides->where('created_at', '>=', Carbon::now()->month);
                $revenue->where('created_at', '>=', Carbon::now()->month);

            }elseif($type == 'yearly'){

                $rides->where('created_at', '>=', Carbon::now()->year);
                $cancel_rides->where('created_at', '>=', Carbon::now()->year);
                $revenue->where('created_at', '>=', Carbon::now()->year);

            }elseif ($type == 'range') {                
                if($request->from_date == $request->to_date) {
                    $rides->whereDate('created_at', date('Y-m-d', strtotime($request->from_date)));
                    $cancel_rides->whereDate('created_at', date('Y-m-d', strtotime($request->from_date)));
                    $revenue->whereDate('created_at', date('Y-m-d', strtotime($request->from_date)));
                } else {
                    $rides->whereBetween('created_at',[Carbon::createFromFormat('Y-m-d', $request->from_date),Carbon::createFromFormat('Y-m-d', $request->to_date)]);
                    $cancel_rides->whereBetween('created_at',[Carbon::createFromFormat('Y-m-d', $request->from_date),Carbon::createFromFormat('Y-m-d', $request->to_date)]);
                    $revenue->whereBetween('created_at',[Carbon::createFromFormat('Y-m-d', $request->from_date),Carbon::createFromFormat('Y-m-d', $request->to_date)]);
                }
            }

            $rides = $rides->paginate($this->perpage);
            if ($type == 'range'){
                $path='range?from_date='.$request->from_date.'&to_date='.$request->to_date;
                $rides->setPath($path);
            }
            $pagination=(new Helper)->formatPagination($rides);
            $cancel_rides = $cancel_rides->count();
            $revenue = $revenue->get();

            return view('admin.providers.statement', compact('rides','cancel_rides','revenue','pagination'))
                    ->with('page',$page)->with('listname',$listname);

        } catch (Exception $e) {
            return back()->with('flash_error','Something Went Wrong!');
        }
    }


    /**
     * account statements today.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_today(){
        return $this->statement('today');
    }

    /**
     * account statements monthly.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_monthly(){
        return $this->statement('monthly');
    }

     /**
     * account statements monthly.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_yearly(){
        return $this->statement('yearly');
    }


    /**
     * account statements range.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_range(Request $request){
        return $this->statement('range', $request);
    }

    /**
     * account statements.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_provider(){

        try{

            $Providers = Provider::paginate($this->perpage);

            $pagination=(new Helper)->formatPagination($Providers);

            foreach($Providers as $index => $Provider){

                $Rides = UserRequests::where('provider_id',$Provider->id)
                            ->where('status','<>','CANCELLED')
                            ->get()->pluck('id');

                $Providers[$index]->rides_count = $Rides->count();

                $Providers[$index]->payment = UserRequestPayment::whereIn('request_id', $Rides)
                                ->select(\DB::raw(
                                   'SUM(ROUND(provider_pay)) as overall, SUM(ROUND(provider_commission)) as commission' 
                                ))->get();
            }

            return view('admin.providers.provider-statement', compact('Providers','pagination'))->with('page','Providers Statement');

        } catch (Exception $e) {
            return back()->with('flash_error','Something Went Wrong!');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function translation(){

        try{
            return view('admin.translation');
        }

        catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function push(){

        try{
            $Pushes = CustomPush::orderBy('id','desc')->get();
            return view('admin.push',compact('Pushes'));
        }

        catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }


    /**
     * pages.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function send_push(Request $request){


        $this->validate($request, [
                'send_to' => 'required|in:ALL,USERS,PROVIDERS',
                'user_condition' => ['required_if:send_to,USERS','in:ACTIVE,LOCATION,RIDES,AMOUNT'],
                'provider_condition' => ['required_if:send_to,PROVIDERS','in:ACTIVE,LOCATION,RIDES,AMOUNT'],
                'user_active' => ['required_if:user_condition,ACTIVE','in:HOUR,WEEK,MONTH'],
                'user_rides' => 'required_if:user_condition,RIDES',
                'user_location' => 'required_if:user_condition,LOCATION',
                'user_amount' => 'required_if:user_condition,AMOUNT',
                'provider_active' => ['required_if:provider_condition,ACTIVE','in:HOUR,WEEK,MONTH'],
                'provider_rides' => 'required_if:provider_condition,RIDES',
                'provider_location' => 'required_if:provider_condition,LOCATION',
                'provider_amount' => 'required_if:provider_condition,AMOUNT',
                'message' => 'required|max:100',
            ]);

        try{

            $CustomPush = new CustomPush;
            $CustomPush->send_to = $request->send_to;
            $CustomPush->message = $request->message;

            if($request->send_to == 'USERS'){

                $CustomPush->condition = $request->user_condition;

                if($request->user_condition == 'ACTIVE'){
                    $CustomPush->condition_data = $request->user_active;
                }elseif($request->user_condition == 'LOCATION'){
                    $CustomPush->condition_data = $request->user_location;
                }elseif($request->user_condition == 'RIDES'){
                    $CustomPush->condition_data = $request->user_rides;
                }elseif($request->user_condition == 'AMOUNT'){
                    $CustomPush->condition_data = $request->user_amount;
                }

            }elseif($request->send_to == 'PROVIDERS'){

                $CustomPush->condition = $request->provider_condition;

                if($request->provider_condition == 'ACTIVE'){
                    $CustomPush->condition_data = $request->provider_active;
                }elseif($request->provider_condition == 'LOCATION'){
                    $CustomPush->condition_data = $request->provider_location;
                }elseif($request->provider_condition == 'RIDES'){
                    $CustomPush->condition_data = $request->provider_rides;
                }elseif($request->provider_condition == 'AMOUNT'){
                    $CustomPush->condition_data = $request->provider_amount;
                }
            }

            if($request->has('schedule_date') && $request->has('schedule_time')){
                $CustomPush->schedule_at = date("Y-m-d H:i:s",strtotime("$request->schedule_date $request->schedule_time"));
            }

            $CustomPush->save();

            if($CustomPush->schedule_at == ''){
                $this->SendCustomPush($CustomPush->id);
            }

            return back()->with('flash_success', 'Message Sent to all '.$request->segment);
        }

        catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }


    public function SendCustomPush($CustomPush){

        try{

            \Log::notice("Starting Custom Push");

            $Push = CustomPush::findOrFail($CustomPush);

            if($Push->send_to == 'USERS'){

                $Users = [];

                if($Push->condition == 'ACTIVE'){

                    if($Push->condition_data == 'HOUR'){

                        $Users = User::whereHas('trips', function($query) {
                            $query->where('created_at','>=',Carbon::now()->subHour());
                        })->get();
                        
                    }elseif($Push->condition_data == 'WEEK'){

                        $Users = User::whereHas('trips', function($query){
                            $query->where('created_at','>=',Carbon::now()->subWeek());
                        })->get();

                    }elseif($Push->condition_data == 'MONTH'){

                        $Users = User::whereHas('trips', function($query){
                            $query->where('created_at','>=',Carbon::now()->subMonth());
                        })->get();

                    }

                }elseif($Push->condition == 'RIDES'){

                    $Users = User::whereHas('trips', function($query) use ($Push){
                                $query->where('status','COMPLETED');
                                $query->groupBy('id');
                                $query->havingRaw('COUNT(*) >= '.$Push->condition_data);
                            })->get();


                }elseif($Push->condition == 'LOCATION'){

                    $Location = explode(',', $Push->condition_data);

                    $distance = Setting::get('provider_search_radius', '10');
                    $latitude = $Location[0];
                    $longitude = $Location[1];

                    $Users = User::whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                            ->get();

                }

                
               \Log::notice($users);

                foreach ($Users as $key => $user) {
                    (new SendPushNotification)->sendPushToUser($user->id, $Push->message);
                }

            }elseif($Push->send_to == 'PROVIDERS'){


                $Providers = [];

                if($Push->condition == 'ACTIVE'){

                    if($Push->condition_data == 'HOUR'){

                        $Providers = Provider::whereHas('trips', function($query){
                            $query->where('created_at','>=',Carbon::now()->subHour());
                        })->get();
                        
                    }elseif($Push->condition_data == 'WEEK'){

                        $Providers = Provider::whereHas('trips', function($query){
                            $query->where('created_at','>=',Carbon::now()->subWeek());
                        })->get();

                    }elseif($Push->condition_data == 'MONTH'){

                        $Providers = Provider::whereHas('trips', function($query){
                            $query->where('created_at','>=',Carbon::now()->subMonth());
                        })->get();

                    }

                }elseif($Push->condition == 'RIDES'){

                    $Providers = Provider::whereHas('trips', function($query) use ($Push){
                               $query->where('status','COMPLETED');
                                $query->groupBy('id');
                                $query->havingRaw('COUNT(*) >= '.$Push->condition_data);
                            })->get();

                }elseif($Push->condition == 'LOCATION'){

                    $Location = explode(',', $Push->condition_data);

                    $distance = Setting::get('provider_search_radius', '10');
                    $latitude = $Location[0];
                    $longitude = $Location[1];

                    $Providers = Provider::whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                            ->get();

                }


                foreach ($Providers as $key => $provider) {
                    (new SendPushNotification)->sendPushToProvider($provider->id, $Push->message);
                }

            }elseif($Push->send_to == 'ALL'){

                $Users = User::all();
                foreach ($Users as $key => $user) {
                    (new SendPushNotification)->sendPushToUser($user->id, $Push->message);
                }

                $Providers = Provider::all();
                foreach ($Providers as $key => $provider) {
                    (new SendPushNotification)->sendPushToProvider($provider->id, $Push->message);
                }

            }
        }

        catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
    }


    public function transactions(){

        try{
            $wallet_transation = AdminWallet::orderBy('id','desc')
                                ->paginate(Setting::get('per_page', '10'));
            
            $pagination=(new Helper)->formatPagination($wallet_transation);   
            
            $wallet_balance=AdminWallet::sum('amount');

            return view('admin.wallet.wallet_transation',compact('wallet_transation','pagination','wallet_balance'));
            
        }

        catch (Exception $e) {
             return back()->with('flash_error',$e->getMessage());
        }
    }

    public function transferlist(Request $request){

        $croute= Route::currentRouteName();
        
        if($croute=='admin.fleettransfer')
            $type='fleet';
        else
            $type='provider';

        $pendinglist = WalletRequests::where('request_from',$type)->where('status',0);
        if($croute=='admin.fleettransfer')
            $pendinglist = $pendinglist->with('fleet');
        else
            $pendinglist = $pendinglist->with('provider');

        $pendinglist = $pendinglist->get();
               
        return view('admin.wallet.transfer',compact('pendinglist','type'));
    }

    public function approve(Request $request, $id){

        if($request->send_by == "online") {
            $response=(new PaymentController)->send_money($request, $id);
        }
        else{
            (new TripController)->settlements($id);
            $response['success']='Amount successfully send';
        }    

        if(!empty($response['error']))
            $result['flash_error']=$response['error'];
        if(!empty($response['success']))
            $result['flash_success']=$response['success'];
       
        return redirect()->back()->with($result);
        
    }

    public function requestcancel(Request $request)
    {
        
        $cancel=(new TripController())->requestcancel($request);
        $response=json_decode($cancel->getContent(),true);
        
        if(!empty($response['error']))
            $result['flash_error']=$response['error'];
        if(!empty($response['success']))
            $result['flash_success']=$response['success'];

        return redirect()->back()->with($result);
    }

    public function transfercreate(Request $request, $id){
        $type=$id;
        return view('admin.wallet.create',compact('type'));        
    }

    public function search(Request $request){

        $results=array();

        $term =  $request->input('stext');       
        $sflag =  $request->input('sflag');

        if($sflag==1)
            $queries = Provider::where('first_name', 'LIKE', $term.'%')->take(5)->get();
        else
            $queries = Fleet::where('name', 'LIKE', $term.'%')->take(5)->get();

        foreach ($queries as $query)
        {
            $results[]=$query;
        }    

        return response()->json(array('success' => true, 'data'=>$results));

    }

    public function transferstore(Request $request){

        try{
            if($request->stype==1)
                $type='provider';
            else
                $type='fleet';

            $nextid=Helper::generate_request_id($type); 

            $amountRequest=new WalletRequests;
            $amountRequest->alias_id=$nextid;
            $amountRequest->request_from=$type;          
            $amountRequest->from_id=$request->from_id;
            $amountRequest->type=$request->type;
            $amountRequest->send_by=$request->by;
            $amountRequest->amount=$request->amount;

            $amountRequest->save();

            //create the settlement transactions
            (new TripController)->settlements($amountRequest->id);            

            return back()->with('flash_success','Settlement processed successfully');
            
        }

        catch (Exception $e) {
             return back()->with('flash_error',$e->getMessage());
        }      
    }

    public function download(Request $request, $id)
    {

        $documents = ProviderDocument::where('provider_id', $id)->get();

        if(!empty($documents->toArray())){

           
            $Provider = Provider::findOrFail($id);

            // Define Dir Folder
            $public_dir=public_path();

            // Zip File Name
            $zipFileName = $Provider->first_name.'.zip';

            // Create ZipArchive Obj
            $zip = new ZipArchive;

            if ($zip->open($public_dir . '/storage/' . $zipFileName, ZipArchive::CREATE) === TRUE) {
                // Add File in ZipArchive
                foreach($documents as $file){
                    $zip->addFile($public_dir.'/storage/'.$file->url);
                }
                
                // Close ZipArchive     
                $zip->close();
            }
            // Set Header
            $headers = array(
                'Content-Type' => 'application/octet-stream',
            );

            $filetopath=$public_dir.'/storage/'.$zipFileName;
            
            // Create Download Response
            if(file_exists($filetopath)){
                return response()->download($filetopath,$zipFileName,$headers)->deleteFileAfterSend(true);
            }            

            return redirect()
                ->route('admin.provider.document.index', $id)
                ->with('flash_success', 'documents downloaded successfully.');   
        }
        
        return redirect()
                ->route('admin.provider.document.index', $id)
                ->with('flash_error', 'failed to downloaded documents.');      
        
    } 
    public function ongoing(Request $request,$id){

        try{  

            $userrequest = UserRequests::with('provider')->findOrFail($id); 

            if($request->ajax()) {
                return $userrequest;
            }else{
                return view('admin.request.ongoing', compact('userrequest')); 
            }
            
        } catch (Exception $e) {  
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }
    public function addtickets(){

         try{  


         } catch (Exception $e) {  
            return back()->with('flash_error', trans('admin.something_wrong'));
        }


    }
   
   public function tickets(Request $request){
		Log::info("======MAIN====");
		
		if($request->ajax()){
			Log::info("====AJAX======");
		  if(!empty($request->district_id) && empty($request->block_id) && empty($request->ticket_id)){

                 $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
                 ->where('master_tickets.district',$request->district_id)
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                 ->get();

              } else if (!empty($request->district_id) && !empty($request->block_id) && empty($request->ticket_id)){

                $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
                 ->where('master_tickets.district',$request->district_id)
                 ->where('master_tickets.mandal',$request->block_id)
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                 ->get();

             } else if (empty($request->district_id) && empty($request->block_id) && !empty($request->ticket_id)){

                 $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
                 ->where('master_tickets.ticketid',$request->ticket_id)
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                 ->get();

              }	else {
				  $tickets = DB::table('master_tickets')
                  ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                ->get();

                }
			   return response()->json(array('success' => true, 'data'=>$tickets));
		}

         

             if(!empty($request->district_id) && empty($request->block_id) && empty($request->ticket_id)){

                 $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
                 ->where('master_tickets.district',$request->district_id)
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                 ->get();

                $districts= DB::table('districts')->get();
                 $blocks= DB::table('blocks')->get();
             return view('admin.searchtickets', compact('tickets','districts','blocks'));

                //->paginate($this->perpage);
                // $pagination=(new Helper)->formatPagination($tickets);


              } else if (!empty($request->district_id) && !empty($request->block_id) && empty($request->ticket_id)){

                $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
                 ->where('master_tickets.district',$request->district_id)
                 ->where('master_tickets.mandal',$request->block_id)
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                 ->get();
             
               $districts= DB::table('districts')->get();
                 $blocks= DB::table('blocks')->get();
             return view('admin.searchtickets', compact('tickets','districts','blocks'));


               //->paginate($this->perpage);
                 //$pagination=(new Helper)->formatPagination($tickets);

             } else if (empty($request->district_id) && empty($request->block_id) && !empty($request->ticket_id)){

                $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
                 ->where('master_tickets.ticketid',$request->ticket_id)
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                 ->get();

                 $districts= DB::table('districts')->get();
                 $blocks= DB::table('blocks')->get();
             return view('admin.searchtickets', compact('tickets','districts','blocks'));

                //->paginate($this->perpage);
                 //$pagination=(new Helper)->formatPagination($tickets);


              }
			  
		 try{
			Log::info("==========");
			 Log::info($this->perpage);
             $tickets = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
                 ->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                ->paginate($this->perpage);
                 $pagination=(new Helper)->formatPagination($tickets);
                  $districts= DB::table('districts')->get();
                 $blocks= DB::table('blocks')->get();
             return view('admin.tickets', compact('tickets','pagination','districts','blocks'));        
        } catch (Exception $e) {  
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }


   public function sendpushnotifications1(){
               $user_id = "78";
               $message = "Hi Hello all";
              (new SendPushNotification)->sendPushToProvider($user_id, $message);
     }


   function sendpushnotifications() {

        $fcm_token="d9ozrOxafRY:APA91bETpL1tQVYyHC7HCMfP4cgRW0DSuj1UUJr6sIIjNXLnvMd_ooqcweF0CU8XqBJBSZ0Kjfv28eK6sNc8Q59WiEGyG5NWtifbdutp4xg2iMYJHo3LNzorg5AgKTGHSHld_fSOYEk3";
        $title="TeraOdisha";
        $message="Hi, You have recieved request form odisha fleet.Please open the app and accept the request!... ";
        $id="78";  
        $push_notification_key = "AAAAsJwKEBE:APA91bGhn0cYMrBr0A4so53XYTYQxe7BOexk_UytxVT8CZQAkyw-No53yy2N49SYQxk4nIgVR7MQcucH7Qz8AW8IGCmlrTBV9Wb2eY5rCsLxNNqItgjQksHgHftX6jXkEenMTCf6Qi5s";    
        $url = "https://fcm.googleapis.com/fcm/send";            
        $header = array("authorization: key=" . $push_notification_key . "",
            "content-type: application/json"
        );    

        $postdata = '{
            "to" : "' . $fcm_token . '",
                "notification" : {
                    "title":"' . $title . '",
                    "text" : "' . $message . '"
                },
            "data" : {
                "id" : "'.$id.'",
                "title":"' . $title . '",
                "description" : "' . $message . '",
                "text" : "' . $message . '",
                "is_read": 0
              }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);    
        // close handle to release resources
        curl_close($ch);

        return $result;
    }

    public function getajaxblocks($id)
   {
    $blocks = \App\Block::where('district_id',$id)->select('name','id')->get();
    return view('admin.providers.ajaxblock', compact('blocks'));
  }

    public function getajaxblockids($id)
   {
    $blocks = \App\Block::where('district_id',$id)->select('name','id')->get();
    return view('admin.providers.ajaxblockid', compact('blocks'));
  }

  
   public function getajaxproviderblocks($id)
   {
    $blocks = \App\Block::where('district_id',$id)->select('name','id')->get();
    return view('admin.providers.ajaxproviderblock', compact('blocks'));
  }


  public function getajaxgps($id){

    $gpslist= DB::table('gp_list')->where('block_id',$id)->select('gp_name','id','latitude','longitude')->get();
    return view('admin.providers.ajaxgps', compact('gpslist'));

   }

public function getajaxblockwiseproviders($id)
{
   $providers= DB::table("providers")
                 ->where("block_id",$id)
                 ->select("first_name","last_name","id")->get(); 
    return view('admin.providers.ajaxproviders', compact('providers'));
}


  public function getSearchblocklist($id)
{
     $blocks= DB::table("blocks")
                 ->where("district_id",$id)
                 ->pluck("name","id");
      //$blocks=DB::table('master_tickets')->select(DB::raw('DISTINCT mandal'))->where("district",$id);
      //$blocks= DB::select("mandal,district")->from('master_tickets')->where("district",$id) ->groupBy('mandal');
       //print_r( $blocks);exit;
     return response()->json($blocks);    
}

 public function getSearchproviderlist($id)
{
     $providers= DB::table("providers")
                 ->where("district_id",$id)
                 ->pluck("first_name","id");      
     return response()->json($providers);    
}


public function deleteticket($id)
{

      try {
            DB::table('master_tickets')->where('ticketid', $id)->delete();
            DB::table('user_requests')->where('booking_id', $id)->delete();
            return back()->with('message', 'Ticket deleted successfully');
        } catch (Exception $e) {
             return back()->with('flash_error','Something Went Wrong!');
        }
}


  public function searchproviders(Request $request){
      $search_name = $request->search_name;
      $type = $request->type;
      
      if(!empty($search_name) && $search_name !=''){
        $AllProviders = Provider::where('first_name', 'like', "%$search_name%")->orWhere('last_name', 'like', "%$search_name%")->with('service','accepted','cancelled')
                    ->orderBy('id', 'DESC');
      } else {
        $AllProviders = Provider::with('service','accepted','cancelled')
                    ->orderBy('id', 'asc');
      }

      // Paginating the generated results
      if(!empty($type) && $type !=''){
        if(request()->has('fleet')){
            $providers = $AllProviders->where('type',$type)->where('fleet',$request->fleet)->paginate($this->perpage);
        }else{
            $providers = $AllProviders->where('type',$type)->paginate($this->perpage);
        }
      } else {
        if(request()->has('fleet')){
            $providers = $AllProviders->where('fleet',$request->fleet)->paginate($this->perpage);
           $providers->appends([
    'search_name' => $search_name,
    'type' => $type,
    'fleet' => $request->fleet // only if you are using fleet filter
]);
        }else{
            $providers = $AllProviders->paginate($this->perpage);
            $providers->appends([
    'search_name' => $search_name,
    'type' => $type
]); 
        }
      }

        $total_documents=Document::count();        
        
        $pagination=(new Helper)->formatPagination($providers);
        
        $url = $providers->url($providers->currentPage());

        $request->session()->put('providerpage', $url);
                    
        return view('admin.providers.providerpagination', compact('providers','pagination','total_documents')); 
 }

/**
 * Show the form for creating a new resource.
 *
 * @return \Illuminate\Http\Response
 */
public function addNewTicket()
{
    $districts = District::get();
    $blocks= Block::get();
    $zonals= DB::table('zonal_managers')->get();
    $gplist = $users = DB::table('gp_list')->get();
    return view('admin.tickets.create',compact('districts','blocks','gplist','zonals'));
}

public function storeTicket(Request $request)
{
    $this->validate($request, [
        'ticketid' => 'required',
        'district' => 'required',
        'gpname' => 'required',
        'mandal' => 'required',
        'gpname' => 'required',
        'lat' => 'required',
        'log' => 'required',
        'downdate' => 'required',
        'downtime' => 'required',        
    ]);

    try{     

        $data = array(
            'district' => $request->district, 
            'mandal' => $request->mandal, 
            'gpname' => $request->gpname,
            'lat' => $request->lat,
            'log' => $request->log, 
            'downtime' => date('h:i:s a',strtotime($request->downtime)),
            'downdate' => date('Y-m-d',strtotime($request->downdate)),
            'downreason' => $request->downreason,
            'downreasonindetailed' => $request->downreasonindetailed,
            'ticketid' => $request->ticketid,
            'ticketinsertstage' =>1
        );
        //DB::table('master_tickets')->insert($data);
        if(DB::table('master_tickets')->insert($data)){

            $latitude = $request->lat;
            $longitude = $request->log;
            $destinationgeocodeFromLatLong = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=".Setting::get('map_key');
            $json = curl($destinationgeocodeFromLatLong);

              $desdetails = json_decode($json, TRUE);
	      $desstatus = $desdetails['status'];
		  //dd($status);
	      $daddress = ($desstatus=="OK")?$desdetails['results'][1]['formatted_address']:'';


            $UserRequest = new UserRequests;
            $UserRequest->booking_id = $request->ticketid;
            $UserRequest->gpname = $request->gpname;

            $UserRequest->downreason = $request->downreasonindetailed;
            $UserRequest->downreasonindetailed = $request->downreasonindetailed;

            $UserRequest->user_id =45;

            // $UserRequest->current_provider_id = $getproviderdetails->id;
            // $UserRequest->provider_id = $getproviderdetails->id;

            $UserRequest->service_type_id = 2;
            $UserRequest->rental_hours = 10;
            $UserRequest->payment_mode = 'CASH';
            $UserRequest->promocode_id = 0;
            
            $UserRequest->status = 'SEARCHING';
            $UserRequest->s_address =" ";
            $UserRequest->d_address =$daddress;

            $UserRequest->s_latitude = " ";
            $UserRequest->s_longitude = " ";

            $UserRequest->d_latitude = $request->lat;
            $UserRequest->d_longitude = $request->log;
            $UserRequest->distance = 1;
            $UserRequest->autoclose = 'Manual';
            $UserRequest->default_autoclose = 'Manual';
  
            $UserRequest->unit = Setting::get('distance', 'Kms');
           
            $UserRequest->use_wallet = 0;

            if(Setting::get('track_distance', 0) == 1){
                $UserRequest->is_track = "YES";
            }

            $UserRequest->otp = mt_rand(1000 , 9999);

            $UserRequest->assigned_at = Carbon::now();
            // $UserRequest->route_key = $route_key;

            $UserRequest->save();
        }

        return redirect()
                ->route('admin.tickets')
                ->with('flash_success', 'New Ticket Details Saved Successfully');
    } catch (Exception $e) {  
        return back()->with('flash_error', 'Issue while saving the ticket details');
    }
}

public function editTicket($id)
{
    try {
        $districts = District::get();
        $blocks = Block::get();
        $ticket = MasterTicket::findOrFail($id);
        $services = ServiceType::get();
        return view('admin.tickets.edit',compact('ticket','districts','blocks','services'));
    } catch (ModelNotFoundException $e) {
        return $e;
    }
}

public function updateTicket(Request $request, $id)
{
    $this->validate($request, [
        'district' => 'required',
        'mandal' => 'required',
        'gpname' => 'required',
        'lat' => 'required',
        'log' => 'required',
        'downdate' => 'required',
        'downtime' => 'required',        
    ]);
    
    $ticket_id = $request->ticket_id;

    try {
        $updateinput = array(
                  'district' => $request->district,
                  'mandal' => $request->mandal,
                  'gpname' => $request->gpname,
                  'lat' => $request->lat,
                  'log' => $request->log,
                  'downdate' => date('Y-m-d',strtotime($request->downdate)),
                  'downtime' => date('h:i:s a',strtotime($request->downtime)),
                  'downreason'=> !empty($request->downreason2)?$request->downreason2:$request->downreason,
                  'downreasonindetailed'=>$request->downreasonindetailed
                );
        DB::table('master_tickets')
            ->where('id',$id)
            ->update($updateinput);


         $updateusertable  = array(
                  'downreason'=> !empty($request->downreason2)?$request->downreason2:$request->downreason,
                  'downreasonindetailed'=>$request->downreasonindetailed
                );

         DB::table('user_requests')
            ->where('booking_id',$ticket_id)
            ->update($updateusertable);


        return redirect()
                ->route('admin.tickets1')
                ->with('flash_success', 'Ticket Details Updated Successfully');
    } 
    catch (ModelNotFoundException $e) {
        return back()->with('flash_error', 'Issue while updating the ticket details');
    }

}

public function import_data(Request $request)
{
     $this->validate($request, [
        'import_file' => 'required|file', 
    ]);

    $response = (object)array();
    $lgd_code = '';
    try{
        $response = (object)array();
        
        $file = $request->file('import_file');
        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize(); //Get size of uploaded file in bytes

            // Validating file size and extension.
            $valid_extension = array("csv"); //Only want csv and excel files
            $maxFileSize = 10097152; // Uploaded file size limit is 10mb
            if (in_array(strtolower($extension), $valid_extension)) {
                if ($fileSize > $maxFileSize){
                    $response->error = "File size should be less than 10 MB";
                    $response->status = 404;
                    return response()->json($response, 404);
                }
            } else {
                $response->error = "Invalid file extension. Accepts only .csv";
                $response->status = 404;
                return response()->json($response, 404);
            }

            $importData_arr = array(); // Read through the file and store the contents as an array
            
            // Read the contents of the file
            $i = 0;
            $j = 0;
            $k = 0;
            $ready_to_import = 0;
            $ignored = 0;
            $creates = 0;
            $updates = 0;
            $gp_type = '';
            $file=fopen($tempPath, 'r');
            while (($filedata = fgetcsv($file)) !== FALSE) {
                $num = count($filedata);
                $data = array(); 
                // Skip first row
                if ($i == 0) {
                    if (str_contains(strtolower($filedata[1]), 'up')) {
                        $gp_type = 'up';
                    } else if (str_contains(strtolower($filedata[1]), 'down')) {
                        $gp_type = 'down';
                    } else {
                        fclose($file);
                        $response->error = "Unable to identify Up or Down gps";
                        $response->status = 404;
                        return response()->json($response, 404);
                        exit();
                    }
                    $i++;
                    continue;
                }
                if(empty($filedata[0]) && empty($filedata[1]) && empty($filedata[2]))
                    break;
                
                $check_lgd_code = DB::table('gp_list')->where('lgd_code', $filedata[0])->first();
                
                if($check_lgd_code){                    
                    if($gp_type == 'down'){
                        $check_ticket_exisits = DB::table('master_tickets')
                                                ->leftJoin('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                                                ->where('lat', $check_lgd_code->latitude)
                                                ->where('log', $check_lgd_code->longitude)
                                                ->where('lgd_code', $check_lgd_code->lgd_code)
                                                ->whereIn('user_requests.status', ['SEARCHING','INCOMING','PICKEDUP','CANCELLED','ONHOLD'])
                                                ->orderBy('master_tickets.id', 'DESC')
                                                ->first();
                        ($check_ticket_exisits)? $updates++: $creates++;
                    } else if($gp_type == 'up'){
                        $updates++;
                    }

                    $ready_to_import++;
                } else {                    
                    $ignored++;
                    $lgd_code .= (!empty($lgd_code))?(" , ".$filedata[0]):$filedata[0];
                }

                $i++;
            }

            fclose($file);

            $response->ready_to_import = $ready_to_import;
            $response->ignored = $ignored;
            $response->creates = $creates;
            $response->updates = $updates;
            $response->lgd_codes = $lgd_code;
            $response->status       = 200;
            return response()->json($response, 200);

        } else {
            $response->error = "Unable to find the file.";
            $response->status = 404;
            return response()->json($response, 404);
        }
        
    } catch (Exception $e) {  
        $response = (object)array();
        $response->error = $e->getMessage();
        $response->line = $e->getLine();
        $response->code = $lgd_code;
        $response->status = 500;
        return response()->json($response, 500);
    }
}



public function process_old(Request $request)
{

    ini_set('max_execution_time', 300);
     $this->validate($request, [
        'import_file' => 'required|file', 
    ]);

    $lgd_code = '';
    try{
        
        $file = $request->file('import_file');
        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize(); //Get size of uploaded file in bytes

            // Validating file size and extension.
            $valid_extension = array("csv"); //Only want csv and excel files
            $maxFileSize = 10097152; // Uploaded file size limit is 10mb
            if (in_array(strtolower($extension), $valid_extension)) {
                if ($fileSize > $maxFileSize){
                    return back()->with('flash_error', 'File size should be less than 10 MB');
                }
            } else {
                return back()->with('flash_error', 'Invalid file extension. Accepts only .csv');
            }

            $importData_arr = array(); // Read through the file and store the contents as an array
            
            // Auto Close Tickets
            $inserted_ids = array();
            $existing_ticket_ids = DB::table('master_tickets')
                                        ->leftJoin('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                                        ->whereIn('user_requests.status', ['SEARCHING', 'INCOMING', 'PICKEDUP','ONHOLD'])
                                        ->pluck('master_tickets.ticketid')->toArray();

            // Read the contents of the file
            $i = 0;
            $ready_to_import = 0;
            $ignored = 0;
            $creates = 0;
            $updates = 0;
            $gp_type = '';
            $file=fopen($tempPath, 'r');
            $isRegular = false;

            while (($filedata = fgetcsv($file)) !== FALSE) {
                $num = count($filedata);
                $data = array(); 
                // Skip first row
                if ($i == 0) {                    
                    if (str_contains(strtolower($filedata[1]), 'up')) {
                        $gp_type = 'up';
                    } else if (str_contains(strtolower($filedata[1]), 'down')) {
                        $gp_type = 'down';
                    } else {
                        fclose($file);
                        return back()->with('flash_error', 'Unable to identify Up or Down time');
                        exit();
                    }
                    $i++;
                    continue;
                }
                if(empty($filedata[0]) && empty($filedata[1]) && empty($filedata[2]))
                    break;
                
                $check_lgd_code = DB::table('gp_list')->where('lgd_code', $filedata[0])->first();
                   if(isset($filedata[3]) && $filedata[3] == 'Auto') {
                        $isRegular = true;
                   }

                if($check_lgd_code){
                    $distict = District::findOrFail($check_lgd_code->district_id);
                    $block = Block::findOrFail($check_lgd_code->block_id);
                    $tkt_id = 'TK25'.mt_rand(100000, 9999999);
                    $data['ticketid'] = $tkt_id;
                    $data['district'] = $distict->name;
                    $data['mandal'] = $block->name;
                    $data['gpname'] = $check_lgd_code->gp_name;
                    $data['lgd_code'] = $check_lgd_code->lgd_code;
                    $data['subsategory'] = "";
                    //$formats = ['m-d-y H:i', 'm-d-y H:i:s', 'm-d-Y H:i', 'm-d-Y H:i:s', 'm/d/y H:i', 'm/d/y H:i:s', 'm/d/Y H:i', 'm/d/Y H:i:s'];
                    
                    $formats = [
    'd-m-Y H:i', 'd-m-Y H:i:s',
    'd/m/Y H:i', 'd/m/Y H:i:s',
    'Y-m-d H:i', 'Y-m-d H:i:s',
    'Y/m/d H:i', 'Y/m/d H:i:s',
    'm-d-Y H:i', 'm-d-Y H:i:s',
    'm/d/Y H:i', 'm/d/Y H:i:s',
    'd-m-y H:i', 'd-m-y H:i:s',
    'd/m/y H:i', 'd/m/y H:i:s'
];

                    $formattedDate = '';
                    $formattedTime = '';
                    foreach($formats as $format){
                        try{
                            $date = Carbon::createFromFormat($format, $filedata[1]);
                            if ($date instanceof Carbon) {
                                if($formattedDate == ''){
                                  $formattedDate = $date->format('Y-m-d');
                                  $formattedTime = $date->format('h:i:s a');
                                }
                            }
                        } catch (Exception $e) { }
                    }

                   if(isset($filedata[3]) && $filedata[3] == 'Manual'){

                      $today = Carbon::now();
                      $todaydate= $today->format('Y-m-d');
                      $todaytime= $today->format('h:i:s a');
                      if($gp_type == 'down'){
                        $data['downdate'] = $todaydate;
                        $data['downtime'] = $todaytime;
                      } else if($gp_type == 'up'){
                        $data['up_date'] = $todaydate;
                        $data['up_time'] = $todaytime;
                        $data['status'] = 1;
                     }
                    } else {

                      if($gp_type == 'down'){
                        $data['downdate'] = $formattedDate;
                        $data['downtime'] = $formattedTime;
                      } else if($gp_type == 'up'){
                        $data['up_date'] = $formattedDate;
                        $data['up_time'] = $formattedTime;
                        $data['status'] = 1;
                      }

                    }
                    $data['downreason'] = $filedata[2];
                    $data['downreasonindetailed'] = $filedata[2];
                    $data['lat'] = $check_lgd_code->latitude;
                    $data['log'] = $check_lgd_code->longitude;
                    $data['ticketinsertstage'] = 1;

                    // $is_created = MasterTicket::updateOrCreate($data);

                    if($gp_type == 'down'){
                        $masterticket = DB::table('master_tickets')
                                            ->leftJoin('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                                            ->where('lat', $check_lgd_code->latitude)
                                            ->where('log', $check_lgd_code->longitude)
                                            ->where('lgd_code', $check_lgd_code->lgd_code)
                                            ->whereIn('user_requests.status', ['SEARCHING','INCOMING','PICKEDUP','CANCELLED','ONHOLD'])
                                            ->orderBy('master_tickets.id', 'DESC')
                                            ->first();
                    } else if($gp_type == 'up'){
                        $masterticket = DB::table('master_tickets')
                                            ->where('lat', $check_lgd_code->latitude)
                                            ->where('log', $check_lgd_code->longitude)
                                            ->where('lgd_code', $check_lgd_code->lgd_code)
                                            ->whereNull('up_date')
                                            ->whereNull('up_time')
                                            ->orderBy('master_tickets.id', 'DESC')
                                            ->first();
                    } 
                    if ($masterticket !== null) {
                        $data['ticketid'] = $masterticket->ticketid;
                        array_push($inserted_ids, $masterticket->ticketid);
                        if($gp_type == 'up'){
                            $Request = UserRequests::where('booking_id', $masterticket->ticketid)
                                                    ->orderBy('id', 'DESC')->first();
                            if ($masterticket !== null){
                                $Request->downreason = $data['downreason'];
                                $Request->downreasonindetailed = $data['downreasonindetailed'];
                                $Request->started_at= Carbon::now();
                                $Request->finished_at= Carbon::now();
                                $Request->status = 'COMPLETED';
                                $Request->save();

                                DB::table('gp_list')->where('lgd_code', $check_lgd_code->lgd_code)
                                    ->update(['status' => 0]);
                            } else {
                                continue;
                            }
                        }

                       $updatedata = array_except($data,['downreason','downreasonindetailed']);

                        DB::table('master_tickets')
                            ->where('ticketid', $masterticket->ticketid)
                            ->update($updatedata);

                    } else {
                        if($gp_type == 'up')
                            continue;

                            $existingClosedRecord = DB::table('master_tickets')
        ->where('lgd_code', $check_lgd_code->lgd_code)
        ->where('downdate', $data['downdate'])
        ->where('downtime', $data['downtime'])
        ->where('status', 'closed')
        ->first();

    if ($existingClosedRecord) {
        // If a matching closed record exists, update its status to 'INCOMING'
        
        // Also update the associated UserRequests record (if applicable)
        DB::table('user_requests')
            ->where('booking_id', $existingClosedRecord->ticketid)
            ->update(['status' => 'INCOMING','started_at' => null,'finished_at' => null]);

        // Add the ticket ID to the inserted_ids array
        array_push($inserted_ids, $existingClosedRecord->ticketid);

        // Skip creating a new record
        continue;
    }

                        // DB::table('master_tickets')->insert($data);
                        if(DB::table('master_tickets')->insert($data)){
                            array_push($inserted_ids, $data['ticketid']);
                            // UserRequest related data starts
                              $mobile = $check_lgd_code->contact_no;
                             //if (stripos($filedata[2], 'fiber') !== false) {
                             //  $mobile = $check_lgd_code->contact_no;
                             //  } else {
                             //  $mobile = $check_lgd_code->petroller_contact_no;
                            //}

                            $getproviderdetails = DB::table('providers')->select( 'providers.id', 'providers.mobile', 'providers.latitude', 'providers.longitude','provider_devices.token')->leftjoin('provider_devices','providers.id','=','provider_devices.provider_id')->where('mobile','=',$mobile)->first();
                            $provider_id = $getproviderdetails->id;
                            $latitude = $check_lgd_code->latitude;
                            $longitude = $check_lgd_code->longitude;

                            // Destination address
                            $destinationgeocodeFromLatLong = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=".Setting::get('map_key');
                            $json = curl($destinationgeocodeFromLatLong);
                            $desdetails = json_decode($json, TRUE);
                            $desstatus = $desdetails['status'];
                            $daddress = ($desstatus=="OK")?$desdetails['results'][1]['formatted_address']:'';

                            // Source address
                            $sourcegeocodeFromLatLong = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$getproviderdetails->latitude.",".$getproviderdetails->longitude."&key=".Setting::get('map_key');
                            $json = curl($sourcegeocodeFromLatLong);
                            $srcdetails = json_decode($json, TRUE);
                            $srcstatus = $srcdetails['status'];
                            $saddress = ($srcstatus=="OK")?$srcdetails['results'][1]['formatted_address']:'';
                            
                            // Route Key
                            $details = "https://maps.googleapis.com/maps/api/directions/json?origin=".$getproviderdetails->latitude.",".$getproviderdetails->longitude."&destination=".$latitude.",".$longitude."&mode=driving&key=".Setting::get('map_key');
                            $json = curl($details);
                            $details = json_decode($json, TRUE);
                            if(isset($details['routes'][0]))
                                $route_key = $details['routes'][0]['overview_polyline']['points'];
                            else
                                $route_key = null;

                            $UserRequest = new UserRequests;
                            $UserRequest->booking_id = $tkt_id;
                            $UserRequest->gpname = $check_lgd_code->gp_name;
                            $UserRequest->downreason = $filedata[2];
                            $UserRequest->downreasonindetailed = $filedata[2];
                            $UserRequest->user_id =45;                    
                         
                            $UserRequest->current_provider_id = $getproviderdetails->id;
                            $UserRequest->provider_id = $getproviderdetails->id;

                            $UserRequest->service_type_id = 2;
                            $UserRequest->rental_hours = 10;
                            $UserRequest->payment_mode = 'CASH';
                            $UserRequest->promocode_id = 0;
                            $UserRequest->default_autoclose = isset($filedata[3])? $filedata[3]:'Manual';
                            $UserRequest->autoclose = isset($filedata[3])? $filedata[3]:'Manual';
                            
                            $UserRequest->status = 'INCOMING';
                            $UserRequest->s_address =$saddress;
                            $UserRequest->d_address =$daddress;

                            $UserRequest->s_latitude = $getproviderdetails->latitude;
                            $UserRequest->s_longitude = $getproviderdetails->longitude;

                            $UserRequest->d_latitude = $latitude;
                            $UserRequest->d_longitude = $longitude;
                            $UserRequest->distance = 1;
                            $UserRequest->unit = Setting::get('distance', 'Kms');
                   
                            $UserRequest->use_wallet = 0;

                            if(Setting::get('track_distance', 0) == 1){
                                $UserRequest->is_track = "YES";
                            }

                            $UserRequest->otp = mt_rand(1000 , 9999);

                            $UserRequest->assigned_at = Carbon::now();
                            $UserRequest->route_key = $route_key;
                            $UserRequest->save();
                            // UserRequest related data end

                            DB::table('gp_list')->where('lgd_code', $check_lgd_code->lgd_code)
                                    ->update(['status' => 1]);
                        } 
                    }

                    $ready_to_import++;
                } else {                    
                    $ignored++;
                    $lgd_code .= (!empty($lgd_code))?(" , ".$filedata[0]):$filedata[0];
                }

                $i++;
            }

            fclose($file);
            if( $isRegular == true) {
            // Auto Close Tickets
            $older_tickets = array_diff($existing_ticket_ids, $inserted_ids);
            $completed_arr = array();
            $completed_arr['started_at'] = Carbon::now();
            $completed_arr['finished_at'] = Carbon::now();
            $completed_arr['status'] = 'COMPLETED';

            if (count($older_tickets) > 0){
                DB::table('user_requests')
                        ->whereIn('booking_id',  $older_tickets)
                        ->where('autoclose','=', 'Auto')
                        ->update($completed_arr);
            }
             }

            return redirect()
                ->route('admin.tickets')
                ->with('flash_success', "Files uploaded successfully!");

        } else {

            return back()->with('flash_error', 'unable to find the file.');
        }
        
    } catch (Exception $e) {  
        echo $e->getLine();
        dd($e);
        return back()->with('flash_error', 'Issue while saving the ticket details');
    }
}







public function process(Request $request)
{

     ini_set('max_execution_time', 300);

     $this->validate($request, [
        'import_file' => 'required|file', 
    ]);

    $lgd_code = '';
    try{
        
        $file = $request->file('import_file');
        $import_type = $request->input('type');

        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize(); //Get size of uploaded file in bytes

            // Validating file size and extension.
            $valid_extension = array("csv"); //Only want csv and excel files
            $maxFileSize = 10097152; // Uploaded file size limit is 10mb
            if (in_array(strtolower($extension), $valid_extension)) {
                if ($fileSize > $maxFileSize){
                    return back()->with('flash_error', 'File size should be less than 10 MB');
                }
            } else {
                return back()->with('flash_error', 'Invalid file extension. Accepts only .csv');
            }

            $importData_arr = array(); // Read through the file and store the contents as an array
            
            // Auto Close Tickets
            $inserted_ids = array();
            $existing_ticket_ids = DB::table('master_tickets')
                                        ->leftJoin('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                                        ->whereIn('user_requests.status', ['SEARCHING', 'INCOMING', 'PICKEDUP','ONHOLD'])
                                        ->pluck('master_tickets.ticketid')->toArray();

            // Read the contents of the file
            $i = 0;
            $ready_to_import = 0;
            $ignored = 0;
            $creates = 0;
            $updates = 0;
            $gp_type = '';
            $file=fopen($tempPath, 'r');
            $isRegular = false;

            while (($filedata = fgetcsv($file)) !== FALSE) {
                $num = count($filedata);
                $data = array(); 
                // Skip first row
                if ($i == 0) {                    
                    if (str_contains(strtolower($filedata[1]), 'up')) {
                        $gp_type = 'up';
                    } else if (str_contains(strtolower($filedata[1]), 'down')) {
                        $gp_type = 'down';
                    } else {
                        fclose($file);
                        return back()->with('flash_error', 'Unable to identify Up or Down time');
                        exit();
                    }
                    $i++;
                    continue;
                }
                if(empty($filedata[0]) && empty($filedata[1]) && empty($filedata[2]))
                    break;
                
                $check_lgd_code = DB::table('gp_list')->where('lgd_code', $filedata[0])->first();
                   if(isset($filedata[3]) && $filedata[3] == 'Auto') {
                        $isRegular = true;
                   }

                if($check_lgd_code){
                    $distict = District::findOrFail($check_lgd_code->district_id);
                    $block = Block::findOrFail($check_lgd_code->block_id);
                    $tkt_id = 'TK25'.mt_rand(100000, 9999999);
                    $data['ticketid'] = $tkt_id;
                    $data['district'] = $distict->name;
                    $data['mandal'] = $block->name;
                    $data['gpname'] = $check_lgd_code->gp_name;
                    $data['lgd_code'] = $check_lgd_code->lgd_code;
                    $data['subsategory'] = "";
                    //$formats = ['m-d-y H:i', 'm-d-y H:i:s', 'm-d-Y H:i', 'm-d-Y H:i:s', 'm/d/y H:i', 'm/d/y H:i:s', 'm/d/Y H:i', 'm/d/Y H:i:s','d-m-Y H:i', 'd-m-Y H:i:s','d/m/Y H:i', 'd/m/Y H:i:s'];
                    $formats = [
                     'd-m-Y H:i', 'd-m-Y H:i:s',
                     'd/m/Y H:i', 'd/m/Y H:i:s',
                     'Y-m-d H:i', 'Y-m-d H:i:s',
                     'Y/m/d H:i', 'Y/m/d H:i:s',
                     'm-d-Y H:i', 'm-d-Y H:i:s',
                     'm/d/Y H:i', 'm/d/Y H:i:s',
                     'd-m-y H:i', 'd-m-y H:i:s',
                     'd/m/y H:i', 'd/m/y H:i:s'
                     ];

                    $formattedDate = '';
                    $formattedTime = '';
                    foreach($formats as $format){
                        try{
                            $date = Carbon::createFromFormat($format, $filedata[1]);
                            if ($date instanceof Carbon) {
                                if($formattedDate == ''){
                                  $formattedDate = $date->format('Y-m-d');
                                  $formattedTime = $date->format('h:i:s a');
                                }
                            }
                        } catch (Exception $e) { }
                    }

                   if(isset($filedata[3]) && $filedata[3] == 'Manual'){

                      $today = Carbon::now();
                      $todaydate= $today->format('Y-m-d');
                      $todaytime= $today->format('h:i:s a');
                      if($gp_type == 'down'){
                        $data['downdate'] = $todaydate;
                        $data['downtime'] = $todaytime;
                      } else if($gp_type == 'up'){
                        $data['up_date'] = $todaydate;
                        $data['up_time'] = $todaytime;
                        $data['status'] = 1;
                     }
                    } else {

                      if($gp_type == 'down'){
                        $data['downdate'] = $formattedDate;
                        $data['downtime'] = $formattedTime;
                      } else if($gp_type == 'up'){
                        $data['up_date'] = $formattedDate;
                        $data['up_time'] = $formattedTime;
                        $data['status'] = 1;
                      }

                    }
                    $data['downreason'] = $filedata[2];
                    $data['downreasonindetailed'] = $filedata[2];
                    $data['lat'] = $check_lgd_code->latitude;
                    $data['log'] = $check_lgd_code->longitude;
                    $data['ticketinsertstage'] = 1;

                    // $is_created = MasterTicket::updateOrCreate($data);

                    if($gp_type == 'down'){
                        $masterticket = DB::table('master_tickets')
                                            ->leftJoin('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                                            ->where('lat', $check_lgd_code->latitude)
                                            ->where('log', $check_lgd_code->longitude)
                                            ->where('lgd_code', $check_lgd_code->lgd_code)
                                            ->whereIn('user_requests.status', ['SEARCHING','INCOMING','PICKEDUP','CANCELLED','ONHOLD'])
                                            ->orderBy('master_tickets.id', 'DESC')
                                            ->first();
                    } else if($gp_type == 'up'){
                        $masterticket = DB::table('master_tickets')
                                            ->where('lat', $check_lgd_code->latitude)
                                            ->where('log', $check_lgd_code->longitude)
                                            ->where('lgd_code', $check_lgd_code->lgd_code)
                                            ->whereNull('up_date')
                                            ->whereNull('up_time')
                                            ->orderBy('master_tickets.id', 'DESC')
                                            ->first();
                    } 
                    if ($masterticket !== null) {
                        $data['ticketid'] = $masterticket->ticketid;
                        array_push($inserted_ids, $masterticket->ticketid);
                        if($gp_type == 'up'){
                            $Request = UserRequests::where('booking_id', $masterticket->ticketid)
                                                    ->orderBy('id', 'DESC')->first();
                            if ($masterticket !== null){
                                $Request->downreason = $data['downreason'];
                                $Request->downreasonindetailed = $data['downreasonindetailed'];
                                $Request->started_at= Carbon::now();
                                $Request->finished_at= Carbon::now();
                                $Request->status = 'COMPLETED';
                                $Request->save();

                                DB::table('gp_list')->where('lgd_code', $check_lgd_code->lgd_code)
                                    ->update(['status' => 0]);
                            } else {
                                continue;
                            }
                        }

                       $updatedata = array_except($data,['downreason','downreasonindetailed']);

                        DB::table('master_tickets')
                            ->where('ticketid', $masterticket->ticketid)
                            ->update($updatedata);

                    } else {
                        if($gp_type == 'up')
                            continue;

                        // DB::table('master_tickets')->insert($data);
                        if(DB::table('master_tickets')->insert($data)){
                            array_push($inserted_ids, $data['ticketid']);
                            // UserRequest related data starts
                            if ($import_type == 2) {  
                            $mobile = $check_lgd_code->petroller_contact_no;
                            } else {
                            $mobile = $check_lgd_code->contact_no;
                            }
                  
                            if ($import_type == 2) {
                            $getproviderdetails = DB::table('providers')->select( 'providers.id', 'providers.mobile', 'providers.type','providers.latitude', 'providers.longitude','provider_devices.token')->leftjoin('provider_devices','providers.id','=','provider_devices.provider_id')->where('providers.type','=',5)->where('mobile','=',$mobile)->first();
                            } else {
                            $getproviderdetails = DB::table('providers')->select( 'providers.id', 'providers.mobile', 'providers.latitude', 'providers.longitude','provider_devices.token')->leftjoin('provider_devices','providers.id','=','provider_devices.provider_id')->where('mobile','=',$mobile)->first();
                            }
                            //dd($getproviderdetails);
                            $provider_id = $getproviderdetails->id;
                            $latitude = $check_lgd_code->latitude;
                            $longitude = $check_lgd_code->longitude;

                            // Destination address
                            $destinationgeocodeFromLatLong = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=".Setting::get('map_key');
                            $json = curl($destinationgeocodeFromLatLong);
                            $desdetails = json_decode($json, TRUE);
                            $desstatus = $desdetails['status'];
                            $daddress = ($desstatus=="OK")?$desdetails['results'][1]['formatted_address']:'';

                            // Source address
                            $sourcegeocodeFromLatLong = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$getproviderdetails->latitude.",".$getproviderdetails->longitude."&key=".Setting::get('map_key');
                            $json = curl($sourcegeocodeFromLatLong);
                            $srcdetails = json_decode($json, TRUE);
                            $srcstatus = $srcdetails['status'];
                            $saddress = ($srcstatus=="OK")?$srcdetails['results'][1]['formatted_address']:'';
                            
                            // Route Key
                            $details = "https://maps.googleapis.com/maps/api/directions/json?origin=".$getproviderdetails->latitude.",".$getproviderdetails->longitude."&destination=".$latitude.",".$longitude."&mode=driving&key=".Setting::get('map_key');
                            $json = curl($details);
                            $details = json_decode($json, TRUE);
                            if(isset($details['routes'][0]))
                                $route_key = $details['routes'][0]['overview_polyline']['points'];
                            else
                                $route_key = null;

                            $UserRequest = new UserRequests;
                            $UserRequest->booking_id = $tkt_id;
                            $UserRequest->gpname = $check_lgd_code->gp_name;
                            $UserRequest->downreason = $filedata[2];
                            $UserRequest->downreasonindetailed = $filedata[2];
                            $UserRequest->user_id =45;                    
                         
                            $UserRequest->current_provider_id = $getproviderdetails->id;
                            $UserRequest->provider_id = $getproviderdetails->id;

                            $UserRequest->service_type_id = 2;
                            $UserRequest->rental_hours = 10;
                            $UserRequest->payment_mode = 'CASH';
                            $UserRequest->promocode_id = 0;
                            $UserRequest->default_autoclose = isset($filedata[3])? $filedata[3]:'Manual';
                            $UserRequest->autoclose = isset($filedata[3])? $filedata[3]:'Manual';
                            
                            $UserRequest->status = 'INCOMING';
                            $UserRequest->s_address =$saddress;
                            $UserRequest->d_address =$daddress;

                            $UserRequest->s_latitude = $getproviderdetails->latitude;
                            $UserRequest->s_longitude = $getproviderdetails->longitude;

                            $UserRequest->d_latitude = $latitude;
                            $UserRequest->d_longitude = $longitude;
                            $UserRequest->distance = 1;
                            $UserRequest->unit = Setting::get('distance', 'Kms');
                   
                            $UserRequest->use_wallet = 0;

                            if(Setting::get('track_distance', 0) == 1){
                                $UserRequest->is_track = "YES";
                            }

                            $UserRequest->otp = mt_rand(1000 , 9999);

                            $UserRequest->assigned_at = Carbon::now();
                            $UserRequest->route_key = $route_key;
                            $UserRequest->save();
                            // UserRequest related data end

                            DB::table('gp_list')->where('lgd_code', $check_lgd_code->lgd_code)
                                    ->update(['status' => 1]);
                        } 
                    }

                    $ready_to_import++;
                } else {                    
                    $ignored++;
                    $lgd_code .= (!empty($lgd_code))?(" , ".$filedata[0]):$filedata[0];
                }

                $i++;
            }

            fclose($file);
            if( $isRegular == true) {
            // Auto Close Tickets
            $older_tickets = array_diff($existing_ticket_ids, $inserted_ids);
            $completed_arr = array();
            $completed_arr['started_at'] = Carbon::now();
            $completed_arr['finished_at'] = Carbon::now();
            $completed_arr['status'] = 'COMPLETED';

            if (count($older_tickets) > 0){
                DB::table('user_requests')
                        ->whereIn('booking_id',  $older_tickets)
                        ->where('autoclose','=', 'Auto')
                        ->update($completed_arr);
            }
             }

            return redirect()
                ->route('admin.tickets')
                ->with('flash_success', "Files uploaded successfully!");

        } else {

            return back()->with('flash_error', 'unable to find the file.');
        }
        
    } catch (Exception $e) {  
        echo $e->getLine();
        dd($e);
        return back()->with('flash_error', 'Issue while saving the ticket details');
    }
}

public function list_schedules()
{   
    $schedules= DB::table('schedule_auto_assign')->get();
    return view('admin.schedules.index',compact('schedules'));
}

public function edit_schedules($id)
{
    try {
        $schedule= DB::table('schedule_auto_assign')->find($id);
        return view('admin.schedules.edit',compact('schedule'));
    } catch (ModelNotFoundException $e) {
        return redirect()
            ->route('admin.schedulers')
            ->with('flash_success', trans('admin.schedule_msgs.schedule_not_found'));
    }
}

public function schedule_autoassign(Request $request, $id)
{
    $this->validate($request, [
        'schedule_time' => 'required', 
    ]);

    try {
        try {
            $schedule_auto_assign = DB::table('schedule_auto_assign')->find($id);
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.schedulers')
                ->with('flash_success', trans('admin.schedule_msgs.schedule_not_found'));
        }
        $current_time = Carbon::now();
        $current_timestamp = $current_time->timestamp;
        $schedule_time = $current_time;

        $schedule_cron = (trim($request->schedule_time) == 'custom')?trim($request->cst):trim($request->schedule_time);

        if($schedule_cron == '* * * * *') // Every Minute
            $schedule_time = $schedule_time->addMinutes(1);
        else if($schedule_cron == '0 * * * *') // Every Hour
            $schedule_time = $schedule_time->addHours(1); 
        else if($schedule_cron == '0 0 * * *') // Every Day/Mid_Night
            $schedule_time = $schedule_time->addHours(24);
        else if($schedule_cron == '0 0 * * 0') // Every Week
            $schedule_time = $schedule_time->addWeeks(1);
        else {
            $cron_array = explode(' ', $schedule_cron);
            if (!empty($cron_array)) {
                if($cron_array[0] != '*' && $cron_array[0] != '0')
                    $schedule_time = $schedule_time->addMinutes(explode('/', $cron_array[0])[1]);
                if($cron_array[1] != '*' && $cron_array[1] != '0')
                    $schedule_time = $schedule_time->addHours(explode('/', $cron_array[1])[1]);
                if($cron_array[2] != '*' && $cron_array[2] != '0')
                    $schedule_time = $schedule_time->addDays($cron_array[2]);
                if($cron_array[3] != '*' && $cron_array[3] != '0')
                    $schedule_time = $schedule_time->addMonths($cron_array[3]);
                if($cron_array[4] != '*' && $cron_array[4] != '0')
                    $schedule_time = $schedule_time->addWeeks($cron_array[4]);
            }
        }

        $schedule_interval = $schedule_time->timestamp;
        $next_interval = $schedule_time->timestamp - $current_timestamp;
     

        $data = array();
        $data['schedule_time'] = $schedule_cron;
        $data['schedule_interval'] = $schedule_interval;
        $data['next_interval'] = $next_interval;
        $data['is_custom'] = (trim($request->schedule_time) == 'custom')?'custom':'';

        DB::table('schedule_auto_assign')->where('id',$id)->update($data);
        return redirect()
                ->route('admin.schedulers')
                ->with('flash_success', trans('admin.schedule_msgs.schedule_saved'));

    } catch(Exception $e) {
        return back()->with('flash_error', trans('admin.schedule_msgs.schedule_not_found'));
    }
}


// New tickets page
public function tickets1(Request $request){

    try{  
        
        $serch_term = $request->searchinfo;
        $status=$request->get('status');
        $district_id=$request->get('district_id');
        $zone_id=$request->get('zone_id');
        $block_id=$request->get('block_id');
        $from_date=$request->get('from_date');
        $autoclose=$request->get('autoclose');
        $default_autoclose=$request->get('default_autoclose');
        $category=$request->get('category');
        $team_id=$request->get('team_id');
        $newfrom_date=$request->get('newfrom_date');
        $newto_date=$request->get('newto_date');
        $to_date=$request->get('to_date');
        $range=$request->get('range');
        $Gpstatus=$request->get('Gpstatus');




        $status_get = $status;
        $district_id_get = $district_id;
        $zone_id_get = $zone_id;
        $block_id_get = $block_id;
        $from_date_get = $from_date;
        $to_date_get = $to_date;
        $autoclose_get = $autoclose;
        $default_autoclose_get = $default_autoclose;
        $category_get = $category;
        $team_id_get =$team_id;
        $newfrom_date_get=$newfrom_date;
        $newto_date_get=$newto_date;
        $serch_term_get=$serch_term;
        $range_get=$range;


        $query_params = array();
        $tickets = DB::table('master_tickets')
         //->select('master_tickets.id as master_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','providers.first_name','providers.last_name','providers.mobile','user_requests.started_at','user_requests.finished_at')
          ->select('master_tickets.id as master_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.lgd_code','master_tickets.subsategory','user_requests.downreason','user_requests.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','zonal_managers.Name as zone_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','providers.zone_id','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at','user_requests.autoclose',DB::Raw('TIMESTAMPDIFF(HOUR, STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now().'") as duringhours'))
         ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
         ->leftjoin('providers', 'providers.id', '=', 'user_requests.provider_id')
         ->leftjoin('gp_list', 'master_tickets.lgd_code', '=', 'gp_list.lgd_code')
         ->leftjoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id');


         if(isset($request->ticket_id) && !empty($request->ticket_id)){
            $query_params['ticket_id'] = $request->ticket_id;
            $tickets->where('master_tickets.ticketid',$request->ticket_id);
         }

         if(isset($request->district_id) && !empty($request->district_id)){
            $query_params['district_id'] = $request->district_id;
            $tickets->where('master_tickets.district',$request->district_id);
         }
         if(isset($request->block_id) && !empty($request->block_id)){
            $query_params['block_id'] = $request->block_id;
            $tickets->where('master_tickets.mandal',$request->block_id);
         }

          if(isset($request->zone_id) && !empty($request->zone_id)){
            $query_params['zone_id'] = $request->zone_id;
            $tickets->where('gp_list.zonal_id',$request->zone_id);
         }
     
          if(isset($request->team_id) && !empty($request->team_id)){
            $query_params['team_id'] = $request->team_id;
            $tickets->where('providers.team_id',$request->team_id);
         }


         if(isset($request->autoclose) && !empty($request->autoclose)){
            $query_params['autoclose'] = $request->autoclose;
            $tickets->where('user_requests.autoclose',$request->autoclose);
         }
 
         if(isset($request->default_autoclose) && !empty($request->default_autoclose)){
            $query_params['default_autoclose'] = $request->default_autoclose;
            $tickets->where('user_requests.default_autoclose',$request->default_autoclose);
         }

         //if(isset($request->category) && !empty($request->category)){
         //   $query_params['category'] = $request->category;
         //   $tickets->where('user_requests.downreason','like', '%'.$request->category.'%');
        // }

          if (isset($request->category) && !empty($request->category)) {
               $query_params['category'] = $request->category;
    
       switch (strtolower($request->category)) {
        case 'power':
            $tickets->where('user_requests.downreason', 'like', '%Power%');
            break;
            
        case 'solar':
            $tickets->where('user_requests.downreason', 'regexp', 'SOLAR|SPV|SLA');
            break;
            
        case 'software/hardware':
            $tickets->where('user_requests.downreason', 'regexp', 'ONT|Software/Hardware');
            break;
            
        case 'ccu/battery':
            $tickets->where('user_requests.downreason', 'regexp', 'CCU|Battery');
            break;

        case 'others':
            $tickets->where('user_requests.downreason', 'regexp', 'Others|No Bin Type|GP Shifting|PP Extension|Other');
            break;
            
        default:
            // Default case if category doesn't match any specific pattern
            $tickets->where('user_requests.downreason', 'like', '%'.$request->category.'%');
        }
     }



         if(isset($request->status) && !empty($request->status)){
            $query_params['status'] = $request->status;
            $tkt_status = array('NotStarted' => 'INCOMING','OnGoing' => 'PICKEDUP', 'Completed' => 'COMPLETED', 'Onhold' => 'ONHOLD');                         
              $tickets->where('user_requests.status',$tkt_status[$request->status]);
           }
         if(isset($request->from_date) && !empty($request->to_date)){
                 $query_params['from_date'] = $request->from_date;
                 $query_params['to_date'] = $request->to_date;
                 $fromDate = $request->from_date . ' 00:00:00'; // Start of the day
                 $toDate = $request->to_date . ' 23:59:59'; // End of the day
     
               if(isset($request->status) && $request->status == 'Completed')
              {
                $tickets->whereBetween('user_requests.finished_at', [$fromDate, $toDate ]);

               } else if(isset($request->status) && $request->status == 'NotStarted'){
                $tickets->whereBetween('master_tickets.downdate', [$fromDate , $toDate ]);

               } else {
                 $tickets->whereBetween('user_requests.started_at', [$fromDate , $toDate ]);

                 }
                 }
          
           if (!empty($request->newfrom_date) && !empty($request->newto_date)) {

             $nfromDate = Carbon::parse($request->newfrom_date)->startOfDay()->toDateTimeString();
            $ntoDate = Carbon::parse($request->newto_date)->endOfDay()->toDateTimeString();

    if (isset($request->status)) {
        if ($request->status == 'Completed') {
            $tickets->whereBetween('user_requests.finished_at', [$nfromDate , $ntoDate ]);
        } elseif ($request->status == 'Onhold') {
            $tickets->whereBetween('user_requests.started_at', [$nfromDate , $ntoDate ]);
        }
        else {
            $tickets->whereDate('user_requests.started_at', '=', $request->newfrom_date);
        }
    }else{
        $tickets->whereDate('user_requests.started_at', '=', $request->newfrom_date);
    }
}

    if(isset($Gpstatus) && !empty($Gpstatus)){
                $tickets->whereIn('user_requests.status', ['INCOMING', 'ONHOLD', 'SCHEDULED']);
            
    
    }

        if(isset($request->range) && !empty($request->range)){
            $query_params['range'] = $request->range;
                                     
              $tickets->whereRaw('STR_TO_DATE(CONCAT(master_tickets.downdate, " ", master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p") < DATE_SUB(NOW(), INTERVAL 24 HOUR)');    
       }



         // Search functionality
         if(isset($request->searchinfo) && !empty($request->searchinfo))
         {
            $query_params['searchinfo'] = $request->searchinfo;
            //$serch_term = $request->searchinfo;
            $tickets->where(function ($query) use($serch_term){
                    $query->where('master_tickets.ticketid', 'like', '%'.$serch_term.'%')
                        ->orWhere('zonal_managers.Name', 'like', '%'.$serch_term.'%')
                        ->orWhere('master_tickets.district', 'like', '%'.$serch_term.'%')
                        ->orWhere('master_tickets.mandal', 'like', '%'.$serch_term.'%')
                        ->orWhere('master_tickets.gpname', 'like', '%'.$serch_term.'%')
                        ->orWhere('master_tickets.lgd_code', 'like', '%'.$serch_term.'%')
                        ->orWhere('providers.first_name', 'like', '%'.$serch_term.'%')
                        ->orWhere('providers.last_name', 'like', '%'.$serch_term.'%')
                        ->orWhere('master_tickets.downreason', 'like', '%'.$serch_term.'%')
                        ->orWhere('master_tickets.downreasonindetailed', 'like', '%'.$serch_term.'%')
                        ->orWhere('user_requests.autoclose', 'like', '%'.$serch_term.'%');
                });
         }

        $tickets = $tickets->orderBy('downdate','desc')
                         ->orderBy('downtime','asc');
                         //->get();
                         //->toSql();

        if($request->ajax()) {
            $tickets = $tickets->get();
            return response()->json(array('success' => true, 'data'=>$tickets));

        } else {
            $tickets = $tickets->paginate($this->perpage);
        }  
                                          
     // dd($tickets);
         $pagination=(new Helper)->formatPagination($tickets);
        //$url = $tickets->url($tickets->currentPage());

       //$request->session()->put('ticketspage', $url);


        $districts= DB::table('districts')->get();
        $blocks= DB::table('blocks')->get();
        $zonals= DB::table('zonal_managers')->get();
        $services= DB::table('service_types')->get();

        $ticket_status = array('NotStarted', 'OnGoing','Completed', 'Onhold');

        return view('admin.tickets_new', compact('services','tickets','districts','blocks', 'zonals','ticket_status', 'query_params','pagination','status_get','district_id_get','zone_id_get','team_id_get','block_id_get','from_date_get','to_date_get','autoclose_get','default_autoclose_get','category_get','newfrom_date_get','newto_date_get','serch_term_get','range_get'));

    } catch (Exception $e) { 
        dd($e);
        return back()->with('flash_error', trans('admin.something_wrong'));
    }
}


 /**
     * Display a listing of the total teams.
     *
     * @return \Illuminate\Http\Response
     */
    public function totalteams(Request $request)
    {   

            $totalteams = Provider::join('zonal_managers','providers.zone_id','=','zonal_managers.id')
                ->where('providers.zone_id', '!=', 0)
                ->where('providers.team_id', '!=', 0)
                ->whereNotNull('providers.team_id')
                ->where('providers.type',2)
                ->get();
            return view('admin.totalteams', compact('totalteams'));
     

    }


   /**
     * Display a listing of the total teams.
     *
     * @return \Illuminate\Http\Response
     */
    public function uniqueteams(Request $request)
    {   
            $uniqueteams = UserRequests::with('masterticket')->join('providers','providers.id','=','user_requests.provider_id')->join('zonal_managers','providers.zone_id','=','zonal_managers.id')->where('providers.zone_id','!=',0)->where('user_requests.status','=','PICKEDUP')->groupBy('user_requests.provider_id')->get();
            return view('admin.runningteams', compact('uniqueteams'));
     

    }


       /**
     * Display a listing of the total teams.
     *
     * @return \Illuminate\Http\Response
     */
    public function completedteams(Request $request)
    {   
            $completedteams = UserRequests::with('masterticket')->join('providers','providers.id','=','user_requests.provider_id')->join('zonal_managers','providers.zone_id','=','zonal_managers.id')->where('providers.zone_id','!=',0)->where('user_requests.autoclose','=','Manual')->where('user_requests.status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->groupBy('user_requests.provider_id')->get();
            return view('admin.completedteams', compact('completedteams'));
     

    }


      /**
     * Display a listing of the holdteams teams.
     *
     * @return \Illuminate\Http\Response
     */
    public function holdteams(Request $request)
    {   
            $holdteams = UserRequests::with('masterticket')->join('providers','providers.id','=','user_requests.provider_id')->join('zonal_managers','providers.zone_id','=','zonal_managers.id')->where('providers.zone_id','!=',0)->where('user_requests.status','=','ONHOLD')->whereDate('user_requests.started_at','=',Carbon::today())->groupBy('user_requests.provider_id')->get();
            return view('admin.holdteams', compact('holdteams'));
     

    }





 /**
     * Display a listing of the occ resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function notstartedteams(Request $request)
    {   
            date_default_timezone_set('Australia/Melbourne');
            $today = date('Y-m-d', time());
            //dd($today);
            //$notstartedteams = DB::select('SELECT providers.id,zonal_managers.Name,providers.first_name,providers.last_name,providers.email,providers.mobile,providers.team_id FROM providers join zonal_managers on providers.zone_id=zonal_managers.id WHERE NOT EXISTS (SELECT * FROM user_requests WHERE user_requests.provider_id = providers.id and user_requests.status ="PICKEDUP") and providers.zone_id != 0');
            $notstartedteams= DB::select(DB::raw('
    SELECT 
        providers.id,
        zonal_managers.Name,
        providers.first_name,
        providers.last_name,
        providers.email,
        providers.mobile,
        providers.team_id 
    FROM providers 
    JOIN zonal_managers ON providers.zone_id = zonal_managers.id 
    WHERE providers.zone_id != 0
    AND (providers.team_id, providers.zone_id) IN (
        SELECT subquery.team_id, subquery.zone_id
        FROM (
            SELECT all_teams.team_id, all_teams.zone_id, IFNULL(running_team_count, 0) AS running_team_count
            FROM (
                SELECT DISTINCT providers.team_id, providers.zone_id
                FROM providers
                WHERE providers.zone_id != 0
            ) AS all_teams
            LEFT JOIN (
                SELECT providers.team_id, providers.zone_id, COUNT(*) AS running_team_count
                FROM user_requests
                JOIN providers ON providers.id = user_requests.provider_id
                WHERE providers.zone_id != 0 AND user_requests.status = "PICKEDUP"
                GROUP BY providers.team_id, providers.zone_id
            ) AS running_teams ON all_teams.team_id = running_teams.team_id AND all_teams.zone_id = running_teams.zone_id
        ) AS subquery
        WHERE subquery.running_team_count = 0
    )
'));


          return view('admin.notstartedteams', compact('notstartedteams'));
     

    }


public function todaynotstartedteams(Request $request)
    {   
              $notRunningTeams = DB::table('providers')
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



      // Step 2: Use the retrieved team_id and zone_id to fetch provider details
$teamZonePairs = $notRunningTeams->map(function ($team) {
    return [
        $team->team_id,
        $team->zone_id,
    ];
})->toArray();

$pairs = implode(',', array_map(function ($pair) {
    return '(' . $pair[0] . ',' . $pair[1] . ')';
}, $teamZonePairs));

//dd($pairs);

//dd($teamZonePairs);


$query = "
    SELECT providers.id, zonal_managers.Name, providers.first_name, providers.last_name, 
           providers.email, providers.mobile, providers.team_id
    FROM providers
    INNER JOIN zonal_managers ON providers.zone_id = zonal_managers.id
    WHERE (providers.team_id, providers.zone_id) IN ($pairs)
";

$notstartedteams= DB::select(DB::raw($query));
//dd($results );
          return view('admin.notstartedteams', compact('notstartedteams'));
     

    }



  /**
     * Inventrory Dashboard.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function inventorydashboard()
    { 
        try{
           
            Session::put('user', Auth::User());
			
	     $parts= DB::table('equipment')->get()->count();
             $inventory = DB::table('inventory')->get()->count();
             $return_note= DB::table('return_note')->get()->count();
             $inwards= DB::table('material_inward')->get()->count();
             $incidents= DB::table('material_incident')->get()->count();
             $issues= DB::table('material_issue')->get()->count();
             $consumptions= DB::table('material_consumption')->get()->count();


			
          return view('admin.inventorydashboard',compact('parts','inventory','return_note','inwards','incidents','issues','consumptions'));
        }
        catch(Exception $e){
            return redirect()->route('admin.inventorydashboard')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }

  public function viewmaps()
{
    // Fetch GPS data from the gpslist table
    $gpsData = DB::table('gpslist')
        ->select('name', 'lattitude', 'longitude','blk_code','blk_name', 'dt_code','dt_name','st_code','st_name')
        ->get();

    $distinctBlocks = DB::table('gpslist')->select('blk_name')->distinct()->pluck('blk_name');
    $distinctDistricts = DB::table('gpslist')->select('dt_name')->distinct()->pluck('dt_name');
    $distinctStates = DB::table('gpslist')->select('st_name')->distinct()->pluck('st_name');

    // Calculate average latitude and longitude for centering the map
    $avgLatitude = $gpsData->avg(function ($item) {
        return (float) $item->lattitude;
    });

    $avgLongitude = $gpsData->avg(function ($item) {
        return (float) $item->longitude;
    });

    // Pass GPS data and central coordinates to the view
    return view('admin.map.viewmaps', compact('gpsData', 'avgLatitude', 'avgLongitude','distinctBlocks', 'distinctDistricts','distinctStates'));
}

public function getMapDistricts(Request $request)
{
    $state = $request->input('state');
    $districts = DB::table('gpslist')
        ->where('st_name', $state)
        ->distinct()
        ->pluck('dt_name');
    return response()->json($districts);
}

public function getMapBlocks(Request $request)
{
    $district = $request->input('district');
    $blocks = DB::table('gpslist')
        ->where('dt_name', $district)
        ->distinct()
        ->pluck('blk_name');
    return response()->json($blocks);
}

public function fetchGPSData(Request $request)
{
    $query = DB::table('gpslist');

    if ($request->has('state') && $request->input('state')) {
        $query->where('st_name', $request->input('state'));
    }

    if ($request->has('district') && $request->input('district')) {
        $query->where('dt_name', $request->input('district'));
    }

    if ($request->has('block') && $request->input('block')) {
        $query->where('blk_name', $request->input('block'));
    }

    $gpsData = $query->get();
    return response()->json($gpsData);
}

public function gp_summary(Request $request)
{
    $from = $request->input('from_date') 
        ? Carbon::parse($request->input('from_date'))->startOfDay() 
        : Carbon::now()->subDays(30)->startOfDay();

    $to = $request->input('to_date') 
        ? Carbon::parse($request->input('to_date'))->endOfDay() 
        : Carbon::now()->endOfDay();

    // Join master_tickets with user_requests
    $tickets = DB::table('master_tickets')
        ->join('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
        ->whereBetween('master_tickets.downdate', [$from->toDateString(), $to->toDateString()])
        ->select(
            'master_tickets.*',
            'user_requests.status',
            'user_requests.finished_at'
        )
        ->get();

    $grouped = $tickets->groupBy('lgd_code');

    $results = [];
    $chartData = [];

    foreach ($grouped as $lgd => $group) {
        $hourCounts = [];
        $allCompleted = true;
        $latestTicket = null;
        $latestTime = null;

        foreach ($group as $ticket) {
            $start = Carbon::parse($ticket->downdate . ' ' . $ticket->downtime);
            $end = $ticket->status === 'completed'
                ? Carbon::parse($ticket->finished_at)
                : Carbon::now();

            $hour = $start->format('H');
             if (!isset($hourCounts[$hour])) {
                $hourCounts[$hour] = 0;
            }
            $hourCounts[$hour] += 1;

            if ($ticket->status !== 'completed') {
                $allCompleted = false;
            }

            if (!$latestTime || $start->gt($latestTime)) {
                $latestTime = $start;
                $latestTicket = $ticket;
            }
        }

        $mostFrequentHour = $hourCounts ? array_keys($hourCounts, max($hourCounts))[0] : null;
      $chartData[$mostFrequentHour] = isset($chartData[$mostFrequentHour])
    ? $chartData[$mostFrequentHour] + 1
    : 1;


        $results[$lgd] = [
            'total_tickets' => count($group),
            'status' => $allCompleted ? 'active' : 'inactive',
            'most_frequent_downtime_hour' => $mostFrequentHour,
            'latest_ticket' => [
                'ticketid' => $latestTicket->ticketid,
                'status' => $latestTicket->status,
                'downreason' => $latestTicket->downreason,
                'downreasonindetailed' => $latestTicket->downreasonindetailed,
                'downdate' => $latestTicket->downdate,
                'downtime' => $latestTicket->downtime,
            ],
        ];
    }

    ksort($chartData);

    return response()->json([
        'from_date' => $from->toDateString(),
        'to_date' => $to->toDateString(),
        'lgd_analysis' => $results,
        'chart_data' => $chartData,
    ]);
}
public function uploadCSV(Request $request)
{
    $this->validate($request, [
        'csv_file' => 'required|mimes:csv,txt|max:2048',
    ]);

    $path = $request->file('csv_file')->getRealPath();
    $file = fopen($path, 'r');
    $header = fgetcsv($file);

    $records = [];

    try {
        while (($row = fgetcsv($file)) !== false) {
            $date = null;
            if (!empty($row[2])) {
                try {
                    $date = \Carbon\Carbon::parse($row[2])->format('Y-m-d');
                } catch (\Exception $e) {
                    // Invalid date format
                    throw new \Exception("Invalid date format in CSV.");
                }
            }

            // Check mandatory fields
            // dd(empty($row[0]));
            if (empty($row[0]) || empty($row[1]) || empty($date)) {
                throw new \Exception("Missing required fields in CSV (LGD Code, Uptime %, or Date).");
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
        return back()->with('error', $e->getMessage());
    }

    fclose($file);

    return back()->with('success', 'CSV uploaded successfully!')->with('records', $records);
}




// public function ONTdashboard(Request $request)
// {
//     $month    = $request->get('month');
//     $fromDate = $request->get('fromDate');
//     $toDate   = $request->get('toDate');

//     $query = OntUptime::query();

//     if (!empty($month)) {
//         try {
//             $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
//             $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
//             $query->whereBetween('record_date', [$start, $end]);
//         } catch (\Exception $e) {
//             $query->whereBetween('record_date', [
//                 Carbon::now()->subDays(6)->toDateString(),
//                 Carbon::now()->toDateString()
//             ]);
//         }
//     } elseif (!empty($fromDate) && !empty($toDate)) {
//         $query->whereBetween('record_date', [$fromDate, $toDate]);
//     } else {
//         $query->whereBetween('record_date', [
//             Carbon::now()->subDays(6)->toDateString(),
//             Carbon::now()->toDateString()
//         ]);
//     }

//     $data = $query->get();

//     // Group by date and count ranges
//     $groupedData = $data->groupBy(function ($item) {
//         return Carbon::parse($item->record_date)->format('Y-m-d');
//     })->map(function ($items) {
//            return [
//         '>=98'   => $items->where('uptime_percent', '>=', 98)->count(),
//         '>=90'   => $items->filter(function($i) {
//             return $i->uptime_percent >= 90 && $i->uptime_percent < 98;
//         })->count(),
//         '>=75'   => $items->filter(function($i) {
//             return $i->uptime_percent >= 75 && $i->uptime_percent < 90;
//         })->count(),
//         '>=50'   => $items->filter(function($i) {
//             return $i->uptime_percent >= 50 && $i->uptime_percent < 75;
//         })->count(),
//         '>=20'   => $items->filter(function($i) {
//             return $i->uptime_percent >= 20 && $i->uptime_percent < 50;
//         })->count(),
//         '<20'    => $items->where('uptime_percent', '<', 20)->count(),
//         'total'  => $items->count(),
//     ];
//     });

//     // Totals across all days
//    $totals = [];
// foreach ($groupedData as $date => $values) {
//     $totals[$date] = $values['total'];
// }


//     // Calculate percentages (>98%)
//     $percentages = [];
//     foreach ($groupedData as $date => $values) {
//         $total = $totals[$date];
//         $percentages[$date] = $total > 0 ? round(($values['>=98'] / $total) * 100, 2) : 0;
//     }


//     return view('admin.ont_uptime', compact('totals', 'percentages','groupedData'));
// }

public function ONTdashboard(Request $request)
{
    $month    = $request->get('month');
    $fromDate = $request->get('fromDate');
    $toDate   = $request->get('toDate');

    $query = OntUptime::query();

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

    return view('admin.ont_uptime', compact('data'));
}



    // CSV Management Tab
    public function csvManagement()
    {
        $records = OntUptime::latest()->paginate(20);
        return view('ont.csv', compact('records'));
    }

    // Upload CSV
    // public function uploadCsv(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:csv,xlsx,xls'
    //     ]);

    //     Excel::import(new OntUptimeImport, $request->file('file'));

    //     return back()->with('success', 'CSV uploaded successfully!');
    // }

    // CRUD
    public function index()
    {
        $records = OntUptime::orderBy('id', 'asc')->paginate(10);

        return view('admin.ont-uptime-index',compact('records'));
    }

    public function store(Request $request)
    {
        OntUptime::create($request->all());
        return back()->with('success', 'Record added');
    }

    public function edit($id)
    {
           $record = OntUptime::findOrFail($id);
            return response()->json($record);

    }

    public function update(Request $request, $id)
    {
        $ont = OntUptime::findOrFail($id);
        $ont->update($request->all());
        return response()->json(['success' => true, 'message' => 'Record updated successfully!']);
        

    }

    public function destroy($id)
    {
        OntUptime::destroy($id);
        return back()->with('success', 'Record deleted');
    }
}
