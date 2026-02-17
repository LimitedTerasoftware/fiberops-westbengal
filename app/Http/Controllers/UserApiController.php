<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use DB;
use Log;
use Auth;
use Hash;
use Route;
use Storage;
use Setting;
use Exception;
use Validator;
use Notification;

use Carbon\Carbon;
use App\Http\Controllers\SendPushNotification;
use App\Notifications\ResetPasswordOTP;
use App\Helpers\Helper;

use App\Card;
use App\User;
use App\Work;
use App\Provider;
use App\Settings;
use App\Promocode;
use App\ServiceType;
use App\District;
use App\UserRequests;
use App\RequestFilter;
use App\PromocodeUsage;
use App\WalletPassbook;
use App\UserWallet;
use App\PromocodePassbook;
use App\ProviderService;
use App\UserRequestRating;
use App\SubmitFile;
use App\MasterTicket;
use App\MasterCoordinate;
use App\ProviderHistory;
use App\Http\Controllers\ProviderResources\TripController;
use App\Services\ServiceTypes;
use App\Services\GisService;
use App\Block;
use App\EmpStockTransaction;
use App\Material_serial_Allocations;
use App\MaterialSerial;
use App\Material;
use App\EmpStockBalance;
use App\EmployeeMaterialLedger;
use App\Services\GoogleMapsService;
use GuzzleHttp\Client;


class UserApiController extends Controller
{
    /**  Check Email/Mobile Availablity Of a User  **/

