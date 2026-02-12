<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use Auth;
use Setting;
use Exception;

use App\User;
use App\Fleet;
use App\Provider;
use App\UserPayment;
use App\ServiceType;
use App\UserRequests;
use App\ProviderService;
use App\UserRequestRating;
use App\UserRequestPayment;
use App\RequestFilter;
use App\FleetWallet;
use App\WalletRequests;
use App\MasterTicket;
use DB;

use Carbon\Carbon;
use App\Http\Controllers\SendPushNotification;
use App\Http\Controllers\TicketController;

class TicketController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }
	
	
	/**
     * Create a function to insert tickets data through api.
     *
     * @return void
     */
	
	
    public function insertData()
    {
        ini_set("allow_url_fopen", 1);
        ini_set('max_execution_time', 5000);
		ini_set('memory_limit', '500M');
        error_reporting(0);
        DB::table('master_tickets')->truncate(); 
        // $url = 'https://dash.apsfl.co.in:8443/Calll/rest/pop/mo';

        // $headers = array('accept: */*','Content-type: application/json', 'Connection: Keep-Alive');
        ob_start();
        $curlSession = curl_init();
        //curl_setopt($curlSession, CURLOPT_URL, 'http://optcl.terasoftware.com:8080/Calll/rest/pop/mo');
         curl_setopt($curlSession, CURLOPT_URL, 'http://optcl.terasoftware.com:8080/Calll/rest/testpop/testmo');
        curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        $jsonData = json_decode(curl_exec($curlSession), true);
        curl_close($curlSession);
        //echo "<pre>";
        //print_r($jsonData);
                        $failed_count = 0;
                        $success_count = 0;
      			foreach ($jsonData as $keyvalue)
			{
			  $data = array('district' => $keyvalue['district'], 
			  'mandal' => $keyvalue['mandal'], 
			  'lat' => $keyvalue['lat'],
			  'log' => $keyvalue['log'], 
			  'downtime' => date('h:i:s a',strtotime($keyvalue['downtime'])),
			  'downdate' => date('Y-m-d',strtotime($keyvalue['downdate'])), 
			  'up_date' =>  date('Y-m-d',strtotime($keyvalue['update'])),
			  'up_time' =>  date('h:i:s a',strtotime($keyvalue['uptime'])),
			  'downreason' => $keyvalue['downreasonindetailed'],
			  'downreasonindetailed' => $keyvalue['downreasonindetailed'], 
			  'subsategory' => $keyvalue['subsategory'],
			  'ticketid' => $keyvalue['ticketid'],
                          'pop_map_key' => $keyvalue['pop_map_key']

			  );
			  
			 if(DB::table('master_tickets')->insert($data)){
                                $success_count++;			  
                         }
			else{
                              $failed_count++;
                          }
                         }
                         if($success_count > $failed_count){
                         echo "Data inserted Successfully !...";
                        }else{
                         echo "something went wrong";
                        }
			//DB::table('users')->insert($values);
			 //MasterTicket::insert($data);
		
        ob_flush();//Flush the data here

/*curl close*/

    }


     
	/**
     * Send the request to user 
     * Added By Ashok
     * @return \Illuminate\Http\Response
     */

    public function send_request(Request $request) {
		
		$getticketdetails = DB::table('master_tickets')
		->where('ticketid','!=','')
                ->where('status','!=',1)
		//->orderBy('created_at','asc')
                ->inRandomOrder()
		->first();
             //dd($getticketdetails);

         $distance = Setting::get('provider_search_radius', '10');
       
        $latitude = $getticketdetails->lat;
        $longitude = $getticketdetails->log;
        $ticketnumber = $getticketdetails->ticketid;

        $service_type = 2;
        //address find
         $destinationgeocodeFromLatLong = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=".Setting::get('map_key');

         $json = curl($destinationgeocodeFromLatLong);

          $desdetails = json_decode($json, TRUE);
		  $desstatus = $desdetails['status'];
		  //dd($status);
		  $daddress = ($desstatus=="OK")?$desdetails['results'][1]['formatted_address']:'';
         

         //close address  
        $Providers = Provider::with('service')
            ->select(DB::Raw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) AS distance"),'id','latitude','longitude','mobile')
            ->where('status', 'approved')
            ->whereRaw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
            ->whereHas('service', function($query) use ($service_type){
                        $query->where('status','active');
                        $query->where('service_type_id',$service_type);
                    })
            ->orderBy('distance','asc')
            ->first();
          dd($Providers);
            //address find
         $sourcegeocodeFromLatLong = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$Providers['latitude'].",".$Providers['longitude']."&key=".Setting::get('map_key');

         $json = curl($sourcegeocodeFromLatLong);

         $srcdetails = json_decode($json, TRUE);
		  $srcstatus = $srcdetails['status'];
		  //dd($status);
		  $saddress = ($srcstatus=="OK")?$srcdetails['results'][1]['formatted_address']:'';
         

         //close address
        
	        if(count($Providers) == 0) {
              echo "no providers found";
            }

            try{
            if(count($Providers) > 0) {
                $this->sendRequestsms($Providers['mobile']);  
             }
         
           $details = "https://maps.googleapis.com/maps/api/directions/json?origin=".$Providers['latitude'].",".$Providers['longitude']."&destination=".$latitude.",".$longitude."&mode=driving&key=".Setting::get('map_key');



            $json = curl($details);

            $details = json_decode($json, TRUE);

            $route_key = $details['routes'][0]['overview_polyline']['points'];

            //DB::table('user_requests')->where('booking_id', $ticketnumber)->delete();

            $UserRequest = new UserRequests;
            //$UserRequest->booking_id = Helper::generate_booking_id();
            $UserRequest->booking_id = $getticketdetails->ticketid;
            $UserRequest->downreason = $getticketdetails->downreason;
            $UserRequest->downreasonindetailed = $getticketdetails->downreasonindetailed;

            $UserRequest->user_id =45;
            
         
            $UserRequest->current_provider_id = $Providers['id'];
            $UserRequest->provider_id = $Providers['id'];

            $UserRequest->service_type_id = 2;
            $UserRequest->rental_hours = 10;
            $UserRequest->payment_mode = 'CASH';
            $UserRequest->promocode_id = 0;
            $UserRequest->status = 'INCOMING';
            $UserRequest->flag= 'Sent';
            $UserRequest->s_address =$saddress;
            $UserRequest->d_address =$daddress;

            $UserRequest->s_latitude = $Providers['latitude'];
            $UserRequest->s_longitude = $Providers['longitude'];

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

            if(count($Providers) <= Setting::get('surge_trigger') && $Providers->count() > 0){
                $UserRequest->surge = 1;
            }

         
             //$UserRequest->schedule_at = " ";
             //$UserRequest->is_scheduled = 'NO';
            

            //dd( $UserRequest);

            $UserRequest->save();
           
             
            
               //dd($Providers['mobile']);
            if(Setting::get('manual_request',0) == 0){
                //foreach ($Providers as $key => $Provider) {

                    if(Setting::get('broadcast_request',0) == 1){
                       (new SendPushNotification)->IncomingRequest($Providers['id']); 
                    }

                    $Filter = new RequestFilter;
                    // Send push notifications to the first provider
                    // incoming request push to provider
                    
                    $Filter->request_id = $UserRequest->id;
                    $Filter->provider_id = $Providers['id']; 
                    $Filter->save();
                //}
            }
             //print_r($UserRequest);
             echo "Request sent successfully";

        } catch (Exception $e) {            
            echo "something went wrong";
        }
     
	 
	 		
	}

      public function send_sms()
      {
           $api_key = '35FEABDB060BF6';
           $contacts = '7036053362';
           $from = 'TERAOD';
           $template_id= '1207161838540605755'; 
           $sms_text = urlencode('Hi,You have Recieved request for odisha fleet.Please open the App and Accept the Request !..');

          $api_url = "http://sms.hitechsms.com/app/smsapi/index.php?key=".$api_key."&campaign=0&routeid=13&type=text&contacts=".$contacts."&senderid=".$from."&msg=".$sms_text."&template_id=".$template_id;

          //Submit to server

          $response = file_get_contents( $api_url);
          echo $response;
       }


      public function sendRequestsms($mobile)
      {
            $api_key = '35FEABDB060BF6';
            $mobile= $mobile;
            $contacts = $mobile;
            $from = 'TERAOD';
            $template_id= '1207161838540605755'; 
            $sms_text = urlencode('Hi,You have Recieved request for odisha fleet.Please open the App and Accept the Request !..');
            $api_url = "http://sms.hitechsms.com/app/smsapi/index.php?key=".$api_key."&campaign=0&routeid=13&type=text&contacts=".$contacts."&senderid=".$from."&msg=".$sms_text."&template_id=".$template_id;
           //Submit to server
            $response = file_get_contents( $api_url);
           echo $response;
       }


     


      public function insertTicketData()
    {
        ini_set("allow_url_fopen", 1);
        ini_set('max_execution_time', 5000);
		ini_set('memory_limit', '500M');
        error_reporting(0);
        //DB::table('master_tickets')->truncate(); 
        // $url = 'https://dash.apsfl.co.in:8443/Calll/rest/pop/mo';

        // $headers = array('accept: */*','Content-type: application/json', 'Connection: Keep-Alive');
        ob_start();
        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, 'http://optcl.terasoftware.com:8080/Calll/rest/pop/mo');
         //curl_setopt($curlSession, CURLOPT_URL, 'http://optcl.terasoftware.com:8080/Calll/rest/testpop/testmo');
        curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        $jsonData = json_decode(curl_exec($curlSession), true);
        curl_close($curlSession);
        //echo "<pre>";
        //print_r($jsonData);exit();
                        $failed_count = 0;
                        $success_count = 0;
      			foreach ($jsonData as $keyvalue)
			{
                          
			  $data = array('district' => $keyvalue['district'], 
			  'mandal' => $keyvalue['mandal'], 
                          'gpname' => $keyvalue['gname'],
			  'lat' => $keyvalue['lat'],
			  'log' => $keyvalue['log'], 
			  'downtime' => date('h:i:s a',strtotime($keyvalue['downtime'])),
			  'downdate' => date('Y-m-d',strtotime($keyvalue['downdate'])), 
			  'up_date' =>  date('Y-m-d',strtotime($keyvalue['update'])),
			  'up_time' =>  date('h:i:s a',strtotime($keyvalue['uptime'])),
			  'downreason' => $keyvalue['downreason'],
			  'downreasonindetailed' => $keyvalue['downreasonindetailed'], 
			  'subsategory' => $keyvalue['subsategory'],
			  'ticketid' => $keyvalue['ticketid'],
                          'ticketinsertstage' =>1,
                          'olt_type' => $keyvalue['type'],
                          'pop_map_key' =>$keyvalue['pop_map_key']
			  );
			  $mobile = $keyvalue['number'];
                          $getproviderdetails = DB::table('providers')->select( 'providers.id', 'providers.mobile', 'providers.latitude', 'providers.longitude','provider_devices.token')->leftjoin('provider_devices','providers.id','=','provider_devices.provider_id')->where('mobile','=',$mobile)->first();
                          $provider_id = $getproviderdetails->id;
                          $latitude = $keyvalue['lat'];
                          $longitude = $keyvalue['log'];

 
			 if(DB::table('master_tickets')->insert($data)){  
                  // send sms here //
		  
                   $api_key = '35FEABDB060BF6';
                   $from = 'TERAOD';
                   $template_id= '1207161838540605755'; 
                   $contacts = $getproviderdetails->mobile;
                   $sms_text = urlencode('Hi,You have Recieved request for odisha fleet.Please open the App and Accept the Request !..');
                   //$api_url = "http://sms.hitechsms.com/app/smsapi/index.php?key=".$api_key."&campaign=0&routeid=13&type=text&contacts=".$contacts."&senderid=".$from."&msg=".$sms_text;
   		            $api_url = "http://sms.hitechsms.com/app/smsapi/index.php?key=".$api_key."&campaign=0&routeid=13&type=text&contacts=".$contacts."&senderid=".$from."&msg=".$sms_text."&template_id=".$template_id; 
                 //Submit to server

                  //send push notification

                  $fcm_token= $getproviderdetails->token;
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
                "id" : "'.$getproviderdetails->id.'",
                "title":"' . $title . '",
                "description" : "' . $message . '",
                "text" : "' . $message . '",
                "is_read": 0
              }
        }';

        $ch = curl_init();
        $timeout = 220;
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

                 //end here

                                
                                
                              $destinationgeocodeFromLatLong = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=".Setting::get('map_key');

                            $json = curl($destinationgeocodeFromLatLong);

                      $desdetails = json_decode($json, TRUE);
		      $desstatus = $desdetails['status'];
		      //dd($status);
		      $daddress = ($desstatus=="OK")?$desdetails['results'][1]['formatted_address']:'';

                      
                       $sourcegeocodeFromLatLong = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$getproviderdetails->latitude.",".$getproviderdetails->longitude."&key=".Setting::get('map_key');

                       $json = curl($sourcegeocodeFromLatLong);

                    $srcdetails = json_decode($json, TRUE);
		    $srcstatus = $srcdetails['status'];
		   //dd($status);
		     $saddress = ($srcstatus=="OK")?$srcdetails['results'][1]['formatted_address']:'';
                              
                               $details = "https://maps.googleapis.com/maps/api/directions/json?origin=".$getproviderdetails->latitude.",".$getproviderdetails->longitude."&destination=".$latitude.",".$longitude."&mode=driving&key=".Setting::get('map_key');
                               $json = curl($details);
                               $details = json_decode($json, TRUE);
                               $route_key = $details['routes'][0]['overview_polyline']['points'];

                               $UserRequest = new UserRequests;
            //$UserRequest->booking_id = Helper::generate_booking_id();
            $UserRequest->booking_id = $keyvalue['ticketid'];
            $UserRequest->gpname = $keyvalue['gname'];

            $UserRequest->downreason = $keyvalue['downreasonindetailed'];
            $UserRequest->downreasonindetailed = $keyvalue['downreasonindetailed'];

            $UserRequest->user_id =45;
            
         
            $UserRequest->current_provider_id = $getproviderdetails->id;
            $UserRequest->provider_id = $getproviderdetails->id;

            $UserRequest->service_type_id = 2;
            $UserRequest->rental_hours = 10;
            $UserRequest->payment_mode = 'CASH';
            $UserRequest->promocode_id = 0;
            
            $UserRequest->status = 'SEARCHING';
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

            //if(count($Providers) <= Setting::get('surge_trigger') && $Providers->count() > 0){
               // $UserRequest->surge = 1;
           // }

         
             //$UserRequest->schedule_at = " ";
             //$UserRequest->is_scheduled = 'NO';
            

            //dd( $UserRequest);

            $UserRequest->save();

             if(Setting::get('manual_request',0) == 0){
                //foreach ($Providers as $key => $Provider) {

                    if(Setting::get('broadcast_request',0) == 1){
                       (new SendPushNotification)->IncomingRequest($provider_id); 
                    }

                    $Filter = new RequestFilter;
                    // Send push notifications to the first provider
                    // incoming request push to provider
                    
                    $Filter->request_id = $UserRequest->id;
                    $Filter->provider_id = $provider_id; 
                    $Filter->save();
                //}
            }
             //print_r($UserRequest);
             $success_count++;
           			  
                         }
			else{
                              $failed_count++;
                          }
                         }
                         if($success_count > $failed_count){
                         echo "Data inserted Successfully !...";
                        }else{
                         echo "something went wrong";
                        }
			//DB::table('users')->insert($values);
			 //MasterTicket::insert($data);
		
        ob_flush();//Flush the data here

/*curl close*/

    }
 
}
