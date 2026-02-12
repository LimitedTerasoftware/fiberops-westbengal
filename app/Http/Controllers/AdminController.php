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
use App\Helpers\DistanceHelper;

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
use PDF;
use Excel;
use App\Services\GoogleMapsService;

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
    public function dashboard_backup()
    { 
        try{
           
            Session::put('user', Auth::User());
            
            /*$UserRequest = UserRequests::with('service_type')->with('provider')->with('payment')->findOrFail(83);

            echo "<pre>";
            print_r($UserRequest->toArray());exit;

            return view('emails.invoice',['Email' => $UserRequest]);*/

            $user = Session::get('user');
	    $company_id = $user->company_id;
	    $state_id = $user->state_id;

            $masterQuery = UserRequests::with('masterticket')
            ->where('company_id', $company_id)
            ->where('state_id', $state_id);
            $master_tickets =  $masterQuery->count();
            //dd($master_tickets);
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
                            ->whereNotNull('mt.lgd_code')
                            ->where('ur.autoclose','Auto')
                            ->where('ur.company_id', $company_id)
                             ->where('ur.state_id', $state_id)
                             ->distinct('mt.lgd_code')
                            ->count('mt.lgd_code');


         
            $totalGp = DB::table('gp_list')->where('company_id', $company_id)->where('state_id', $state_id)->where('type', "GP")->count();

            
            $powerQuery = UserRequests::with('masterticket')
            ->where('company_id', $company_id)
            ->where('state_id', $state_id)
            ->where('downreason', 'like', '%Power%');

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

           
            $electronicsQuery = UserRequests::with('masterticket')
            ->where('company_id', $company_id)
            ->where('state_id', $state_id)
            ->where('downreason', 'regexp', 'ONT|Software/Hardware');

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



            $solorQuery = UserRequests::with('masterticket')
            ->where('company_id', $company_id)
            ->where('state_id', $state_id)
            ->where('downreason', 'regexp', 'SOLAR|SPV|SLA');

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



            $oltQuery = UserRequests::with('masterticket')
            ->where('company_id', $company_id)
            ->where('state_id', $state_id)
            ->where('downreason', 'regexp', 'OLT');

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


            $ccuQuery = UserRequests::with('masterticket')
            ->where('company_id', $company_id)
            ->where('state_id', $state_id)
            ->where('downreason', 'regexp', 'CCU|Battery');

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


            $fiberQuery = UserRequests::with('masterticket')
            ->where('company_id', $company_id)
            ->where('state_id', $state_id)
            ->where('downreason', 'regexp', 'FIBER');

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



  
            $otherQuery = UserRequests::with('masterticket')
            ->where('company_id', $company_id)
            ->where('state_id', $state_id)
            ->where('downreason', 'regexp', 'Others|No Bin Type|GP Shifting|PP Extension|Other');
           
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

            $masterbase = UserRequests::with('masterticket')->where('company_id', $company_id)
            ->where('state_id', $state_id);
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
                ->where('providers.company_id', $company_id)
                ->where('providers.state_id', $state_id)
                ->whereNotNull('providers.team_id')
                ->where('providers.type',2)
                ->count();
            $runningteamcount = UserRequests::with('masterticket')->join('providers','providers.id','=','user_requests.provider_id')->where('providers.zone_id','!=',0)->where('user_requests.status','=','PICKEDUP')->where('user_requests.company_id', $company_id)
                ->where('user_requests.state_id', $state_id)->groupBy('providers.team_id')->groupBy('providers.zone_id')->get();
            $runningteams = $runningteamcount->count();

            $completedteamcount = UserRequests::with('masterticket')->join('providers','providers.id','=','user_requests.provider_id')->where('providers.zone_id','!=',0)->where('user_requests.autoclose','=','Manual')->where('user_requests.company_id', $company_id)
                ->where('user_requests.state_id', $state_id)->where('user_requests.status','=','COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->groupBy('providers.team_id')->groupBy('providers.zone_id')->get();
            $completedteams = $completedteamcount->count();

             $holdteamcount = UserRequests::with('masterticket')->join('providers','providers.id','=','user_requests.provider_id')->where('providers.zone_id','!=',0)->where('user_requests.status','=','ONHOLD')->where('user_requests.company_id', $company_id)
            ->where('user_requests.state_id', $state_id)->whereDate('user_requests.started_at','=',Carbon::today())->groupBy('providers.team_id')->groupBy('providers.zone_id')->get();
            $holdteams = $holdteamcount->count();


            $notrunningteams = $teamcount - $runningteams;

            $todaynotRunningTeams = DB::table('providers')
        ->select('providers.zone_id', 'providers.team_id')
        ->where('providers.zone_id', '!=', 0)
        ->where('providers.company_id', $company_id)
        ->where('providers.state_id', $state_id)

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
    public function heatmap(Request $request)
    {
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id   = $user->state_id;
    $district_id = $user->district_id;

    $distQuery = DB::table('districts')->where('state_id',$state_id);
                if (!empty($district_id)) {
                    $distQuery->where('id', $district_id);
                    
                }
    $districts = $distQuery->get();
    $blocksQuery = DB::table('blocks');
                   if (!empty($district_id)) {
                        $blocksQuery->where('district_id', $district_id);
                    }

    $blocks = $blocksQuery->get();

    // Default GP list
    $allGPsQuery = DB::table('gp_list')->select('lgd_code','gp_name')->where(['company_id'=>$company_id,'state_id'=>$state_id]);
                    if (!empty($district_id)) {
                        $allGPsQuery->where('district_id', $district_id);
                    }
    $allGPs=$allGPsQuery->get();

    //  GP query
    $query = DB::table('gp_list')->where('type','GP')->where(['company_id'=>$company_id,'state_id'=>$state_id]);
                if (!empty($district_id)) {
                        $query->where('district_id', $district_id);
                }

     // --- GP Down  ---
    $downGPSQuery = DB::table('user_requests as ur')
        ->leftJoin('master_tickets as mt', 'mt.ticketid', '=', 'ur.booking_id')
        ->whereIn('ur.status', ['INCOMING', 'ONHOLD', 'SCHEDULED', 'PICKEDUP'])
        ->whereNotNull('mt.lgd_code')
        ->where('ur.autoclose', 'Auto')
        ->where(['ur.company_id'=>$company_id,'ur.state_id'=>$state_id]);
          if (!empty($district_id)) {
                $downGPSQuery->where('ur.district_id', $district_id);
            }
       
    // --- District Filter ---
    if ($request->district_id) {
        $query->whereIn('district_id', $request->district_id);
        $districtNames = DB::table('districts')
        ->whereIn('id', $request->district_id)
        ->pluck('name')
        ->toArray();
        $downGPSQuery->whereIn('mt.district', $districtNames);


        $blocks = DB::table('blocks')
            ->whereIn('district_id', $request->district_id)
            ->get();

        $allGPs = DB::table('gp_list')
            ->whereIn('district_id', $request->district_id)->where(['company_id'=>$company_id,'state_id'=>$state_id])
            ->select('lgd_code','gp_name')
            ->get();
    }

    // --- Block Filter ---
    if ($request->block_id) {
        $query->whereIn('block_id', $request->block_id);
         $blockNames = DB::table('blocks')
                ->whereIn('id', $request->block_id)
                ->pluck('name')
                ->toArray();

        $downGPSQuery->whereIn('mt.mandal', $blockNames);
        $allGPs = DB::table('gp_list')
            ->whereIn('block_id', $request->block_id)->where(['company_id'=>$company_id,'state_id'=>$state_id])
            ->select('lgd_code','gp_name')
            ->get();
    }

    // --- District + Block Combined Filter ---
    if ($request->district_id && $request->block_id) {
        $allGPs = DB::table('gp_list')
            ->whereIn('district_id', $request->district_id)
            ->whereIn('block_id', $request->block_id)->where(['company_id'=>$company_id,'state_id'=>$state_id])
            ->select('lgd_code','gp_name')
            ->get();
    }

    // --- GP Filter ---
    if ($request->gp_id) {
        $downGPSQuery->whereIn('mt.lgd_code', $request->gp_id);
        $query->whereIn('lgd_code', $request->gp_id);
    }

    // Final GP points
    $gpsPoints = $query->get();
    $downGPSCodes = $downGPSQuery->distinct()->pluck('mt.lgd_code')->toArray();

   

    // --- Providers Filter  ---

     $today = Carbon::today();

    // Get all providers
    // $User = DB::table('providers')->where(['company_id'=>$company_id,'state_id'=>$state_id])->whereIn('type', [2, 5]);
    $User = DB::table('providers')
    ->select(
        'providers.id',
        'providers.first_name',
        'providers.last_name',
        'providers.mobile',
        'providers.latitude',
        'providers.longitude',
        'providers.district_id',
        'providers.block_id',
        'providers.type',
        'providers.version',
        'districts.name as district_name',
        'zonal_managers.Name as zone_name',
        'blocks.name as blockname',
        'attendance.status as attendance_status',
        'attendance.created_at as attendance_created_at'
    )
    ->leftJoin('attendance', function($join) {
        $join->on('providers.id', '=', 'attendance.provider_id')
             ->whereDate('attendance.created_at', Carbon::today());
    })
    ->join('districts', 'providers.district_id', '=', 'districts.id')
    ->leftJoin('zonal_managers', 'providers.zone_id', '=', 'zonal_managers.id')
    ->leftJoin('blocks', 'providers.block_id', '=', 'blocks.id')
    ->where('providers.company_id', $company_id)
    ->where('providers.state_id', $state_id)
    ->where('providers.status', 'approved')
    ->whereIn('providers.type', [2, 5,4])
    ->orderBy('attendance.created_at', 'desc');
        if (!empty($district_id)) {
            $User->where('providers.district_id', $district_id);
        }
       

        if ($request->district_id) {
            $User->whereIn('providers.district_id', $request->district_id);
        }

        if ($request->block_id) {
            $User->whereIn('providers.block_id', $request->block_id);
        }

        if ($request->provider_id) {
            $User->whereIn('providers.id', $request->provider_id);
        }

        $providers = $User->get();
       

        $data = [];
        $threshold = 1800;
        foreach ($providers as $provider) {
            $status = 'obsent';
            $isIdleNow = false;

            // If attended today -> take today's last tracking
            if ($provider->attendance_created_at) {
                $status = 'present';
                $latestTracking = DB::table('provider_tracking_histories')
                    ->where('provider_id', $provider->id)
                    ->whereDate('created_at', $today)
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
            } else {
               
                // Not attended -> take last known tracking (any day)
                $latestTracking = DB::table('provider_tracking_histories')
                    ->where('provider_id', $provider->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                   
                    
            }
          

            $liveLocation = null;
              
            if ($latestTracking && $latestTracking->latlng) {
                $points = json_decode($latestTracking->latlng, true);
                          
                
                if (is_array($points) && count($points) > 0) {
                    $lastPoint = end($points);
                    $previousPoint = count($points) > 1 ? $points[count($points)-2] : null;

                    $liveLocation = [
                        'latitude' => $lastPoint['latitude'] ? $lastPoint['latitude']  :  null,
                        'longitude' => $lastPoint['longitude'] ? $lastPoint['longitude'] : null,
                        'datetime' => $lastPoint['datetime'] ? $lastPoint['datetime'] : $latestTracking->created_at,
                        'address'  => $lastPoint['longitude'] ? $lastPoint['longitude'] : null,
                    ];
                  
                    if ($previousPoint &&  $status == 'present') {
                        $lat1 = $previousPoint['latitude'] ? $previousPoint['latitude']: null;
                        $lng1 = $previousPoint['longitude'] ?  $previousPoint['longitude']: null;
                        $lat2 = $lastPoint['latitude'] ? $lastPoint['latitude']: null;
                        $lng2 = $lastPoint['longitude'] ? $lastPoint['longitude']: null;

                        if ($lat1 !== null && $lng1 !== null && $lat2 !== null && $lng2 !== null) {
                            $time1 = strtotime($previousPoint['datetime']);
                            $time2 = strtotime($lastPoint['datetime']);
                            $timeDiff = $time2 - $time1;
                            if(($this->isSameLocation($lat1, $lng1, $lat2, $lng2)) ) {
                                 if ($timeDiff > 60) {
                                        $isIdleNow = true;
                                    } 
                            }
                            else if($timeDiff > $threshold){
                                  $isIdleNow = true;
                            }

                            $now = time();
                            $idleSinceLast = $now - $time2;
                            if ($idleSinceLast > $threshold) {
                                $isIdleNow = true;
                            }
                        }
                    }
                }
            }

            $data[] = [
                'provider' => $provider,
                'live_location' => $liveLocation,
                'status'        => $status, 
                'idle_now' => $isIdleNow,
            ];
        }

            

    // --- Providers Data for Filter Dropdowns ---
    $providersDataQuery = DB::table('providers')
        ->when($request->district_id, function ($q) use ($request) {
            return $q->whereIn('district_id', $request->district_id);
        })
        ->when($request->block_id, function ($q) use ($request) {
            return $q->whereIn('block_id', $request->block_id);
        });
      
        if (!empty($district_id)) {
            $providersDataQuery->where('district_id', $district_id);
        }
    $providersData = $providersDataQuery->select('id', 'first_name', 'last_name')
                     ->get();


    return view('admin.heatmap', compact(
        'districts',
        'blocks',
        'allGPs',
        'providersData',
        'gpsPoints',
        'downGPSCodes',
        'data',
        'state_id'
    ));
    }
    
    private function isSameLocation($lat1, $lng1, $lat2, $lng2, $threshold = 0.0005)
    {
        return (abs($lat1 - $lat2) < $threshold) && (abs($lng1 - $lng2) < $threshold);
    }
    public function generateEmployeePDFReport($id,$fromDate = null, $toDate = null, Request $request)
    {
        $user = Session::get('user');
        $company_id = $user->company_id;
        $state_id = $user->state_id;
        
        // Get employee details
        $provider = DB::table('providers')
            ->leftJoin('districts', 'providers.district_id', '=', 'districts.id')
            ->leftJoin('zonal_managers', 'providers.zone_id', '=', 'zonal_managers.id')
            ->leftJoin('blocks', 'providers.block_id', '=', 'blocks.id')
            ->select(
                'providers.*',
                'districts.name as district_name',
                'zonal_managers.Name as zone_name',
                'blocks.name as block_name'
            )
            ->where('providers.id', $id)
            ->where('providers.company_id', $company_id)
            ->where('providers.state_id', $state_id)
            ->first();

        if (!$provider) {
            abort(404, 'Employee not found');
        }

       
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        $fromDate = $fromDate ? $fromDate : Carbon::create($year, $month, 1)->format('Y-m-d');
    
        $toDate =$toDate ? $toDate : ((Carbon::now()->month == $month && Carbon::now()->year == $year) 
                        ? Carbon::today()->format('Y-m-d') 
                        : Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d'));



        // Get attendance data
        $attendances = DB::table('attendance')
            ->where('provider_id', $id)
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get attendance statistics
        $attendanceStats = $this->calculateAttendanceStats($id, $fromDate, $toDate);
        
        // Get request statistics
        $requestStats = $this->calculateRequestStats($id, $fromDate, $toDate);
        
        // Get recent requests with details
        $recentRequests = $this->getRecentRequestsWithDetails($id, $fromDate, $toDate);
        
        // Get tracking data for map
        $trackingData = $this->getTrackingDataForPDF($id, $fromDate, $toDate);
        
          // Generate static map URL
        $staticMapUrl = $this->generateStaticMapUrl($trackingData);
        
        // Get latest selfie
        $latestSelfie = DB::table('attendance')
            ->where('provider_id', $id)
            ->whereNotNull('online_image')
            ->orderBy('created_at', 'desc')
            ->value('online_image');

        // Role names mapping
        $roleNames = [
            1 => 'OFC',
            2 => 'FRT',
            5 => 'Patroller',
            3 => 'Zonal Incharge',
            4 => 'District Incharge'
        ];

        $data = [
            'provider' => $provider,
            'attendances' => $attendances,
            'attendanceStats' => $attendanceStats,
            'requestStats' => $requestStats,
            'recentRequests' => $recentRequests,
            'trackingData' => $trackingData,
            'latestSelfie' => $latestSelfie,
             'staticMapUrl' => $staticMapUrl,
            'roleNames' => $roleNames,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'month' => $month,
            'year' => $year,
            'reportDate' => Carbon::now()->format('d M Y, h:i A')
        ];
        $reportDate =Carbon::now()->format('d M Y, h:i A');

        // Generate PDF
         $pdf = PDF::loadView('admin.AttendanceDashboard.employee_pdf_report', compact(
        'provider','attendances','attendanceStats','requestStats','recentRequests',
        'trackingData','latestSelfie','staticMapUrl','roleNames','fromDate','toDate',
        'month','year','reportDate'
        ));
         $pdf->setPaper('A4', 'portrait');
          $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 150,
            'defaultPaperSize' => 'A4',
            'chroot' => public_path(),
        ]);
        
        
        $filename = $provider->first_name . '_' . $provider->last_name . '_Report_' . date('Y-m-d') . '.pdf';
        
         return $pdf->download($filename);
   
    }

    /**
     * Calculate attendance statistics
     */
   private function calculateAttendanceStats($providerId, $fromDate, $toDate)
   {
    $attendances = DB::table('attendance')
        ->where('provider_id', $providerId)
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->get();

    $daysInRange = Carbon::parse($fromDate)->diffInDays(Carbon::parse($toDate)) + 1;

    $presentDays = $attendances->count();

    $avgDuration = DB::table('user_requests')
        ->where('provider_id', $providerId)
        ->where('autoclose', 'Manual')
        ->whereNotNull('started_at')
        ->whereNotNull('finished_at')
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, started_at, finished_at)) as avg_minutes')
        ->value('avg_minutes');

    $avgDurationFormatted = $avgDuration
        ? floor($avgDuration / 60) . 'h ' . ($avgDuration % 60) . 'm'
        : '0h 0m';

    return [
        'present_days' => $presentDays,
        'total_days' => $daysInRange,
        'attendance_percentage' => $daysInRange > 0
            ? round(($presentDays / $daysInRange) * 100, 1)
            : 0,
        'avg_duration' => $avgDurationFormatted,
    ];
}


    /**
     * Calculate request statistics
     */
private function calculateRequestStats($providerId, $fromDate, $toDate)
{
    $baseQuery = DB::table('user_requests')
        ->where('provider_id', $providerId)
        ->where('autoclose', 'Manual')
        ->whereBetween('created_at', [$fromDate, $toDate]);

    $total = (clone $baseQuery)->count();

    $completed = (clone $baseQuery)
        ->where('status', 'COMPLETED')
        ->count();

    $cancelled = (clone $baseQuery)
        ->where('status', 'CANCELLED')
        ->count();

    $pending = (clone $baseQuery)
        ->whereIn('status', [
            'SEARCHING', 'ACCEPTED', 'STARTED', 'ARRIVED', 'PICKEDUP', 
            'DROPPED', 'INCOMING', 'ONHOLD', 'SCHEDULED', 'ONCALL', 
            'REACHED', 'REASSIGNED'
        ])
        ->count();

    return [
        'total' => $total,
        'completed' => $completed,
        'cancelled' => $cancelled,
        'pending' => $pending,
        'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0
    ];
}


    /**
     * Get recent requests with details
     */
    private function getRecentRequestsWithDetails($providerId, $fromDate, $toDate)
    {
        return DB::table('attendance as a')
            ->where('a.provider_id', $providerId)
            ->whereBetween(DB::raw('DATE(a.created_at)'), [$fromDate, $toDate])
            ->select(
                'a.id as attendance_id',
                'a.provider_id as provider_id',
                DB::raw('DATE(a.created_at) as attendance_date'),
                'a.created_at as check_in',
                'a.updated_at as check_out',
                'a.status as onlinestatus'
            )
            ->orderBy('a.created_at', 'desc')
            ->get()
            ->map(function ($attendance) use ($providerId) {
                // Get user requests for this attendance date
                $requests = DB::table('user_requests')
                    ->where('provider_id', $providerId)
                    ->where('autoclose', 'Manual')
                    ->whereDate('created_at', $attendance->attendance_date)
                    ->get();
                
                $totalTickets = $requests->count();
                $completedTickets = $requests->where('status', 'COMPLETED')->count();
                
                $trackingHistory = DB::table('provider_tracking_histories')
                        ->where('provider_id', $providerId)
                        ->whereDate('created_at', $attendance->attendance_date)
                        ->orderBy('created_at', 'asc')
                        ->get();

                  $trackingPoints = [];

                foreach ($trackingHistory as $history) {
                    if ($history->latlng) {
                        $points = json_decode($history->latlng, true);
                        if (is_array($points)) {
                            foreach ($points as $point) {
                                if (isset($point['latitude'], $point['longitude'])) {
                                    $trackingPoints[] = [
                                        'latitude' => (float)$point['latitude'],
                                        'longitude' => (float)$point['longitude'],
                                        'datetime' => $point['datetime'] ?? $history->created_at
                                    ];
                                }
                            }
                        }
                    }
                }
                   // --- Sort by datetime globally ---
                usort($trackingPoints, function($a, $b) {
                    return strtotime($a['datetime']) <=> strtotime($b['datetime']);
                });


                $totalDistance = DistanceHelper::calculateAccurateDistance($trackingPoints);

               

                                                
                // Get image count
                $imageCount = DB::table('submitfiles')
                    ->where('provider_id', $providerId)
                    ->whereDate('created_at', $attendance->attendance_date)
                    ->count();
                
                $attendance->total_tickets = $totalTickets;
                $attendance->completed_tickets = $completedTickets;
                $attendance->total_distance = $totalDistance;
                $attendance->images = $imageCount;
                
                return $attendance;
            });
    }

    /**
     * Get tracking data for PDF map
     */
    private function getTrackingDataForPDF($providerId, $fromDate, $toDate)
    {
        $trackingHistory = DB::table('provider_tracking_histories')
            ->where('provider_id', $providerId)
            ->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ])
            ->orderBy('created_at', 'asc')
            ->get();
        
        $trackingData = [];
        foreach ($trackingHistory as $history) {
            if ($history->latlng) {
                $points = json_decode($history->latlng, true);
                if (is_array($points)) {
                    $historyDate = date('Y-m-d', strtotime($history->created_at));
                    
                    foreach ($points as $point) {
                        if (isset($point['latitude']) && isset($point['longitude'])) {
                            $trackingData[] = [
                                'latitude' => $point['latitude'],
                                'longitude' => $point['longitude'],
                                'datetime' => $point['datetime'] ?  $point['datetime'] : $history->created_at,
                                'date' => $historyDate,
                                'address' => $this->getAddressFromCoordinates($point['latitude'], $point['longitude'])
                            ];
                        }
                    }
                }
            }
        }
        
        return $trackingData;
    }
    private function generateStaticMapUrl($trackingData, $width = 600, $height = 400)
    {
        if (empty($trackingData)) {
            return null;
        }

        $apiKey = Setting::get('map_key');
        if (!$apiKey) {
            return null;
        }

        $baseUrl = 'https://maps.googleapis.com/maps/api/staticmap';
        
        // Get bounds of all points
        $lats = array_column($trackingData, 'latitude');
        $lngs = array_column($trackingData, 'longitude');
        
        if (empty($lats) || empty($lngs)) {
            return null;
        }

        $minLat = min($lats);
        $maxLat = max($lats);
        $minLng = min($lngs);
        $maxLng = max($lngs);

        // Calculate center
        $centerLat = ($minLat + $maxLat) / 2;
        $centerLng = ($minLng + $maxLng) / 2;

        // Build URL parameters
        $params = [
            'center' => $centerLat . ',' . $centerLng,
            'size' => $width . 'x' . $height,
            'maptype' => 'roadmap',
            'key' => $apiKey,
            'format' => 'png'
        ];

        // Add markers for start and end points
        if (count($trackingData) > 0) {
            $startPoint = $trackingData[0];
            $endPoint = end($trackingData);
            
            $params['markers'] = 'color:green|label:S|' . $startPoint['latitude'] . ',' . $startPoint['longitude'];
            
            if (count($trackingData) > 1) {
                $params['markers'] .= '&markers=color:red|label:E|' . $endPoint['latitude'] . ',' . $endPoint['longitude'];
            }
        }

        // Add path if we have multiple points
        if (count($trackingData) > 1) {
            $pathPoints = [];
            // Sample points to avoid URL length limits (max ~20 points)
            $step = max(1, floor(count($trackingData) / 20));
            
            for ($i = 0; $i < count($trackingData); $i += $step) {
                $pathPoints[] = $trackingData[$i]['latitude'] . ',' . $trackingData[$i]['longitude'];
            }
            
            // Always include the last point
            if (end($pathPoints) !== $trackingData[count($trackingData) - 1]['latitude'] . ',' . $trackingData[count($trackingData) - 1]['longitude']) {
                $pathPoints[] = end($trackingData)['latitude'] . ',' . end($trackingData)['longitude'];
            }
            
            $params['path'] = 'color:0x0000ff|weight:3|' . implode('|', $pathPoints);
        }

        // Auto-fit zoom if we have multiple points
        if (count($trackingData) > 1) {
            // Calculate appropriate zoom level based on bounds
            $latDiff = $maxLat - $minLat;
            $lngDiff = $maxLng - $minLng;
            $maxDiff = max($latDiff, $lngDiff);
            
            if ($maxDiff > 0.1) {
                $params['zoom'] = '10';
            } elseif ($maxDiff > 0.01) {
                $params['zoom'] = '12';
            } else {
                $params['zoom'] = '14';
            }
        } else {
            $params['zoom'] = '14';
        }

        // Build final URL
        $url = $baseUrl . '?' . http_build_query($params);
        
        return $url;
    }
 

    /**
     * Calculate distance between two points
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }

    /**
     * Get address from coordinates 
     */
    private function getAddressFromCoordinates($lat, $lng)
    {
        return "Location: {$lat}, {$lng}";
    }
    // private function getAddressFromCoordinates($latitude, $longitude)
    // {
        
    //     try {
    //         $key = Setting::get('map_key');
    //         $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$key}";

    //         $response = file_get_contents($url);
    //         $data = json_decode($response, true);

    //         if (isset($data['results'][0]['formatted_address'])) {
    //             return $data['results'][0]['formatted_address'];
    //         }
    //     } catch (\Exception $e) {
    //         // Log error
    //     }

    //     return 'Unknown Location';
    // }


    	/**
     * Attendance.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function attendance(Request $request)
    {

            $user = Session::get('user');
            $company_id = $user->company_id;
            $state_id   = $user->state_id;
            $district_id = $user->district_id;



		
        try{
             if(!empty($request->district_id) && !empty($request->from_date) && !empty($request->to_date)){
            $providersQuery =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','providers.version','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
            ->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')->where('providers.company_id', $company_id)->where('providers.state_id', $state_id)->where('providers.district_id', $request->district_id )->whereDate('attendance.created_at','>=',$request->from_date)->whereDate('attendance.created_at','<=',$request->to_date);
             if (!empty($district_id)) {
                $providersQuery->where('providers.district_id', $district_id);
            }
            $providers = $providersQuery->orderBy('attendance.created_at','desc')->get();
              } else if(!empty($request->from_date) && !empty($request->to_date)){
            $providersQuery =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','providers.version','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
            ->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')->where('providers.company_id', $company_id)->where('providers.state_id', $state_id)->whereDate('attendance.created_at','>=',$request->from_date)->whereDate('attendance.created_at','<=',$request->to_date);
               if (!empty($district_id)) {
                $providersQuery->where('providers.district_id', $district_id);
            }
            $providers=$providersQuery->orderBy('attendance.created_at','desc')->get();
              }
              else{
              $providersQuery =DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.type','providers.version','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
            ->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')->where('providers.company_id', $company_id)->where('providers.state_id', $state_id)->where('attendance.created_at','>=',Carbon::today());
               if (!empty($district_id)) {
                $providersQuery->where('providers.district_id', $district_id);
               }
              $providers=$providersQuery->orderBy('attendance.created_at','desc')->get();
              }
              
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

			// $districts= DB::table('districts')->get();
            //             $blocks= DB::table('blocks')->get();
	
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
        $user = Session::get('user');
        $company_id = $user->company_id;
        $state_id = $user->state_id;
        $district_id = $user->district_id;
	
        try{
         $totalQuery = DB::table('providers')->where("company_id", $company_id)->where("state_id",$state_id);
          if (!empty($district_id)) {
                $totalQuery->where('district_id', $district_id);
            }
         $totalusers = $totalQuery->get()->count();
         $loggedinQuery=DB::table('providers')
            ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.company_id','providers.state_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'))
            ->join('attendance','providers.id','=','attendance.provider_id')->join('districts','providers.district_id','=','districts.id')
           ->where("providers.company_id", $company_id)->where("providers.state_id",$state_id)->where('attendance.created_at','>=',Carbon::today());
         if (!empty($district_id)) {
                $loggedinQuery->where('providers.district_id', $district_id);
            }
        $loggedinusers = $loggedinQuery->orderBy('attendance.created_at','desc')->count();
         $notloggedinusers = 0;
         return view('admin.todayattendancereport',compact('totalusers','loggedinusers','notloggedinusers'));
        }
        catch(Exception $e){
            return redirect()->route('admin.todayattendancereport')->with('flash_error','Something Went Wrong with Dashboard!');
        }
    }

          /**
 * Raise tickets � Patroller tickets list
 */