    public function verify(Request $request)
    {
        $this->validate($request, [
                'email' => 'required|email|unique:users',
                
            ]);

        try{
            
            return response()->json(['message' => trans('api.email_available')]);

        } catch (Exception $e) {
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    public function checkUserEmail(Request $request)
    {
        $this->validate($request, [
                'email' => 'required|email',                
            ]);

        try{
            
            $email=$request->email;

            $results=User::where('email',$email)->first();

            if(empty($results))
                return response()->json(['message' => trans('api.email_available'),'is_available' => true]);                
            else        
                return response()->json(['message' => trans('api.email_not_available'),'is_available' => false]);

        } catch (Exception $e) {
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    public function login(Request $request)
    {
        $tokenRequest = $request->create('/oauth/token', 'POST', $request->all());
        $request->request->add([
           "client_id"     => $request->client_id,
           "client_secret" => $request->client_secret,
           "grant_type"    => 'password',
           "code"          => '*',
        ]);
        $response = Route::dispatch($tokenRequest);

        $json = (array) json_decode($response->getContent());

        if(!empty($json['error'])){
            $json['error']=$json['message'];
        }

        // $json['status'] = true;
        $response->setContent(json_encode($json));

        $update = User::where('email', $request->username)->update(['device_token' => $request->device_token , 'device_id' => $request->device_id , 'device_type' => $request->device_type]);    

        return $response;
    }

    public function signup(Request $request)
    {
        $this->validate($request, [
                'social_unique_id' => ['required_if:login_by,facebook,google','unique:users'],
                'device_type' => 'required|in:android,ios',
                'device_token' => 'required',
                'device_id' => 'required',
                'login_by' => 'required|in:manual,facebook,google',
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'mobile' => 'required',
                'password' => 'required|min:6',
            ]);

            
            $User = $request->all();

            $User['payment_mode'] = 'CASH';
            $User['password'] = bcrypt($request->password);
            $User = User::create($User);

            $User=Auth::loginUsingId($User->id);
            $UserToken = $User->createToken('AutoLogin');
            $User['access_token'] = $UserToken->accessToken;
            $User['currency'] = Setting::get('currency');
            $User['sos'] = Setting::get('sos_number', '911');                
            $User['app_contact'] = Setting::get('app_contact', '5777');
            $User['measurement'] = Setting::get('distance', 'Kms');            

            if(Setting::get('send_email', 0) == 1) {
                // send welcome email here
                Helper::site_registermail($User);
            }    

            return $User;
       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function logout(Request $request)
    {
        try {
            User::where('id', $request->id)->update(['device_id'=> '', 'device_token' => '']);
            return response()->json(['message' => trans('api.logout_success')]);
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function change_password(Request $request){

        $this->validate($request, [
                'password' => 'required|confirmed|min:6',
                'old_password' => 'required',
            ]);

        $User = Auth::user();

        if(Hash::check($request->old_password, $User->password))
        {
            $User->password = bcrypt($request->password);
            $User->save();

            if($request->ajax()) {
                return response()->json(['message' => trans('api.user.password_updated')]);
            }else{
                return back()->with('flash_success', trans('api.user.password_updated'));
            }

        } else {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.user.incorrect_old_password')], 422);
            }else{
                return back()->with('flash_error',trans('api.user.incorrect_old_password'));
            }
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function update_location(Request $request){

        $this->validate($request, [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

        if($user = User::find(Auth::user()->id)){

            $user->latitude = $request->latitude;
            $user->longitude = $request->longitude;
            $user->save();

            return response()->json(['message' => trans('api.user.location_updated')]);

        }else{

            return response()->json(['error' => trans('api.user.user_not_found')], 422);

        }

    }

    public function update_language(Request $request){

        $this->validate($request, [
                'language' => 'required',                
            ]);

        if($user = User::find(Auth::user()->id)){

            $user->language = $request->language;           
            $user->save();

            return response()->json(['message' => trans('api.user.language_updated'),'language'=>$request->language]);

        }else{

            return response()->json(['error' => trans('api.user.user_not_found')], 422);

        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function details(Request $request){

        $this->validate($request, [
            'device_type' => 'in:android,ios',
        ]);

        try{

            if($user = User::find(Auth::user()->id)){

                if($request->has('device_token')){
                    $user->device_token = $request->device_token;
                }

                if($request->has('device_type')){
                    $user->device_type = $request->device_type;
                }

                if($request->has('device_id')){
                    $user->device_id = $request->device_id;
                }

                $user->save();

                $user->currency = Setting::get('currency');
                $user->sos = Setting::get('sos_number', '911');                
                $user->app_contact = Setting::get('app_contact', '5777');                
                $user->measurement = Setting::get('distance', 'Kms');                
                $user->stripe_secret_key = Setting::get('stripe_secret_key', '');
                $user->stripe_publishable_key = Setting::get('stripe_publishable_key', '');
                $user->driverapplink = Setting::get('driverapplink', '');
                return $user;

            } else {
                return response()->json(['error' => trans('api.user.user_not_found')], 422);
            }
        }
        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function update_profile(Request $request)
    {

        $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'max:255',
                'email' => 'email|unique:users,email,'.Auth::user()->id,
                'mobile' => 'required',
                'picture' => 'mimes:jpeg,bmp,png',
            ]);

         try {

            $user = User::findOrFail(Auth::user()->id);

            if($request->has('first_name')){ 
                $user->first_name = $request->first_name;
            }
            
            if($request->has('last_name')){
                $user->last_name = $request->last_name;
            }
            
            if($request->has('email')){
                $user->email = $request->email;
            }
        
            if($request->has('mobile')){
                $user->mobile = $request->mobile;
            }
            
            if($request->has('gender')){
                $user->gender = $request->gender;
            }

            if($request->has('language')){
                $user->language = $request->language;
            }

            if ($request->picture != "") {
                Storage::delete($user->picture);
                $user->picture = $request->picture->store('user/profile');
            }

            $user->save();

            $user->currency = Setting::get('currency');
            $user->sos = Setting::get('sos_number', '911');                
            $user->app_contact = Setting::get('app_contact', '5777');
            $user->measurement = Setting::get('distance', 'Kms');

            if($request->ajax()) {
                return response()->json($user);
            }else{
                return back()->with('flash_success', trans('api.user.profile_updated'));
            }
        }

        catch (ModelNotFoundException $e) {
             return response()->json(['error' => trans('api.user.user_not_found')], 422);
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function services() {

        if($serviceList = ServiceType::all()) {
            return $serviceList;
        } else {
            return response()->json(['error' => trans('api.services_not_found')], 422);
        }

    }


    /**
     * get the district list.
     *
     * 29/4/2019 added by Ashok.
     * @return \Illuminate\Http\Response
     */

    public function districts() {

        if($districtList = District::all()) {
            return response()->json($districtList);
        } else {
            return response()->json(['error' => trans('api.services_not_found')], 422);
        }

    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function send_request(Request $request) {

        $this->validate($request, [
                's_latitude' => 'required|numeric',
                'd_latitude' => 'required|numeric',
                's_longitude' => 'numeric',
                'd_longitude' => 'numeric',
                'service_type' => 'required|numeric|exists:service_types,id',
                //'promo_code' => 'exists:promocodes,promo_code',
                'distance' => 'required|numeric',
                'use_wallet' => 'numeric',
                'payment_mode' => 'required|in:CASH,CARD,PAYPAL',
                'card_id' => ['required_if:payment_mode,CARD','exists:cards,card_id,user_id,'.Auth::user()->id],
            ],['s_latitude.required'=>'Source address required','d_latitude.required'=>'Destination address required']);

        /*Log::info('New Request from User: '.Auth::user()->id);
        Log::info('Request Details:', $request->all());*/

        $ActiveRequests = UserRequests::PendingRequest(Auth::user()->id)->count();

        if($ActiveRequests > 0) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.ride.request_inprogress')], 422);
            } else {
                return redirect('dashboard')->with('flash_error', trans('api.ride.request_inprogress'));
            }
        }

        if($request->has('schedule_date') && $request->has('schedule_time')){
            $beforeschedule_time = (new Carbon("$request->schedule_date $request->schedule_time"))->subHour(1);
            $afterschedule_time = (new Carbon("$request->schedule_date $request->schedule_time"))->addHour(1);

            $CheckScheduling = UserRequests::where('status','SCHEDULED')
                            ->where('user_id', Auth::user()->id)
                            ->whereBetween('schedule_at',[$beforeschedule_time,$afterschedule_time])
                            ->count();


            if($CheckScheduling > 0){
                if($request->ajax()) {
                    return response()->json(['error' => trans('api.ride.request_scheduled')], 422);
                }else{
                    return redirect('dashboard')->with('flash_error', trans('api.ride.request_scheduled'));
                }
            }

        }

        $distance = Setting::get('provider_search_radius', '10');
       
        $latitude = $request->s_latitude;
        $longitude = $request->s_longitude;
        $service_type = $request->service_type;

        $Providers = Provider::with('service')
            ->select(DB::Raw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) AS distance"),'id')
            ->where('status', 'approved')
            ->whereRaw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
            ->whereHas('service', function($query) use ($service_type){
                        $query->where('status','active');
                        $query->where('service_type_id',$service_type);
                    })
            ->orderBy('distance','asc')
            ->get();
   //  dd($Providers);
        // List Providers who are currently busy and add them to the filter list.

        if(count($Providers) == 0) {
            if($request->ajax()) {
                // Push Notification to User
                return response()->json(['error' => trans('api.ride.no_providers_found')], 422); 
            }else{
                return back()->with('flash_success', trans('api.ride.no_providers_found'));
            }
        }

        try{
            $googleMaps = new GoogleMapsService();
            $details = $googleMaps->getDirections($request->s_latitude, $request->s_longitude, $request->d_latitude, $request->d_longitude);
            $route_key = isset($details['routes'][0]['overview_polyline']['points']) ? $details['routes'][0]['overview_polyline']['points'] : '';



            $UserRequest = new UserRequests;
            $UserRequest->booking_id = Helper::generate_booking_id();
         

            $UserRequest->user_id = Auth::user()->id;
            
            if((Setting::get('manual_request',0) == 0) && (Setting::get('broadcast_request',0) == 0)){
                $UserRequest->current_provider_id = $Providers[0]->id;
            }else{
                $UserRequest->current_provider_id = 0;
            }

            $UserRequest->service_type_id = $request->service_type;
            $UserRequest->rental_hours = $request->rental_hours;
            $UserRequest->payment_mode = $request->payment_mode;
            $UserRequest->promocode_id = $request->promocode_id ? : 0;
            
            $UserRequest->status = 'SEARCHING';

            $UserRequest->s_address = $request->s_address ? : "";
            $UserRequest->d_address = $request->d_address ? : "";

            $UserRequest->s_latitude = $request->s_latitude;
            $UserRequest->s_longitude = $request->s_longitude;

            $UserRequest->d_latitude = $request->d_latitude;
            $UserRequest->d_longitude = $request->d_longitude;
            $UserRequest->distance = $request->distance;
            $UserRequest->unit = Setting::get('distance', 'Kms');

            if(Auth::user()->wallet_balance > 0){
                $UserRequest->use_wallet = $request->use_wallet ? : 0;
            }

            if(Setting::get('track_distance', 0) == 1){
                $UserRequest->is_track = "YES";
            }

            $UserRequest->otp = mt_rand(1000 , 9999);

            $UserRequest->assigned_at = Carbon::now();
            $UserRequest->route_key = $route_key;

            if($Providers->count() <= Setting::get('surge_trigger') && $Providers->count() > 0){
                $UserRequest->surge = 1;
            }

            if($request->has('schedule_date') && $request->has('schedule_time')){
                $UserRequest->schedule_at = date("Y-m-d H:i:s",strtotime("$request->schedule_date $request->schedule_time"));
                $UserRequest->is_scheduled = 'YES';
            }

             if((Setting::get('manual_request',0) == 0) && (Setting::get('broadcast_request',0) == 0)){
                //Log::info('New Request id : '. $UserRequest->id .' Assigned to provider : '. $UserRequest->current_provider_id);
                (new SendPushNotification)->IncomingRequest($Providers[0]->id);
            }

            $UserRequest->save();
           

            // update payment mode
            User::where('id',Auth::user()->id)->update(['payment_mode' => $request->payment_mode]);

            if($request->has('card_id')){

                Card::where('user_id',Auth::user()->id)->update(['is_default' => 0]);
                Card::where('card_id',$request->card_id)->update(['is_default' => 1]);
            }

            if(Setting::get('manual_request',0) == 0){
                foreach ($Providers as $key => $Provider) {

                    if(Setting::get('broadcast_request',0) == 1){
                       (new SendPushNotification)->IncomingRequest($Provider->id); 
                    }

                    $Filter = new RequestFilter;
                    // Send push notifications to the first provider
                    // incoming request push to provider
                    
                    $Filter->request_id = $UserRequest->id;
                    $Filter->provider_id = $Provider->id; 
                    $Filter->save();
                }
            }

            if($request->ajax()) {
                return response()->json([
                        'message' => 'New request Created!',
                        'request_id' => $UserRequest->id,
                        'current_provider' => $UserRequest->current_provider_id,
                    ]);
            }else{
                return redirect('dashboard');
            }

        } catch (Exception $e) {            
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', trans('api.something_went_wrong'));
            }
        }
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function cancel_request(Request $request) {

        $this->validate($request, [
            'request_id' => 'required|numeric|exists:user_requests,id,user_id,'.Auth::user()->id,
        ]);

        try{

            $UserRequest = UserRequests::findOrFail($request->request_id);

            if($UserRequest->status == 'CANCELLED')
            {
                if($request->ajax()) {
                    return response()->json(['error' => trans('api.ride.already_cancelled')], 422); 
                }else{
                    return back()->with('flash_error', trans('api.ride.already_cancelled'));
                }
            }

            if(in_array($UserRequest->status, ['SEARCHING','STARTED','ARRIVED','SCHEDULED'])) {

                if($UserRequest->status != 'SEARCHING'){
                    $this->validate($request, [
                        'cancel_reason'=> 'max:255',
                    ]);
                }

                $UserRequest->status = 'CANCELLED';
                $UserRequest->cancel_reason = $request->cancel_reason;
                $UserRequest->cancelled_by = 'USER';
                $UserRequest->save();

                RequestFilter::where('request_id', $UserRequest->id)->delete();

                if($UserRequest->status != 'SCHEDULED'){

                    if($UserRequest->provider_id != 0){

                        ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' => 'active']);

                    }
                }

                 // Send Push Notification to User
                (new SendPushNotification)->UserCancellRide($UserRequest);

                if($request->ajax()) {
                    return response()->json(['message' => trans('api.ride.ride_cancelled')]); 
                }else{
                    return redirect('dashboard')->with('flash_success',trans('api.ride.ride_cancelled'));
                }

            } else {
                if($request->ajax()) {
                    return response()->json(['error' => trans('api.ride.already_onride')], 422); 
                }else{
                    return back()->with('flash_error', trans('api.ride.already_onride'));
                }
            }
        }

        catch (ModelNotFoundException $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')],500);
            }else{
                return back()->with('flash_error', trans('api.something_went_wrong'));
            }
        }

    }

    /**
     * Show the request status check.
     *
     * @return \Illuminate\Http\Response
     */

    public function request_status_check() {

        try{
            $check_status = ['CANCELLED', 'SCHEDULED'];

            $UserRequests = UserRequests::UserRequestStatusCheck(Auth::user()->id, $check_status)
                                        ->get()
                                        ->toArray();
                                        

            $search_status = ['SEARCHING','SCHEDULED'];
            $UserRequestsFilter = UserRequests::UserRequestAssignProvider(Auth::user()->id,$search_status)->get(); 

             //Log::info($UserRequestsFilter);



            $Timeout = Setting::get('provider_select_timeout', 180);

            if(!empty($UserRequestsFilter)){
                for ($i=0; $i < sizeof($UserRequestsFilter); $i++) {
                    $ExpiredTime = $Timeout - (time() - strtotime($UserRequestsFilter[$i]->assigned_at));
                    if($UserRequestsFilter[$i]->status == 'SEARCHING' && $ExpiredTime < 0) {
                        $Providertrip = new TripController();
                        $Providertrip->assign_next_provider($UserRequestsFilter[$i]->id);
                    }else if($UserRequestsFilter[$i]->status == 'SEARCHING' && $ExpiredTime > 0){
                        break;
                    }
                }
            }
          
            return response()->json(['data' => $UserRequests , 'sos' => Setting::get('sos_number', '911'), 'cash' => (int)Setting::get('CASH', 1), 'card' => (int)Setting::get('CARD', 0),'currency'=>Setting::get('currency','$')]);

        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function rate_provider(Request $request) {

        $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id,user_id,'.Auth::user()->id,
                'rating' => 'required|integer|in:1,2,3,4,5',
                'comment' => 'max:255',
            ]);
    
        $UserRequests = UserRequests::where('id' ,$request->request_id)
                ->where('status' ,'COMPLETED')
                ->where('paid', 0)
                ->first();

        if ($UserRequests) {
            if($request->ajax()){
                return response()->json(['error' => trans('api.user.not_paid')], 422);
            } else {
                return back()->with('flash_error', trans('api.user.not_paid'));
            }
        }

        try{

            $UserRequest = UserRequests::findOrFail($request->request_id);
            
            if($UserRequest->rating == null) {
                UserRequestRating::create([
                        'provider_id' => $UserRequest->provider_id,
                        'user_id' => $UserRequest->user_id,
                        'request_id' => $UserRequest->id,
                        'user_rating' => $request->rating,
                        'user_comment' => $request->comment,
                    ]);
            } else {
                $UserRequest->rating->update([
                        'user_rating' => $request->rating,
                        'user_comment' => $request->comment,
                    ]);
            }

            $UserRequest->user_rated = 1;
            $UserRequest->save();

            $average = UserRequestRating::where('provider_id', $UserRequest->provider_id)->avg('user_rating');

            Provider::where('id',$UserRequest->provider_id)->update(['rating' => $average]);

            // Send Push Notification to Provider 
            if($request->ajax()){
                return response()->json(['message' => trans('api.ride.provider_rated')]); 
            }else{
                return redirect('dashboard')->with('flash_success', trans('api.ride.provider_rated'));
            }
        } catch (Exception $e) {
            if($request->ajax()){
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', trans('api.something_went_wrong'));
            }
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function modifiy_request(Request $request) {

        $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id,user_id,'.Auth::user()->id,
                'latitude' => 'sometimes|nullable|numeric',
                'longitude' => 'sometimes|nullable|numeric',
                'address' => 'sometimes|nullable',
                'payment_mode' => 'sometimes|nullable|in:CASH,CARD,PAYPAL',
                'card_id' => ['required_if:payment_mode,CARD','exists:cards,card_id,user_id,'.Auth::user()->id],
            ]);

        try{

            $UserRequest = UserRequests::findOrFail($request->request_id);

            if(!empty($request->latitude) && !empty($request->longitude)){
                $UserRequest->d_latitude = $request->latitude?:$UserRequest->d_latitude;
                $UserRequest->d_longitude = $request->longitude?:$UserRequest->d_longitude;
                $UserRequest->d_address =  $request->address?:$UserRequest->d_address;
            }

            if(!empty($request->payment_mode)){
                $UserRequest->payment_mode = $request->payment_mode?:$UserRequest->payment_mode;
                if($request->payment_mode=='CARD' && $UserRequest->status=='DROPPED'){
                    $UserRequest->status='COMPLETED';
                }
            }
                
            $UserRequest->save();

            

            if($request->has('card_id')){

                Card::where('user_id',Auth::user()->id)->update(['is_default' => 0]);
                Card::where('card_id',$request->card_id)->update(['is_default' => 1]);
            }

            // Send Push Notification to Provider 
            if($request->ajax()){
                return response()->json(['message' => trans('api.ride.request_modify_location')]); 
            }else{
                return redirect('dashboard')->with('flash_success', trans('api.ride.request_modify_location'));
            }
        } catch (Exception $e) {
            if($request->ajax()){
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', trans('api.something_went_wrong'));
            }
        }

    } 


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function trips() {
    
        try{
            $UserRequests = UserRequests::UserTrips(Auth::user()->id)->get();
            if(!empty($UserRequests)){
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($UserRequests as $key => $value) {
                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                            "autoscale=1".
                            "&size=320x130".
                            "&maptype=terrian".
                            "&format=png".
                            "&visual_refresh=true".
                            "&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
                            "&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
                            "&path=color:0x191919|weight:3|enc:".$value->route_key.
                            "&key=".Setting::get('map_key');
                }
            }
            return $UserRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function estimated_fare(Request $request){

        $this->validate($request,[
                's_latitude' => 'required|numeric',
                's_longitude' => 'numeric',
                'd_latitude' => 'required|numeric',
                'd_longitude' => 'numeric',
                'service_type' => 'required|numeric|exists:service_types,id',
            ],['s_latitude.required'=>'Source address required','d_latitude.required'=>'Destination address required']);

        try{       
            $response = new ServiceTypes();

            $responsedata=$response->calculateFare($request->all(), 1);

            if(!empty($responsedata['errors'])){
                throw new Exception($responsedata['errors']);
            }
            else{
                return response()->json($responsedata['data']);
            }

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function trip_details(Request $request) {

         $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id',
            ]);
    
        try{
            $UserRequests = UserRequests::UserTripDetails(Auth::user()->id,$request->request_id)->get();
            if(!empty($UserRequests)){
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($UserRequests as $key => $value) {
                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                            "autoscale=1".
                            "&size=320x130".
                            "&maptype=terrian".
                            "&format=png".
                            "&visual_refresh=true".
                            "&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
                            "&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
                            "&path=color:0x191919|weight:3|enc:".$value->route_key.
                            "&key=".Setting::get('map_key');
                }
            }
            return $UserRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }

    /**
     * get all promo code.
     *
     * @return \Illuminate\Http\Response
     */

    public function promocodes() {
        try{
            //$this->check_expiry();

            return PromocodeUsage::Active()
                    ->where('user_id', Auth::user()->id)
                    ->with('promocode')
                    ->get();

        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    } 


    /*public function check_expiry(){
        try{
            $Promocode = Promocode::all();
            foreach ($Promocode as $index => $promo) {
                if(date("Y-m-d") > $promo->expiration){
                    $promo->status = 'EXPIRED';
                    $promo->save();
                    PromocodeUsage::where('promocode_id', $promo->id)->update(['status' => 'EXPIRED']);
                }else{
                    PromocodeUsage::where('promocode_id', $promo->id)
                            ->where('status','<>','USED')
                            ->update(['status' => 'ADDED']);

                    PromocodePassbook::create([
                            'user_id' => Auth::user()->id,
                            'status' => 'ADDED',
                            'promocode_id' => $promo->id
                        ]);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }*/


    /**
     * add promo code.
     *
     * @return \Illuminate\Http\Response
     */
    public function list_promocode(Request $request){
        try{

        $promo_list =Promocode::where('expiration','>=',date("Y-m-d H:i"))
                ->whereDoesntHave('promousage', function($query) {
                            $query->where('user_id',Auth::user()->id);
                        })
                ->get(); 
        if($request->ajax()){
            return response()->json([
                    'promo_list' => $promo_list
                ]);  
             }else{
                return $promo_list;
             }    
        } catch (Exception $e) {
            if($request->ajax()){
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', trans('api.something_went_wrong'));
            }
        }
    }
    

    public function add_promocode(Request $request) {

        $this->validate($request, [
                'promocode' => 'required|exists:promocodes,promo_code',
            ]);

        try{

            $find_promo = Promocode::where('promo_code',$request->promocode)->first();

            if($find_promo->status == 'EXPIRED' || (date("Y-m-d") > $find_promo->expiration)){

                if($request->ajax()){

                    return response()->json([
                        'message' => trans('api.promocode_expired'), 
                        'code' => 'promocode_expired'
                    ]);

                }else{
                    return back()->with('flash_error', trans('api.promocode_expired'));
                }

            }elseif(PromocodeUsage::where('promocode_id',$find_promo->id)->where('user_id', Auth::user()->id)->whereIN('status',['ADDED','USED'])->count() > 0){

                if($request->ajax()){

                    return response()->json([
                        'message' => trans('api.promocode_already_in_use'), 
                        'code' => 'promocode_already_in_use'
                        ]);

                }else{
                    return back()->with('flash_error', trans('api.promocode_already_in_use'));
                }

            }else{

                $promo = new PromocodeUsage;
                $promo->promocode_id = $find_promo->id;
                $promo->user_id = Auth::user()->id;
                $promo->status = 'ADDED';
                $promo->save();
                
                $count_id = PromocodePassbook::where('promocode_id' , $find_promo->id)->count();
                //dd($count_id); 
                if($count_id == 0){

                   PromocodePassbook::create([
                            'user_id' => Auth::user()->id,
                            'status' => 'ADDED',
                            'promocode_id' => $find_promo->id
                        ]);
                }
                if($request->ajax()){

                    return response()->json([
                            'message' => trans('api.promocode_applied') ,
                            'code' => 'promocode_applied'
                         ]); 

                }else{
                    return back()->with('flash_success', trans('api.promocode_applied'));
                }
            }

        }

        catch (Exception $e) {
            if($request->ajax()){
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', trans('api.something_went_wrong'));
            }
        }

    } 

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function upcoming_trips() {
    
        try{
            $UserRequests = UserRequests::UserUpcomingTrips(Auth::user()->id)->get();
            if(!empty($UserRequests)){
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($UserRequests as $key => $value) {
                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                            "autoscale=1".
                            "&size=320x130".
                            "&maptype=terrian".
                            "&format=png".
                            "&visual_refresh=true".
                            "&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
                            "&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
                            "&path=color:0x000000|weight:3|enc:".$value->route_key.
                            "&key=".Setting::get('map_key');
                }
            }
            return $UserRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function upcoming_trip_details(Request $request) {

         $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id',
            ]);
    
        try{
            $UserRequests = UserRequests::UserUpcomingTripDetails(Auth::user()->id,$request->request_id)->get();
            if(!empty($UserRequests)){
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($UserRequests as $key => $value) {
                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                            "autoscale=1".
                            "&size=320x130".
                            "&maptype=terrian".
                            "&format=png".
                            "&visual_refresh=true".
                            "&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
                            "&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
                            "&path=color:0x000000|weight:3|enc:".$value->route_key.
                            "&key=".Setting::get('map_key');
                }
            }
            return $UserRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }


    /**
     * Show the nearby providers.
     *
     * @return \Illuminate\Http\Response
     */

    public function show_providers(Request $request) {

        $this->validate($request, [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'service' => 'numeric|exists:service_types,id',
            ]);

        try{

            $distance = Setting::get('provider_search_radius', '10');
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            if($request->has('service')){

                $ActiveProviders = ProviderService::AvailableServiceProvider($request->service)
                                    ->get()->pluck('provider_id');

                $Providers = Provider::with('service')->whereIn('id', $ActiveProviders)
                    ->where('status', 'approved')
                    ->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                    ->get();

            } else {

                $ActiveProviders = ProviderService::where('status', 'active')
                                    ->get()->pluck('provider_id');

                $Providers = Provider::with('service')->whereIn('id', $ActiveProviders)
                    ->where('status', 'approved')
                    ->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                    ->get();
            }

        
            return $Providers;

        } catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', trans('api.something_went_wrong'));
            }
        }
    }


    /**
     * Forgot Password.
     *
     * @return \Illuminate\Http\Response
     */


    public function forgot_password(Request $request){

        $this->validate($request, [
                'email' => 'required|email|exists:users,email',
            ]);

        try{  
            
            $user = User::where('email' , $request->email)->first();

            $otp = mt_rand(100000, 999999);

            $user->otp = $otp;
            $user->save();

            Notification::send($user, new ResetPasswordOTP($otp));

            return response()->json([
                'message' => 'OTP sent to your email!',
                'user' => $user
            ]);

        }catch(Exception $e){
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }


    /**
     * Reset Password.
     *
     * @return \Illuminate\Http\Response
     */

    public function reset_password(Request $request){

        $this->validate($request, [
                'password' => 'required|confirmed|min:6',
                'id' => 'required|numeric|exists:users,id'

            ]);

        try{

            $User = User::findOrFail($request->id);
            // $UpdatedAt = date_create($User->updated_at);
            // $CurrentAt = date_create(date('Y-m-d H:i:s'));
            // $ExpiredAt = date_diff($UpdatedAt,$CurrentAt);
            // $ExpiredMin = $ExpiredAt->i;
            $User->password = bcrypt($request->password);
            $User->save();
            if($request->ajax()) {
                return response()->json(['message' => trans('api.user.password_updated')]);
            }
           
            

        }catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }
        }
    }

    /**
     * help Details.
     *
     * @return \Illuminate\Http\Response
     */

    public function help_details(Request $request){

        try{

            if($request->ajax()) {
                return response()->json([
                    'contact_number' => Setting::get('contact_number',''), 
                    'contact_email' => Setting::get('contact_email','')
                     ]);
            }

        }catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }
        }
    }   



    /**
     * Show the wallet usage.
     *
     * @return \Illuminate\Http\Response
     */

    public function wallet_passbook(Request $request)
    {
        try{
            $start_node= $request->start_node;
            $limit= $request->limit;
            
            $wallet_transation = UserWallet::where('user_id',Auth::user()->id);
            if(!empty($limit)){
                $wallet_transation =$wallet_transation->offset($start_node);
                $wallet_transation =$wallet_transation->limit($limit);
            }

            $wallet_transation =$wallet_transation->orderBy('id','desc')->get();

            return response()->json(['wallet_transation' => $wallet_transation,'wallet_balance'=>Auth::user()->wallet_balance]);

        } catch (Exception $e) {
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }


    /**
     * Show the promo usage.
     *
     * @return \Illuminate\Http\Response
     */

    public function promo_passbook(Request $request)
    {
        try{
            
            return PromocodePassbook::where('user_id',Auth::user()->id)->with('promocode')->get();

        } catch (Exception $e) {
             
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function test(Request $request)
    {
         //$push =  (new SendPushNotification)->IncomingRequest($request->id); 
         $push = (new SendPushNotification)->Arrived($request->id);

         
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function pricing_logic($id)
    {
       //return $id;
       $logic = ServiceType::select('calculator')->where('id',$id)->first();
       return $logic;

    }

    public function fare(Request $request){

        $this->validate($request,[
                's_latitude' => 'required|numeric',
                's_longitude' => 'numeric',
                'd_latitude' => 'required|numeric',
                'd_longitude' => 'numeric',
                'service_type' => 'required|numeric|exists:service_types,id',
            ],['s_latitude.required'=>'Source address required','d_latitude.required'=>'Destination address required']);

        try{       
            $response = new ServiceTypes();
            $responsedata=$response->calculateFare($request->all());

            if(!empty($responsedata['errors'])){
                throw new Exception($responsedata['errors']);
            }
            else{
                return response()->json($responsedata['data']);
            }

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    /**
     * Show the wallet usage.
     *
     * @return \Illuminate\Http\Response
     */

    /*public function check(Request $request)
    {

        $this->validate($request, [
                'name' => 'required',
                'age' => 'required',
                'work' => 'required',
            ]);
         return Work::create(request(['name', 'age' ,'work']));
    }*/    

    public function chatPush(Request $request){

        $this->validate($request,[
                'user_id' => 'required|numeric',
                'message' => 'required',
            ]);       

        try{

            $user_id=$request->user_id;
            $message=$request->message;
            $sender=$request->sender;

            $message = \PushNotification::Message($message,array(
            'badge' => 1,
            'sound' => 'default',
            'custom' => array('type' => 'chat')
            ));

            (new SendPushNotification)->sendPushToUser($user_id, $message);         

            return response()->json(['success' => 'true']);

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function CheckVersion(Request $request){

        $this->validate($request,[
                'sender' => 'in:user,provider',
                'device_type' => 'in:android,ios',
                'version' => 'required',
            ]);       

        try{

            $sender=$request->sender;
            $device_type=$request->device_type;
            $version=$request->version;

            if($sender=='user'){
                if($device_type=='ios'){
                    $curversion=Setting::get('version_ios_user');
                    if($curversion==$version){
                        return response()->json(['force_update' => false]);
                    }
                    elseif($curversion>$version){
                        return response()->json(['force_update' => true, 'url'=>Setting::get('store_link_ios_user')]);
                    }
                    else{
                        return response()->json(['force_update' => false]);
                    }
                }
                else{
                    $curversion=Setting::get('version_android_user');
                    if($curversion==$version){
                        return response()->json(['force_update' => false]);
                    }
                    elseif($curversion>$version){                        
                        return response()->json(['force_update' => true, 'url'=>Setting::get('store_link_android_user')]);
                    }
                    else{
                        return response()->json(['force_update' => false]);
                    }
                }
            }
            else{
                if($device_type=='ios'){
                    $curversion=Setting::get('version_ios_provider');
                    if($curversion==$version){
                        return response()->json(['force_update' => false]);
                    }
                    elseif($curversion>$version){                        
                        return response()->json(['force_update' => true, 'url'=>Setting::get('store_link_ios_provider')]);
                    }
                    else{
                        return response()->json(['force_update' => false]);
                    }
                }
                else{
                    $curversion=Setting::get('version_android_provider');
                    if($curversion==$version){
                        return response()->json(['force_update' => false]);
                    }
                    elseif($curversion>$version){
                        return response()->json(['force_update' => true, 'url'=>Setting::get('store_link_android_provider')]);                        
                    }
                    else{
                        return response()->json(['force_update' => false]);
                    }
                }
            }           

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function checkapi(Request $request)
    {
        Log::info('Request Details:', $request->all());
        return response()->json(['sucess' => true]);        
       
    }
    /**
     * reassign request.
     *
     * @return \Illuminate\Http\Response
     */
    public function Reassign(Request $request){

        try{

            $data=$request->all();
            $request_id = $data['request_id'];
            $provider_id = $data['provider_id'];
            $downreason = $data['downreason'];
            $downreasonindetailed = $data['downreasonindetailed'];
            $category= $data['category'];
            $subcategory= $data['subcategory'];
            $description= $data['description'];



            $Request = UserRequests::findOrFail($request_id);
           
            $getticketdetails = DB::table('master_tickets')
		->where('ticketid','!=','')
                ->where('status','!=',1)
		->orderBy('created_at','desc')
        ->inRandomOrder()
		->take(1)
		->first();

                
                      //$Provider = Provider::findOrFail($provider_id);
           
           $distance = Setting::get('provider_search_radius', '10');
       
           $latitude = $getticketdetails->lat;
           $longitude = $getticketdetails->log; 
           $service_type = 2;

            /*$Provider = Provider::with('service')
            ->select(DB::Raw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) AS distance"),'id','latitude','longitude','mobile')
            ->where('status', 'approved')
            ->where('id', '!=' ,$provider_id)
            ->whereRaw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
            ->whereHas('service', function($query) use ($service_type){
                        $query->where('status','active');
                        $query->where('service_type_id',$service_type);
                    })
            ->orderBy('distance','asc')
            ->first();*/

           $Provider = Provider::findOrFail($provider_id);

           //dd($Provider->mobile);
            

            if($Provider) {
            $api_key = '35FEABDB060BF6';
            $mobile= $Provider->mobile;
            $contacts = $Provider->mobile;
            $from = 'TERAOD';
            $template_id= ''; 
            $sms_text = urlencode('Hi,You have Recieved request for odisha fleet.Please open the App and Accept the Request !..');

            $api_url = "http://sms.hitechsms.com/app/smsapi/index.php?key=".$api_key."&campaign=0&routeid=13&type=text&contacts=".$contacts."&senderid=".$from."&msg=".$sms_text;

           //Submit to server

            $response = file_get_contents( $api_url);
             }

            $Request->provider_id =  $Provider->id;
            $Request->downreason = $downreason ;
            $Request->downreasonindetailed = $downreasonindetailed;
            $Request->status = 'INCOMING';
            $Request->current_provider_id = $Provider->id;
            $Request->description = $description;
            $Request->save();

          
       
           // Delete from filter so that it doesn't show up in status checks.
            RequestFilter::where('request_id', $request_id)->delete();

 
            ProviderService::where('provider_id',$Provider->id)->update(['status' =>'active']);

            (new SendPushNotification)->IncomingRequest($Provider->id);

            try {
                RequestFilter::where('request_id', $Request->id)
                    ->where('provider_id', $Provider->id)
                    ->firstOrFail();
            } catch (Exception $e) {
                $Filter = new RequestFilter;
                $Filter->request_id = $Request->id;
                $Filter->provider_id = $Provider->id; 
                $Filter->status = 0;
                $Filter->save();
            }

            return response()->json(['message' => 'success','status'=>1]);

        } catch(Exception $e) {
            return response()->json(['message' => 'failure','status'=>0,'error' => $e->getMessage()], 500);
        }

    }

       /**
     * save the upload documents
     *
     * @return \Illuminate\Http\Response
     */
    public function savedocuments(Request $request){

        try{

           ini_set('post_max_size', '100M');
           ini_set('upload_max_filesize', '100M');

            $documents = $request->all();
             Log::info('Received request data:', $documents); 
            $downreason = $request->category;
            $downreasondetailed = $request->description;
            $issue_type	= $request->issue_type ? $request->issue_type : null;
            $ownership=$request->ownership ? $request->ownership : null;
            
            $request_ids = $documents['request_id'];
            $requestids= explode(',',$request_ids);
                    Log::info('Request IDs to process:', $requestids);

             //dd($requestids);   
               $i=0;    
              $beforefile_names = [];
              $afterfile_names = [];
              $otdrfile_names=[];
              $joint_beforeimgs = [];
              $joint_afterimgs=[];
            //   $video_name = null;

             foreach($requestids as $request_id){
                           Log::info("Processing request ID: $request_id");

                 DB::table('user_requests')->where('id',$request_id)->update(array(
                                 'status'=>'COMPLETED',
                                 'downreason'=>$downreason,
                                 'downreasonindetailed'=>$downreasondetailed,
                                 'issue_type'=>$issue_type,
                                 'ownership'=>$ownership,
                                 'autoclose'=>'Manual',
                                 'finished_at'=> date('Y-m-d H:i:s')
                  ));
              Log::info("Updated user_requests for request ID: $request_id");

                 
                   if($i==0){
                        $getLatLong = function ($key) use ($request) {

                            $lat = '0_0';
                            $long = '0_0';

                            if ($request->has($key)) {
                                $val = $request->input($key);

                                if ($val) {
                                    // remove quotes, spaces
                                    $val = trim($val, "\"' ");

                                    // support both "lat,long" and "lat:long"
                                    if (str_contains($val, ',')) {
                                        [$lat, $long] = explode(',', $val);
                                    } elseif (str_contains($val, ':')) {
                                        [$lat, $long] = explode(':', $val);
                                    }
                                }
                            }

                            // make filename-safe
                            // $lat  = str_replace(['.', '-', '"'], '_', $lat);
                            // $long = str_replace(['.', '-', '"'], '_', $long);

                            return [
                                'lat' => $lat,
                                'long' => $long
                            ];
                        };



                if ($request->hasFile('before_image')) {
                        $before_image = $request->before_image;
                        $coords = $getLatLong('before_img_latlong');

                        foreach ($before_image as $image) {
                            $extension = $image->getClientOriginalExtension();
                            $beforefilename =  time() . uniqid() . '_' .
                                                $coords['lat'] . '_' .
                                                $coords['long'] .
                                                '.' . $extension;
                            $image->move(public_path('uploads/SubmitFiles'), $beforefilename);
                           array_push($beforefile_names, $beforefilename);
                        Log::info("Uploaded before_image: $beforefilename");

                          }
                }


                if ($request->hasFile('after_image')) {
                        $after_image = $request->after_image;
                        $coords = $getLatLong('after_img_latlong');
                        foreach ($after_image as $image) {
                           $extension = $image->getClientOriginalExtension();
                           $afterfilename = time() . uniqid() . '_' . $coords['lat'] . '_' . $coords['long'] . '.' . $extension;

                           $image->move(public_path('uploads/SubmitFiles'), $afterfilename);
                           array_push($afterfile_names, $afterfilename);
                        Log::info("Uploaded after_image: $afterfilename");

                          }
                }
                if ($request->hasFile('otdr_img')) {
                        $otdr_img = $request->otdr_img;
                        $coords = $getLatLong('otdr_img_latlong');

                        foreach ($otdr_img as $image) {
                            $extension = $image->getClientOriginalExtension();
                            $otdrfilename = time() . uniqid() . '_' . $coords['lat'] . '_' . $coords['long'] . '.' . $extension;
                            $image->move(public_path('uploads/SubmitFiles'), $otdrfilename);
                           array_push($otdrfile_names, $otdrfilename);
                        Log::info("Uploaded otdr_img: $otdrfilename");

                          }
                }
                if ($request->hasFile('joint_enclouser_beforeimg')) {
                        $joint_befimg = $request->joint_enclouser_beforeimg;
                        $coords = $getLatLong('joint_enclosurebefore_latlong');

                        foreach ($joint_befimg as $image) {
                            $extension = $image->getClientOriginalExtension();
                            $joint_before_filename = time() . uniqid() . '_' . $coords['lat'] . '_' . $coords['long'] . '.' . $extension;
                       
                           $image->move(public_path('uploads/SubmitFiles'), $joint_before_filename);
                           array_push($joint_beforeimgs, $joint_before_filename);
                        Log::info("Uploaded joint_enclouser_beforeimg: $joint_before_filename");

                          }
                }
                if ($request->hasFile('joint_enclouser_afterimg')) {
                        $joint_aftimg = $request->joint_enclouser_afterimg;
                        $coords = $getLatLong('joint_enclosureafter_latlong');


                        foreach ($joint_aftimg as $image) {
                            $extension = $image->getClientOriginalExtension();
                            $joint_after_filename = time() . uniqid() . '_' . $coords['lat'] . '_' . $coords['long'] . '.' . $extension;
                        
                           $image->move(public_path('uploads/SubmitFiles'), $joint_after_filename);
                           array_push($joint_afterimgs, $joint_after_filename);
                        Log::info("Uploaded joint_enclouser_afterimg: $joint_after_filename");

                          }
                }
                //  if ($request->hasFile('video')) {
                //         $video = $request->video;
                //         $extension = $video->getClientOriginalExtension();
                //         $allowedExtensions = ['mp4', 'avi', 'mov', 'wmv'];
                //         if (in_array(strtolower($extension), $allowedExtensions)) {
                //             $videofilename = $video->getClientOriginalName();
                //             $destinationPath = public_path('uploads/SubmitFiles/videos');
                //             if (!file_exists($destinationPath)) {
                //                 mkdir($destinationPath, 0777, true);
                //             }
                //             $video->move($destinationPath, $videofilename);
                //             $video_name = $videofilename;
                //             Log::info("Uploaded video: $videofilename");
                //         } else {
                //             return response()->json(['error' => 'Invalid video format. Allowed: mp4, avi, mov, wmv'], 422);
                //         }
                //     }

                 }  
                 $i++;
 

                $documents['request_id'] =$request_id;
                $documents['before_image'] =json_encode($beforefile_names);
                $documents['after_image'] =json_encode($afterfile_names);
                $documents['otdr_img'] =json_encode($otdrfile_names);
                $documents['joint_enclouser_beforeimg'] =json_encode($joint_beforeimgs);
                $documents['joint_enclouser_afterimg'] =json_encode($joint_afterimgs);
                // $documents['video'] = $video_name;

                  //Log::info($documents);

                 Log::info("Inserting SubmitFile record:", $documents);

                SubmitFile::create($documents);

              $UserRequest = UserRequests::where('id', $request_id)
                ->where('status', 'COMPLETED')
                ->firstOrFail();

                        if($UserRequest->rating == null) {
                UserRequestRating::create([
                        'provider_id' => $UserRequest->provider_id,
                        'user_id' => $UserRequest->user_id,
                        'request_id' => $UserRequest->id,
                        'provider_rating' => 5,
                        'provider_comment' => 'test',
                    ]);
            } else {
                $UserRequest->rating->update([
                        'provider_rating' => 5,
                        'provider_comment' => 'test',
                    ]);
            }

            $UserRequest->update(['provider_rated' => 1]);

           //MasterTicket::where('ticketid', 'like', '%TKTN1115%')->update(['status' =>1]);

            DB::table('master_tickets')->where('ticketid',$UserRequest->booking_id)->update(array(
                                 'status'=>1,
                  ));


            // Delete from filter so that it doesn't show up in status checks.
            RequestFilter::where('request_id', $request_id)->delete();

            ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' =>'active']);

             }
                        



            return response()->json(['success' => 'true','status'=>1]);

        } catch(Exception $e) {
              dd($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

private function getAvailableQty($employeeId, $materialId, $serial = null)
{
    $query = EmployeeMaterialLedger::where([
        'employee_id' => $employeeId,
        'material_id' => $materialId
    ]);

    if ($serial) {
        $query->where('serial_number', $serial);
    }

    $issued = (clone $query)->where('transaction_type', 'ISSUE')->sum('quantity');
    $used   = (clone $query)->where('transaction_type', 'USED')->sum('quantity');
    $return = (clone $query)->where('transaction_type', 'RETURN')->sum('quantity');

    return $issued - $used - $return;
}


public function consumeMaterials(Request $request)
{
    DB::beginTransaction();
      Log::info('Request Details:', $request->all());

    try {
           ini_set('post_max_size', '100M');
           ini_set('upload_max_filesize', '100M');
           $validator = Validator::make($request->all(), [
                'request_id'  => 'required',
                'ticket_id'   => 'required',
                'employee_id' => 'required|integer',
                'state_id'    => 'required|integer',
                'district_id' => 'required|integer',
                'materials'    => 'required|array',
                'materials.data' => 'required|array|min:1',
                'materials.issues' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

        $employeeId = $request->employee_id;
        $stateId    = $request->state_id;
        $districtId = $request->district_id;
        $ticketId   = $request->ticket_id;
        $documents = $request->all();
        $downreason = $request->category;
        $downreasondetailed = $request->description;
        $issue_type	= $request->issue_type ? $request->issue_type : null;
        $ownership=$request->ownership ? $request->ownership : null;
        
        $request_ids = $documents['request_id'];
        $requestids= explode(',',$request_ids);
                Log::info('Request IDs to process:', $requestids);

            $i=0;    
            $beforefile_names = [];
            $afterfile_names = [];
            $otdrfile_names=[];
            $joint_beforeimgs = [];
            $joint_afterimgs=[];
            $video_name = null;

            $materialsData = $request->input('materials.data', []);
            $issuesData    = $request->input('materials.issues', []);

        $materialIds = collect($materialsData)
            ->pluck('material_id')
            ->unique()
            ->toArray();

        $materialsMaster = Material::whereIn('id', $materialIds)
            ->pluck('name', 'id');
        $materialUsage = [];

        foreach ($materialsData as $item) {

            $materialId = $item['material_id'];
            $materialName = $materialsMaster[$materialId] ?? null;

            if (!$materialName) {
                continue;
            }

            // Non-serial material
            if ($item['is_serial'] == 0) {
                $qty = (float) $item['quantity'];

                if ($qty > 0) {
                    $materialUsage[$materialName] =
                        ($materialUsage[$materialName] ?? 0) + $qty;
                }
            }

            // Serial material (drum, cable, etc.)
            if ($item['is_serial'] == 1 && !empty($item['serials'])) {

                foreach ($item['serials'] as $serial) {
                    $qty = (float) $serial['quantity'];

                    if ($qty > 0) {
                        $materialUsage[$materialName] =
                            ($materialUsage[$materialName] ?? 0) + $qty;
                    }
                }
            }
        }

        $parts = [];

        foreach ($materialUsage as $name => $qty) {
            if ($qty > 0) {
                $parts[] = "{$name}={$qty}";
            }
        }

        $materialsString = '{' . implode(', ', $parts) . '}';



        foreach($requestids as $request_id){
                Log::info("Processing request ID: $request_id");

                 DB::table('user_requests')->where('id',$request_id)->update(array(
                                 'status'=>'COMPLETED',
                                 'downreason'=>$downreason,
                                 'downreasonindetailed'=>$downreasondetailed,
                                 'issue_type'=>$issue_type,
                                 'ownership'=>$ownership,
                                 'autoclose'=>'Manual',
                                 'finished_at'=> date('Y-m-d H:i:s')
                  ));
              Log::info("Updated user_requests for request ID: $request_id");

                 
            if($i==0){
                 $getLatLong = function ($key) use ($request) {

                            $lat = '0_0';
                            $long = '0_0';

                            if ($request->has($key)) {
                                $val = $request->input($key);

                                if ($val) {
                                    // remove quotes, spaces
                                    $val = trim($val, "\"' ");

                                    // support both "lat,long" and "lat:long"
                                    if (str_contains($val, ',')) {
                                        [$lat, $long] = explode(',', $val);
                                    } elseif (str_contains($val, ':')) {
                                        [$lat, $long] = explode(':', $val);
                                    }
                                }
                            }

                            // make filename-safe
                            // $lat  = str_replace(['.', '-', '"'], '_', $lat);
                            // $long = str_replace(['.', '-', '"'], '_', $long);

                            return [
                                'lat' => $lat,
                                'long' => $long
                            ];
                        };


              
                if ($request->hasFile('before_image')) {
                        $before_image = $request->before_image;
                        $coords = $getLatLong('before_img_latlong');

                        foreach ($before_image as $image) {
                            $extension = $image->getClientOriginalExtension();
                            $beforefilename =  time() . uniqid() . '_' .
                                                $coords['lat'] . '_' .
                                                $coords['long'] .
                                                '.' . $extension;
                            $image->move(public_path('uploads/SubmitFiles'), $beforefilename);
                           array_push($beforefile_names, $beforefilename);
                        Log::info("Uploaded before_image: $beforefilename");

                          }
                }


                if ($request->hasFile('after_image')) {
                        $after_image = $request->after_image;
                        $coords = $getLatLong('after_img_latlong');
                        foreach ($after_image as $image) {
                           $extension = $image->getClientOriginalExtension();
                           $afterfilename = time() . uniqid() . '_' . $coords['lat'] . '_' . $coords['long'] . '.' . $extension;

                           $image->move(public_path('uploads/SubmitFiles'), $afterfilename);
                           array_push($afterfile_names, $afterfilename);
                        Log::info("Uploaded after_image: $afterfilename");

                          }
                }
                if ($request->hasFile('otdr_img')) {
                        $otdr_img = $request->otdr_img;
                        $coords = $getLatLong('otdr_img_latlong');

                        foreach ($otdr_img as $image) {
                            $extension = $image->getClientOriginalExtension();
                            $otdrfilename = time() . uniqid() . '_' . $coords['lat'] . '_' . $coords['long'] . '.' . $extension;
                            $image->move(public_path('uploads/SubmitFiles'), $otdrfilename);
                           array_push($otdrfile_names, $otdrfilename);
                        Log::info("Uploaded otdr_img: $otdrfilename");

                          }
                }
                if ($request->hasFile('joint_enclouser_beforeimg')) {
                        $joint_befimg = $request->joint_enclouser_beforeimg;
                        $coords = $getLatLong('joint_enclosurebefore_latlong');

                        foreach ($joint_befimg as $image) {
                            $extension = $image->getClientOriginalExtension();
                            $joint_before_filename = time() . uniqid() . '_' . $coords['lat'] . '_' . $coords['long'] . '.' . $extension;
                       
                           $image->move(public_path('uploads/SubmitFiles'), $joint_before_filename);
                           array_push($joint_beforeimgs, $joint_before_filename);
                        Log::info("Uploaded joint_enclouser_beforeimg: $joint_before_filename");

                          }
                }
                if ($request->hasFile('joint_enclouser_afterimg')) {
                        $joint_aftimg = $request->joint_enclouser_afterimg;
                        $coords = $getLatLong('joint_enclosureafter_latlong');


                        foreach ($joint_aftimg as $image) {
                            $extension = $image->getClientOriginalExtension();
                            $joint_after_filename = time() . uniqid() . '_' . $coords['lat'] . '_' . $coords['long'] . '.' . $extension;
                        
                           $image->move(public_path('uploads/SubmitFiles'), $joint_after_filename);
                           array_push($joint_afterimgs, $joint_after_filename);
                        Log::info("Uploaded joint_enclouser_afterimg: $joint_after_filename");

                          }
                }
                if($request->hasFile('video')) {
                        $video = $request->video;
                        $extension = $video->getClientOriginalExtension();
                        $allowedExtensions = ['mp4', 'avi', 'mov', 'wmv'];
                        if (in_array(strtolower($extension), $allowedExtensions)) {
                            $videofilename = $video->getClientOriginalName();
                            $destinationPath = public_path('uploads/SubmitFiles/videos');
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0777, true);
                            }
                            $video->move($destinationPath, $videofilename);
                            $video_name = $videofilename;
                            Log::info("Uploaded video: $videofilename");
                        } else {
                            return response()->json(['error' => 'Invalid video format. Allowed: mp4, avi, mov, wmv'], 422);
                        }
                    }

            }  
                 $i++;
 
                 
            
                $documents['request_id'] =$request_id;
                $documents['before_image'] =json_encode($beforefile_names);
                $documents['after_image'] =json_encode($afterfile_names);
                $documents['otdr_img'] =json_encode($otdrfile_names);
                $documents['joint_enclouser_beforeimg'] =json_encode($joint_beforeimgs);
                $documents['joint_enclouser_afterimg'] =json_encode($joint_afterimgs);
                $documents['materials']  = $materialsString;
                $documents['issues'] = json_encode($issuesData);
                $documents['video'] = $video_name;

               Log::info("Inserting SubmitFile record:", $documents);

                SubmitFile::create($documents);

               $UserRequest = UserRequests::where('id', $request_id)
                ->where('status', 'COMPLETED')
                ->firstOrFail();

                        if($UserRequest->rating == null) {
                UserRequestRating::create([
                        'provider_id' => $UserRequest->provider_id,
                        'user_id' => $UserRequest->user_id,
                        'request_id' => $UserRequest->id,
                        'provider_rating' => 5,
                        'provider_comment' => 'test',
                    ]);
            } else {
                $UserRequest->rating->update([
                        'provider_rating' => 5,
                        'provider_comment' => 'test',
                    ]);
            }

            $UserRequest->update(['provider_rated' => 1]);

           //MasterTicket::where('ticketid', 'like', '%TKTN1115%')->update(['status' =>1]);

            DB::table('master_tickets')->where('ticketid',$UserRequest->booking_id)->update(array(
                                 'status'=>1,
                  ));


            // Delete from filter so that it doesn't show up in status checks.
            RequestFilter::where('request_id', $request_id)->delete();

            ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' =>'active']);

             }
       foreach ($materialsData as $item) {

            /* ---------------- NON SERIAL ---------------- */
            if ($item['is_serial'] == 0) {

                $available = $this->getAvailableQty(
                    $employeeId,
                    $item['material_id']
                );

                if ($item['quantity'] > $available) {
                    throw new Exception('Insufficient stock for material ID ' . $item['material_id']);
                }

                EmployeeMaterialLedger::create([
                    'request_id'       => $item['request_id'] ?? null,
                    'issued_item_id'   => $item['issued_item_id'] ?? null,
                    'indent_no'        =>$item['indent_no'] ?? null,
                    'employee_id'      => $employeeId,
                    'state_id'         => $stateId,
                    'district_id'      => $districtId,
                    'material_id'      => $item['material_id'],
                    'material_code'    =>$item['material_code'],
                    'has_serial'       => 0,
                    'transaction_type' => 'USED',
                    'quantity'         => $item['quantity'],
                    'ticket_id'        => $ticketId,
                    'issue_date'       => Carbon::now(),
                ]);
            }

            /* ---------------- SERIAL ---------------- */
            if ($item['is_serial'] == 1) {

                foreach ($item['serials'] as $serial) {

                    $available = $this->getAvailableQty(
                        $employeeId,
                        $item['material_id'],
                        $serial['serial_id']
                    );

                    if ($serial['quantity'] > $available) {
                        throw new Exception(
                            "Insufficient stock for serial {$serial['serial_id']}"
                        );
                    }

                    EmployeeMaterialLedger::create([
                        'request_id'       => $serial['request_id'] ?? null,
                        'issued_item_id'   => $serial['issued_item_id'] ?? null,
                        'indent_no'        =>$serial['indent_no'] ?? null,
                        'employee_id'       => $employeeId,
                        'state_id'          => $stateId,
                        'district_id'       => $districtId,
                        'material_id'       => $item['material_id'],
                        'material_code'    =>$item['material_code'],
                        'has_serial'        => 1,
                        'serial_number'     => $serial['serial_id'],
                        'replaced_serial_number' => $serial['old_serial_number'] ?? null,
                        'transaction_type'  => 'USED',
                        'quantity'          => $serial['quantity'],
                        'ticket_id'         => $ticketId,
                        'issue_date'        =>  Carbon::now(),
                    ]);
                }
            }
        }
                 
    DB::commit();

        return response()->json([
           'success' => 'true','status'=>1
        ]);

    } catch (Exception $e) {
        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}


public function getEmployeeMaterials(Request $request)
{
    $employeeId = $request->emp_id;
    $stateId    = $request->state_id;
    $districtId = $request->dist_id;

    if (!$employeeId || !$stateId || !$districtId) {
        return response()->json([
            'status'  => false,
            'message' => 'emp_id, state_id and dist_id are required'
        ], 422);
    }

    $ledgerRows = EmployeeMaterialLedger::with('material')
        ->where('employee_id', $employeeId)
        ->where('state_id', $stateId)
        ->where('district_id', $districtId)
        ->get();

    $materials = [];

    foreach ($ledgerRows as $row) {

        $materialId = $row->material_id;

        if (!isset($materials[$materialId])) {
            $materials[$materialId] = [
                'request_id'    =>$row->request_id,
                'issued_item_id' =>$row->issued_item_id,
                'indent_no'=>$row->indent_no,
                'material_id'   => $materialId,
                'material_code' => $row->material->code,
                'material_name' => $row->material->name,
                'base_unit'     => $row->material->base_unit,
                'is_serial'     => (bool)$row->has_serial,
                'issued'        => 0,
                'used'          => 0,
                'quantity'      => 0,
                'serials'       => []
            ];
        }

        if ($row->transaction_type === 'ISSUE') {
            $materials[$materialId]['issued'] += $row->quantity;
        }

        if ($row->transaction_type === 'USED') {
            $materials[$materialId]['used'] += $row->quantity;
        }

        if ($row->has_serial && $row->serial_number) {

            if (!isset($materials[$materialId]['serials'][$row->serial_number])) {
                $materials[$materialId]['serials'][$row->serial_number] = [
                    'request_id'    =>$row->request_id,
                    'issued_item_id' =>$row->issued_item_id,
                    'indent_no'=>$row->indent_no,
                    'serial_number' => $row->serial_number,
                    'issued' => 0,
                    'used' => 0,
                    'balance' => 0
                ];
            }

            if ($row->transaction_type === 'ISSUE') {
                $materials[$materialId]['serials'][$row->serial_number]['issued'] += $row->quantity;
            }

            if ($row->transaction_type === 'USED') {
                $materials[$materialId]['serials'][$row->serial_number]['used'] += $row->quantity;
            }
        }
    }

    foreach ($materials as &$mat) {
        $mat['quantity'] = $mat['issued'] - $mat['used'];

        if ($mat['is_serial']) {
            foreach ($mat['serials'] as &$s) {
                $s['balance'] = $s['issued'] - $s['used'];
                if ($s['balance'] <= 0) {
                    unset($s);
                }
            }
            $mat['serials'] = array_values($mat['serials']);
        }

        if ($mat['quantity'] <= 0) {
            unset($mat);
        }
    }

    return response()->json([
        'status' => true,
        'data'   => array_values($materials)
    ]);
}


public function updateJointImages(Request $request, $id)
{
    try {

        ini_set('post_max_size', '100M');
        ini_set('upload_max_filesize', '100M');

        $submitFile = SubmitFile::findOrFail($id);

        $joint_beforeimgs = [];
        $joint_afterimgs  = [];

        // upload before images
        if ($request->hasFile('joint_enclouser_beforeimg')) {
            foreach ($request->file('joint_enclouser_beforeimg') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/SubmitFiles'), $filename);
                $joint_beforeimgs[] = $filename;
            }
        }

        // upload after images
        if ($request->hasFile('joint_enclouser_afterimg')) {
            foreach ($request->file('joint_enclouser_afterimg') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/SubmitFiles'), $filename);
                $joint_afterimgs[] = $filename;
            }
        }

        // Update only if images are uploaded
        if (!empty($joint_beforeimgs)) {
            $submitFile->joint_enclouser_beforeimg = json_encode($joint_beforeimgs);
        }

        if (!empty($joint_afterimgs)) {
            $submitFile->joint_enclouser_afterimg = json_encode($joint_afterimgs);
        }

        $submitFile->save();

        return response()->json([
            'success' => true,
            'message' => 'Joint enclosure images updated successfully',
            'data' => $submitFile
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}




      /**
     * save the multi upload documents
     *
     * @return \Illuminate\Http\Response
     */
    public function multiupload(Request $request){

        try{
            if ($request->hasFile('before_image')) {
            $before_images= $request->file('before_image');

             foreach($before_images as $image){
                       $filename = $image->getClientOriginalName();
                       $extension = $image->getClientOriginalExtension();
                       print_r($filename);
                }
              }
        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }



    /**
     * Save the Track histroy.
     *
     * @return \Illuminate\Http\Response
     */

    public function savehistory(Request $request){

        try{

            $history = $request->all();
           
            MasterCoordinate::create($history);

            return response()->json(['success' => 'true','status'=>1]);

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

     /**
     * Save the Track histroy.
     *
     * @return \Illuminate\Http\Response
     */

    public function providerhistory(Request $request){

        try{

            $history = $request->all();
           
            ProviderHistory::create($history);

            return response()->json(['success' => 'true','status'=>1]);

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }



    /**
     * Save the Track histroy.
     *
     * @return \Illuminate\Http\Response
     */

    public function dhqhistory(Request $request){
            try{
           
            $history = $request->all();
            $user_id = $history['user_id'];
            $total_tickets = UserRequests::where('user_id',$user_id)->count();
            $total_tickets_data = UserRequests::where('user_id',$user_id)->get();
            $ongoing_tickets = UserRequests::where('status','PICKEDUP')->where('user_id',$user_id)->count();
            $ongoing_tickets_data = UserRequests::where('status','PICKEDUP')->where('user_id',$user_id)->get();
            $completed_tickets = UserRequests::where('status','COMPLETED')->where('user_id',$user_id)->count();
            $completed_tickets_data = UserRequests::where('status','COMPLETED')->where('user_id',$user_id)->get();
            $cancelled_tickets = UserRequests::where('status','CANCELLED')->where('user_id',$user_id)->count();
            $cancelled_tickets_data = UserRequests::where('status','CANCELLED')->where('user_id',$user_id)->get();
            $pending_tickets = UserRequests::where('status','REASSIGNED')->where('user_id',$user_id)->count();
            $pending_tickets_data = UserRequests::where('status','REASSIGNED')->where('user_id',$user_id)->get();

             $data = array(
             "total" =>  $total_tickets,
             "total_data" =>  $total_tickets_data,
             "ongoing" =>  $ongoing_tickets,
             "ongoing_data" =>  $ongoing_tickets_data,
             "completed" =>  $completed_tickets,
             "completed_data" =>  $completed_tickets_data,
             "cancelled" =>  $cancelled_tickets,
             "cancelled_data" =>  $cancelled_tickets_data,
             "pending" =>  $pending_tickets,
             "pending_data" =>  $pending_tickets_data
             );
            return response()->json(['success' => 'true','data'=>$data,'status'=>1]);

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }


public function userhistory(Request $request){
            try{
           
            $user_id = $request->input('user_id');
            $total_tickets = UserRequests::where('provider_id',$user_id)->count();
            $total_tickets_data = UserRequests::where('provider_id',$user_id)->get();
            $income_tickets = UserRequests::where('status','INCOMING')->where('provider_id',$user_id)->count();
            $income_tickets_data = UserRequests::where('status','INCOMING')->where('provider_id',$user_id)->get();
            $ongoing_tickets = UserRequests::where('status','PICKEDUP')->where('provider_id',$user_id)->count();
            $ongoing_tickets_data = UserRequests::where('status','PICKEDUP')->where('provider_id',$user_id)->get();
            $completed_tickets = UserRequests::where('status','COMPLETED')->where('provider_id',$user_id)->count();
            $completed_tickets_data = UserRequests::where('status','COMPLETED')->where('provider_id',$user_id)->get();
            $hold_tickets = UserRequests::where('status','HOLD')->where('provider_id',$user_id)->count();
            $hold_tickets_data = UserRequests::where('status','HOLD')->where('provider_id',$user_id)->get();
            
             $data = array(
             "total" =>  $total_tickets,
             "total_data" =>  $total_tickets_data,
             "ongoing" =>  $ongoing_tickets,
             //"ongoing_data" =>  $ongoing_tickets_data,
             "completed" =>  $completed_tickets,
             //"completed_data" =>  $completed_tickets_data,
             "open" =>  $income_tickets,
             //"open_data" =>  $income_tickets_data,
             "hold" =>  $hold_tickets,
             //"hold_data" =>  $hold_tickets_data
             );
            return response()->json(['success' => 'true','data'=>$data,'status'=>1]);

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }




    /**
     * Save the Track histroy.
     *
     * @return \Illuminate\Http\Response
     */

    
    public function userperformance(Request $request)
{
    try {
        $user_id = $request->input('user_id');

        // Base query for the user
        $userRequests = UserRequests::where('provider_id', $user_id)->get();

        $assigned = $userRequests->count();
        $resolved = $userRequests->where('status', 'COMPLETED')->count();
        $opened = $userRequests->where('status', 'INCOMING')->count();


        // SLA Met: if 'finished_at' exists and is within X time (e.g., 4 hours) from 'started_at'
        $slaThresholdMinutes = 240; // example SLA window

        $slaMet = $userRequests->filter(function ($ticket) use ($slaThresholdMinutes) {
            if ($ticket->started_at && $ticket->finished_at) {
                $start = \Carbon\Carbon::parse($ticket->started_at);
                $end = \Carbon\Carbon::parse($ticket->finished_at);
                return $start->diffInMinutes($end) <= $slaThresholdMinutes;
            }
            return false;
        })->count();

        $slaMissed = $resolved - $slaMet;

        // Total Distance Traveled
        $totalDistance = $userRequests->sum('distance'); // assuming distance is logged per ticket

        // Total and Average Time Spent
        $timeDurations = $userRequests->filter(function ($ticket) {
            return $ticket->started_at && $ticket->finished_at;
        })->map(function ($ticket) {
            $start = \Carbon\Carbon::parse($ticket->started_at);
            $end = \Carbon\Carbon::parse($ticket->finished_at);
            return $start->diffInMinutes($end);
        });

        $totalTimeSpent = $timeDurations->sum();
        $avgTimePerTicket = $timeDurations->count() > 0 ? round($timeDurations->avg(), 2) : 0;

        $checklistCount = \DB::table('patroller_checklists')
            ->where('provider_id', $user_id)
            ->count();

        $lastChecklist = \DB::table('patroller_checklists')
            ->where('provider_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->value('created_at');

        $lastChecklistFormatted = $lastChecklist
            ? \Carbon\Carbon::parse($lastChecklist)->format('Y-m-d H:i:s')
            : null;

       $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
       $endOfMonth = \Carbon\Carbon::now()->endOfMonth();

       $attendanceCount = \DB::table('attendance')
       ->where('provider_id', $user_id)
       ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
       ->count();

        return response()->json([
            'status' => true,
            'data' => [
                'total_tickets' => $assigned,
                'tickets_resolved' => $resolved,
                'tickets_opened' => $opened,
                'sla_met' => $slaMet,
                'sla_missed' => $slaMissed,
                'total_distance_traveled' => round($totalDistance, 2) . ' Kms',
                'total_time_spent_minutes' => $totalTimeSpent,
                'avg_time_per_ticket_minutes' => $avgTimePerTicket,
                'checklist_count' => $checklistCount,
                'last_checklist_submission' => $lastChecklistFormatted,
                'attendance_current_month' => $attendanceCount,
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'error' => $e->getMessage()
        ], 500);
    }
    }


public function gpperformance(Request $req)
{
    $lat = $req->latitude;
    $lng = $req->longitude;
    $phone = $req->phone_number;

    // Query with Distance + Joins + extra fields
    $gpList = DB::table('gp_list')
        ->select(
            'gp_list.id',
            'gp_list.gp_name',
            'gp_list.phase',
            'gp_list.lgd_code',
            'gp_list.petroller_contact_no',
            'gp_list.petroller',
            'gp_list.latitude',
            'gp_list.longitude',
            'gp_list.status',
            'gp_list.olt_lgdcode as OLT_Code',

            // State, District, Block names
            'states.state_name',
            'districts.name as district_name',
            'blocks.name as block_name',

            DB::raw("(
                6371 * acos(
                    cos(radians($lat)) *
                    cos(radians(gp_list.latitude)) *
                    cos(radians(gp_list.longitude) - radians($lng)) +
                    sin(radians($lat)) *
                    sin(radians(gp_list.latitude))
                )
            ) AS distance_km")
        )
        ->leftJoin('states', 'states.state_id', '=', 'gp_list.state_id')
        ->leftJoin('districts', 'districts.id', '=', 'gp_list.district_id')
        ->leftJoin('blocks', 'blocks.id', '=', 'gp_list.block_id')
        ->where('gp_list.petroller_contact_no', $phone)
        ->orderBy('distance_km', 'asc')
        ->get();

    // Add Distance & ETA to each GP
    $processed = $gpList->map(function ($row) {
        $row->distance_km = round($row->distance_km, 2);
        $row->eta_minutes = round(($row->distance_km / 40) * 60, 2); // ETA at 40km/h

        $statusCounts = DB::table('user_requests')
            ->join('master_tickets', 'user_requests.booking_id','=','master_tickets.ticketid')
            ->where('master_tickets.lgd_code', $row->lgd_code)
            ->selectRaw("
                SUM(IF(user_requests.status='PICKEDUP',1,0)) AS inprogress,
                SUM(IF(user_requests.status='COMPLETED',1,0)) AS completed,
                SUM(IF(user_requests.status='INCOMING',1,0)) AS pending,
                SUM(IF(user_requests.status='ONHOLD',1,0)) AS hold
            ")
            ->first();
        $row->status_counts = $statusCounts;
        return $row;
    });

    $OltData = DB::table('olt_locations')->select('olt_location','olt_location_code','lgd_code as OLT_Code','olt_ip','no_of_gps')->get();

    return response()->json([
        "success" => true,
        "data" => [
            "totalgp" => $processed->count(),
            "totalgp_data" => $processed,
            "OltData"=>$OltData
        ],
        "status" => 1
    ]);
}




    public function merge_tickets(Request $request){

         $this->validate($request, [
                'ticket_id' => 'required',                
            ]);

        try{
            $ticket_id = $request->ticket_id;
            $request_id = $request->request_id;
            $provider_id = $request->provider_id;




             DB::table('user_requests')
            ->where('booking_id',$ticket_id)
            ->update(['status' => 'COMPLETED']);

             DB::table('master_tickets')
            ->where('ticketid',$ticket_id)
            ->update(['status' => 1]);

            RequestFilter::where('request_id', $request_id)->delete();

            ProviderService::where('provider_id',$provider_id)->update(['status' =>'active']);


           return response()->json(['success' => 'true','status'=>1]);

           
        } catch (Exception $e) {
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    }


   public function PilotAcceptedRejected(Request $request){
        try{
            info($request->all());
            $booking_id = $request->booking_id;
            $provider_id = $request->provider_id;
            $status = $request->status;

             if($status == 'ACCEPTED'){

               $statusDetails = [
             'status' =>$status,
              ];

              DB::table('user_requests')
            ->where('booking_id',$booking_id)
            ->where('provider_id',$provider_id)
            ->update($statusDetails);

               return response()->json(['success' => 'true','status'=>1]);


             } else if($status == 'REJECTED'){
              
              
             $statusDetails = [
             'status' =>$status,
              ];

              DB::table('user_requests')
            ->where('booking_id',$booking_id)
            ->where('provider_id',$provider_id)
            ->update($statusDetails);

                return response()->json(['success' => 'true','status'=>0]);

             }
             else {
           
           return response()->json(['success' => 'true','status'=>1]);
             }

           
        } catch (Exception $e) {
              info($e);
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    }



   public function ProviderRequestStatus(Request $request){
        try{

            info($request->all());
            $request_id = $request->request_id;
            $provider_id = $request->provider_id;
            $status = $request->status;

            if ($status == 'PICKEDUP') {

                $statusDetails = [
                    'status' => $status,
                ];

                if (!empty($request->started_at)) {
                    $statusDetails['started_at'] = date('Y-m-d H:i:s', strtotime($request->started_at));
                }

            } else {

                $statusDetails = [
                    'status' => $status,
                ];
            }

           


             DB::table('user_requests')
            ->where('id',$request_id)
            ->where('provider_id',$provider_id)
            ->update($statusDetails);

           return response()->json(['success' => 'true','status'=>1]);
           
        } catch (Exception $e) {
             
        Log::error("startdate Exception: " . $e->getMessage(), [
            'request' => $request->all(),
            'trace' => $e->getTraceAsString()
        ]);
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    }


  public function ProviderWorkStatus(Request $request){
        try{

            Log::info('ProviderWorkStatus Request:', $request->all());

            $request_id = $request->request_id;
            $provider_id = $request->provider_id;
            $status= $request->status;
            Log::info("Processing status '{$status}' for request_id: {$request_id}, provider_id: {$provider_id}");

           
           if($status == 'ONCALL'){
               $statusDetails = [
             'started_at' =>date('y-m-d H:i:s',strtotime($request->started_at)),
             'started_location' =>$request->started_location,
             'started_latitude' =>isset($request->started_latitude)?($request->started_latitude):'',
             'started_longitude' =>isset($request->started_longitude)?($request->started_longitude):'',
            ];
           Log::info('Updating user_requests table with ONCALL details:', $statusDetails);


              DB::table('user_requests')
            ->where('id',$request_id)
            ->where('provider_id',$provider_id)
            ->update($statusDetails);

            Log::info("ONCALL update successful for request_id: {$request_id}");

          return response()->json(['success' => 'true','status'=>1]);

           }
           else if($status == 'REACHED') {
             
            $statusDetails = [
           'reached_at' =>date('y-m-d H:i:s',strtotime($request->reached_at)),
           'reached_location' =>$request->reached_location,
            ];

            Log::info('Updating user_requests table with REACHED details:', $statusDetails);

             DB::table('user_requests')
            ->where('id',$request_id)
            ->where('provider_id',$provider_id)
            ->update($statusDetails);
            Log::info("REACHED update successful for request_id: {$request_id}");

             return response()->json(['success' => 'true','status'=>1]);


          }
 
          else {
     
             return response()->json(['success' => 'true','status'=>1]);

            }
           
        } catch (Exception $e) {
        Log::error("ProviderWorkStatus Exception: " . $e->getMessage(), [
            'request' => $request->all(),
            'trace' => $e->getTraceAsString()
        ]);

             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    }



   public function autoSubmit(Request $request){

         $this->validate($request, [
                'ticket_id' => 'required', 
                'message' => 'required',               
            ]);

        try{
            $ticket_id = $request->ticket_id;
            $message= $request->message;
            $request_id = $request->request_id;
            $provider_id = $request->provider_id;


            $updateDetails = [
           'status' =>'COMPLETED',
           'downreason' => $message
            ];

           $masterupdateDetails = [
           'status' =>1,
           'downreason' => $message
            ];


             DB::table('user_requests')
            ->where('booking_id',$ticket_id)
            ->update($updateDetails);

             DB::table('master_tickets')
            ->where('ticketid',$ticket_id)
            ->update($masterupdateDetails);

            RequestFilter::where('request_id', $request_id)->delete();

            ProviderService::where('provider_id',$provider_id)->update(['status' =>'active']);


           return response()->json(['success' => 'true','status'=>1]);

           
        } catch (Exception $e) {
             return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    }

    
    /**
     * get the received ticket list.
     *
     * 25/08/2019 added by Ashok.
     * @return \Illuminate\Http\Response
     */

    public function receivedTicketList() {

        if($receivedTicketList= MasterTicket::select('ticketid','pop_map_key')->where('ticketinsertstage',1)->get()) {
            return response()->json($receivedTicketList);
        } else {
            return response()->json(['error' => trans('api.services_not_found')], 422);
        }

    }


  /**
     * get the complete ticket list.
     *
     * 25/08/2019 added by Ashok.
     * @return \Illuminate\Http\Response
     */

    public function completeTicketList() {

        if($completeTicketList= MasterTicket::select('ticketid','pop_map_key')->where('status',1)->get()) {
            return response()->json($completeTicketList);
        } else {
            return response()->json(['error' => trans('api.services_not_found')], 422);
        }

    }

    
   /**
     * get the user assigned ticket list.
     *
     * 23/02/2023 added by Ashok.
     * @return \Illuminate\Http\Response
     */

    public function userAssignedTicketList(Request $request) {

         $provider_id = $request->provider_id;
         $status = $request->status;

        if( $status == 'All'){

                $tickets = DB::table('master_tickets')
                  ->select('user_requests.provider_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at',DB::raw('TIMESTAMPDIFF(HOUR,STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"),"2023-03-17 06:36:08 am") as hours'))
                  ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' ,'!=','COMPLETED')
                                 ->where('user_requests.status' ,'!=','SEARCHING')
                 ->where('user_requests.provider_id' , $provider_id)
                 ->orderBy('hours','desc')
                ->get();

            return response()->json($tickets);
        } else if($status == 'notstarted'){
                   $tickets = DB::table('master_tickets')
                  ->select('user_requests.provider_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at',DB::raw('TIMESTAMPDIFF(HOUR,STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"),"2023-03-17 06:36:08 am") as hours'))
                  ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' ,'=','INCOMING')
                 ->where('user_requests.provider_id' , $provider_id)
                 ->orderBy('hours','desc')
                ->get();

            return response()->json($tickets);

       } else if($status == 'inprogress'){

             $tickets = DB::table('master_tickets')
                  ->select('user_requests.provider_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at',DB::raw('TIMESTAMPDIFF(HOUR,STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"),"2023-03-17 06:36:08 am") as hours'))
                  ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' ,'=','PICKEDUP')
                 ->where('user_requests.provider_id' , $provider_id)
                 ->orderBy('hours','desc')
                ->get();

            return response()->json($tickets);


        } else if ($status == 'Completed'){
              $tickets = DB::table('master_tickets')
                  ->select('user_requests.provider_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at',DB::raw('TIMESTAMPDIFF(HOUR,STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"),"2023-03-17 06:36:08 am") as hours'))
                  ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' ,'=','COMPLETED')
                 ->where('user_requests.provider_id' , $provider_id)
                 ->orderBy('finished_at','desc')
                ->get();

                 return response()->json($tickets);

         } else if ($status == 'Onhold'){
              $tickets = DB::table('master_tickets')
                  ->select('user_requests.provider_id','master_tickets.ticketid','master_tickets.district','master_tickets.mandal','master_tickets.gpname','master_tickets.subsategory','master_tickets.downreason','master_tickets.downreasonindetailed','user_requests.id as request_id','user_requests.status','master_tickets.downdate','master_tickets.downtime','service_types.name as service_name','providers.first_name','providers.last_name','providers.last_name','providers.mobile','user_requests.s_address','user_requests.d_address','user_requests.s_latitude','user_requests.s_longitude','user_requests.d_latitude','user_requests.d_longitude','user_requests.assigned_at','user_requests.started_at','user_requests.started_location','user_requests.reached_at','user_requests.reached_location','user_requests.finished_at',DB::raw('TIMESTAMPDIFF(HOUR,STR_TO_DATE(CONCAT(master_tickets.downdate," ",master_tickets.downtime), "%Y-%m-%d %H:%i:%s"),"2023-03-17 06:36:08 am") as hours'))
                  ->leftjoin('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
				 ->leftjoin('service_types', 'user_requests.service_type_id', '=', 'service_types.id')
				 ->leftjoin('providers', 'user_requests.provider_id', '=', 'providers.id')
				 ->where('user_requests.status' ,'=','ONHOLD')
                 ->where('user_requests.provider_id' , $provider_id)
                 ->orderBy('hours','desc')
                ->get();

                 return response()->json($tickets);


       } else {
            return response()->json(['error' => trans('api.user.user_not_found')], 422);

    }


  }

    public function auto_assign_tickets(Request $request)
    {
        $schdeuled_tasks = DB::table('schedule_auto_assign')->get();

        $now_ime = Carbon::now()->timestamp;
        foreach($schdeuled_tasks as $index => $schdeule){
            if ($now_ime >= $schdeule->schedule_interval)
            {   

                $schedule_interval = (int)$schdeule->schedule_interval + (int)$schdeule->next_interval;
                DB::table('schedule_auto_assign')
                    ->where('id', $schdeule->id)
                    ->update(['schedule_interval' => $schedule_interval]);

                $curl = curl_init();
                curl_setopt_array($curl, array(
                   CURLOPT_URL => $schdeule->url,
                   CURLOPT_RETURNTRANSFER => true,
                   CURLOPT_ENCODING => "",
                   CURLOPT_MAXREDIRS => 10,
                   CURLOPT_TIMEOUT => 0,
                   CURLOPT_FOLLOWLOCATION => true,
                   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                   CURLOPT_CUSTOMREQUEST => "GET",
                ));
                $curl_resp = curl_exec($curl);
                $httpStatus = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
                $err = curl_error($curl);
                curl_close($curl);

                echo json_encode("yes it runned");
               exit;
            }
        }
           echo json_encode("Nothing to run!");
               exit;
    }

   public function getBharatNetNeStatus(Request $request)
    {
        $stateName = $request->query('stateName');
        $stateCode = $request->query('stateCode');
        if (!$stateName || !$stateCode) {
            return response()->json([
                "reqstatus" => "FAILURE",
                "remarks" => "Mandatory parameters missing"
            ], 400);
        }

        $service = new GisService();
        // Service will handle session key generation internally
        $result = $service->getBharatNetNeStatus($stateName, $stateCode);
        return response()->json($result);
    }
      

public function insertBharatNetNeStatus(Request $request)
{

    ini_set('max_execution_time', 300);

    $oltcode = $request->input('oltcode');
    $ontcode = $request->input('ontcode');
  
    $service = new GisService();
    $result = $service->getOntStatus($oltcode, $ontcode);

   $responseArray = json_decode(json_encode($result), true);
  

    if (!isset($responseArray['ontStatusDetails']) || empty($responseArray['ontStatusDetails'])) {
        return response()->json([
            'reqstatus' => 'FAILURE',
            'message' => 'No ONT status details found'
        ]);
    }

    $records = [];
    foreach ($responseArray['ontStatusDetails'] as $ont) {
       
        $alarm = $ont['alarmInfo'];
        $incomingType = strtolower(trim($alarm['type'] ? $alarm['type'] : '' ));     
        $incomingCategory = strtolower(trim( $alarm['category'] ? $alarm['category'] : '' )); 
        $match = DB::table('alarm_masters')
                ->whereRaw('LOWER(alarm_type) = ?', [$incomingType])
                ->whereRaw('LOWER(category) = ?', [$incomingCategory])
                ->first();
        if ($match) {
            $downreason = $match->bin_type;     
            $downreasonDetails = $match->alarm_type; 
        } else {
           $downreason = $alarm['type'] ? $alarm['type'] : 'OTHERS';
           $downreasonDetails = $alarm['category'] ? $alarm['category']  : null;
        }        
        $records[] = [
            'lgd_code' => $ont['ontnecode'],
            'gp_type' => strtolower($ont['ontStatus']) === 'unknown' 
                            ? 'down' 
                            : strtolower($ont['ontStatus']),
            'autoclose'=> 'Auto',
            'datetime' => $alarm && isset($alarm['occurrenceTime']) 
                        ? $alarm['occurrenceTime'] 
                        : Carbon::now()->toDateTimeString(),
            'downreason' => $downreason,                     
            'downreasonindetailed' => $downreasonDetails,
        ];
    }
  
    $inserted_ids = [];
    $ignored = [];
    
    foreach ($records as $filedata) {
    try {
        $lgd_code_record = DB::table('gp_list')->where('lgd_code', $filedata['lgd_code'])->first();

        if (!$lgd_code_record) {
            $ignored[] = $filedata['lgd_code'];
            continue;
        }

        $gp_type = strtolower($filedata['gp_type'] ? $filedata['gp_type'] : 'down');
        $datetime = Carbon::parse($filedata['datetime']);
        $formattedDate = $datetime->format('Y-m-d');
        $formattedTime = $datetime->format('h:i:s a');

        $district = District::findOrFail($lgd_code_record->district_id);
        if (!$district) {
            \Log::warning("District not found for LGD: {$lgd_code_record->lgd_code}");
            continue;
        }
        $block = Block::findOrFail($lgd_code_record->block_id);
        if (!$block) {
            \Log::warning("Block not found for LGD: {$lgd_code_record->lgd_code}, Block ID: {$lgd_code_record->block_id}");
            continue; 
        }
        // Check if ticket exists
        if ($gp_type === 'down') {
            $existing_ticket = DB::table('master_tickets')
                                ->leftJoin('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                                ->where('lat', $lgd_code_record->latitude)
                                ->where('log', $lgd_code_record->longitude)
                                ->where('lgd_code', $lgd_code_record->lgd_code)
                                ->whereIn('user_requests.status', ['SEARCHING','INCOMING','PICKEDUP','CANCELLED','ONHOLD'])
                                ->whereNull('up_date')
                                ->whereNull('up_time')
                                ->where('user_requests.autoclose', '!=' ,'Manual')
                                ->orderBy('master_tickets.id', 'DESC')
                                ->first();
            if ($existing_ticket) {
                $hasActiveRequest = DB::table('user_requests')
                                        ->where('booking_id', $existing_ticket->ticketid)
                                        ->where('status', '!=', 'ONHOLD')
                                        ->where('downreason','!=','Permanent Down')
                                        ->exists();

                if ($hasActiveRequest) {

                    DB::table('master_tickets')
                        ->where('ticketid', $existing_ticket->ticketid)
                        ->update([
                            'downreason' => $filedata['downreason'],
                            'updated_at' => Carbon::now()
                        ]);

                    DB::table('user_requests')
                        ->where('booking_id', $existing_ticket->ticketid)
                        ->where('status', '!=', 'ONHOLD')
                        ->update([
                            'downreason' => $filedata['downreason'],
                            'updated_at' => Carbon::now()
                        ]);
                }

             
                continue;
            }
            // $ticket_id = $existing_ticket ? $existing_ticket->ticketid : 'TK25'.mt_rand(100000, 9999999);
            //   $ticket_id = 'TK26' . mt_rand(100000, 9999999);
                    
                do {
                    $ticket_id = 'TK26' . mt_rand(100000, 9999999);
                } while (DB::table('master_tickets')->where('ticketid', $ticket_id)->exists());



            $master_data = [
                'ticketid' => $ticket_id,
                'district' => $district->name ? $district->name : '-' ,
                'mandal' => $block->name ? $block->name : '-',
                'gpname' => $lgd_code_record->gp_name,
                'lgd_code' => $lgd_code_record->lgd_code,
                'downreason' => $filedata['downreason'],
                'downreasonindetailed' => $filedata['downreasonindetailed'],
                'subsategory' => "",
                'lat' => $lgd_code_record->latitude,
                'log' => $lgd_code_record->longitude,
                'ticketinsertstage' => 1,
                'downdate' => $formattedDate,
                'downtime' => $formattedTime
            ];
             DB::table('master_tickets')->insert($master_data);
          
           

            // UserRequests assignment
            $checkcat = strtolower(trim($filedata['downreason']));
            if (strpos($checkcat, 'fiber') !== false) {
                 $mobile = $lgd_code_record->contact_no;
            } else {
                $mobile = $lgd_code_record->petroller_contact_no;

            }

            $provider = DB::table('providers')
                ->leftJoin('provider_devices', 'providers.id', '=', 'provider_devices.provider_id')
                ->where('mobile', $mobile)
                ->select('providers.id as provider_id', 'providers.*', 'provider_devices.*')
                ->first();

            

            if (!$provider) {
                \Log::error("Provider not found for mobile: {$mobile}");
                continue;
            }

            // Addresses
            $googleMaps = new GoogleMapsService();
            $daddress = $googleMaps->getReverseGeocode($lgd_code_record->latitude, $lgd_code_record->longitude);

            $saddress = $googleMaps->getReverseGeocode($provider->latitude, $provider->longitude);

            $direction_json = $googleMaps->getDirections($provider->latitude, $provider->longitude, $lgd_code_record->latitude, $lgd_code_record->longitude);
            $route_key = isset($direction_json['routes'][0]['overview_polyline']['points']) ? $direction_json['routes'][0]['overview_polyline']['points'] : null;
            $UserRequest = new UserRequests;
                            $UserRequest->booking_id = $ticket_id;
                            $UserRequest->gpname = $lgd_code_record->gp_name;
                            $UserRequest->downreason =$filedata['downreason'];
                            $UserRequest->downreasonindetailed =$filedata['downreasonindetailed'];
                            $UserRequest->user_id =45;                    
                         
                            $UserRequest->current_provider_id = $provider->provider_id;
                            $UserRequest->provider_id = $provider->provider_id;

                            $UserRequest->service_type_id = 2;
                            $UserRequest->rental_hours = 10;
                            $UserRequest->payment_mode = 'CASH';
                            $UserRequest->promocode_id = 0;
                            $UserRequest->default_autoclose = $filedata['autoclose'];
                            $UserRequest->autoclose =$filedata['autoclose'];
                            
                            $UserRequest->status = 'INCOMING';
                            $UserRequest->s_address =$saddress;
                            $UserRequest->d_address =$daddress;

                            $UserRequest->s_latitude = $provider->latitude;
                            $UserRequest->s_longitude = $provider->longitude;

                            $UserRequest->d_latitude = $lgd_code_record->latitude;
                            $UserRequest->d_longitude = $lgd_code_record->longitude;
                            $UserRequest->distance = 1;
                            $UserRequest->unit = Setting::get('distance', 'Kms');
                   
                            $UserRequest->use_wallet = 0;

                            if(Setting::get('track_distance', 0) == 1){
                                $UserRequest->is_track = "YES";
                            }

                            $UserRequest->otp = mt_rand(1000 , 9999);
                            $UserRequest->company_id= $lgd_code_record->company_id;
                            $UserRequest->state_id= $lgd_code_record->state_id;
                            $UserRequest->district_id= $lgd_code_record->district_id;


                            $UserRequest->assigned_at = Carbon::now();
                            $UserRequest->route_key = $route_key;
                            $UserRequest->save();
           

            DB::table('gp_list')->where('lgd_code', $lgd_code_record->lgd_code)->update(['status' => 1]);
             $inserted_ids[] = $ticket_id;
        }

        // Handle UP tickets
        if ($gp_type === 'up') {

               $existing_ticket = DB::table('master_tickets')
                                ->leftJoin('user_requests', 'master_tickets.ticketid', '=', 'user_requests.booking_id')
                                ->where('master_tickets.lat', $lgd_code_record->latitude)
                                ->where('master_tickets.log', $lgd_code_record->longitude)
                                ->where('master_tickets.lgd_code', $lgd_code_record->lgd_code)
                                ->whereNull('master_tickets.up_date')
                                ->whereNull('master_tickets.up_time')
                                ->where('user_requests.autoclose','=', 'Auto')
                                ->orderBy('master_tickets.id', 'DESC')
                                ->get();

                foreach ($existing_ticket as $t) {

                    DB::table('master_tickets')
                        ->where('ticketid', $t->ticketid)
                        ->update([
                            'up_date' => $formattedDate,
                            'up_time' => $formattedTime,
                            'status'  => 1,
                        ]);

                    UserRequests::where('booking_id', $t->ticketid)
                        ->update([
                            'status' => 'COMPLETED',
                            'finished_at' => Carbon::now(),
                            'autoclose' => 'Auto',
                            'default_autoclose' => 'Auto',
                        ]);

                    $inserted_ids[] = $t->ticketid;
                }

                DB::table('gp_list')
                    ->where('lgd_code', $lgd_code_record->lgd_code)
                    ->update(['status' => 0]);
            }

      
         } catch (\Throwable $e) {
        \Log::error("Error processing LGD code {$filedata['lgd_code']}: " . $e->getMessage());
        continue;
    }
    }
    return response()->json([
        'reqstatus' => 'SUCCESS',
        'processed_ids' => $inserted_ids,
        'ignored_lgd_codes' => $ignored,
        'message' => count($inserted_ids) . " records processed successfully"
    ]);
}



private function mapDownReason($input)
{
    $text = strtolower(trim($input));

    if (strpos($text, 'power') !== false) {
        return 'POWER';
    }
    if (in_array($text, ['pon_los', 'pon_losi'])) {
        return 'FIBER';
    }

    if ($text === 'ont power off') {
        return 'POWER';
    }

    if (in_array($text, ['losi_dgi'])) {
        return 'OTHERS';
    }

    if ($text === 'pon_sufi') {
        return 'SOFTWARE/HARDWARE';
    }

    // if ($text === 'gp unknown') {
    //     return 'OLT DOWN';
    // }
  
    return 'OTHERS';
}


  public function getOltStatus(Request $request)
    {
        $oltcode = $request->query('oltcode'); // can be null
        $service = new GisService();
        $result = $service->getOltStatus($oltcode);
        return response()->json($result);
    }

    public function getOntStatus(Request $request)
    { 
        $oltcode = $request->query('oltcode');
       
        $ontcode = $request->query('ontcode');
        $service = new GisService();
        $result = $service->getOntStatus($oltcode, $ontcode);
        return response()->json($result);
    }

public function raise_ticket(Request $request)
{
    try {
Log::info('raised request: ' . json_encode($request->all()));

        $data = $request->only([
            'patroller_id',
            'gp_name',
            'date',
            'time',
            'latitude',
            'longitude',
            'landmark',
            'issue_type',
            'issue_sub_type',
            'priority',
            'details',
        ]);

        $attachments = [];
        $attachment_latlong=[];

        // Handle multiple image uploads
        if ($request->hasFile('attachment')) {

            $destinationPath = public_path('uploads/patroller_tickets');

            // Ensure directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $latlongs = $request->input('attachment_latlong', []);


            foreach ($request->file('attachment') as $index => $image) {
                if(!$image->isValid()){
                    continue;
                }
                $coords = $this->parseLatLong(
                    $latlongs[$index] ?? null,
                    $request->latitude,
                    $request->longitude
                );
                $imageName = time() . uniqid() . '_' . $coords['lat'] . '_' . $coords['long'] . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $imageName);
                $attachments[] = 'uploads/patroller_tickets/' . $imageName;
                $attachment_latlong[] = $coords['lat'] . ',' . $coords['long'];


              
            }
        }

        // Save as JSON (recommended)
        $data['attachments'] = !empty($attachments) ? json_encode($attachments) : null;
        $data['attachment_latlong'] = $attachment_latlong ? json_encode($attachment_latlong) : null;

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $inserted = DB::table('raise_tickets')->insert($data);

        if ($inserted) {
            return response()->json([
                'status' => 1,
                'message' => 'Request inserted successfully with images'
            ], 201);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Failed to insert request'
        ], 500);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 0,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
private function parseLatLong($val, $fallbackLat = null, $fallbackLong = null)
{
    $lat  = $fallbackLat ?? '0.0';
    $long = $fallbackLong ?? '0.0';

    if (!empty($val)) {

        // Remove brackets, quotes & spaces
        $val = trim($val);
        $val = str_replace(['[', ']', '"', "'"], '', $val);
        $val = preg_replace('/\s+/', '', $val);

        if (str_contains($val, ',')) {
            [$lat, $long] = explode(',', $val);
        } elseif (str_contains($val, ':')) {
            [$lat, $long] = explode(':', $val);
        }
    }

    return [
        'lat'  => $lat,
        'long' => $long,
        'lat_safe'  => str_replace(['.', '-'], '_', $lat),
        'long_safe' => str_replace(['.', '-'], '_', $long),
    ];
}




public function patroller_checklist(Request $request)
{
    try {

        // Collect main fields
        $data = $request->only([
            'provider_id',
            'patroller_name',
            'patrol_start_time',
            'patrol_end_time',
            'auto_location',
            'gp_name',

            // Route Surveillance
            'civil_activity',
            'agency_involved',
            'manhole_chamber',
            'manhole_status',
            'route_marker',
            'route_marker_status',
            'overhead_cable_sagging',
            'joint_enclosure',
            'bridge_culvert_status',
            'fire_hazard',

            // Power
            'power_available',
            'ups_backup_working',
            'earthing_condition',

            // Equipment
            'olt_ont_router_working',
            'rack_condition',
            'cable_dressing',

            // Signage
            'rack_labels',
            'port_labels',
            'ofc_cable_labels',

            // Issues
            'issue_identified',
            'issue_category',
            'severity',
            'remarks'
        ]);

        /* ---------------------------------------------
           Handle Multiple Photo Uploads
        ----------------------------------------------- */
        $photoPaths = [];

        if ($request->hasFile('photos')) {

            foreach ($request->file('photos') as $photo) {
                if ($photo->isValid()) {

                    $imageName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                    $destinationPath = public_path('uploads/patroller_checklist');

                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    $photo->move($destinationPath, $imageName);

                    $photoPaths[] = 'uploads/patroller_checklist/' . $imageName;
                }
            }
        }

        // Store photos as JSON
        $data['photos'] = json_encode($photoPaths);

        // JSON encode multi-select fields
        if ($request->has('agency_involved')) {
            $data['agency_involved'] = json_encode($request->agency_involved);
        }

        // Add timestamps
        $data['created_at'] =  date('Y-m-d H:i:s');
        $data['updated_at'] =  date('Y-m-d H:i:s');

        // Insert into DB
        $inserted = DB::table('patroller_checklists')->insert($data);

        if ($inserted) {
            return response()->json([
                'status' => 1,
                'message' => 'Patroller checklist submitted successfully'
            ], 201);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Failed to insert patroller checklist'
        ], 500);


    } catch (\Exception $e) {
        return response()->json([
            'status' => 0,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

public function patrollerChecklistRecent(Request $request)
{
    try {

        $provider_id = $request->input('provider_id');

        if (!$provider_id) {
            return response()->json([
                'status' => 0,
                'message' => 'provider_id is required'
            ], 400);
        }

        // Latest submission
        $latest = DB::table('patroller_checklists')
            ->where('provider_id', $provider_id)
            ->orderBy('created_at', 'desc')
            ->first();

        // Last 5 submissions
        $recentList = DB::table('patroller_checklists')
            ->where('provider_id', $provider_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Total checklist count
        $total = DB::table('patroller_checklists')
            ->where('provider_id', $provider_id)
            ->count();

        return response()->json([
            'status' => 1,
            'message' => 'Recent checklist activity fetched successfully',
            'data' => [
                'provider_id' => $provider_id,
                'total_checklists' => $total,
                'latest_submission' => $latest,
                'recent_5_submissions' => $recentList
            ]
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 0,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}


public function saveTracking(Request $request)
{
    try {

        DB::table('patroller_checklist_tracking')->insert([
            'provider_id' => $request->provider_id,
            'track'       => json_encode($request->track),
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Tracking saved successfully'
        ], 201);

    } catch (\Exception $e) {

        return response()->json([
            'status' => 0,
            'message' => 'Error: '.$e->getMessage()
        ], 500);

    }
}

public function get_employee_list(Request $request)
{
    try {

        $district_code = $request->query('district_code');
        $state_code    = $request->query('state_code');

        $query = DB::table('providers as p')
            ->join('districts as d', 'd.id', '=', 'p.district_id')
            ->select(
                'p.id',
                'p.first_name',
                'p.last_name',
                'p.mobile as phone_number'
            )->where('p.type',2)->where('p.status','approved');

        if (!empty($district_code)) {
            $query->where('d.district_code', $district_code);
        }

        if (!empty($state_code)) {
            $query->where('d.state_code', $state_code);
        }

        $employees = $query->get();

        return response()->json([
            'status'  => 1,
            'message' => 'Employee list fetched successfully',
            'data'    => $employees
        ], 200);

    } catch (\Throwable $e) {

        return response()->json([
            'status'  => 0,
            'message' => $e->getMessage()
        ], 500);
    }
}

 public function createmastertickets(Request $request)
    {
        try {
            $mappedData = $this->mapKolkataRequest($request->all());

            return $this->processTicketData($mappedData);
        } catch (\Exception  $e) {
            return response()->json([
            'err' => 'X',
            'message' => $e->getMessage(),
            ], 500);
        }
      
    }

 private function mapKolkataRequest(array $payload)
    {
        return [[
            'district' => $payload['CI_DISTRICT'] ?? null,
            'mandal'   => $payload['CI_BLOCK'] ?? null,
            'gname'    => $payload['CI_GP'] ?? null,
            'lat'      => $payload['CI_LATITUDE'] ?? null,
            'log'      => $payload['CI_LONGITUDE'] ?? null,

            'downtime' => $payload['ADDITIONAL_DATA']['alert_timestamp'] ?? Carbon::now(),
            'downdate' => $payload['ADDITIONAL_DATA']['alert_timestamp'] ?? Carbon::now(),
            'update'   => date('Y-m-d H:i:s'),
            'uptime'   => date('Y-m-d H:i:s'),

            'downreason'           => $payload['ERROR_DESCROIION'] ?? null,
            'downreasonindetailed' => $payload['DETAILED_DESCRIPTION'] ?? null,
            'subsategory'          => $payload['PRIORITY_ID'] ?? null,
            'ticketid'             => $payload['TICKET_ID'],
            'type'                 => $payload['SERIAL_NO'] ?? null,
            'pop_map_key'          => $payload['HOST_NAME'] ?? null,

            // provider mobile must come from Kolkata
            'number' => $payload['PROVIDER_MOBILE'] ?? null,
            'problem_type' => $payload['PROBLEM_TYPE'] ?? null,
        ]];
    }

private function processTicketData(array $jsonData)
{
    ini_set('max_execution_time', 5000);
    ini_set('memory_limit', '500M');
    error_reporting(0);

    Log::info('Ticket bulk process started', [
        'total_records' => count($jsonData)
    ]);

    $success_count = 0;
    $failed_count  = 0;
    $results = [];
    $hasError = false;
    $errormeg="";

    DB::beginTransaction();

    try {

        foreach ($jsonData as $index => $keyvalue) {

            $ticketId = $keyvalue['ticketid'] ?? null;

            if (!$ticketId) {
                $failed_count++;
                $hasError = true;
                $errormeg = 'Ticket ID missing';
                Log::warning('Ticket ID missing', $keyvalue);
                continue;
            }

            Log::info('Processing ticket', [
                'index' => $index,
                'ticketid' => $ticketId
            ]);

            // Duplicate check
            if (DB::table('master_tickets')->where('ticketid', $ticketId)->exists()) {
                $failed_count++;
                $hasError = true;
                $errormeg='Duplicate ticket detected';

                Log::warning('Duplicate ticket detected', [
                    'ticketid' => $ticketId
                ]);

                $results[] = [
                    'ticketid' => $ticketId,
                    'error' => 'Duplicate ticket'
                ];
                continue;
            }

            //  Prepare master ticket data
            $data = [
                'district' => $keyvalue['district'],
                'mandal' => $keyvalue['mandal'],
                'gpname' => $keyvalue['gname'],
                'lat' => $keyvalue['lat'],
                'log' => $keyvalue['log'],
                'downtime' => date('h:i:s a', strtotime($keyvalue['downtime'])),
                'downdate' => date('Y-m-d', strtotime($keyvalue['downdate'])),
                'up_date' => date('Y-m-d', strtotime($keyvalue['update'])),
                'up_time' => date('h:i:s a', strtotime($keyvalue['uptime'])),
                'downreason' => $keyvalue['downreason'],
                'downreasonindetailed' => $keyvalue['downreasonindetailed'],
                'subsategory' => $keyvalue['subsategory'],
                'ticketid' => $ticketId,
                'ticketinsertstage' => 0, // default UNASSIGNED
                'olt_type' => $keyvalue['type'],
                'pop_map_key' => $keyvalue['pop_map_key'],
                'problem_type' => $keyvalue['problem_type'],

            ];

            // Insert master ticket (always)
            if (!DB::table('master_tickets')->insert($data)) {
                Log::error('Master ticket insert failed', [
                    'ticketid' => $ticketId
                ]);
                $failed_count++;
                continue;
            }

            //  Find nearest provider by lat/long
            $lat = $keyvalue['lat'];
            $lng = $keyvalue['log'];

            $provider = $this->getNearestProvider($lat, $lng);

            Log::info('Nearest provider lookup', [
                'ticketid' => $ticketId,
                'lat' => $lat,
                'lng' => $lng,
                'provider_found' => $provider ? $provider->id : null
            ]);

            if (!$provider) {
                // $hasError = true;
                // $errormeg='Ticket created but not assigned (no provider)';

                Log::warning('Ticket created but not assigned (no provider)', [
                    'ticketid' => $ticketId
                ]);

                // $results[] = [
                //     'ticketid' => $ticketId,
                //     'status' => 'CREATED_NOT_ASSIGNED'
                // ];

                continue;
            }

            $UserRequest = new UserRequests;
            $UserRequest->booking_id = $ticketId;
            $UserRequest->gpname = $keyvalue['gname'];
            $UserRequest->downreason = $keyvalue['downreasonindetailed'];
            $UserRequest->downreasonindetailed = $keyvalue['downreasonindetailed'];
            $UserRequest->user_id = 45;
            $UserRequest->provider_id = $provider->id;
            $UserRequest->current_provider_id = $provider->id;
            $UserRequest->service_type_id = 2;
            $UserRequest->status = 'SEARCHING';
            $UserRequest->s_latitude = $provider->latitude;
            $UserRequest->s_longitude = $provider->longitude;
            $UserRequest->d_latitude = $lat;
            $UserRequest->d_longitude = $lng;
            $UserRequest->otp = mt_rand(1000, 9999);
            $UserRequest->assigned_at = Carbon::now();
            $UserRequest->save();

            // Request filter
            $filter = new RequestFilter;
            $filter->request_id = $UserRequest->id;
            $filter->provider_id = $provider->id;
            $filter->save();

            // Update ticket as assigned
            DB::table('master_tickets')
                ->where('ticketid', $ticketId)
                ->update(['ticketinsertstage' => 1]);

            $success_count++;

            Log::info('Ticket created and assigned', [
                'ticketid' => $ticketId,
                'provider_id' => $provider->id
            ]);

            $results[] = [
                'ticketid' => $ticketId,
                'provider_id' => $provider->id,
                'status' => 'CREATED_AND_ASSIGNED'
            ];
        }

        DB::commit();

        Log::info('Ticket bulk process completed', [
            'success' => $success_count,
            'failed' => $failed_count
        ]);

        return response()->json([
            'err' => $hasError ? 'X' : '',
            'message' => $hasError
                ? $errormeg
                : 'Ticket created successfully',
              ], 200);

    } catch (\Exception $e) {

        DB::rollBack();

        Log::error('Ticket bulk process exception', [
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'err' => 'X',
            'message' => $e->getMessage(),
        ], 500);
    }
}

private function getNearestProvider($lat, $lng, $radius = 150)
{
    return DB::table('providers')
        ->select(
            'providers.id',
            'providers.latitude',
            'providers.longitude',
            DB::raw("
                (6371 * acos(
                    cos(radians($lat))
                    * cos(radians(latitude))
                    * cos(radians(longitude) - radians($lng))
                    + sin(radians($lat))
                    * sin(radians(latitude))
                )) AS distance
            ")
        )
        ->having('distance', '<=', $radius)
        ->orderBy('distance', 'ASC')
        ->first();
}


private function mapSnocStatusId($statusId)
{
    switch ($statusId) {
        case 5:
            return 'resolved';
        case 2:
            return 'onhold';
        case 3:
            return 'active';
        default:
            return null;
    }
}

private function updateSnocTicketStatus($incidentId, $teamId, $projectId, $status, $comments, $rcaFlag)
{
    $statusId = $this->mapSnocStatusId($status);

    if (!$statusId) {
        throw new \Exception('Invalid SNOC status mapping');
    }

    $finalComment = $comments;
    if ($rcaFlag === 'rca') {
        $finalComment .= ' | RCA included';
    }

    try {
        $client = new Client([
            'base_uri' => 'http://61.246.80.58:8084',
            'timeout'  => 30,
        ]);

        $response = $client->post('/snoc/changeTicketStatus', [
            'auth' => ['snoc', 'Welcome@123'], // Basic Auth
            'json' => [
                'incidentId'       => $incidentId,
                'teamId'           => $teamId,
                'projectId'        => $projectId,
                'statusId'         => $statusId,
                'incidentComments' => $finalComment,
                'incidentRcaFlag'  => $rcaFlag ?? ''
            ]
        ]);

        $body = json_decode($response->getBody(), true);

        return $body;

    } catch (\GuzzleHttp\Exception\RequestException $e) {
        throw new \Exception(
            $e->hasResponse()
                ? $e->getResponse()->getBody()->getContents()
                : 'SNOC status update failed'
        );
    }
}


public function getticketstatus(Request $request)
{
    try {

        $ticketId = $request->incidentId;
        $comments = $request->incidentComments;
        $rcaFlag  = $request->incidentRCAFlag;
        $status   = $request->status;
        $teamId    = 1;
        $projectId = 'TeraProject';
   

        if (!$ticketId) {
             return response()->json([
                'err' => 'X',
                'message' => 'incidentId missing',
                ], 400);
        }
        $mappedStatus = $this->mapStatus($status);
        if ($mappedStatus === 'UNKNOWN') {
             return response()->json([
                'err' => 'X',
                'message' => 'Invalid status value',
                ], 400);
        }
        $ticketExists = DB::table('master_tickets')
            ->where('ticketid', $ticketId)
            ->exists();

        if (!$ticketExists) {
            return response()->json([
                'err' => 'X',
                'message' => 'Ticket not found',
                ], 404);
        }

        $finalReason = $comments . ' | ' . $rcaFlag;

        $masterUpdated = DB::table('master_tickets')
            ->where('ticketid', $ticketId)
            ->update([
                'status'=>$status
            ]);

        $userUpdated = DB::table('user_requests')
            ->where('booking_id', $ticketId)
            ->update([
                'description' => $finalReason,
                'status' => $mappedStatus

            ]);

         $this->updateSnocTicketStatus(
   	 $ticketId,
    	$teamId,
    	$projectId,
    	$status,
    	$comments,
    	$rcaFlag
	);

        if ($masterUpdated === 0 && $userUpdated === 0) {
           return response()->json([
                'err' => 'X',
                'message' => 'No records updated',
              ], 409);
        }


         return response()->json([
            'err' => '',
            'message' => 'Ticket updated successfully',
            'Result' => [
                'ticketid' => $ticketId,
                'master_status' => $status,
                'user_request_status' => $mappedStatus,
                'remarks' => $finalReason
            ]
        ], 200);

    } catch (\Exception $e) {

            return response()->json([
            'err' => 'X',
            'message' => $e->getMessage(),
             ], 500);
    }
}

   private function mapStatus($status)
{
    return [
        1 => 'ACCEPTED',
        2 => 'ONHOLD',
        3 => 'PICKEDUP',
        4 => 'ARRIVED',
        5 => 'COMPLETED',
    ][$status] ?? 'UNKNOWN';
}
   public function exportNonGeotaggedImages()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $filename = "non_geotagged_images_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel utf-8 compatibility
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

            $columns = [
                'Record ID',
                'Request ID',
                'Image Type',
                'Filename',
                'DB LatLong',
                'GPS Status'
            ];
            fputcsv($file, $columns);

           
            \App\SubmitFile::where(function ($q) {
                $q->whereNotNull('joint_enclouser_beforeimg')
                    ->orWhereNotNull('joint_enclouser_afterimg');
            })
                ->orderBy('id', 'desc')
                ->chunk(50, function ($records) use ($file) {

                    foreach ($records as $rec) {

                        $imagesToCheck = [];

                        // 1. Process Joint Enclosure Before Images
                        $beforeImgs = json_decode($rec->joint_enclouser_beforeimg, true);
                        // Handle case where it might be a simple string or invalid JSON
                        if (!is_array($beforeImgs) && !empty($rec->joint_enclouser_beforeimg)) {
                            $beforeImgs = [$rec->joint_enclouser_beforeimg];
                        }
                        if (is_array($beforeImgs)) {
                            foreach ($beforeImgs as $img) {
                                $imagesToCheck[] = [
                                    'type' => 'Joint Enclosure Before',
                                    'filename' => $img,
                                    'db_latlong' => $rec->joint_enclosurebefore_latlong
                                ];
                            }
                        }

                        // 2. Process Joint Enclosure After Images
                        $afterImgs = json_decode($rec->joint_enclouser_afterimg, true);
                        if (!is_array($afterImgs) && !empty($rec->joint_enclouser_afterimg)) {
                            $afterImgs = [$rec->joint_enclouser_afterimg];
                        }
                        if (is_array($afterImgs)) {
                            foreach ($afterImgs as $img) {
                                $imagesToCheck[] = [
                                    'type' => 'Joint Enclosure After',
                                    'filename' => $img,
                                    'db_latlong' => $rec->joint_enclosureafter_latlong
                                ];
                            }
                        }

                        // Check each image
                        foreach ($imagesToCheck as $item) {
                            $status = 'OK';

                            $path = public_path('uploads/SubmitFiles/' . $item['filename']);

                            if (file_exists($path)) {
                                if (function_exists('exif_read_data')) {
                                    // Suppress errors for files without EXIF or invalid format
                                    $exif = @exif_read_data($path);

                                    if ($exif && isset($exif['GPSLatitude']) && isset($exif['GPSLongitude'])) {
                                        $status = 'OK';
                                    } else {
                                        $status = 'Missing EXIF GPS';
                                    }
                                } else {
                                    $status = 'EXIF Extension Missing';
                                }
                            } else {
                                $status = 'File Not Found';
                            }

                            // If not OK, add to Report
                            if ($status !== 'OK') {
                                fputcsv($file, [
                                    $rec->id,
                                    $rec->request_id,
                                    $item['type'],
                                    $item['filename'],
                                    $item['db_latlong'],
                                    $status
                                ]);
                            }
                        }
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}