public function patrollertickets(Request $request)
{
    if (!empty($request->page) && $request->page == 'all') {

        $tickets = DB::table('raise_tickets as rt')
            ->leftJoin('providers as p', 'p.id', '=', 'rt.patroller_id')
            ->select(
                'rt.id',
                'rt.patroller_id',
                DB::raw("CONCAT(p.first_name, ' ', p.last_name) as patroller_name"),
                'p.mobile as patroller_mobile',
                'rt.gp_name',
                'rt.date',
                'rt.time',
                'rt.issue_type',
                'rt.issue_sub_type',
                'rt.priority',
                'rt.latitude',
                'rt.longitude',
                'rt.attachments',
                'rt.details',
                'rt.created_at'
            )
            ->orderBy('rt.id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tickets
        ]);
    }
    else {
       
        $user = Session::get('user');
        $company_id = $user->company_id;
        $state_id = $user->state_id;
        $district_id = $user->district_id;
        
        $zoneIdsQuery = DB::table('gp_list')->where('state_id', $state_id);
        if (!empty($district_id)) {
            $zoneIdsQuery->where('district_id', $district_id);
        }

        $zoneIds = $zoneIdsQuery->pluck('zonal_id')->unique();
        $zonals = DB::table('zonal_managers')->whereIn('id',$zoneIds)->get();


        $districtQuery = District::where('state_id',$state_id);
        if (!empty($district_id)) {
            $districtQuery->where('id', $district_id);
        }
        $districts = $districtQuery->get();


        $blockQuery= Block::query();
        if (!empty($district_id)) {
            $blockQuery->where('district_id', $district_id);
        }
    

        if ($request->has('district_id') && !empty($request->district_id)) {
        
            $blockQuery->where('district_id', $request->district_id);
        }
        $blocks = $blockQuery->get();

        $ticketsQuery = DB::table('raise_tickets as rt')
            ->leftJoin('gp_list as gp', 'gp.gp_name', '=', 'rt.gp_name')
            ->leftJoin('providers as p', 'p.id', '=', 'rt.patroller_id')
            ->select(
                'rt.*',
                DB::raw("CONCAT(p.first_name, ' ', p.last_name) as patroller_name"),
                'p.mobile as patroller_mobile'
            );
           

       $ticketsQuery->where('gp.state_id', $state_id);

       if (!empty($district_id)) {
            $ticketsQuery->where('gp.district_id', $district_id);
        }
        if ($request->has('district_id')) {
            $ticketsQuery->where('gp.district_id', $request->district_id);
        }

        if ($request->has('block_id')) {
            $ticketsQuery->where('gp.block_id', $request->block_id);
        }

        if ($request->has('zone_id')) {
            $ticketsQuery->where('gp.zonal_id', $request->zone_id);
        }

        if ($request->has('from_date') && $request->has('to_date')) {
            $ticketsQuery->whereBetween(
                DB::raw('DATE(rt.created_at)'),
                [$request->from_date, $request->to_date]
            );
        }
        if($request->has('issue_type')){
            $ticketsQuery->where('rt.issue_type',$request->issue_type);
        }
        $tickets = $ticketsQuery
        ->orderBy('rt.created_at', 'desc')->distinct('rt.id')
        ->paginate($this->perpage)
         ->appends(request()->query());
       
        $pagination = (new Helper)->formatPagination($tickets);

        $serviceType = ServiceType::select('name')->get();


        return view('admin.tickets.patrollertickets', compact('tickets', 'pagination','blocks','districts','zonals','serviceType'));
    }
}


	/**
     * Attendance.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    // public function attendancereport(Request $request)
    // {
	// 	$user = Session::get('user');
	//     $company_id = $user->company_id;
	//     $state_id = $user->state_id;
    //     $district_id = $user->district_id;
    //      try{
    //     if(!empty($request->district_id) && !empty($request->from_date) && !empty($request->to_date)){
    //          $providerQuery =DB::table('providers')
    //         ->select('providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.company_id','providers.state_id','providers.type','districts.name as district_name','attendance.status','attendance.address',
    //         'providers.joiningdate','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('count(attendance.created_at) as present '),DB::raw('group_concat(attendance.created_at) as presentdates'),DB::raw('group_concat(date_format(attendance.created_at,"%Y-%m-%d")) as origindate'))
    //         ->join('attendance','providers.id','=','attendance.provider_id')
	// 		->join('districts','providers.district_id','=','districts.id')
	// 		->where('providers.district_id', $request->district_id )->where('providers.state_id', $state_id )->where('providers.company_id', $company_id )->whereDate('attendance.created_at','>=',$request->from_date)->whereDate('attendance.created_at','<=',$request->to_date);
    //           if (!empty($district_id)) {
    //             $providerQuery->where('providers.district_id', $district_id);
    //         }
    //         $providers =$providerQuery->groupBy('providers.id')->orderBy('attendance.created_at','desc')->get();
    //         //dd($providers);
    //           } else if(!empty($request->district_id)){
    //             $providerQuery =DB::table('providers')
    //         ->select('providers.joiningdate','providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.company_id','providers.state_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('count(attendance.created_at) as present '),DB::raw('group_concat(attendance.created_at) as presentdates'),DB::raw('group_concat(date_format(attendance.created_at,"%Y-%m-%d")) as origindate'))
    //         ->join('attendance','providers.id','=','attendance.provider_id')
    //         ->join('districts','providers.district_id','=','districts.id')
    //         ->where('providers.district_id', $request->district_id )
    //         ->where('providers.state_id', $state_id )->where('providers.company_id', $company_id);
    //           if (!empty($district_id)) {
    //             $providerQuery->where('providers.district_id', $district_id);
    //         }
    //         $providers =$providerQuery->groupBy('providers.id')->orderBy('attendance.created_at','desc')->get();
    //         // echo $providers;
    //           } else if(!empty($request->from_date) && !empty($request->to_date)){
    //             $providerQuery =DB::table('providers')
    //         ->select('providers.joiningdate','providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.company_id','providers.state_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('count(attendance.created_at) as present '),DB::raw('group_concat(attendance.created_at) as presentdates'),DB::raw('group_concat(date_format(attendance.created_at,"%Y-%m-%d")) as origindate'))
    //         ->join('attendance','providers.id','=','attendance.provider_id')
    //         ->join('districts','providers.district_id','=','districts.id')->whereDate('attendance.created_at','>=',$request->from_date)->where('providers.state_id', $state_id )->where('providers.company_id', $company_id )->whereDate('attendance.created_at','<=',$request->to_date);
    //           if (!empty($district_id)) {
    //             $providerQuery->where('providers.district_id', $district_id);
    //         }
    //         $providers =$providerQuery->groupBy('providers.id')->orderBy('attendance.created_at','desc')->get();
    //           }
    //           else{
				  
	// 		$providerQuery =DB::table('providers')
    //         ->select('providers.joiningdate','providers.first_name','providers.last_name','providers.mobile','providers.latitude','providers.longitude','providers.district_id','providers.company_id','providers.state_id','providers.type','districts.name as district_name','attendance.status','attendance.address','attendance.offaddress','attendance.created_at','attendance.updated_at',DB::raw('count(attendance.created_at) as present '),DB::raw('group_concat(attendance.created_at) as presentdates'),DB::raw('group_concat(date_format(attendance.created_at,"%Y-%m-%d")) as origindate'))
    //         ->join('attendance','providers.id','=','attendance.provider_id')
	// 		->join('districts','providers.district_id','=','districts.id')
    //         ->where('providers.state_id', $state_id )->where('providers.company_id', $company_id);
	// 		// ->whereDate('attendance.created_at','>=', DB::raw('DATE_FORMAT(NOW(),"%Y/%m/01")'))
	// 		// ->whereDate('attendance.created_at','<=', DB::raw('DATE_FORMAT(last_day(NOW()), "%Y/%m/%d")'));
            

    //         if (!empty($district_id)) {
    //             $providerQuery->where('providers.district_id', $district_id);
    //         }
    //         $providers = $providerQuery->groupBy('providers.id')->orderBy('attendance.created_at','desc')->get();

 
			
    //          //dd($providers);
    //           }
			  

    //        $districtQuery = District::query();
    //         if (!empty($district_id)) {
    //             $districtQuery->where('id', $district_id);
    //         }
    //         $districts = $districtQuery->get();
           
    //         return view('admin.attendancereport1',compact('providers','districts'));
    //     }
    //     catch(Exception $e){
    //         return redirect()->route('admin.reportattendance')->with('flash_error','Something Went Wrong with Dashboard!');
    //     }
    // }

    public function attendancereport(Request $request)
{
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    $district_id = $user->district_id;

    try {
        $from = $request->from_date ? \Carbon\Carbon::parse($request->from_date) : \Carbon\Carbon::now()->startOfMonth();
        $to   = $request->to_date ? \Carbon\Carbon::parse($request->to_date) : \Carbon\Carbon::now()->endOfMonth();

        $actualTo = $to->gt(\Carbon\Carbon::today()) ? \Carbon\Carbon::today() : $to;

        $providerQuery = DB::table('providers')
            ->select('providers.*', 'districts.name as district_name')
            ->join('districts', 'providers.district_id', '=', 'districts.id')
            ->where('providers.state_id', $state_id)
            ->where('providers.company_id', $company_id);

        if (!empty($request->district_id)) {
            $providerQuery->where('providers.district_id', $request->district_id);
        } elseif (!empty($district_id)) {
            $providerQuery->where('providers.district_id', $district_id);
        }

        $providers = $providerQuery->get();

        $results = [];

        foreach ($providers as $provider) {
            $joining = $provider->joiningdate ? \Carbon\Carbon::parse($provider->joiningdate) : null;

            $workingStart = ($joining && $joining->gt($from)) ? $joining : $from;

            if ($joining && $joining->gt($actualTo)) {
                $totalWorkingDays = 0;
                $workingDates = [];
            } else {
                $totalWorkingDays = $workingStart->diffInDays($actualTo) + 1;

              $workingDates = [];
             $current = $workingStart->copy();
                while ($current->lte($actualTo)) {
                    $workingDates[] = $current->format('Y-m-d');
                    $current->addDay();
                }
            }

         $attendanceRecords = DB::table('attendance')
            ->where('provider_id', $provider->id)
            ->whereDate('created_at', '>=', $workingStart->format('Y-m-d'))
            ->whereDate('created_at', '<=', $actualTo->format('Y-m-d'))
            ->pluck('created_at')
            ->map(function ($d) { return \Carbon\Carbon::parse($d)->format('Y-m-d'); })
            ->toArray();
             $presentCount = count($attendanceRecords);

      $leaveRecords = DB::table('leaves')
        ->where('provider_id', $provider->id)
        ->where('status', 'approved')
        ->where('type', 'leave')
        ->where(function($q) use ($workingStart, $actualTo) {
            $q->whereBetween('start_date', [$workingStart->format('Y-m-d'), $actualTo->format('Y-m-d')])
            ->orWhereBetween('end_date', [$workingStart->format('Y-m-d'), $actualTo->format('Y-m-d')])
            ->orWhere(function($qq) use ($workingStart, $actualTo) {
                $qq->where('start_date', '<=', $workingStart->format('Y-m-d'))
                    ->where('end_date', '>=', $actualTo->format('Y-m-d'));
            });
        })
        ->get();

      $leaveDates = [];
        foreach ($leaveRecords as $leave) {
            $start = \Carbon\Carbon::parse($leave->start_date)->max($workingStart);
            $end   = \Carbon\Carbon::parse($leave->end_date)->min($actualTo);
            $current = $start->copy();
            while ($current->lte($end)) {
                $leaveDates[] = $current->format('Y-m-d');
                $current->addDay();
            }
        }

        $absentDates = array_diff($workingDates, array_merge($attendanceRecords, $leaveDates));

            $results[] = [
                'provider' => $provider,
                'total_working_days' => count($workingDates),
                'present_days' => count($attendanceRecords),
                'leave_days' => count($leaveDates),
                'absent_days' => count($absentDates),
                'attendance_dates' => $attendanceRecords,
                'leave_dates' => $leaveDates,
                'absent_dates' => $absentDates
            ];
        }

        $districtQuery = District::query();
        if (!empty($district_id)) {
            $districtQuery->where('id', $district_id);
        }
        $districts = $districtQuery->get();

        return view('admin.attendancereport1', compact('results','districts'));

    } catch (Exception $e) {
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
            $user = Session::get('user');
            $state_id = $user->state_id;
            $district_id = $user->district_id;

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
       
		$providerQuery= DB::table('providers');
         if (!empty($district_id)) {
                $providerQuery->where('district_id', $district_id);
            }
        $providers = $providerQuery->get();
        $district_id = $request->district_id;
        $block_id = $request->block_id;
		$provider_id = $request->provider_id;
		$from_date = $request->from_date;
		$to_date = $request->to_date;
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
        $user = Session::get('user');
	    $company_id = $user->company_id;
	    $state_id = $user->state_id;
        $district_id = $user->district_id;

            //dd($state_id);
        try {
           if(!empty($request->district_id)){
            $ProviderQuery = Provider::where('latitude', '!=', 0)
                    ->where('longitude', '!=', 0)
                    ->where('district_id', '=', $request->district_id)
                     ->where('block_id', '=', $request->block_id);
                    
             if (!empty($district_id)) {
                $ProviderQuery->where('district_id', $district_id);
            }
            $Providers = $ProviderQuery->with('service')
                         ->get();
            
             }else{
              $ProviderQuery = Provider::where('latitude', '!=', 0)
                    ->where('longitude', '!=', 0);
                    
                if (!empty($district_id)) {
                    $ProviderQuery->where('district_id', $district_id);
                }
            $Providers = $ProviderQuery->with('service')
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
             $user = Session::get('user');
	         $company_id = $user->company_id;
	         $state_id = $user->state_id;
             $district_id = $user->district_id;

		
		if($request->ajax()){
			Log::info("====AJAX======");
		  if(!empty($request->district_id) && empty($request->block_id) && empty($request->ticket_id)){

                 $ticketsQuery = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
                 ->where('user_requests.district_id',$request->district_id)
                 ->where('user_requests.company_id',$company_id)
                 ->where('user_requests.state_id',$state_id); 
                    if (!empty($district_id)) {
                        $ticketsQuery->where('user_requests.district_id', $district_id);
                    } 
                 
                 $tickets = $ticketsQuery->orderBy('downdate','desc')
                            ->orderBy('downtime','asc')
                            ->get();


              } else if (!empty($request->district_id) && !empty($request->block_id) && empty($request->ticket_id)){

                $ticketsQuery = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
                 ->where('user_requests.district_id',$request->district_id)
                 ->where('master_tickets.mandal',$request->block_id)
                 ->where('user_requests.company_id',$company_id)
                 ->where('user_requests.state_id',$state_id);  
                
                  if (!empty($district_id)) {
                        $ticketsQuery->where('user_requests.district_id', $district_id);
                    } 
                 
                 $tickets = $ticketsQuery->orderBy('downdate','desc')
                                ->orderBy('downtime','asc')
                                ->get();

             } else if (empty($request->district_id) && empty($request->block_id) && !empty($request->ticket_id)){

                 $ticketsQuery = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
                 ->where('master_tickets.ticketid',$request->ticket_id)
                 ->where('user_requests.company_id',$company_id)
                 ->where('user_requests.state_id',$state_id);  
               
                   if (!empty($district_id)) {
                        $ticketsQuery->where('user_requests.district_id', $district_id);
                    } 
                 
                 $tickets = $ticketsQuery->orderBy('downdate','desc')
                                ->orderBy('downtime','asc')
                                ->get();

              }	else {
				  $ticketsQuery = DB::table('master_tickets')
                  ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
                 ->where('user_requests.company_id',$company_id)
                 ->where('user_requests.state_id',$state_id); 
               
                  if (!empty($district_id)) {
                        $ticketsQuery->where('user_requests.district_id', $district_id);
                    } 
                 
                 $tickets = $ticketsQuery->orderBy('downdate','desc')
                                ->orderBy('downtime','asc')
                                ->get();

                }
			   return response()->json(array('success' => true, 'data'=>$tickets));
		}

         

             if(!empty($request->district_id) && empty($request->block_id) && empty($request->ticket_id)){

                 $ticketsQuery = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
                 ->where('user_requests.district_id',$request->district_id)
                 ->where('user_requests.company_id',$company_id)
                 ->where('user_requests.state_id',$state_id);  
                 
                 if (!empty($district_id)) {
                        $ticketsQuery->where('user_requests.district_id', $district_id);
                    } 
                 
                 $tickets = $ticketsQuery->orderBy('downdate','desc')
                                ->orderBy('downtime','asc')
                                ->get();

               
                $Disquery = DB::table('districts');
                    if (!empty($district_id)) {
                            $Disquery->where('id', $district_id);
                        }
                $districts = $Disquery->get();
                $blocksQuery= DB::table('blocks');
                if (!empty($district_id)) {
                            $blocksQuery->where('district_id', $district_id);
                    }

                 $blocks =  $blocksQuery->get();

             return view('admin.searchtickets', compact('tickets','districts','blocks'));

                //->paginate($this->perpage);
                // $pagination=(new Helper)->formatPagination($tickets);


              } else if (!empty($request->district_id) && !empty($request->block_id) && empty($request->ticket_id)){

                $ticketsQuery = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
                 ->where('user_requests.district_id',$request->district_id)
                 ->where('master_tickets.mandal',$request->block_id)
                 ->where('user_requests.company_id',$company_id)
                 ->where('user_requests.state_id',$state_id);  
                
                  if (!empty($district_id)) {
                        $ticketsQuery->where('user_requests.district_id', $district_id);
                    } 
                 
                 $tickets = $ticketsQuery->orderBy('downdate','desc')
                                ->orderBy('downtime','asc')
                                ->get();

             
               $Disquery = DB::table('districts');
                    if (!empty($district_id)) {
                            $Disquery->where('id', $district_id);
                        }
                $districts = $Disquery->get();
                $blocksQuery= DB::table('blocks');
                if (!empty($district_id)) {
                            $blocksQuery->where('district_id', $district_id);
                    }

                 $blocks =  $blocksQuery->get();
             return view('admin.searchtickets', compact('tickets','districts','blocks'));


               //->paginate($this->perpage);
                 //$pagination=(new Helper)->formatPagination($tickets);

             } else if (empty($request->district_id) && empty($request->block_id) && !empty($request->ticket_id)){

                $ticketsQuery = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
                 ->where('master_tickets.ticketid',$request->ticket_id)
                 ->where('user_requests.company_id',$company_id)
                 ->where('user_requests.state_id',$state_id);  
                
                 
                  if (!empty($district_id)) {
                        $ticketsQuery->where('user_requests.district_id', $district_id);
                    } 
                 
                 $tickets = $ticketsQuery->orderBy('downdate','desc')
                                ->orderBy('downtime','asc')
                                ->get();

                   $Disquery = DB::table('districts');
                    if (!empty($district_id)) {
                            $Disquery->where('id', $district_id);
                        }
                $districts = $Disquery->get();
                $blocksQuery= DB::table('blocks');
                if (!empty($district_id)) {
                            $blocksQuery->where('district_id', $district_id);
                    }

                 $blocks =  $blocksQuery->get();
             return view('admin.searchtickets', compact('tickets','districts','blocks'));

                //->paginate($this->perpage);
                 //$pagination=(new Helper)->formatPagination($tickets);


              }
			  
		 try{
			Log::info("==========");
			 Log::info($this->perpage);
             $ticketsQuery = DB::table('master_tickets')
                 ->select('master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime')
                 ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
                 ->where('user_requests.company_id',$company_id)
                 ->where('user_requests.state_id',$state_id);
                  if (!empty($district_id)) {
                        $ticketsQuery->where('user_requests.district_id', $district_id);
                    }   
                $tickets =$ticketsQuery->orderBy('downdate','desc')
                 ->orderBy('downtime','asc')
                ->paginate($this->perpage);

                 $pagination=(new Helper)->formatPagination($tickets);
                  $Disquery = DB::table('districts');
                    if (!empty($district_id)) {
                            $Disquery->where('id', $district_id);
                        }
                $districts = $Disquery->get();
                $blocksQuery= DB::table('blocks');
                if (!empty($district_id)) {
                            $blocksQuery->where('district_id', $district_id);
                    }

                 $blocks =  $blocksQuery->get();
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
                  ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) AS full_name"))
                 ->pluck("full_name","id");      
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
     Session::put('user', Auth::User());
    $user = Session::get('user');
    $district_id = $user->district_id;

    $query = District::query();
     if (!empty($district_id)) {
            $query->where('id', $district_id);
        }
    $districts = $query->get();

    $blocksQuery= Block::query();
     if (!empty($district_id)) {
            $blocksQuery->where('district_id', $district_id);
        }
    $blocks = $blocksQuery->get();
    $zonals= DB::table('zonal_managers')->get();
    $gplistQuery =  DB::table('gp_list');
     if (!empty($district_id)) {
            $gplistQuery->where('district_id', $district_id);
        }

    $gplist = $gplistQuery->get();

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

    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
  
    $DistrictData = DB::table('districts')->where('id',$request->district)->first();



    try{     

        $data = array(
            'district' => $DistrictData->name, 
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

            $UserRequest->company_id = $company_id;
            $UserRequest->state_id= $state_id;
            $UserRequest->district_id= $DistrictData->id;
            

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
        $userrequest = UserRequests::where('booking_id',$ticket->ticketid)->first();
        $service_types = ServiceType::get();
        $providers =provider::get();
        return view('admin.tickets.edit_ticket',compact('ticket','districts','blocks','service_types','userrequest','providers'));
    } catch (ModelNotFoundException $e) {
        return $e;
    }
}

public function updateTicket(Request $request, $id)
{
    $this->validate($request, [
        'downreason' => 'required',
        'sub_category' => 'required',
        'downreasonindetailed' => 'required',
             
    ]);
    
//dd($request);
    $ticket_id = $request->booking_id;

    try {
        $updateinput = array(
                  'downreason'=> $request->downreason_name,
                  'downreasonindetailed'=>$request->downreasonindetailed
                );
        DB::table('master_tickets')
            ->where('id',$id)
            ->update($updateinput);


         $updateusertable  = array(
                  'downreason'=> $request->downreason_name,
                  'subcategory'=> $request->sub_category_name,
                  'downreasonindetailed'=>$request->downreasonindetailed,
                  'purpose' => isset($request->purpose) ? $request->purpose : null
                );

         DB::table('user_requests')
            ->where('booking_id',$ticket_id)
            ->update($updateusertable);


        return redirect()
                ->to('admin/tickets?searchinfo=' . $ticket_id)
                ->with('flash_success', 'Ticket Details Updated Successfully');
    } 
    catch (ModelNotFoundException $e) {
        return back()->with('flash_error', 'Issue while updating the ticket details');
    }

}
public function DeleteTicketByAdmin($id)
{
    $ticket = MasterTicket::findOrFail($id);

    $deletedRequests = DB::table('user_requests')
        ->where('booking_id', $ticket->ticketid)
        ->delete();
    
   $deleted = $ticket->delete();

    return response()->json([  'success' => (bool) $deleted,'message' => 'Ticket and related request deleted successfully',
 'deleted_requests_count' => $deletedRequests]);
}

public function import_data_old1(Request $request)
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
            $manualDownSeen = [];

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
                $isManual = (isset($filedata[3]) && trim($filedata[3]) === 'Manual');

                $ticketType = $isManual ? 'Manual' : 'Auto';

                
                if($check_lgd_code){                    
                    if($gp_type == 'down'){
                            $key = $check_lgd_code->lgd_code;

                        if ($isManual) {
                         
                           if (!isset($manualDownSeen[$key])) {
           			 $manualDownSeen[$key] = true;
            			 $creates++;
        			} else {
          			  // Already seen in same file ? also CREATE
           			 $creates++;
       				 }
                        } else {
                        $check_ticket_exisits = DB::table('master_tickets')
                                                ->leftJoin('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                                                //->where('lat', $check_lgd_code->latitude)
                                                //->where('log', $check_lgd_code->longitude)
                                                ->where('lgd_code', $check_lgd_code->lgd_code)
                                                ->where('user_requests.default_autoclose', $ticketType)
                                                ->whereIn('user_requests.status', ['SEARCHING','INCOMING','PICKEDUP','CANCELLED','ONHOLD'])
                                                ->orderBy('master_tickets.id', 'DESC')
                                                ->first();
                        ($check_ticket_exisits)? $updates++: $creates++;
                      }
                     
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

public function import_data(Request $request)
{
    $this->validate($request, [
        'import_file' => 'required|file',
    ]);

    $response = (object)[];
    $lgd_code = '';

    try {

        $file = $request->file('import_file');
        if (!$file) {
            return response()->json([
                'error' => 'Unable to find the file.',
                'status' => 404
            ], 404);
        }

        // ================= FILE VALIDATION =================
        $extension = strtolower($file->getClientOriginalExtension());
        $fileSize  = $file->getSize();

        if ($extension !== 'csv') {
            return response()->json([
                'error' => 'Invalid file extension. Accepts only .csv',
                'status' => 404
            ], 404);
        }

        if ($fileSize > 10097152) {
            return response()->json([
                'error' => 'File size should be less than 10 MB',
                'status' => 404
            ], 404);
        }

        // ================= INIT =================
        $ready_to_import = 0;
        $ignored  = 0;
        $creates  = 0;
        $updates  = 0;
        $gp_type  = '';

        $handle = fopen($file->getRealPath(), 'r');
        $row = 0;

        // ================= READ CSV =================
        while (($filedata = fgetcsv($handle)) !== false) {

            // -------- HEADER --------
            if ($row === 0) {
                $header = strtolower($filedata[1] ?? '');

                if (str_contains($header, 'up')) {
                    $gp_type = 'up';
                } elseif (str_contains($header, 'down')) {
                    $gp_type = 'down';
                } else {
                    fclose($handle);
                    return response()->json([
                        'error' => 'Unable to identify UP or DOWN gps',
                        'status' => 404
                    ], 404);
                }

                $row++;
                continue;
            }

            // -------- EMPTY ROW STOP --------
            if (
                empty($filedata[0]) &&
                empty($filedata[1]) &&
                empty($filedata[2])
            ) {
                break;
            }

            $lgd = trim($filedata[0]);

            // -------- LGD CHECK --------
            $gp = DB::table('gp_list')
                ->where('lgd_code', $lgd)
                ->first();

            if (!$gp) {
                $ignored++;
                $lgd_code .= $lgd_code ? ', ' . $lgd : $lgd;
                $row++;
                continue;
            }

            // -------- AUTO / MANUAL --------
            $isManual   = (isset($filedata[3]) && trim($filedata[3]) === 'Manual');
            $ticketType = $isManual ? 'Manual' : 'Auto';

            // ================= DOWN LOGIC =================
            if ($gp_type === 'down') {

                $existingTicket = DB::table('master_tickets')
                    ->join('user_requests', function ($join) use ($ticketType) {
                        $join->on('master_tickets.ticketid', '=', 'user_requests.booking_id')
                             ->where('user_requests.default_autoclose', $ticketType);
                    })
                    ->where('master_tickets.lgd_code', $lgd)
                    ->whereIn('user_requests.status', [
                        'SEARCHING',
                        'INCOMING',
                        'PICKEDUP',
                        'ONHOLD'
                    ])
                    ->orderBy('master_tickets.id', 'DESC')
                    ->first();

                if ($existingTicket) {
                    $updates++;
                } else {
                    $creates++;
                }
            }

            // ================= UP LOGIC =================
            if ($gp_type === 'up') {
                $updates++;
            }

            $ready_to_import++;
            $row++;
        }

        fclose($handle);

        // ================= RESPONSE =================
        return response()->json([
            'ready_to_import' => $ready_to_import,
            'ignored' => $ignored,
            'creates' => $creates,
            'updates' => $updates,
            'lgd_codes' => $lgd_code,
            'status' => 200
        ], 200);

    } catch (\Exception $e) {

        return response()->json([
            'error' => $e->getMessage(),
            'line'  => $e->getLine(),
            'code'  => $lgd_code,
            'status'=> 500
        ], 500);
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
                    $tkt_id = 'TK26'.mt_rand(100000, 9999999);
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
    $user = Session::get('user');
    $user_id = $user->id;
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
                     if (!$distict) {
                        \Log::warning("District not found for LGD: {$check_lgd_code->lgd_code}");
                        continue;
                    }
                    $block = Block::findOrFail($check_lgd_code->block_id);
                     if (!$block) {
                        \Log::warning("Block not found for LGD: {$check_lgd_code->lgd_code}, Block ID: {$check_lgd_code->block_id}");
                        continue; 
                    }
                    do {
                        $tkt_id = 'TK26' . mt_rand(100000, 9999999);
                    } while (DB::table('master_tickets')->where('ticketid', $tkt_id)->exists());

                    // $tkt_id = 'TK26'.mt_rand(100000, 9999999);
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
                    $isManual = (isset($filedata[3]) && trim($filedata[3]) === 'Manual');

                    $masterticket = null;

                    if($gp_type == 'down' && !$isManual){
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
                            $downReasonnew = strtolower(trim($filedata[2] ?? ''));
                            if ($import_type == 2) {  
                            $mobile = $check_lgd_code->petroller_contact_no;
                            } else {
                            
                                 if (strpos($downReasonnew, 'fiber') !== false) {
                                 $mobile = $check_lgd_code->contact_no;
                                } else {
                               $mobile = $check_lgd_code->petroller_contact_no;
                                }


                            }
                  
                            if ($import_type == 2) {
                            $getproviderdetails = DB::table('providers')->select( 'providers.id', 'providers.mobile', 'providers.type','providers.latitude', 'providers.longitude','provider_devices.token')->leftjoin('provider_devices','providers.id','=','provider_devices.provider_id')->whereIn('providers.type', [2, 5])->where('mobile','=',$mobile)->first();
                            } else {
                            $getproviderdetails = DB::table('providers')->select( 'providers.id', 'providers.mobile', 'providers.latitude', 'providers.longitude','provider_devices.token')->leftjoin('provider_devices','providers.id','=','provider_devices.provider_id')->where('mobile','=',$mobile)->first();
                            }
                            //dd($getproviderdetails);
                            \Log::error("Provider not found for mobile: {$mobile}, import_type: {$import_type}");
                             Log::error('Provider detailsManual: ' . json_encode($getproviderdetails));

                            $provider_id = $getproviderdetails->id;
                            $latitude = $check_lgd_code->latitude;
                            $longitude = $check_lgd_code->longitude;

                            $googleMaps = new GoogleMapsService();
                            $daddress = $googleMaps->getReverseGeocode($latitude, $longitude);

                            $saddress = $googleMaps->getReverseGeocode($getproviderdetails->latitude, $getproviderdetails->longitude);

                            $direction_json = $googleMaps->getDirections($getproviderdetails->latitude, $getproviderdetails->longitude, $latitude, $longitude);
                            $route_key = isset($direction_json['routes'][0]['overview_polyline']['points']) ? $direction_json['routes'][0]['overview_polyline']['points'] : null;
                            
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
                            $UserRequest->purpose = isset($filedata[4]) ? $filedata[4] : null; //rahul added
                            
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
                            $UserRequest->company_id= $check_lgd_code->company_id;
                            $UserRequest->state_id= $check_lgd_code->state_id;
                            $UserRequest->district_id = $check_lgd_code->district_id;

                            $UserRequest->assigned_at = Carbon::now();
                            $UserRequest->route_key = $route_key;
                            $UserRequest->created_by=$user_id;
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
            if( $isRegular == true && $import_type != 2) {
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

// New tickets page testing by rahul
public function tickets1(Request $request){

    try{  
        $user = Session::get('user');
    
        $company_id = $user->company_id;
        $state_id = $user->state_id;
        $Roledistrict_id = $user->district_id;

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
        $provider_id=$request->get('provider_id');
        $interval =$request->get('interval');
        $c_from_date=$request->get('c_from_date');
        $c_to_date=$request->get('c_to_date');

    


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
        $provider_id_get=$provider_id;
        $interval_get =$interval;
        $c_from_date_get=$c_from_date;
        $c_to_date_get=$c_to_date;

        


        $query_params = array();
        $tickets = DB::table('master_tickets')
         //->select('master_tickets.id as master_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','providers.first_name','providers.last_name','providers.mobile','user_requests.started_at','user_requests.finished_at')
          ->select('user_requests.created_by','user_requests.description','user_requests.issue_type','master_tickets.id as master_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.lgd_code','user_requests.subcategory','user_requests.downreason','user_requests.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','user_requests.purpose','master_tickets.downtime','zonal_managers.Name as zone_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','providers.zone_id','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at','user_requests.autoclose','user_requests.default_autoclose',DB::Raw('TIMESTAMPDIFF(HOUR, STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"), "'.Carbon::now().'") as duringhours'))
         ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
         ->leftjoin('providers', 'providers.id', '=', 'user_requests.provider_id')
         ->leftjoin('gp_list', 'master_tickets.lgd_code', '=', 'gp_list.lgd_code')
         ->leftjoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id')
          ->where('user_requests.company_id', $company_id)
          ->where('user_requests.state_id', $state_id);
          
        if (!empty($Roledistrict_id)) {
            $tickets->where('user_requests.district_id', $Roledistrict_id);
        }


         if(isset($request->ticket_id) && !empty($request->ticket_id)){
            $query_params['ticket_id'] = $request->ticket_id;
            $tickets->where('master_tickets.ticketid',$request->ticket_id);
         }

         if(isset($request->district_id) && !empty($request->district_id)){
            $query_params['district_id'] = $request->district_id;
            $tickets->where('user_requests.district_id',$request->district_id);
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

          if(isset($request->provider_id) && !empty($request->provider_id)){
            $query_params['provider_id'] = $request->provider_id;
            $tickets->where('providers.id',$request->provider_id);
         }


          if(isset($request->c_from_date) && !empty($request->c_to_date)){

                $query_params['c_from_date'] = $request->c_from_date;
                 $query_params['c_to_date'] = $request->c_to_date;
                 $cfromDate = $request->c_from_date . ' 00:00:00'; // Start of the day
                 $ctoDate = $request->c_to_date . ' 23:59:59'; // End of the day
                $tickets->whereBetween('user_requests.created_at', [$cfromDate , $ctoDate ]);
         }


         if(isset($request->autoclose) && !empty($request->autoclose)){
            $autoclose = ucfirst(strtolower($request->autoclose));

            $query_params['autoclose'] = $autoclose;
            $tickets->where('user_requests.autoclose',$autoclose);
         }

        
 
         if(isset($request->default_autoclose) && !empty($request->default_autoclose)){
              $default_autoclose = ucfirst(strtolower($request->default_autoclose));
            $query_params['default_autoclose'] = $default_autoclose;
            $tickets->where('user_requests.default_autoclose',$default_autoclose);
         }
      


        
             if (isset($request->interval) && !empty($request->interval)) {
                $query_params['interval'] = $request->interval;
                $interval = $request->interval;

                if (preg_match('/^\d+-\d+$/', $interval) || $interval === '>48' || $interval === 'above_48') {

                    if ($interval == '0-4') {
                        $tickets->whereRaw('TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) <= 240');
                    } elseif ($interval == '4-10') {
                        $tickets->whereRaw('TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) > 240 AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) <= 600');
                    } elseif ($interval == '10-24') {
                        $tickets->whereRaw('TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) > 600 AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) <= 1440');
                    } elseif ($interval == '24-48') {
                        $tickets->whereRaw('TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) > 1440 AND TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) <= 2880');
                    } elseif ($interval == '>48' || $interval == 'above_48' || $interval == 'completed_gt_48') {
                        $tickets->whereRaw('TIMESTAMPDIFF(MINUTE, user_requests.started_at, user_requests.finished_at) > 2880');
                    }

                } else {
                    if ($interval == 'below_4_hours') {
                        $tickets->where('user_requests.status', '=', 'INCOMING')->whereRaw('TIMESTAMPDIFF(HOUR, DATE_FORMAT(STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p"), "%Y-%m-%d %H:%i:%s"), "' . Carbon::now()->format("Y-m-d H:i:s") . '") < 4');
                        ;
                    } else if ($interval == 'between_4_to_10_hours') {
                        $tickets->where('user_requests.status', '=', 'INCOMING')->whereRaw('TIMESTAMPDIFF(HOUR, DATE_FORMAT(STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p"), "%Y-%m-%d %H:%i:%s"), "' . Carbon::now()->format("Y-m-d H:i:s") . '") BETWEEN 4 AND 10');
                    } else if ($interval == 'between_10_to_24_hours') {
                        $tickets->where('user_requests.status', '=', 'INCOMING')->whereRaw('TIMESTAMPDIFF(HOUR, DATE_FORMAT(STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p"), "%Y-%m-%d %H:%i:%s"), "' . Carbon::now()->format("Y-m-d H:i:s") . '") BETWEEN 11 AND 24');
                    } else if ($interval == 'above_24_hours') {
                        $tickets->where('user_requests.status', '=', 'INCOMING')->whereRaw('TIMESTAMPDIFF(HOUR, DATE_FORMAT(STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %h:%i:%s %p"), "%Y-%m-%d %H:%i:%s"), "' . Carbon::now()->format("Y-m-d H:i:s") . '") > 24');
                    }
                }

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
        case 'permanent down':    
            $tickets->where('user_requests.downreason', 'like', '%Permanent Down%')
                ->where(function($query) {
                    $query->where('subcategory', 'not like', '%ETR Fiber Cut%')
                          ->where('subcategory', 'not like', '%OLT Down%');
                });
        break;
        case 'bsnl scope':    
        $tickets->where(function($query) {
                    $query->where('subcategory', 'like', '%ETR Fiber Cut%')
                          ->orWhere('subcategory', 'like', '%OLT Down%');
                });
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
            $tkt_status = array('Open' => 'INCOMING','OnGoing' => 'PICKEDUP', 'Completed' => 'COMPLETED', 'Onhold' => 'ONHOLD');                         
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

               } else if(isset($request->status) && $request->status == 'Open'){
                $tickets->whereBetween('master_tickets.downdate', [$fromDate , $toDate ]);

               } else if(isset($request->district_id) && !empty($request->district_id)){
                 $tickets->where('user_requests.district_id',$request->district_id)
                  ->whereBetween('user_requests.created_at', [$fromDate , $toDate ]);

               } else if(isset($request->status) && $request->status == 'OnGoing'){
                $tickets->whereBetween('user_requests.started_at', [$fromDate , $toDate ]);

               } else if(isset($request->status) && $request->status == 'Onhold'){
                $tickets->whereBetween('user_requests.started_at', [$fromDate , $toDate ]);

               } else {
                 $tickets->where(function($q) use ($fromDate, $toDate){
            $q->whereBetween('user_requests.created_at', [$fromDate , $toDate])
              ->orWhereBetween('user_requests.started_at', [$fromDate, $toDate]);
        });
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
        $tickets->where('user_requests.autoclose','Auto')->whereNotNull('master_tickets.lgd_code')->distinct('master_tickets.lgd_code')
        ->whereIn('user_requests.status', ['INCOMING', 'ONHOLD', 'SCHEDULED','PICKEDUP']);
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
                        ->orWhere('user_requests.purpose', 'like', '%'.$serch_term.'%')
                        ->orWhere('user_requests.autoclose', 'like', '%'.$serch_term.'%');
                });
         }


      

        $tickets = $tickets->orderBy('downdate','desc')
                         ->orderBy('downtime','asc');
                         // With joins (your current logic)


                         //->get();
                         //->toSql();
         
         $countstatus = clone $tickets;

         $statusCounts = $countstatus->select('user_requests.status', \DB::raw('COUNT(*) as total'))->groupBy('user_requests.status')->pluck('total','user_requests.status','user_requests.downreason');
         
         $countstatus1 = clone $tickets;
          
         $permanentDownCount = $countstatus1->where('user_requests.status', 'onhold')->where('user_requests.downreason', 'Permanent Down')->count();

    
        
        if ($request->ajax()) {

                    $tickets = $tickets->get();

                    $ticketIds = $tickets->pluck('ticketid');

                    $materialsData = DB::table('submitfiles')
                        ->whereIn('ticket_id', $ticketIds)
                        ->get()
                        ->groupBy('ticket_id');

                    $tickets->each(function ($ticket) use ($materialsData) {

                        // Initialize all materials with empty string
                        $ticket->materials = [
                            '24F CABLE' => '',
                            '48F CABLE' => '',
                            '4F CABLE'  => '',
                            '6F CABLE'  => '',
                            '8F CABLE'  => '',
                            '12F CABLE' => '',
                            'PATCH CORD' => '',
                            'JOINT ENCLOSURE' => '',
                            'Other Joint BOX' => '',
                            'SS STRIP' => '',
                            'TENSION CLAMP' => '',
                            'BUCKLES' => '',
                            'FRAMES' => '',
                            'ENCLOSURES' => '',
                            'Joint chamber' => ''
                        ];

                        $ticket->joint_enclosure_before_latlong = '';
                        $ticket->joint_enclosure_after_latlong = '';

                        // Fill materials and lat/longs if exist
                        if (isset($materialsData[$ticket->ticketid])) {
                            foreach ($materialsData[$ticket->ticketid] as $m) {

                                // Parse materials string
                                $raw = trim($m->materials, '{}');

                                foreach (explode(',', $raw) as $pair) {
                                    if (strpos($pair, '=') !== false) {
                                        [$key, $value] = explode('=', $pair);
                                        $key = trim($key);
                                        $value = trim($value);

                                        if (array_key_exists($key, $ticket->materials)) {
                                            $ticket->materials[$key] = $value;
                                        }
                                    }
                                }

                                // Only fill lat/long if available
                                if (!empty($m->joint_enclosurebefore_latlong)) {
                                    $ticket->joint_enclosure_before_latlong = $m->joint_enclosurebefore_latlong;
                                }
                                if (!empty($m->joint_enclosureafter_latlong)) {
                                    $ticket->joint_enclosure_after_latlong = $m->joint_enclosureafter_latlong;
                                }
                            }
                        }
                    });

            return response()->json([
                'success' => true,
                'data' => $tickets
            ]);



        } else {
            
            $tickets = $tickets->paginate($this->perpage);
        }  
                                          
      //dd($tickets);
         $pagination=(new Helper)->formatPagination($tickets);
        //$url = $tickets->url($tickets->currentPage());

       //$request->session()->put('ticketspage', $url);

        $districtName = null;
        $distriQuery= DB::table('districts')->where('state_id',$state_id);
        if (!empty($Roledistrict_id)) {
            $distriQuery->where('id', $Roledistrict_id);
             $districtName = DB::table('districts')
                ->where('state_id', $state_id)
                ->where('id', $Roledistrict_id)
                ->value('name');
        }
        $districts = $distriQuery->get();
        $blockQuery= DB::table('blocks')->whereIn('district_id', $districts->pluck('id')->all());
         if (!empty($Roledistrict_id)) {
            $blockQuery->where('district_id', $Roledistrict_id);
        }
        $blocks = $blockQuery->get();
        $zonals= DB::table('zonal_managers')
                  ->where(function($query) use ($state_id)
                          {if($state_id ==1){
                            $query->where('id','!=',6);
                          }else{
                             $query->where('id',6);
                          }
                          })->get();
        $services= DB::table('service_types')->get();

        $ticket_status = array('Open', 'OnGoing','Completed', 'Onhold');

        return view('admin.dashboard.tickets', compact('services','tickets','statusCounts','permanentDownCount','districts','blocks', 'zonals','ticket_status', 'query_params','pagination','status_get','district_id_get','zone_id_get','team_id_get','provider_id_get','block_id_get','from_date_get','to_date_get','autoclose_get','default_autoclose_get','interval_get','category_get','newfrom_date_get','newto_date_get','serch_term_get','range_get'));

    } catch (Exception $e) { 
        dd($e);
        return back()->with('flash_error', trans('admin.something_wrong'));
    }
}

// New tickets page
public function tickets1_old(Request $request){

    try{  
    $user = Session::get('user');
   
	$company_id = $user->company_id;
	$state_id = $user->state_id;

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
         ->leftjoin('zonal_managers', 'gp_list.zonal_id', '=', 'zonal_managers.id')
          ->where('user_requests.company_id', $company_id)
          ->where('user_requests.state_id', $state_id);


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
            $tickets->groupBy('user_requests.id');
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
        $tickets->where('user_requests.autoclose','Auto')->whereNotNull('master_tickets.lgd_code')->distinct('master_tickets.lgd_code')
        ->whereIn('user_requests.status', ['INCOMING', 'ONHOLD', 'SCHEDULED','PICKEDUP']);
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


        $districts= DB::table('districts')->where('state_id',$state_id)->get();
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

    // Normalize line endings (handle \r, \r\n, etc.)
    $content = file_get_contents($path);
    $content = preg_replace("/\r\n|\r/", "\n", $content);
    file_put_contents($path, $content);

    $file = fopen($path, 'r');

    $header = fgetcsv($file);

    $records = [];

    try {
        while (($row = fgetcsv($file)) !== false) {
            // Skip empty rows
            if ($row === null || empty(array_filter($row))) {
                continue;
            }

            $date = null;

            if (!empty($row[2])) {
                try {
                    $date = \Carbon\Carbon::parse($row[2])->format('Y-m-d');
                } catch (\Exception $e) {
                    throw new \Exception("Invalid date format in CSV.");
                }
            }

            $data = [
                'lgd_code'      => $row[0],
                'uptime_percent'=> $row[1],
                'record_date'   => $date,
            ];

            OntUptime::create($data);
            $records[] = $data;
        }
    } catch (\Exception $e) {
        fclose($file);
        return back()->with('error', $e->getMessage());
    }

    fclose($file);

    return back()
        ->with('success', 'CSV uploaded successfully!')
        ->with('records', $records);
}



public function ONTdashboard(Request $request)
{
    $month    = $request->get('month');
    $fromDate = $request->get('fromDate');
    $toDate   = $request->get('toDate');

    Session::put('user', Auth::User());
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;


    $query = OntUptime::query()
             ->join('gp_list', 'gp_list.lgd_code', '=', 'ont_uptime.lgd_code')
        ->where('gp_list.company_id', $company_id)
        ->where('gp_list.state_id', $state_id);

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


    return view('admin.ont_uptime', compact('data','averages'));
}


  public function csvManagement()
    {
        $records = OntUptime::latest()->paginate(20);
        return view('ont.csv', compact('records'));
    }

   public function index()
{
    // Get logged-in user details
    Session::put('user', Auth::User());
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id   = $user->state_id;
    $records = OntUptime::query()
        ->join('gp_list', 'gp_list.lgd_code', '=', 'ont_uptime.lgd_code')
        ->join('districts','gp_list.district_id','=','districts.id')
        ->join('blocks','gp_list.block_id','=','blocks.id')
        ->leftJoin('zonal_managers','gp_list.zonal_id','=','zonal_managers.id')
        ->where('gp_list.company_id', $company_id)
        ->where('gp_list.state_id', $state_id)
        ->orderBy('ont_uptime.id', 'asc')
        ->select(
            'ont_uptime.*',
            'districts.name as district_name',
            'blocks.name as block_name',
            'zonal_managers.name as zone_name',
            'gp_list.phase',
            'gp_list.gp_name'
        )->paginate(10);

    return view('admin.ont-uptime-index', compact('records'));
}

    public function index_old()
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

//Rahul Added

      public function newdashboard2()
    {
        
      
       return view('admin.dashboard.tickets');
    }


    public function dashboard()
    {
        
      
       return view('admin.dashboard.dashboard');
    }


public function getDashboardData_old(Request $request)
{
    Session::put('user', Auth::User());
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;

    // Master Tickets
    $masterQuery = UserRequests::with('masterticket')
        ->where('company_id', $company_id)
        ->where('state_id', $state_id);

    $master_tickets = $masterQuery->count();

    $clonemaster = clone $masterQuery;
    $ongoing_tickets = $clonemaster->where('status', 'PICKEDUP')->count();

    $clonemaster1 = clone $masterQuery;
    $onhold_tickets = $clonemaster1->where('status', 'ONHOLD')->count();

    $clonemaster2 = clone $masterQuery;
    $notstarted_tickets = $clonemaster2->where('status', 'INCOMING')->count();

    $clonemaster3 = clone $masterQuery;
    $completed_tickets = $clonemaster3->where('status', 'COMPLETED')->count();

    $clonemaster4 = clone $masterQuery;
    $yesterday_completed_tickets = $clonemaster4->where('status', 'COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::yesterday())->count();
    $clonemaster5 = clone $masterQuery;
    $today_completed_tickets = $clonemaster5->where('status', 'COMPLETED')->whereDate('user_requests.finished_at','=',Carbon::today())->count();

    $clonemaster6 = clone $masterQuery;
    $yesterday_ongoing_tickets = $clonemaster6->where('status', 'PICKEDUP')->whereDate('user_requests.started_at','=',Carbon::yesterday())->count();
    $clonemaster7 = clone $masterQuery;
    $today_ongoing_tickets = $clonemaster7->where('status', 'PICKEDUP')->whereDate('user_requests.started_at','=',Carbon::today())->count();

    $clonemaster8 = clone $masterQuery;
    $today_onhold_tickets = $clonemaster8->where('status', 'ONHOLD')->whereDate('user_requests.started_at','=',Carbon::today())->count();

    $clonemaster9 = clone $masterQuery;
    $yesterday_onhold_tickets = $clonemaster9->where('status', 'ONHOLD')->whereDate('user_requests.started_at','=',Carbon::yesterday())->count();

    $clonemaster10 = clone $masterQuery;
    $auto_tickets = $clonemaster10->where('user_requests.autoclose', 'Auto')->count();

    $clonemaster11 = clone $masterQuery;
    $manual_tickets = $clonemaster11->where('user_requests.autoclose', 'Manual')->count();






    // Unique LGD Count
    $uniqueLgdCount = DB::table('user_requests as ur')
        ->leftJoin('master_tickets as mt', 'mt.ticketid', '=', 'ur.booking_id')
        ->whereIn('ur.status', array('INCOMING', 'ONHOLD', 'SCHEDULED', 'PICKEDUP'))
        ->whereNotNull('mt.lgd_code')
        ->where('ur.company_id', $company_id)
        ->where('ur.state_id', $state_id)
        ->distinct('mt.lgd_code')
        ->count('mt.lgd_code');

    // GP Count
    $totalGp = DB::table('gp_list')
        ->where('company_id', $company_id)
        ->where('state_id', $state_id)
        ->where('type', "GP")
        ->count();

  // permanent_downTickets
    $permanent_downQuery = UserRequests::with('masterticket')
        ->where('company_id', $company_id)
        ->where('state_id', $state_id)
        ->where('downreason', 'like', '%Permanent Down%');

    $pwn1 = clone $permanent_downQuery;
    $permanent_down = $pwn1->where('status', 'ONHOLD')->count();

 

    // Power Tickets
    $powerQuery = UserRequests::with('masterticket')
        ->where('company_id', $company_id)
        ->where('state_id', $state_id)
        ->where('downreason', 'like', '%Power%');

    $power = $powerQuery->count();

    $clone1 = clone $powerQuery;
    $notstartedups = $clone1->where('status', 'INCOMING')->count();

    $clone2 = clone $powerQuery;
    $ongoingups = $clone2->where('status', 'PICKEDUP')->count();

    $clone3 = clone $powerQuery;
    $holdups = $clone3->where('status', 'ONHOLD')->count();

    $clone4 = clone $powerQuery;
    $completedups = $clone4->where('status', 'COMPLETED')->count();

    $clone5 = clone $powerQuery;
    $completedups_yesterday = $clone5->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', '=', Carbon::yesterday())->count();

    $clone6 = clone $powerQuery;
    $completedups_today = $clone6->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', '=', Carbon::today())->count();

    // Electronics Tickets
    $electronicsQuery = UserRequests::with('masterticket')
        ->where('company_id', $company_id)
        ->where('state_id', $state_id)
        ->where('downreason', 'regexp', 'ONT|Software/Hardware');

    $electronics = $electronicsQuery->count();

    $e1 = clone $electronicsQuery;
    $notstartedelectronics = $e1->where('status', 'INCOMING')->count();

    $e2 = clone $electronicsQuery;
    $ongoingelectronics = $e2->where('status', 'PICKEDUP')->count();

    $e3 = clone $electronicsQuery;
    $holdelectronics = $e3->where('status', 'ONHOLD')->count();

    $e4 = clone $electronicsQuery;
    $completedelectronics = $e4->where('status', 'COMPLETED')->count();

    $e5 = clone $electronicsQuery;
    $completedelectronics_yesterday = $e5->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', '=', Carbon::yesterday())->count();

    $e6 = clone $electronicsQuery;
    $completedelectronics_today = $e6->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', '=', Carbon::today())->count();

    // Solar Tickets
    $solorQuery = UserRequests::with('masterticket')
        ->where('company_id', $company_id)
        ->where('state_id', $state_id)
        ->where('downreason', 'regexp', 'SOLAR|SPV|SLA');

    $solar = $solorQuery->count();

    $s1 = clone $solorQuery;
    $notstartedsolar = $s1->where('status', 'INCOMING')->count();

    $s2 = clone $solorQuery;
    $ongoingsolar = $s2->where('status', 'PICKEDUP')->count();

    $s3 = clone $solorQuery;
    $holdsolar = $s3->where('status', 'ONHOLD')->count();

    $s4 = clone $solorQuery;
    $completedsolar = $s4->where('status', 'COMPLETED')->count();

    $s5 = clone $solorQuery;
    $completedsolar_yesterday = $s5->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', '=', Carbon::yesterday())->count();

    $s6 = clone $solorQuery;
    $completedsolar_today = $s6->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', '=', Carbon::today())->count();

   $fiberQuery = UserRequests::with('masterticket')
            ->where('company_id', $company_id)
            ->where('state_id', $state_id)
            ->where('downreason', 'regexp', 'FIBER');

   $fiber = $fiberQuery->count();

    $f1 = clone $fiberQuery;
    $notstartedfiber = $f1->where('status', 'INCOMING')->count();

    $f2 = clone $fiberQuery;
    $ongoingfiber = $f2->where('status', 'PICKEDUP')->count();

    $f3 = clone $fiberQuery;
    $holdfiber = $f3->where('status', 'ONHOLD')->count();

    $f4 = clone $fiberQuery;
    $completedfiber = $f4->where('status', 'COMPLETED')->count();

    $f5 = clone $fiberQuery;
    $completedfiber_yesterday = $f5->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', '=', Carbon::yesterday())->count();

    $f6 = clone $fiberQuery;
    $completedfiber_today = $f6->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', '=', Carbon::today())->count();


    $ccuQuery = UserRequests::with('masterticket')
            ->where('company_id', $company_id)
            ->where('state_id', $state_id)
            ->where('downreason', 'regexp', 'CCU|Battery');


     $battery = $ccuQuery->count();

    $b1 = clone $ccuQuery;
    $notstartedbattery = $b1->where('status', 'INCOMING')->count();

    $b2 = clone $ccuQuery;
    $ongoingbattery = $b2->where('status', 'PICKEDUP')->count();

    $b3 = clone $ccuQuery;
    $holdbattery = $b3->where('status', 'ONHOLD')->count();

    $b4 = clone $ccuQuery;
    $completedbattery = $b4->where('status', 'COMPLETED')->count();

    $b5 = clone $ccuQuery;
    $completedbattery_yesterday = $b5->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', '=', Carbon::yesterday())->count();

    $b6 = clone $ccuQuery;
    $completedbattery_today = $b6->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', '=', Carbon::today())->count();


    $oltQuery = UserRequests::with('masterticket')
            ->where('company_id', $company_id)
            ->where('state_id', $state_id)
            ->where('downreason', 'regexp', 'OLT');

     $olt = $oltQuery->count();

    $o1 = clone $oltQuery;
    $notstartedolt = $o1->where('status', 'INCOMING')->count();

    $o2 = clone $oltQuery;
    $ongoingolt = $o2->where('status', 'PICKEDUP')->count();

    $o3 = clone $oltQuery;
    $holdolt = $o3->where('status', 'ONHOLD')->count();

    $o4 = clone $oltQuery;
    $completedolt = $o4->where('status', 'COMPLETED')->count();

    $o5 = clone $oltQuery;
    $completedolt_yesterday = $o5->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', '=', Carbon::yesterday())->count();

    $o6 = clone $oltQuery;
    $completedolt_today = $o6->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', '=', Carbon::today())->count();

    $otherQuery = UserRequests::with('masterticket')
            ->where('company_id', $company_id)
            ->where('state_id', $state_id)
            ->where('downreason', 'regexp', 'Others|No Bin Type|GP Shifting|PP Extension|Other');
           
     $other= $otherQuery->count();

         $ot1 = clone $otherQuery;
    $notstartedother = $ot1->where('status', 'INCOMING')->count();

    $ot2 = clone $otherQuery;
    $ongoingother = $ot2->where('status', 'PICKEDUP')->count();

    $ot3 = clone $otherQuery;
    $holdother = $ot3->where('status', 'ONHOLD')->count();

    $ot4 = clone $otherQuery;
    $completedother = $ot4->where('status', 'COMPLETED')->count();

    $ot5 = clone $otherQuery;
    $completedother_yesterday = $ot5->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', '=', Carbon::yesterday())->count();

    $ot6 = clone $otherQuery;
    $completedother_today = $ot6->where('status', 'COMPLETED')
        ->whereDate('user_requests.finished_at', '=', Carbon::today())->count();



    // Return JSON
    return response()->json(array(
        'tickets' => array(
            'total'      => $master_tickets,
            'ongoing'    => $ongoing_tickets,
            'onhold'     => $onhold_tickets,
            'notstarted'  => $notstarted_tickets,
            'completed'  => $completed_tickets,
            'today_completed'  => $today_completed_tickets,
            'yesterday_completed'  => $yesterday_completed_tickets,
            'today_onhold'  => $today_onhold_tickets,
            'yesterday_onhold'  => $yesterday_onhold_tickets,
            'today_ongoing'  => $today_ongoing_tickets,
            'yesterday_ongoing'  => $yesterday_ongoing_tickets,
            'total_auto'    => $auto_tickets,
            'total_manual'    => $manual_tickets,
            'permanent_down' => $permanent_down

        ),
        'unique_lgd' => $uniqueLgdCount,
        'gp_total'   => $totalGp,
        'categories' => array(
            'power' => array(
                'total'      => $power,
                'notstarted' => $notstartedups,
                'ongoing'    => $ongoingups,
                'hold'       => $holdups,
                'completed'  => $completedups,
                'completed_yesterday' => $completedups_yesterday,
                'completed_today'     => $completedups_today,
            ),
            'electronics' => array(
                'total'      => $electronics,
                'notstarted' => $notstartedelectronics,
                'ongoing'    => $ongoingelectronics,
                'hold'       => $holdelectronics,
                'completed'  => $completedelectronics,
                'completed_yesterday' => $completedelectronics_yesterday,
                'completed_today'     => $completedelectronics_today,
            ),
            'solar' => array(
                'total'      => $solar,
                'notstarted' => $notstartedsolar,
                'ongoing'    => $ongoingsolar,
                'hold'       => $holdsolar,
                'completed'  => $completedsolar,
                'completed_yesterday' => $completedsolar_yesterday,
                'completed_today'     => $completedsolar_today,
            ),
            'fiber' => array(
                'total'      => $fiber,
                'notstarted' => $notstartedfiber,
                'ongoing'    => $ongoingfiber,
                'hold'       => $holdfiber,
                'completed'  => $completedfiber,
                'completed_yesterday' => $completedfiber_yesterday,
                'completed_today'     => $completedfiber_today,
            ),
            'battery' => array(
                'total'      => $battery,
                'notstarted' => $notstartedbattery,
                'ongoing'    => $ongoingbattery,
                'hold'       => $holdbattery,
                'completed'  => $completedbattery,
                'completed_yesterday' => $completedbattery_yesterday,
                'completed_today'     => $completedbattery_today,
            ),
            'olt' => array(
                'total'      => $olt,
                'notstarted' => $notstartedolt,
                'ongoing'    => $ongoingolt,
                'hold'       => $holdolt,
                'completed'  => $completedolt,
                'completed_yesterday' => $completedolt_yesterday,
                'completed_today'     => $completedolt_today,
            ),
           'other' => array(
                'total'      => $other,
                'notstarted' => $notstartedother,
                'ongoing'    => $ongoingother,
                'hold'       => $holdother,
                'completed'  => $completedother,
                'completed_yesterday' => $completedother_yesterday,
                'completed_today'     => $completedother_today,
            ),


        )
    ));
}

public function getDashboardData(Request $request)
{

// Get current user
   Session::put('user', Auth::User());
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    $district_id = $user->district_id;
   
    // Master tickets summary
   $masterTicketsquery = DB::table('user_requests')
    ->selectRaw("
        COUNT(*) AS total,
        SUM(IF(status='PICKEDUP',1,0)) AS ongoing,
        SUM(IF(status='ONHOLD',1,0)) AS onhold,
        SUM(IF(status='INCOMING',1,0)) AS notstarted,
        SUM(IF(autoclose='Auto' AND status='INCOMING', 1, 0)) AS auto_notstarted,
        SUM(IF(autoclose='Manual' AND status='INCOMING', 1, 0)) AS manual_notstarted,
        SUM(IF(status='COMPLETED',1,0)) AS completed,

        SUM(IF(status='COMPLETED' AND DATE(finished_at)=CURDATE(),1,0)) AS today_completed,
        SUM(IF(status='COMPLETED' AND DATE(finished_at)=CURDATE() - INTERVAL 1 DAY,1,0)) AS yesterday_completed,

        SUM(IF(status='PICKEDUP' AND DATE(started_at)=CURDATE(),1,0)) AS today_ongoing,
        SUM(IF(status='PICKEDUP' AND DATE(started_at)=CURDATE() - INTERVAL 1 DAY,1,0)) AS yesterday_ongoing,

        SUM(IF(status='ONHOLD' AND DATE(started_at)=CURDATE(),1,0)) AS today_onhold,
        SUM(IF(status='ONHOLD' AND DATE(started_at)=CURDATE() - INTERVAL 1 DAY,1,0)) AS yesterday_onhold,

        SUM(IF(autoclose='Auto',1,0)) AS total_auto,
        SUM(IF(autoclose='Manual',1,0)) AS total_manual,
        SUM( IF( status='ONHOLD' AND (subcategory LIKE '%ETR Fiber Cut%' OR subcategory LIKE '%OLT Down%'), 1, 0 ) ) AS etr_fiber_cut_olt,
        SUM( IF( downreason LIKE '%Permanent Down%' AND status='ONHOLD' AND NOT ( subcategory LIKE '%ETR Fiber Cut%' OR subcategory LIKE '%OLT Down%' ), 1, 0 ) ) AS permanent_down
    ")
    ->where('company_id', $company_id)
    ->where('state_id', $state_id);
  
    if (!empty($district_id)) {
        $masterTicketsquery->where('district_id', $district_id);
    }
   $masterTickets=$masterTicketsquery->first();

    // Categories summary query function
$getCategorySummary = function($pattern, $useRegexp = false) use ($company_id, $state_id) {
    $condition = $useRegexp 
        ? "downreason REGEXP ?" 
        : "downreason LIKE ?";

    $query =  DB::table('user_requests')
        ->selectRaw("
            SUM(IF($condition,1,0)) AS total,
            SUM(IF($condition AND status='INCOMING',1,0)) AS notstarted,
            SUM(IF($condition AND status='PICKEDUP',1,0)) AS ongoing,
            SUM(IF($condition AND status='ONHOLD',1,0)) AS hold,
            SUM(IF($condition AND status='COMPLETED',1,0)) AS completed,
            SUM(IF($condition AND status='COMPLETED' AND DATE(finished_at)=CURDATE(),1,0)) AS completed_today,
            SUM(IF($condition AND status='COMPLETED' AND DATE(finished_at)=CURDATE() - INTERVAL 1 DAY,1,0)) AS completed_yesterday
        ", array_fill(0, 7, $pattern)) // repeat pattern 7 times
        ->where('company_id', $company_id)
        ->where('state_id', $state_id);
          if (!empty($district_id)) {
                $query->where('district_id', $district_id);
            }
        return $query->first();
};


    // $categories = [
    //     'power'       => $getCategorySummary('%Power%'),
    //     'electronics' => $getCategorySummary('ONT|Software/Hardware',true), // Adjust regex if needed
    //     'solar'       => $getCategorySummary('SOLAR|SPV|SLA',true),
    //     'fiber'       => $getCategorySummary('%FIBER%'),
    //     'battery'     => $getCategorySummary('CCU|Battery',true),
    //     'olt'         => $getCategorySummary('%OLT%'),
    //     'other'       => $getCategorySummary('Others|No Bin Type|GP Shifting|PP Extension|Other', true),
    //     'p_down'         => $getCategorySummary('%Permanent Down%'),
    //     'administrative'         => $getCategorySummary('%Administrative%')  ,
  
    //   ];
    $categories = [
    'power'          => $getCategorySummary('%Power%'),
    'electronics'    => $getCategorySummary('ONT|Software/Hardware', true),
    'solar'          => $getCategorySummary('SOLAR|SPV|SLA', true),
    'fiber'          => $getCategorySummary('%FIBER%'),
    'battery'        => $getCategorySummary('CCU|Battery', true),
    'olt'            => $getCategorySummary('%OLT%'),
    'other'          => $getCategorySummary('Others|No Bin Type|GP Shifting|PP Extension|Other', true),
    'administrative' => $getCategorySummary('%Administrative%'),
    'etr_fiber_cut_olt' => DB::table('user_requests')
        ->selectRaw("
            SUM(IF( (
                subcategory LIKE '%ETR Fiber Cut%'
                OR subcategory LIKE '%OLT Down%'
            ), 1, 0)) AS total,
            SUM(IF(status='INCOMING' AND (
                subcategory LIKE '%ETR Fiber Cut%'
                OR subcategory LIKE '%OLT Down%'
            ), 1, 0)) AS notstarted,
            SUM(IF(status='PICKEDUP' AND (
                subcategory LIKE '%ETR Fiber Cut%'
                OR subcategory LIKE '%OLT Down%'
            ), 1, 0)) AS ongoing,
            SUM(IF(status='ONHOLD' AND (
                subcategory LIKE '%ETR Fiber Cut%'
                OR subcategory LIKE '%OLT Down%'
            ), 1, 0)) AS hold,
            SUM(IF(status='COMPLETED' AND (
                subcategory LIKE '%ETR Fiber Cut%'
                OR subcategory LIKE '%OLT Down%'
            ), 1, 0)) AS completed
        ")
        ->where('company_id', $company_id)
        ->where('state_id', $state_id)
        ->when(!empty($district_id), function ($query) use ($district_id) {
            return $query->where('district_id', $district_id);
        })
        ->first(),

    'p_down' => DB::table('user_requests')
        ->selectRaw("
            SUM(IF(
                downreason LIKE '%Permanent Down%'
                AND status='ONHOLD'
                AND NOT (
                    subcategory LIKE '%ETR Fiber Cut%'
                    OR subcategory LIKE '%OLT Down%'
                ),
                1,
                0
            )) AS hold,
            SUM(IF(
                downreason LIKE '%Permanent Down%'
                AND status='INCOMING'
                AND NOT (
                    subcategory LIKE '%ETR Fiber Cut%'
                    OR subcategory LIKE '%OLT Down%'
                ),
                1,
                0
            )) AS notstarted,
            SUM(IF(
                downreason LIKE '%Permanent Down%'
                AND status='PICKEDUP'
                AND NOT (
                    subcategory LIKE '%ETR Fiber Cut%'
                    OR subcategory LIKE '%OLT Down%'
                ),
                1,
                0
            )) AS ongoing,
            SUM(IF(
                downreason LIKE '%Permanent Down%'
                AND status='COMPLETED'
                AND NOT (
                    subcategory LIKE '%ETR Fiber Cut%'
                    OR subcategory LIKE '%OLT Down%'
                ),
                1,
                0
            )) AS completed
        ")
        ->where('company_id', $company_id)
        ->where('state_id', $state_id)
        ->when(!empty($district_id), function ($query) use ($district_id) {
            return $query->where('district_id', $district_id);
        })
        ->first(),
];

   
    // Unique LGD count

  $uniqueLgdCount = DB::table('gp_list')
    ->where('state_id',$state_id)
    // District condition (optional)
    ->when(!empty($district_id), function ($query) use ($district_id) {
        return $query->where('district_id', $district_id);
    })
    ->whereIn('lgd_code', function ($query) {
        return $query->select(DB::raw('DISTINCT master_tickets.lgd_code'))
            ->from('master_tickets')
            ->join('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
            ->whereIn('user_requests.status', ['INCOMING', 'ONHOLD', 'SCHEDULED','PICKEDUP'])
            ->where('user_requests.autoclose', 'Auto');
    })
    ->count();


    // GP count
    $totalGpquery = DB::table('gp_list')
        ->where('company_id', $company_id)
        ->where('state_id', $state_id)
        ->where('type', "GP");
         if (!empty($district_id)) {
            $totalGpquery->where('district_id', $district_id);
        }
       $totalGp =   $totalGpquery->count();   

    return response()->json([
        'tickets' => $masterTickets,
        'unique_lgd' => $uniqueLgdCount,
        'gp_total' => $totalGp,
        'categories' => $categories
    ]);


 }


public function getTeamStatus(Request $request)
{
    Session::put('user', Auth::User());
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id   = $user->state_id;
    $district_id = $user->district_id;

    $inputFromDate = request()->input('from_date');
    $inputToDate = request()->input('to_date');

    $fromDate = $inputFromDate !== null ? $inputFromDate : date('Y-m-d'); // Default to today's date
    $toDate   = $inputToDate !== null ? $inputToDate : date('Y-m-d');     // Default to today's date

    // Build your existing query
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

    $teamsquery = DB::table('providers')
        ->join('zonal_managers','zonal_managers.id','providers.zone_id')
        ->join('teams','teams.id','providers.team_id')
        ->leftJoin('user_requests','user_requests.provider_id','providers.id')
        ->leftJoin('master_tickets','user_requests.booking_id','master_tickets.ticketid')
        ->leftJoin('districts','districts.id','providers.district_id')
        ->where('providers.zone_id', '!=', 0)
        ->where('providers.company_id', $company_id)
        ->where('providers.state_id', $state_id)
        ->whereIn('providers.type', [2]);
       
        if (!empty($district_id)) {
            $teamsquery->where('providers.district_id', $district_id);
        }
       
        $teams = $teamsquery->groupBy('providers.zone_id','providers.team_id')
                    ->select(
                        'teams.id as team_id',
                        'teams.name as team_name',
                        DB::raw('COUNT(CASE WHEN DATE(master_tickets.downdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as total_tickets'),
                        DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as completed_tickets'),
                        DB::raw('COUNT(CASE WHEN user_requests.status = "ONHOLD" AND DATE(user_requests.started_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as hold_tickets'),
                        DB::raw('COUNT(CASE WHEN user_requests.status = "PICKEDUP" AND DATE(user_requests.started_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as pickup_tickets'),
                        DB::raw($pendingTicketsQuery),
                        DB::raw($pendingTicketsMorethen24),
                        DB::raw('COUNT(CASE WHEN user_requests.status = "PICKEDUP" AND DATE(user_requests.started_at) < "' . $fromDate . '" THEN user_requests.id END) as old_ongoing_tickets')
                    )->get();

    // --- Calculate summary stats ---
    $totalTeams      = $teams->count();
    $workingTeams    = 0;
    $notStartedTeams = 0;
    $completedTeams  = 0;
    $noTicketTeams   = 0;
    $onlyHoldTeams   = 0;
    $notStartedMoreThan2AndOngoing0 = 0; // <-- New counter
    $teamsWorkingOnOldTickets = 0;

    foreach ($teams as $team) {
        if ($team->pickup_tickets > 0) {
            $workingTeams++;
        }

        if ($team->pending_tickets > 0 && 
            $team->pickup_tickets == 0 && 
            $team->hold_tickets == 0 && 
            $team->completed_tickets == 0
        ) {
            $notStartedTeams++;
        }

        if ($team->completed_tickets > 0) {
            $completedTeams++;
        }

        if (
            $team->pending_tickets == 0 &&
            $team->pickup_tickets == 0 &&
            $team->hold_tickets == 0 &&
            $team->completed_tickets == 0
          ) {
            $noTicketTeams++;
        }

        if (
            $team->hold_tickets > 0 &&
            $team->pending_tickets == 0 &&
            $team->pickup_tickets == 0 &&
            $team->completed_tickets == 0
        ) {
            $onlyHoldTeams++;
        }

        // NEW CONDITION: more than 2 not started tickets 
        if ($team->pending_tickets > 2 ) {
            $notStartedMoreThan2AndOngoing0++;
        }

       // Count teams with old ongoing tickets
       if ($team->old_ongoing_tickets > 0) {
        $teamsWorkingOnOldTickets++;
       }

    }

    return response()->json([
        'from_date'       => $fromDate,
        'to_date'         => $toDate,
        'total_teams'     => $totalTeams,
        'working_teams'   => $workingTeams,
        'not_started'     => $notStartedTeams,
        'completed_teams' => $completedTeams,
        'no_ticket_teams' => $noTicketTeams,
        'only_hold_teams' => $onlyHoldTeams,
        'not_started_morethan2' => $notStartedMoreThan2AndOngoing0, // ? New value
        'teams_working_on_old_tickets' => $teamsWorkingOnOldTickets,
    ]);
}



public function getTeamStatus_old(Request $request)
    {
        $user = Session::get('user');
        $company_id = $user->company_id;
        $state_id   = $user->state_id;


        $inputFromDate = request()->input('from_date');
       $inputToDate = request()->input('to_date');
   
        $fromDate = $inputFromDate !== null ? $inputFromDate : date('Y-m-d'); // Default to today's date
      $toDate = $inputToDate !== null ? $inputToDate : date('Y-m-d');       // Default to today's date


        // Build your existing query
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

    $teams = DB::table('providers')
        ->join('zonal_managers','zonal_managers.id','providers.zone_id')
        ->join('teams','teams.id','providers.team_id')
        ->leftJoin('user_requests','user_requests.provider_id','providers.id')
        ->leftJoin('master_tickets','user_requests.booking_id','master_tickets.ticketid')
        ->leftJoin('districts','districts.id','providers.district_id')
        ->where('providers.zone_id', '!=', 0)
        ->where('providers.company_id', $company_id)
        ->where('providers.state_id', $state_id)
        ->groupBy('providers.zone_id','providers.team_id')
        ->select(
            'teams.id as team_id',
            'teams.name as team_name',
            DB::raw('COUNT(CASE WHEN DATE(master_tickets.downdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as total_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "COMPLETED" AND user_requests.autoclose= "Manual" AND DATE(user_requests.finished_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as completed_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "ONHOLD" AND DATE(user_requests.started_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as hold_tickets'),
            DB::raw('COUNT(CASE WHEN user_requests.status = "PICKEDUP" AND DATE(user_requests.started_at) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" THEN user_requests.id END) as pickup_tickets'),
            DB::raw($pendingTicketsQuery),
            DB::raw($pendingTicketsMorethen24)
        )
        ->get();

    // --- Calculate summary stats ---
    $totalTeams      = $teams->count();
    $workingTeams    = 0;
    $notStartedTeams = 0;
    $completedTeams  = 0;
    $noTicketTeams   = 0;
    $onlyHoldTeams   = 0;

    foreach ($teams as $team) {
        if ($team->pickup_tickets > 0) {
            $workingTeams++;
        }

        if ($team->pending_tickets > 0 && 
            $team->pickup_tickets == 0 && 
            $team->hold_tickets == 0 && 
            $team->completed_tickets == 0
        ) {
            $notStartedTeams++;
        }

        if ($team->completed_tickets > 0) {
            $completedTeams++;
        }

        if (
            $team->pending_tickets == 0 &&
            $team->pickup_tickets == 0 &&
            $team->hold_tickets == 0 &&
            $team->completed_tickets == 0
          ) {
            $noTicketTeams++;
        }

        if (
        $team->hold_tickets > 0 &&
        $team->pending_tickets == 0 &&
        $team->pickup_tickets == 0 &&
        $team->completed_tickets == 0
    ) {
        $onlyHoldTeams++;
    }

    }

    return response()->json([
        'from_date'       => $fromDate,
        'to_date'         => $toDate,
        'total_teams'     => $totalTeams,
        'working_teams'   => $workingTeams,
        'not_started'     => $notStartedTeams,
        'completed_teams' => $completedTeams,
        'no_ticket_teams' => $noTicketTeams,
        'only_hold_teams'  => $onlyHoldTeams,
    ]);
}



public function dashboardMap_old(Request $request)
{
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id   = $user->state_id;

    try {
        $today = Carbon::today()->toDateString(); // e.g. 2025-09-01

        // Subquery: latest tracking ID per provider for today only
        $latestTrackingIds = DB::table('provider_tracking as pt1')
            ->selectRaw('MAX(pt1.id) as id')
            ->join(DB::raw('(SELECT provider_id, MAX(created_at) as max_created
                              FROM provider_tracking
                              WHERE DATE(created_at) = ?
                              GROUP BY provider_id) pt2'), function ($join) {
                $join->on('pt1.provider_id', '=', 'pt2.provider_id')
                     ->on('pt1.created_at', '=', 'pt2.max_created');
            })
            ->setBindings([$today])
            ->groupBy('pt1.provider_id')   // ensure one row per provider
            ->pluck('id');

        // Fetch providers + latest tracking for today
        $providers = Provider::where('company_id', $company_id)
            ->where('state_id', $state_id)
            ->with('service')
            ->join('provider_tracking', 'provider_tracking.provider_id', '=', 'providers.id')
            ->whereIn('provider_tracking.id', $latestTrackingIds)
            ->get([
                'providers.id',
                'providers.first_name',
                'providers.last_name',
                'providers.type',
                'provider_tracking.latitude',
                'provider_tracking.longitude',
                'provider_tracking.created_at',
            ]);

        // Add status field
        foreach ($providers as $provider) {
            $provider->status = 'user';
        }

        return response()->json($providers);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function dashboardMap(Request $request)
{
    // Get the logged-in user from session
    Session::put('user', Auth::User());
    $user = Session::get('user');

    //if (!$user) {
    //    return response()->json(['error' => 'User not authenticated'], 401);
    //}

    $company_id = $user->company_id;
    $state_id   = $user->state_id;
    $district_id = $user->district_id;


    try {
        // Today range for index-friendly queries
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd   = Carbon::today()->endOfDay();

        // Fetch latest attendance for today using ID for speed
        $latestAttendance = DB::table('attendance as a')
            ->select('a.provider_id', 'a.status', 'a.address', 'a.created_at')
            ->join(DB::raw('(SELECT provider_id, MAX(id) as max_id
                              FROM attendance
                              WHERE created_at BETWEEN ? AND ?
                              GROUP BY provider_id) sub'), function ($join) {
                $join->on('a.id', '=', 'sub.max_id');
            })
            ->setBindings([$todayStart, $todayEnd])
            ->get()
            ->keyBy('provider_id');

        $attendanceProviderIds = $latestAttendance->keys();

        if (empty($attendanceProviderIds) || count($attendanceProviderIds) == 0) {
            return response()->json([]); // No attendance today
        }

        // Fetch latest provider_tracking IDs for those providers today
        $latestTrackingIds = DB::table('provider_tracking')
            ->selectRaw('MAX(id) as id')
            ->whereIn('provider_id', $attendanceProviderIds)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->groupBy('provider_id')
            ->pluck('id');

        if (empty($latestTrackingIds) || count($latestTrackingIds) == 0) {
            return response()->json([]); // No tracking data today
        }

        // Fetch providers with tracking data
        $providersquery = Provider::where('company_id', $company_id)
            ->where('state_id', $state_id)
            ->join('provider_tracking', 'provider_tracking.provider_id', '=', 'providers.id')
            ->whereIn('provider_tracking.id', $latestTrackingIds);
             if (!empty($district_id)) {
            $providersquery->where('providers.district_id', $district_id);
        }
          
        $providers = $providersquery->get(array(
                'providers.id',
                'providers.first_name',
                'providers.last_name',
                'providers.type',
                'provider_tracking.latitude',
                'provider_tracking.longitude',
                'provider_tracking.created_at as tracking_time',
            ));

        // Attach attendance info (PHP 5 compatible)
        foreach ($providers as $provider) {
            $attendance = isset($latestAttendance[$provider->id]) ? $latestAttendance[$provider->id] : null;

            $provider->status = isset($attendance->status) ? $attendance->status : null;
            $provider->address = isset($attendance->address) ? $attendance->address : null;
            $provider->attendance_time = isset($attendance->created_at) ? $attendance->created_at : null;
        }

        return response()->json($providers);

    } catch (Exception $e) {
        return response()->json(array('error' => $e->getMessage()), 500);
    }
}


public function dashboard_workforce(Request $request)
{
return view('admin.reports.dashboard');


}


public function dashboardMap_test(Request $request)
{
return view('admin.reports.dashboard');


}


  public function AttendanceDashboard(Request $request){
    $user = Session::get('user');
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


    // $districts= DB::table('districts')->get();
    // $blocks= DB::table('blocks')->get();
    // $zonals = DB::table('zonal_managers')->get();


    $providersQuery = DB::table('providers')
        ->select(
            'providers.id',
            'providers.first_name',
            'providers.last_name',
            'providers.mobile',
            'providers.latitude',
            'providers.longitude',
            'providers.district_id',
            'providers.type',
            'providers.version',
            'districts.name as district_name',
            'zonal_managers.Name as zone_name',
            'attendance.status as attendance_status',
            'attendance.address',
            'attendance.offaddress',
            'attendance.start_time as check_in',
            'attendance.end_time as check_out',
            DB::raw('TIMESTAMPDIFF(HOUR, attendance.start_time, attendance.end_time) as duration')
        )
        ->leftJoin('attendance','providers.id','=','attendance.provider_id')
        ->join('districts','providers.district_id','=','districts.id')
        ->leftJoin('zonal_managers','providers.zone_id','=','zonal_managers.id')
        ->orderBy('attendance.start_time','desc')
        ->where('providers.company_id',$company_id)
        ->where('providers.state_id',$state_id)
        ->where('providers.status','approved');
        if (!empty($district_id)) {
            $providersQuery->where('providers.district_id', $district_id);
        }
        

    // filters
    if ($request->has('district_id') && !empty($request->district_id)) {
        $providersQuery->where('providers.district_id', $request->district_id);
        $blocks = DB::table('blocks')->where('district_id', $request->district_id)->get();
    }
    if ($request->has('zone_id') && !empty($request->zone_id)) {
      
        $providersQuery->where('providers.zone_id',(int)$request->zone_id);

    }

    if ($request->has('block_id') && !empty($request->block_id)) {
        $providersQuery->where('providers.block_id', $request->block_id);
    }
   

    if ($request->has('from_date') && $request->has('to_date') && !empty($request->from_date) && !empty($request->to_date)) {
            $providersQuery->whereBetween(DB::raw('DATE(attendance.start_time)'), [$request->from_date, $request->to_date]);
    }

   if ($request->has('role') && !empty($request->role)) {
        $providersQuery->where('providers.type', $request->role);
    }

    if ($request->has('date_range') && !empty($request->date_range)) {

            switch ($request->date_range) {
            case 'today':
                $providersQuery->whereDate('attendance.start_time', Carbon::today());
                break;
            case 'yesterday':
                $providersQuery->whereDate('attendance.start_time', Carbon::yesterday());
                break;
            case 'week':
                $providersQuery->whereBetween('attendance.start_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $providersQuery->whereBetween('attendance.start_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                break;
        }
    }
    if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $providersQuery->where(function ($q) use ($search) {
            $q->where('providers.first_name', 'like', "%{$search}%")
              ->orWhere('providers.last_name', 'like', "%{$search}%")
              ->orWhere('providers.mobile', 'like', "%{$search}%");
        });
    }
    
    $totalsQuery = clone $providersQuery;
    $totals = $totalsQuery->select('providers.type as role_id', 'providers.zone_id', DB::raw('COUNT(DISTINCT providers.id) as total'))
                ->groupBy('providers.type', 'providers.zone_id')
                ->get();



    $totalStaff = $providersQuery->distinct('providers.id')->count('providers.id');

    $presentQuery = clone $providersQuery;

    if ($request->has('from_date') && $request->has('to_date') && !empty($request->from_date) && !empty($request->to_date)) {
        $presentQuery->whereBetween(DB::raw('DATE(attendance.start_time)'), [$request->from_date, $request->to_date]);
    } elseif ($request->has('date_range') && !empty($request->date_range)) {
        switch ($request->date_range) {
            case 'today':
                $presentQuery->whereDate('attendance.start_time', Carbon::today());
                break;
            case 'yesterday':
                $presentQuery->whereDate('attendance.start_time', Carbon::yesterday());
                break;
            case 'week':
                $presentQuery->whereBetween('attendance.start_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $presentQuery->whereBetween('attendance.start_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                break;
        }
    }else{
      
    $presentQuery->whereDate('attendance.start_time', Carbon::today());
       
    }

    $present = $presentQuery->distinct('providers.id')->count('providers.id');

   
    $absent = $totalStaff - $present;

   
    $lateCheckinsQuery = clone $presentQuery;
    $lateCheckins = $lateCheckinsQuery->whereTime('attendance.start_time', '>', '09:30:00')->count();

    
    $avgDurationQuery = clone $presentQuery;
    $avgDuration = $avgDurationQuery
        ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as avg_minutes'))
        ->value('avg_minutes');

    $avgDurationFormatted = $avgDuration
        ? floor($avgDuration / 60) . "h " . ($avgDuration % 60) . "m"
        : "0h 0m";



    $providers = $providersQuery->paginate(10)->appends($request->all());

    //    $liveOnMap = Provider::where('latitude', '!=', 0)
    //     ->where('longitude', '!=', 0)
    //     ->whereHas('service', function ($q) {
    //         $q->where('status', 'active');
    //     })
    //     ->count();

        // total staff grouped by role & zone
    $presentsQuery = DB::table('attendance')
        ->join('providers', 'attendance.provider_id', '=', 'providers.id');

    // apply same filters
    if ($request->has('district_id') && !empty($request->district_id)) {
        $presentsQuery->where('providers.district_id', $request->district_id);
    }
    if ($request->has('block_id') && !empty($request->block_id)) {
        $presentsQuery->where('providers.block_id', $request->block_id);
    }
    if ($request->has('zone_id') && !empty($request->zone_id)) {
        $presentsQuery->where('providers.zone_id', (int) $request->zone_id);
    }
   if ($request->has('role') && !empty($request->role)) {
        $presentsQuery->where('providers.type', $request->role);
    }

    // handle date_range OR from-to
    if ($request->has('date_range') && !empty($request->date_range)) {

        switch ($request->date_range) {
            case 'today':
                $presentsQuery->whereDate('attendance.start_time', Carbon::today());
                break;
            case 'yesterday':
                $presentsQuery->whereDate('attendance.start_time', Carbon::yesterday());
                break;
            case 'week':
                $presentsQuery->whereBetween('attendance.start_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $presentsQuery->whereBetween('attendance.start_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                break;
        }
    } elseif ($request->has('from_date') && $request->has('to_date') && !empty($request->from_date) && !empty($request->to_date)) {

        $presentsQuery->whereBetween(DB::raw('DATE(attendance.start_time)'), [$request->from_date, $request->to_date]);
    }else{
     
        $presentsQuery->whereDate('attendance.start_time', Carbon::today());
    }

    $Zonepresents = $presentsQuery
        ->select('providers.type as role_id', 'providers.zone_id', DB::raw('COUNT(DISTINCT providers.id) as present'))
        ->groupBy('providers.type', 'providers.zone_id')
        ->get();


    $matrix = [];

    // foreach ($totals as $t) {
    //     $matrix[$t->role_id][$t->zone_id]['total'] = $t->total;
    //     $matrix[$t->role_id][$t->zone_id]['present'] = 0;
    // }
    // foreach ($Zonepresents as $p) {
    //     $matrix[$p->role_id][$p->zone_id]['present'] = $p->present;
    // }
    foreach ($totals as $t) {
        $matrix[$t->role_id][$t->zone_id] = [
            'total' => $t->total,
            'present' => 0,
        ];
    }

    foreach ($Zonepresents as $p) {
        if (!isset($matrix[$p->role_id][$p->zone_id])) {
            // Zone not found in totals → create it with total=0 and present=0
            $matrix[$p->role_id][$p->zone_id] = [
                'total' => 0,
                'present' => 0,
            ];
        } else {
            // Only update present if total > 0
            $matrix[$p->role_id][$p->zone_id]['present'] = 
                $matrix[$p->role_id][$p->zone_id]['total'] > 0 
                ? $p->present 
                : 0;
        }
    }
    // dd($totals);

    return view('admin.AttendanceDashboard.Dashboard', compact(
        'districts',
        'blocks',
        'zonals',
        'providers',
        'totalStaff',
        'present',
        'absent',
         'lateCheckins',
        'avgDurationFormatted',
        //  'liveOnMap',
        "matrix"
    ));

}
private function buildAttendanceQuery(Request $request)
{
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id   = $user->state_id;
    $district_id = $user->district_id;
    $leaveSubSql = '
    (
            SELECT
                provider_id,
                id AS leave_id,
                type AS leave_type,
                reason AS leave_reason,
                start_date
            FROM leaves
            WHERE status = "approved"
            AND CURDATE() BETWEEN start_date AND end_date
        ) AS leaves_today
    ';
    $lastAttendanceSubSql = '
    (
        SELECT 
            provider_id,
            MAX(created_at) AS last_attendance_date
        FROM attendance
        GROUP BY provider_id
    ) AS last_attendance
    ';


    $providersQuery = DB::table('providers')
        ->select(
            'providers.id',
            DB::raw('DATE(providers.created_at) as date'),
            'providers.first_name',
            'providers.last_name',
            'providers.mobile',
            'providers.latitude',
            'providers.longitude',
            'providers.district_id',
            'providers.type',
            'providers.version',
            'districts.name as district_name',
            'zonal_managers.Name as zone_name',
            'attendance.status as attendance_status',
            'attendance.address',
            'attendance.offaddress',
            'attendance.created_at as check_in',
            'attendance.updated_at as check_out',
            'attendance.online_image',
            DB::raw('TIMESTAMPDIFF(HOUR, attendance.created_at, attendance.updated_at) as duration'),
            'leaves_today.leave_id',
            'leaves_today.leave_type',
            'leaves_today.leave_reason',
            'leaves_today.start_date',
            DB::raw('IF(leaves_today.leave_id IS NOT NULL,
                CONCAT("On (", leaves_today.leave_type, ")"),
                NULL
            ) as leave_status'),
             DB::raw('
                COALESCE(
                    DATE(attendance.created_at),
                    DATE(last_attendance.last_attendance_date)
                ) AS display_attendance_date
            ')

            )
        ->leftJoin(DB::raw($leaveSubSql), function ($join) {
                $join->on('leaves_today.provider_id', '=', 'providers.id');
            })
        ->leftJoin('attendance', function($join) use ($request) {
            $join->on('providers.id','=','attendance.provider_id');

                if ($request->has('from_date') && $request->has('to_date') && !empty($request->from_date) && !empty($request->to_date)) {
                    $join->whereBetween(DB::raw('DATE(attendance.created_at)'), [$request->from_date, $request->to_date]);
                } elseif ($request->has('date_range') && !empty($request->date_range)) {
                    switch ($request->date_range) {
                        case 'today':
                            $join->whereDate('attendance.created_at', Carbon::today());
                            break;
                        case 'yesterday':
                            $join->whereDate('attendance.created_at', Carbon::yesterday());
                            break;
                        case 'week':
                            $join->whereBetween('attendance.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                            break;
                        case 'month':
                            $join->whereBetween('attendance.created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                            break;
                    }
                } else {
                    $join->whereDate('attendance.created_at', Carbon::today());
                }
        })
        ->leftJoin(DB::raw($lastAttendanceSubSql), function ($join) {
            $join->on('last_attendance.provider_id', '=', 'providers.id')
                ->whereNull('attendance.created_at');
        })

        ->join('districts','providers.district_id','=','districts.id')
        ->leftJoin('zonal_managers','providers.zone_id','=','zonal_managers.id')
        ->where('providers.company_id',$company_id)
        ->where('providers.state_id',$state_id)
        ->where('providers.status','approved');
      

    if (!empty($district_id)) {
        $providersQuery->where('providers.district_id', $district_id);
    }

    // district filter
    if ($request->district_id) {
        $providersQuery->where('providers.district_id', $request->district_id);
    }

    // block filter
    if ($request->block_id) {
        $providersQuery->where('providers.block_id', $request->block_id);
    }

    // zone filter
    if ($request->zone_id) {
        $providersQuery->where('providers.zone_id',(int)$request->zone_id);
    }

    // role filter
    if ($request->role) {
        $providersQuery->where('providers.type', $request->role);
    }

    // search filter
    if ($request->search) {
        $search = $request->search;
        $providersQuery->where(function ($q) use ($search) {
            $q->where('providers.first_name', 'like', "%{$search}%")
              ->orWhere('providers.last_name', 'like', "%{$search}%")
              ->orWhere('providers.mobile', 'like', "%{$search}%")
              ->orWhere('zonal_managers.Name', 'like', "%{$search}%")
              ->orWhere('districts.name', 'like', "%{$search}%")
              ->orWhere('attendance.status', 'like', "%{$search}%")
              ->orWhere('providers.status', 'like', "%{$search}%")
              ->orWhere('leaves_today.leave_type', 'like', "%{$search}%")
              ->orWhere(DB::raw('DATE_FORMAT(attendance.created_at, "%h:%i %p")'), 'like', "%{$search}%")
              ->orWhere(DB::raw('DATE_FORMAT(attendance.updated_at, "%h:%i %p")'), 'like', "%{$search}%");
        });
    }

    // status filter
    if ($request->status) {
        switch ($request->status) {
            case 'present':
                $providersQuery->whereNotNull('attendance.created_at')
                                ->whereNull('leaves_today.leave_id'); 
                break;
            case 'absent':
                $providersQuery->where(function ($q) {
                            $q->whereNull('attendance.created_at')
                            ->orWhereNotNull('leaves_today.leave_id');
                        });
                break;
            case 'late':
                $providersQuery->whereTime('attendance.created_at', '>', '09:30:00')
                               ->whereNull('leaves_today.leave_id'); 
                break;
        }
    }

    return $providersQuery->orderBy('attendance.created_at','desc');
}

public function PatrollerAttendanceList(Request $request){
    $user = Session::get('user');
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
   

    if ($request->has('district_id') && !empty($request->district_id)) {
      
        $blockQuery->where('district_id', $request->district_id);
    }
     $blocks = $blockQuery->get();

    $providers = $this->buildAttendanceQuery($request)
                      ->paginate(10)
                      ->appends($request->all());
    return view('admin.AttendanceDashboard.PatrollerAttendance', compact(
        'districts',
        'blocks',
        'zonals',
        'providers'
       
    ));
}



public function exportAttendance(Request $request)
{
    $fromDate = $request->from_date;
    $toDate   = $request->to_date;
    $dateRange = $request->date_range;
    if($request->has('date_range') && !empty($request->date_range) && 
        ($request->has('from_date') && $request->has('to_date') && !empty($request->from_date) && !empty($request->to_date))) {
           $fromDate=  Carbon::today()->format('Y-m-d');
           $toDate=  Carbon::today()->format('Y-m-d');

    }
      

    $providersQuery = $this->buildAttendanceQuery($request);
 
    $providers = $providersQuery->get();
    $fileName = 'attendance.xlsx';
    foreach ($providers as $provider) {
        $attendanceDetails = $this->getRecentRequestsWithDetails(
            $provider->id,
            $fromDate ?? $this->getDateRangeStart($dateRange),
            $toDate   ?? $this->getDateRangeEnd($dateRange)
        );
        
        if ($attendanceDetails->isNotEmpty()) {
            $detail = $attendanceDetails->first();
            $provider->total_tickets     = $detail->total_tickets;
            $provider->completed_tickets = $detail->completed_tickets;
            $provider->total_distance    = $detail->total_distance;
            $provider->images            = $detail->images;
        } else {
            $provider->total_tickets     = 0;
            $provider->completed_tickets = 0;
            $provider->total_distance    = 0;
            $provider->images            = 0;
        }
    }
    // Generate Excel as string
    $excelContent = Excel::create('attendance', function($excel) use ($providers) {
        $excel->sheet('Sheet1', function($sheet) use ($providers) {
            $data = [];
            // Headings
            $data[] = [
                'Name','Mobile Number','District','Zone','Check In','Check Out',
                'Duration','Status','Version','Tickets','Completed','Distance'
            ];

            foreach($providers as $p){
                $name = $p->first_name . ' ' . $p->last_name;
                $checkIn = $p->check_in ? \Carbon\Carbon::parse($p->check_in)->format('h:i A') : '—';
                $checkOut = $p->attendance_status == 'active' ? '-' : ($p->check_out ? \Carbon\Carbon::parse($p->check_out)->format('h:i A') : '—');
                $status = $p->attendance_status == 'active' ? 'Online' : 'Offline';
                $startTime = $p->check_in ? \Carbon\Carbon::parse($p->check_in) : null;
                $finishTime = $p->attendance_status == 'active' ? \Carbon\Carbon::now() : ($p->check_out ? \Carbon\Carbon::parse($p->check_out) : \Carbon\Carbon::now());
                $duration = $startTime ? gmdate('H:i:s', $finishTime->diffInSeconds($startTime)) : '—';
                $distance = round($p->total_distance, 2);

                $data[] = [
                    $name,
                    $p->mobile,
                    $p->district_name,
                    $p->zone_name,
                    $checkIn,
                    $checkOut,
                    $duration,
                    $status,
                    $p->version,
                    $p->total_tickets,
                    $p->completed_tickets,
                    $distance,
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


public function exportProviders(Request $request)
{
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    $district_id = $user->district_id;

    // Build same query as your index
    $providersQuery = Provider::join('zonal_managers', 'providers.zone_id', '=', 'zonal_managers.id')
        ->join('districts', 'providers.district_id', '=', 'districts.id')
        ->select(
            'providers.*',
            'zonal_managers.Name as zone_name',
            'districts.name as district_name'
        )
        ->where('providers.company_id', $company_id)
        ->where('providers.state_id', $state_id);
         if (!empty($district_id)) {
                $providersQuery->where('providers.district_id', $district_id);
         }

    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $providersQuery->where(function ($q) use ($search) {
            $q->where('providers.first_name', 'like', "%{$search}%")
              ->orWhere('providers.last_name', 'like', "%{$search}%")
              ->orWhere('providers.mobile', 'like', "%{$search}%")
              ->orWhere('providers.email', 'like', "%{$search}%");
        });
    }

    if ($request->has('zone_id') && $request->zone_id != '') {
        $providersQuery->where('providers.zone_id', $request->zone_id);
    }

    if ($request->has('district_id') && $request->district_id != '') {
        $providersQuery->where('providers.district_id', $request->district_id);
    }

    if ($request->has('role') && $request->role != '') {
        $providersQuery->where('providers.type', $request->role);
    }

    $providers = $providersQuery->orderBy('providers.id', 'desc')->get();

    // File name
    $fileName = 'providers.xlsx';

    // Generate Excel file
    $excelContent = Excel::create('providers', function($excel) use ($providers) {
        $excel->sheet('Providers', function($sheet) use ($providers) {

           $roles = [
                1 => 'OFC',
                2 => 'FRT',
                5 => 'Patroller',
                3 => 'Zonal incharge',
                4 => 'District incharge'
            ];

            $data = [];
            // Header row
            $data[] = [
                'Name', 'Mobile', 'Email','Date of joining','Zone', 'District', 'Role', 'Version', 'Status'
            ];

            foreach ($providers as $p) {
                $status = ($p->attendance_status == 'active') ? 'Online' : 'Offline';
                 $role = isset($roles[$p->type]) ? $roles[$p->type] : 'N/A';

                $data[] = [
                    $p->first_name . ' ' . $p->last_name,
                    $p->mobile,
                    $p->email,
                    $p->joiningdate,
                    $p->zone_name ?? 'N/A',
                    $p->district_name ?? 'N/A',
                    $role,
                    $p->version ?? '-',
                    $status
                    
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

private function getDateRangeStart($range)
{
    switch($range) {
        case 'today': return Carbon::today()->format('Y-m-d');
        case 'yesterday': return Carbon::yesterday()->format('Y-m-d');
        case 'week': return Carbon::now()->startOfWeek()->format('Y-m-d');
        case 'month': return Carbon::now()->startOfMonth()->format('Y-m-d');
        default: return Carbon::today()->format('Y-m-d');
    }
}

private function getDateRangeEnd($range)
{
    switch($range) {
        case 'today': return Carbon::today()->format('Y-m-d');
        case 'yesterday': return Carbon::yesterday()->format('Y-m-d');
        case 'week': return Carbon::now()->endOfWeek()->format('Y-m-d');
        case 'month': return Carbon::now()->endOfMonth()->format('Y-m-d');
        default: return Carbon::today()->format('Y-m-d');
    }
}
public function StaffView($id, Request $request){
    // Basic provider info
     $provider = DB::table('providers')
         ->leftJoin('districts','providers.district_id','=','districts.id')
         ->leftJoin('zonal_managers','providers.zone_id','=','zonal_managers.id')
           ->leftJoin('attendance', function($join) use ($request) {
            $join->on('providers.id','=','attendance.provider_id');
            $join->whereDate('attendance.created_at', Carbon::today());
        })
         ->select(
             'providers.*',
             'districts.name as district_name',
             'zonal_managers.Name as zone_name',
             'attendance.online_image'
             
         )
         ->where('providers.id', $id)
         ->first();
    $filter = $request->get('filter', 'monthyear'); 
    $startDate = null;
    $endDate = null;
    $month = $request->get('month', Carbon::now()->month);
    $year  = $request->get('year', Carbon::now()->year);
    $monthyear = $request->get('monthyear'); 
    if ($monthyear) {
        [$year, $month] = explode('-', $monthyear);
    }else if($request->get('month') && $request->get('year')){
        $monthyear = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        $filter='monthyear';
    }
    if ($filter === 'today') {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->endOfDay();
    } elseif ($filter === 'yesterday') {
        $startDate = Carbon::yesterday();
        $endDate = Carbon::yesterday()->endOfDay();
    } elseif ($filter === 'last7') {
        $startDate = Carbon::today()->subDays(6); 
        $endDate = Carbon::today()->endOfDay();
    }elseif ($filter === 'custom') {
        $startDate = Carbon::parse($request->get('start_date'));
        $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
    }

    // Get attendance data
    $attendances = DB::table('attendance')
         ->where('provider_id', $id)
         ->whereMonth('created_at', $month)
         ->whereYear('created_at', $year)
         ->get();

    // Map days → present/absent  online/offline
     $attendanceMap = [];
     foreach ($attendances as $a) {
         $day = Carbon::parse($a->created_at)->day;

         $attendanceMap[$day] = [
             'status' => $a->status,   // online / offline
             'present' => true         // has record → present
         ];
     }

    // Get user requests data
   
    $totalRequests = DB::table('user_requests')->where('provider_id', $id)->count();
    
    
    $requestsQuery = DB::table('user_requests')->where('provider_id', $id)->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year);
    // Calculate request statistics
    $requestStats = [
        'total'   => (clone $requestsQuery)->count(),

        'completed' => (clone $requestsQuery)
            ->where('status', 'COMPLETED')
            ->count(),

        'cancelled' => (clone $requestsQuery)
            ->where('status', 'CANCELLED')
            ->count(),

        'pending'   => (clone $requestsQuery)
            ->whereIn('status', ['SEARCHING', 'ACCEPTED', 'STARTED','INCOMING','PICKEDUP','ONHOLD','SCHEDULED'])
            ->count(),
    ];

    $attendanceQuery = DB::table('attendance as a')
    ->where('a.provider_id', $id);

    $recentRequests = DB::table('attendance as a')
        ->where('a.provider_id', $id)
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween(DB::raw('DATE(a.created_at)'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        })
        ->when(!$startDate || !$endDate, function ($query) use ($month, $year) {
            return $query->whereMonth('a.created_at', $month)->whereYear('a.created_at', $year);
        })
        ->select(
            'a.id as attendance_id',
            'a.provider_id as provider_id',
            DB::raw('DATE(a.created_at) as attendance_date'),
            'a.created_at as check_in',
            'a.updated_at as check_out',
            'a.status as onlinestatus'
        )
        ->orderBy('a.created_at', 'desc')
        ->get()
        ->map(function ($attendance) use ($id) {

        $trackingHistory = DB::table('provider_tracking_histories')
                            ->where('provider_id', $id)
                            ->whereDate('created_at', $attendance->attendance_date)
                            ->orderBy('created_at', 'asc')
                            ->get();

       $trackingPoints = [];
        foreach ($trackingHistory as $history) {
            if ($history->latlng) {
                $points = json_decode($history->latlng, true);
                if (is_array($points)) {
                    foreach ($points as $point) {
                        if (isset($point['latitude'], $point['longitude'])) {
                             $trackingPoints[] = [
                            'latitude' => (float)$point['latitude'],
                            'longitude' => (float)$point['longitude'],
                            'datetime' => $point['datetime'] ? $point['datetime'] : $history->created_at
                        ];
                        }
                    }
                }
            }
        }
            // --- Sort by datetime globally ---
        usort($trackingPoints, function($a, $b) {
            return strtotime($a['datetime']) <=> strtotime($b['datetime']);
        });

        $totalDistance = DistanceHelper::calculateAccurateDistance($trackingPoints);


        $attendance->total_distance = $totalDistance;

            // Get user requests for this attendance date
        $requests = DB::table('user_requests')
                ->where('provider_id', $id)
                ->whereDate('created_at', $attendance->attendance_date)
                ->get();
            
            $totalTickets = $requests->count();
            $completedTickets = $requests->where('status', 'COMPLETED')->count();
            
            
            $imageCount =[];
            foreach ($requests as $request) {
               
                  // Get image count from submitfiles
            $imageCount = DB::table('submitfiles')
                ->where('provider_id', $id)
                ->where('request_id', $request->id)
                ->get();
            }
            
          
            
            // Add calculated fields to attendance record
            $attendance->total_tickets = $totalTickets;
            $attendance->completed_tickets = $completedTickets;
            // $attendance->total_distance = round($totalDistance, 2);
            $attendance->images = $imageCount;
            
            return $attendance;
        });




    // Calculate attendance statistics
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $presentDays = count($attendanceMap);
    $streak = $this->calculateStreak($attendances);
    
    // Calculate average duration from requests
    $avgDuration = DB::table('user_requests')
        ->where('provider_id', $id)
        ->whereNotNull('started_at')
        ->whereNotNull('finished_at')->whereMonth('created_at', $month)
        ->whereYear('created_at', $year)->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, started_at, finished_at)) as avg_minutes')
        ->value('avg_minutes');

    
    $avgDurationFormatted = $avgDuration ? floor($avgDuration / 60) . 'h ' . ($avgDuration % 60) . 'm' : '0h 0m';
    
    $attendanceStats = [
        'present_days' => $presentDays,
        'total_days' => $daysInMonth,
        'streak' => $streak,
        'avg_duration' => $avgDurationFormatted,
        'completion_rate' => $requestStats['total'] > 0 
            ? round(($requestStats['completed'] / $requestStats['total']) * 100) 
            : 0
    ];


  return view('admin.AttendanceDashboard.ProviderView', compact(
         'provider',
         'attendanceMap',
         'month',
         'year',
         'monthyear',
        'totalRequests',
        'recentRequests',
        'requestStats',
        'attendanceStats',
        'filter',
        'startDate',
        'endDate'
     ));
}

private function calculateStreak($attendances)
{
    if ($attendances->isEmpty()) {
        return 0;
    }
    
    $sortedAttendances = $attendances->sortByDesc('created_at');
    $streak = 0;
    $lastDate = null;
    
    foreach ($sortedAttendances as $attendance) {
        $currentDate = Carbon::parse($attendance->created_at)->startOfDay();
        
        if ($lastDate === null) {
            $streak = 1;
            $lastDate = $currentDate;
        } elseif ($lastDate->diffInDays($currentDate) === 1) {
            $streak++;  
            $lastDate = $currentDate;
        }else {
            break;
        }
    }
    
    return $streak;
 }
 public function showLocationHistory($id, Request $request)
    {
       
       
        $provider = DB::table('providers')
                    ->leftJoin('attendance', function ($join) {
                        $join->on('providers.id', '=', 'attendance.provider_id')
                            ->whereDate('attendance.created_at', Carbon::today());
                    })
                    ->where('providers.id', $id)
                    ->select('providers.*', 'attendance.online_image')
                    ->first();

        $column = $provider->type === 2 ? 'contact_no' : 'petroller_contact_no';

        $GP = DB::table('gp_list')
            ->where('type', 'GP')
            ->where($column, $provider->mobile)->get();

         

        
        if (!$provider) {
            abort(404, 'Employee not found');
        }

        return view('admin.AttendanceDashboard.live_track', compact('provider','GP'));
    }

public function getTrackingData($id, Request $request)
{
    $fromDate = $request->get('from_date', date('Y-m-d'));
    $toDate = $request->get('to_date', date('Y-m-d'));

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
                    if (isset($point['latitude'], $point['longitude'])) {
                        $trackingData[] = [
                            'latitude' => (float)$point['latitude'],
                            'longitude' => (float)$point['longitude'],
                            'datetime' => $point['datetime'] ?? $history->created_at,
                            'date' => $historyDate,
                            'address' => $this->getAddressFromCoordinates($point['latitude'], $point['longitude'])
                        ];
                    }
                }
            }
        }
    }

     // --- Sort by datetime globally ---
    usort($trackingData, function($a, $b) {
        return strtotime($a['datetime']) <=> strtotime($b['datetime']);
    });

    // --- Group points by date ---
    $groupedByDate = [];
    foreach ($trackingData as $p) {
        $groupedByDate[$p['date']][] = $p;
    }

    // --- Process each day ---
    foreach ($groupedByDate as $date => $points) {

        $dayDistance = 0;
        $dayIdleTime = 0;
        $travelPoints = [];

        // Calculate total distance using helper
        $dayDistance = DistanceHelper::calculateAccurateDistance($points);
        $totalDistance += $dayDistance;

            // Sort by datetime
        usort($points, function($a, $b) {
            $t1 = strtotime($a['datetime']);
            $t2 = strtotime($b['datetime']);
            if ($t1 == $t2) return 0;
            return ($t1 < $t2) ? -1 : 1;
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
            } else {
                $travelPoints[] = $prevPoint;
                $travelPoints[] = $currPoint;
            }

            $prevPoint = $currPoint;
        }


        $dailyDistances[$date] = $dayDistance;
        $dailyIdleTimes[$date] = $dayIdleTime;
    }

    $requests = DB::table('user_requests')
                ->where('provider_id', $id)
                ->whereBetween('created_at', [
                    $fromDate . ' 00:00:00',
                    $toDate . ' 23:59:59'
                ])
                ->get();


    return response()->json([
        'tracking' => $trackingData,
        'total_distance' =>$totalDistance,
        'daily_distances' => $dailyDistances,
        'daily_idle_time' => $dailyIdleTimes,
        'TicketsData'=>$requests
    ]);
}




  public function StaffTrack($id,Request $request){
    $date = $request->get('date'); 
    $zonals = DB::table('zonal_managers')->get();

    $provider = DB::table('providers')
         ->leftJoin('districts','providers.district_id','=','districts.id')
         ->leftJoin('zonal_managers','providers.zone_id','=','zonal_managers.id')
         ->leftJoin('attendance', function ($join) {
                $join->on('providers.id', '=', 'attendance.provider_id')
                    ->whereDate('attendance.created_at', Carbon::today());
            })
         ->select(
             'providers.*',
             'districts.name as district_name',
             'zonal_managers.Name as zone_name',
             'attendance.online_image'
         )
         ->where('providers.id', $id)
         ->first();

    $recentRequests = DB::table('attendance as a')
        ->where('a.provider_id', $id)
        ->when($date, function ($query) use ($date) {
            return $query->whereDate('a.created_at', $date);
        })
        ->select(
            'a.id as attendance_id',
            'a.provider_id as provider_id',
            DB::raw('DATE(a.created_at) as attendance_date'),
            'a.created_at as check_in',
            'a.updated_at as check_out',
            'a.status as onlinestatus'
        )
        ->orderBy('a.created_at', 'desc')
        ->get()
        ->map(function ($attendance) use ($id) {
        $trackingHistory = DB::table('provider_tracking_histories')
            ->where('provider_id', $id)
            ->whereDate('created_at', $attendance->attendance_date)
            ->orderBy('created_at', 'asc')
            ->get();

        $trackingPoints = [];
        foreach ($trackingHistory as $history) {
            if ($history->latlng) {
                $points = json_decode($history->latlng, true);
                if (is_array($points)) {
                    foreach ($points as $point) {
                        if (isset($point['latitude'], $point['longitude'])) {
                             $trackingPoints[] = [
                            'latitude' => (float)$point['latitude'],
                            'longitude' => (float)$point['longitude'],
                            'datetime' => $point['datetime'] ? $point['datetime'] : $history->created_at
                        ];
                        }
                    }
                }
            }
        }
             // --- Sort by datetime globally ---
        usort($trackingPoints, function($a, $b) {
            return strtotime($a['datetime']) <=> strtotime($b['datetime']);
        });
        $totalDistance = DistanceHelper::calculateAccurateDistance($trackingPoints);



       
        $attendance->total_distance = $totalDistance;
    
            // Get user requests for this attendance date
        $requests = DB::table('user_requests')
                ->where('provider_id', $id)
                ->whereDate('created_at', $attendance->attendance_date)
                ->get();
            
            $totalTickets = $requests->count();
            $completedTickets = $requests->where('status', 'COMPLETED')->count();
            
            // Calculate total distance using your formula
           
            $imageCount =[];
            foreach ($requests as $request) {
               
                  // Get image count from submitfiles
            $imageCount = DB::table('submitfiles')
                ->where('provider_id', $id)
                ->where('request_id', $request->id)
                ->get();
            }
            
            
            
            // Add calculated fields to attendance record
            $attendance->total_tickets = $totalTickets;
            $attendance->completed_tickets = $completedTickets;
            // $attendance->total_distance = round($totalDistance, 2);
            $attendance->images = $imageCount;
            
            return $attendance;
        });
          
           // Get user requests for the date
            $requests = DB::table('user_requests')
                ->where('provider_id', $id)
                ->whereDate('created_at', $date)
                ->get();
           // Get tracking data for the date
                $trackingHistory = DB::table('provider_tracking_histories')
                    ->where('provider_id', $id)
                    ->whereDate('created_at', $date)
                    ->first();
                
                $trackingData = [];
                if ($trackingHistory && $trackingHistory->latlng) {
                    $trackingData = json_decode($trackingHistory->latlng, true) ?: [];
                }
          

    return view('admin.AttendanceDashboard.attendance_track',compact('provider','recentRequests','zonals','requests','trackingData'));

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

public function php_test(){

phpinfo();


}


public function uploadImages(Request $request)
{
    $this->validate($request, [
        'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
    ]);

   //dd($request);

    // Fetch existing document row
    $document = DB::table('submitfiles')->where('ticket_id', $request->request_id)->first();

    $field = 'otdr_img'; // change to your field name

    // Step 1: Get existing images
    $existingImages = [];

    if ($document && !empty($document->$field)) {
        $decoded = json_decode($document->$field, true);
        $existingImages = is_array($decoded) ? $decoded : explode(',', $document->$field);
    }

    // Step 2: Upload new images
    $newImages = [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $name = time() . '_' . uniqid() . '.' . $file->extension();
            $file->move(public_path('/uploads/SubmitFiles/'), $name);
            $newImages[] = $name;
        }
    }

    // Step 3: Merge old + new images
    $finalArray = array_merge($existingImages, $newImages);

    // Step 4: Save back to DB
    DB::table('submitfiles')
        ->where('ticket_id', $request->request_id)
        ->update([
            $field => json_encode($finalArray)
        ]);

    return back()->with('success', 'Images uploaded successfully');
}

public function dashboard_test(Request $request)
{
return view('admin.dashboard.dashboard-test');


}



public function velocityTrend(Request $request)
{

    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;
    $district_id = $user->district_id;
    $daysMap = [
        '7days'  => 7,
        '15days' => 15,
        '30days' => 30,
    ];

    $range = $request->range ? $request->range : '7days';
     // default fallback
    $days = isset($daysMap[$range]) ? $daysMap[$range] : 7;

    // from date (including today)
    $fromDate = date('Y-m-d 00:00:00', strtotime('-' . ($days - 1) . ' days'));
    $toDate   = date('Y-m-d 23:59:59');
    $type  = $request->type ? $request->type : 'all';
    $g_type  = $request->g_type ? $request->g_type : 'all';

    /* ================= BASE QUERY ================= */
    $baseQuery = DB::table('user_requests')->where('state_id',$state_id);

    if (!empty($district_id)) {
        $baseQuery->where('district_id', $district_id);
    }


    // Ticket generation type
    if ($g_type == 'auto') {
        $baseQuery->where('default_autoclose', 'Auto');
    } elseif ($g_type == 'manual') {
        $baseQuery->where('default_autoclose', 'Manual');
    }

     // Ticket generation type
    if ($type == 'auto') {
        $baseQuery->where('autoclose', 'Auto');
    } elseif ($type == 'manual') {
        $baseQuery->where('autoclose', 'Manual');
    }


    /* ================= TODAY ================= */
    if ($range == 'today') {

        $query = clone $baseQuery;

        $rows = $query
            ->selectRaw("
                HOUR(created_at) as label,
                COUNT(*) as assigned,
                SUM(status = 'COMPLETED') as closed,
                SUM(status = 'ONHOLD') as hold,
                SUM(status = 'INCOMING') as not_started,
                SUM(status='PICKEDUP') as on_going
            ")
            ->whereDate('created_at', date('Y-m-d'))
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('label')
            ->get();

        $labels = array();
        foreach ($rows as $r) {
            $labels[] = sprintf('%02d:00', $r->label);
        }

        return response()->json(array(
            'labels' => $labels,
            'assigned' => array_column($rows->toArray(), 'assigned'),
            'closed' => array_column($rows->toArray(), 'closed'),
            'hold' => array_column($rows->toArray(), 'hold'),
            'not_started' => array_column($rows->toArray(), 'not_started'),
            'on_going'=>array_column($rows->toArray(),'on_going')
        ));
    }

    /* ================= YESTERDAY ================= */
    if ($range == 'yesterday') {

        $query = clone $baseQuery;

        $rows = $query
            ->selectRaw("
                HOUR(created_at) as label,
                COUNT(*) as assigned,
                SUM(status = 'COMPLETED') as closed,
                SUM(status = 'ONHOLD') as hold,
                SUM(status = 'INCOMING') as not_started,
                SUM(status='PICKEDUP') as on_going
            ")
            ->whereDate('created_at', date('Y-m-d', strtotime('-1 day')))
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('label')
            ->get();

        $labels = array();
        foreach ($rows as $r) {
            $labels[] = sprintf('%02d:00', $r->label);
        }

        return response()->json(array(
            'labels' => $labels,
            'assigned' => array_column($rows->toArray(), 'assigned'),
            'closed' => array_column($rows->toArray(), 'closed'),
            'hold' => array_column($rows->toArray(), 'hold'),
            'not_started' => array_column($rows->toArray(), 'not_started'),
             'on_going'=>array_column($rows->toArray(),'on_going')
        ));
    }

    /* ================= LAST 7 DAYS ================= */
    $query = clone $baseQuery;

    $rows = $query
        ->selectRaw("
            DATE(created_at) as label,
            COUNT(*) as assigned,
            SUM(status = 'COMPLETED') as closed,
            SUM(status = 'ONHOLD') as hold,
            SUM(status = 'INCOMING') as not_started,
             SUM(status='PICKEDUP') as on_going
        ")
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('label')
        ->get();

    return response()->json(array(
        'labels' => array_column($rows->toArray(), 'label'),
        'assigned' => array_column($rows->toArray(), 'assigned'),
        'closed' => array_column($rows->toArray(), 'closed'),
        'hold' => array_column($rows->toArray(), 'hold'),
        'not_started' => array_column($rows->toArray(), 'not_started'),
        'on_going'=>array_column($rows->toArray(),'on_going')
    ));
}

public function districtHeatmap(Request $request)
{
    $user = Session::get('user');
    $company_id  = $user->company_id;
    $state_id    = $user->state_id;
    $district_id = isset($user->district_id) ? $user->district_id : null;

    $range  = $request->range ? $request->range : '7days';
    $type   = $request->type ? $request->type : 'all';      // close type
    $g_type = $request->g_type ? $request->g_type : 'all';  // generate type

    /* ================= DATE RANGE ================= */
    if ($range == 'today') {
        $from = date('Y-m-d 00:00:00');
        $to   = date('Y-m-d 23:59:59');
    } elseif ($range == 'yesterday') {
        $from = date('Y-m-d 00:00:00', strtotime('-1 day'));
        $to   = date('Y-m-d 23:59:59', strtotime('-1 day'));
    } else {
        $from = date('Y-m-d 00:00:00', strtotime('-6 days'));
        $to   = date('Y-m-d 23:59:59');
    }

    /* ================= BASE QUERY (START FROM DISTRICTS) ================= */
    $query = DB::table('districts')
        ->leftJoin('user_requests', function ($join) use (
            $from, $to, $company_id, $state_id, $type, $g_type
        ) {
            $join->on('districts.id', '=', 'user_requests.district_id')
                 ->whereBetween('user_requests.created_at', array($from, $to))
                 ->where('user_requests.company_id', $company_id)
                 ->where('user_requests.state_id', $state_id);

            /* CLOSE TYPE */
            if ($type == 'auto') {
                $join->where('user_requests.autoclose', 'Auto');
            } elseif ($type == 'manual') {
                $join->where('user_requests.autoclose', 'Manual');
            }

            /* GENERATE TYPE */
            if ($g_type == 'auto') {
                $join->where('user_requests.default_autoclose', 'Auto');
            } elseif ($g_type == 'manual') {
                $join->where('user_requests.default_autoclose', 'Manual');
            }
        })
        ->leftJoin('master_tickets', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
        ->where('districts.state_id', $state_id);

    if (!empty($district_id)) {
        $query->where('districts.id', $district_id);
    }

    /* ================= RAW DATA ================= */
    $rows = $query
        ->select(
            'districts.name as district',
            'user_requests.status',
            'user_requests.finished_at as closed_at',
            'master_tickets.downdate',
            'master_tickets.downtime'
        )
        ->orderBy('districts.name', 'ASC')
        ->get();

    /* ================= PROCESS ================= */
    $summary = array();

    foreach ($rows as $r) {

        if (!isset($summary[$r->district])) {
            $summary[$r->district] = array(
                'assigned'     => 0,
                'closed'       => 0,
                'backlog'      => 0,
                'hold'         => 0,
                'not_started'  => 0,
                'ongoing'      => 0,
                'sla_pass'     => 0,
                'sla_fail'     => 0
            );
        }

        /* If no ticket row */
        if (empty($r->status)) {
            continue;
        }

        /* Assigned */
        $summary[$r->district]['assigned']++;

        /* STATUS COUNTS */
        if ($r->status == 'ONHOLD') {
            $summary[$r->district]['hold']++;
        }

        if ($r->status == 'INCOMING') {
            $summary[$r->district]['not_started']++;
        }

        if ($r->status == 'PICKEDUP') {
            $summary[$r->district]['ongoing']++;
        }

        /* CLOSED & SLA */
        if ($r->status == 'COMPLETED' && !empty($r->closed_at)) {

            $summary[$r->district]['closed']++;

            $startTime = strtotime(
                date('Y-m-d H:i:s', strtotime($r->downdate . ' ' . $r->downtime))
            );
            $closeTime = strtotime($r->closed_at);

            if ($startTime && $closeTime) {
                $diffHours = ($closeTime - $startTime) / 3600;

                if ($diffHours <= 8) {
                    $summary[$r->district]['sla_pass']++;
                } else {
                    $summary[$r->district]['sla_fail']++;
                }
            }

        } else {
            $summary[$r->district]['backlog']++;
        }
    }

    /* ================= RESPONSE ================= */
    $data = array();

    foreach ($summary as $district => $v) {

        $closed = $v['closed'];

        $slaPct = $closed > 0
            ? round(($v['sla_fail'] / $closed) * 100, 1)
            : 0;

        $data[] = array(
            'district'      => $district,
            'assigned'      => $v['assigned'],
            'closed'        => $closed,
            'backlog'       => $v['backlog'],
            'hold'          => $v['hold'],
            'not_started'   => $v['not_started'],
            'ongoing'       => $v['ongoing'],
            'sla_percent'   => $slaPct,
            'sla_pass'      => $v['sla_pass'],
            'sla_fail'      => $v['sla_fail'],
            'net_velocity'  => $closed - $v['assigned']
        );
    }

    return response()->json($data);
}


  

}
