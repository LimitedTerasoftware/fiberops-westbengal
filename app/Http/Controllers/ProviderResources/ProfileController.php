<?php

namespace App\Http\Controllers\ProviderResources;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;

use Auth;
use Setting;
use Storage;
use Exception;
use Carbon\Carbon;

use App\Provider;
use App\Leave;
use App\ProviderProfile;
use App\UserRequests;
use App\ProviderService;
use App\Fleet;
use App\RequestFilter;
use App\Document;
use App\Attendance;
use App\Http\Controllers\SendPushNotification;
use App\Http\Controllers\ProviderResources\DocumentController;
use App\ProviderTrackingHistory;
use DB;
use Log;

class ProfileController extends Controller
{
    /**
     * Create a new user instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('provider.api', ['except' => ['show', 'store', 'available', 'location_edit', 'location_update','stripe']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            Auth::user()->service = ProviderService::where('provider_id',Auth::user()->id)
                                            ->with('service_type')
                                            ->first();
            Auth::user()->fleet = Fleet::find(Auth::user()->fleet);
            Auth::user()->currency = Setting::get('currency', '$');
            Auth::user()->sos = Setting::get('sos_number', '911');
            Auth::user()->measurement = Setting::get('distance', 'Kms');
            Auth::user()->profile = ProviderProfile::where('provider_id',Auth::user()->id)
                                            ->first();
            Auth::user()->cash =(int)Setting::get('CASH', 1);
            Auth::user()->card =(int)Setting::get('CARD', 0);
            Auth::user()->stripe_secret_key = Setting::get('stripe_secret_key', '');
            Auth::user()->stripe_publishable_key = Setting::get('stripe_publishable_key', '');
            return Auth::user();

        } catch (Exception $e) {
            return $e->getMessage();
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
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'mobile' => 'required',
                'avatar' => 'mimes:jpeg,bmp,png',
                'language' => 'max:255',
                'address' => 'max:255',
                'address_secondary' => 'max:255',
                'city' => 'max:255',
                'country' => 'max:255',
                'postal_code' => 'max:255',
            ]);

        try {

            $Provider = Auth::user();

            if($request->has('first_name')) 
                $Provider->first_name = $request->first_name;

            if($request->has('last_name')) 
                $Provider->last_name = $request->last_name;

            if ($request->has('mobile'))
                $Provider->mobile = $request->mobile;

            if ($request->hasFile('avatar')) {
                Storage::delete($Provider->avatar);
                $Provider->avatar = $request->avatar->store('provider/profile');
            }

            if($request->has('service_type')) {
                if($Provider->service) {
                    if($Provider->service->service_type_id != $request->service_type) {
                        $Provider->status = 'banned';
                    }
                    //$ProviderService = ProviderService::where('provider_id',Auth::user()->id);
                    $Provider->service->service_type_id = $request->service_type;
                    $Provider->service->service_number = $request->service_number;
                    $Provider->service->service_model = $request->service_model;
                    $Provider->service->save();

                } else {
                    ProviderService::create([
                        'provider_id' => $Provider->id,
                        'service_type_id' => $request->service_type,
                        'service_number' => $request->service_number,
                        'service_model' => $request->service_model,
                    ]);
                    $Provider->status = 'banned';
                }
            }

            if($Provider->profile) {
                $Provider->profile->update([
                        'language' => $request->language ? : $Provider->profile->language,
                        'address' => $request->address ? : $Provider->profile->address,
                        'address_secondary' => $request->address_secondary ? : $Provider->profile->address_secondary,
                        'city' => $request->city ? : $Provider->profile->city,
                        'country' => $request->country ? : $Provider->profile->country,
                        'postal_code' => $request->postal_code ? : $Provider->profile->postal_code,
                    ]);
            } else {
                ProviderProfile::create([
                        'provider_id' => $Provider->id,
                        'language' => $request->language,
                        'address' => $request->address,
                        'address_secondary' => $request->address_secondary,
                        'city' => $request->city,
                        'country' => $request->country,
                        'postal_code' => $request->postal_code,
                    ]);
            }


            $Provider->save();

            return redirect(route('provider.profile.index'));
        }

        catch (ModelNotFoundException $e) {
            return response()->json(['error' => trans('api.provider.provider_not_found')], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('provider.profile.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'mobile' => 'required',
                'avatar' => 'mimes:jpeg,bmp,png',
                'language' => 'max:255',
                'address' => 'max:255',
                'address_secondary' => 'max:255',
                'city' => 'max:255',
                'country' => 'max:255',
                'postal_code' => 'max:255',
            ]);

        try {

            $Provider = Auth::user();

            if($request->has('first_name')) 
                $Provider->first_name = $request->first_name;

            if($request->has('last_name')) 
                $Provider->last_name = $request->last_name;

            if ($request->has('mobile'))
                $Provider->mobile = $request->mobile;

            if ($request->hasFile('avatar')) {
                Storage::delete($Provider->avatar);
                $Provider->avatar = $request->avatar->store('provider/profile');
            }

            if($Provider->profile) {
                $Provider->profile->update([
                        'language' => $request->language ? : $Provider->profile->language,
                        'address' => $request->address ? : $Provider->profile->address,
                        'address_secondary' => $request->address_secondary ? : $Provider->profile->address_secondary,
                        'city' => $request->city ? : $Provider->profile->city,
                        'country' => $request->country ? : $Provider->profile->country,
                        'postal_code' => $request->postal_code ? : $Provider->profile->postal_code,
                    ]);
            } else {
                ProviderProfile::create([
                        'provider_id' => $Provider->id,
                        'language' => $request->language,
                        'address' => $request->address,
                        'address_secondary' => $request->address_secondary,
                        'city' => $request->city,
                        'country' => $request->country,
                        'postal_code' => $request->postal_code,
                    ]);
            }


            $Provider->save();

            return $Provider;
        }

        catch (ModelNotFoundException $e) {
            return response()->json(['error' => trans('api.provider.provider_not_found')], 404);
        }
    }

    /**
     * Update latitude and longitude of the user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function location(Request $request)
    { 
        $this->validate($request, [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

        if($Provider = Auth::user()){

            $Provider->latitude = $request->latitude;
            $Provider->longitude = $request->longitude;
            $Provider->save();

            $update = ProviderTrackingHistory::whereprovider_id(Auth::user()->id)->whereDate('created_at',Carbon::today())->first();

            if($update)
            {
                 
                $old_latlng = json_decode($update->latlng); 
                $new_latlng = array(['latitude' => $request->latitude,'longitude' => $request->longitude,'datetime' => Carbon::now()->format('Y-m-d H:i:s')]);
                $latlng_json = json_encode(array_merge($old_latlng,$new_latlng));
                $update->latlng = $latlng_json;
                $update->save();  

                DB::table('provider_tracking')->insert([
                'provider_id' => Auth::user()->id,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude,
                ]);

            }
            else
            {
                $new_latlng = array(['latitude' => $request->latitude,'longitude' => $request->longitude,'datetime' => Carbon::now()->format('Y-m-d H:i:s')]);

                $insert = new ProviderTrackingHistory;
                $insert->provider_id = Auth::user()->id; 
                $insert->latlng = json_encode($new_latlng); 
                $insert->save(); 

                 DB::table('provider_tracking')->insert([
                'provider_id' => Auth::user()->id,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude,
                ]);

            }

            return response()->json(['message' => trans('api.provider.location_updated')]);

        } else {
            return response()->json(['error' => trans('api.provider.provider_not_found')]);
        }
    }

    public function locationTest(Request $request)
    {
            Log::info("=== LocationTest API Hit ===");

            Log::info("Incoming Request Data: ", $request->all());
        $this->validate($request, [
            'locations'               => 'required|array',
            'locations.*.latitude'    => 'required|numeric',
            'locations.*.longitude'   => 'required|numeric',
            'locations.*.offon_timestamp' => 'required|date',
        ]);

        $provider = Auth::user();

        if (!$provider) {
             Log::error("Provider not found for token.");

            return response()->json(['error' => trans('api.provider.provider_not_found')]);
        }
        Log::info("Authenticated Provider ID: " . $provider->id);

        $locations = $request->input('locations', []);

        if (empty($locations)) {
            Log::warning("No locations array provided in request.");

            return response()->json(['error' => 'No locations provided']);
        }


        $lastLocation = end($locations); 
        Log::info("Last Location being saved: ", $lastLocation);

        $provider->latitude = $lastLocation['latitude'];
        $provider->longitude = $lastLocation['longitude'];
        $provider->save();
    Log::info("Provider last lat/long updated.");

        foreach ($request->locations as $loc) {
        Log::info("---- Processing Location ----", $loc);

            // Check if history exists for today
            $update = ProviderTrackingHistory::where('provider_id', $provider->id)
                        ->whereDate('created_at', Carbon::today())
                        ->first();
              if ($update) {
                 Log::info("Existing ProviderTrackingHistory found for today. ID: ".$update->id);
                } else {
                    Log::info("No ProviderTrackingHistory exists for today. Creating new.");
                }

            $new_latlng = [
                'latitude'  => $loc['latitude'],
                'longitude' => $loc['longitude'],
                'datetime'  => $loc['offon_timestamp'], 
            ];
                    Log::info("New latlng entry to merge: ", $new_latlng);


            if ($update) {
                
                $old_latlng = json_decode($update->latlng, true);
                if (!is_array($old_latlng)) $old_latlng = [];
                Log::info("Old latlng data count: ".count($old_latlng));

                $update->latlng = json_encode(array_merge($old_latlng, [$new_latlng]));
                $update->save();
                 Log::info("Updated today's ProviderTrackingHistory latlng.");

            } else {
                $insert = new ProviderTrackingHistory;
                $insert->provider_id = $provider->id;
                $insert->latlng = json_encode([$new_latlng]);
                $insert->save();
                            Log::info("New ProviderTrackingHistory created with first latlng.");

            }

            // Insert into provider_tracking table
            DB::table('provider_tracking')->insert([
                'provider_id' => $provider->id,
                'latitude'    => $loc['latitude'],
                'longitude'   => $loc['longitude'],
                'created_at'  => $loc['offon_timestamp'], 
                'updated_at'  => $loc['offon_timestamp'],
            ]);
                    Log::info("Inserted into provider_tracking table.");

        }

        return response()->json(['message' => 'Location history updated successfully']);
    }

    public function update_language(Request $request)
    {
        $this->validate($request, [
               'language' => 'required',
            ]);

        try {

            $Provider = Auth::user();

            if($Provider->profile) {
                $Provider->profile->update([
                        'language' => $request->language ? : $Provider->profile->language
                    ]);
            } else {
                ProviderProfile::create([
                        'provider_id' => $Provider->id,
                        'language' => $request->language,
                    ]);
            }

            return response()->json(['message' => trans('api.provider.language_updated'),'language'=>$request->language]);
        }

        catch (ModelNotFoundException $e) {
            return response()->json(['error' => trans('api.provider.provider_not_found')], 404);
        }
    }

    /**
     * Toggle service availability of the provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function available(Request $request)
    {
      
        $this->validate($request, [
                'service_status' => 'required|in:active,offline',
            ]);
     
                $Provider = Auth::user();
                if ($request->has('version')) {
                        $Provider->update(['version' => $request->version]);
                    }
                $provider_id = $Provider->id;
                
                // if ($this->isOnLeave($provider_id)) {
                //     return response()->json([
                //         'status' => false,
                //         'message' => 'You are on leave today. Attendance cannot be marked.'
                //     ], 200);
                // }
                //dd(date('Y-m-d'));
		       $findattendancerecord = Attendance::where('provider_id',$provider_id)->where('created_at','>=',Carbon::today())->first();

		       if(count($findattendancerecord)>0){
                 if($request->service_status == 'active')
                   {
                Attendance::where('provider_id', $provider_id)->where('created_at','>=',Carbon::today())->update(['status'=>$request->service_status,'address'=>$request->address]);
                   }
                   else{

                  Attendance::where('provider_id', $provider_id)->where('created_at','>=',Carbon::today())->update(['status'=>$request->service_status,'offaddress'=>$request->address]);  
                   }
                    if ($request->hasFile('online_image')) {
                        $file = $request->file('online_image');
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $file->move(public_path('uploads/attendance_images'), $filename);

                        // $file->storeAs('attendance_images', $filename); 
                        Attendance::where('provider_id', $provider_id)->where('created_at','>=',Carbon::today())->update(['online_image'=>$filename]);  

                    }

		       } else {
		       $attendance = new Attendance;
		       $attendance->provider_id = $Provider->id;
		       $attendance->address = $request->address;
		       $attendance->status = $request->service_status;
               if ($request->hasFile('online_image')) {
                    $file = $request->file('online_image');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/attendance_images'), $filename);

                    // $file->storeAs('attendance_images', $filename);
                    $attendance->online_image = $filename; 
                }
		       $attendance->save(); // returns false
		      }
		    
        if($Provider->service) {
            
            $provider = $Provider->id;
            $OfflineOpenRequest = RequestFilter::with(['request.provider','request'])
                ->where('provider_id', $provider)
                ->whereHas('request', function($query) use ($provider){
                    $query->where('status','SEARCHING');
                    $query->where('current_provider_id','<>',$provider);
                    $query->orWhereNull('current_provider_id');
                    })->pluck('id');

            if(count($OfflineOpenRequest)>0) {
                RequestFilter::whereIn('id',$OfflineOpenRequest)->delete();
            }   
           
            $Provider->service->update(['status' => $request->service_status]);
           

        } else {
            if($Provider->type == 2){
              $service_type_id = 2 ;
            }
            else {
              $a=array("1"=>"1","3"=>"3","4"=>"4");
              $service_type_id = array_rand($a,1);   
            }

            
            
           $productservice =  ProviderService::create([
                        'provider_id' => $Provider->id,
                        'service_type_id' => $service_type_id,
                        'service_number' => '122',
                        'service_model' => 'test',
                        'status'  =>  $request->service_status,
                        'address' => $request->address
                    ]);

             
              
          
            //return response()->json(['error' => trans('api.provider.not_approved')]);
        }

        
        //  return response()->json([
        //                 'status' => true,
        //                 'message' => 'Success.',
        //                 'Provider'=>$Provider
        //             ], 200);
         return $Provider;
    }
    public function availableTest(Request $request)
    {
      
        $this->validate($request, [
                'service_status' => 'required|in:active,offline',
            ]);
     
                $Provider = Auth::user();
                if ($request->has('version')) {
                        $Provider->update(['version' => $request->version]);
                    }
                $provider_id = $Provider->id;
                
                if ($this->isOnLeave($provider_id)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'You are on leave today. Attendance cannot be marked.',
                         'Provider'=>$Provider
                    ], 200);
                }
                //dd(date('Y-m-d'));
		       $findattendancerecord = Attendance::where('provider_id',$provider_id)->where('created_at','>=',Carbon::today())->first();

		       if(count($findattendancerecord)>0){
                 if($request->service_status == 'active')
                   {
                Attendance::where('provider_id', $provider_id)->where('created_at','>=',Carbon::today())->update(['status'=>$request->service_status,'address'=>$request->address]);
                   }
                   else{

                  Attendance::where('provider_id', $provider_id)->where('created_at','>=',Carbon::today())->update(['status'=>$request->service_status,'offaddress'=>$request->address]);  
                   }
                    if ($request->hasFile('online_image')) {
                        $file = $request->file('online_image');
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $file->move(public_path('uploads/attendance_images'), $filename);

                        // $file->storeAs('attendance_images', $filename); 
                        Attendance::where('provider_id', $provider_id)->where('created_at','>=',Carbon::today())->update(['online_image'=>$filename]);  

                    }

		       } else {
		       $attendance = new Attendance;
		       $attendance->provider_id = $Provider->id;
		       $attendance->address = $request->address;
		       $attendance->status = $request->service_status;
               if ($request->hasFile('online_image')) {
                    $file = $request->file('online_image');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/attendance_images'), $filename);

                    // $file->storeAs('attendance_images', $filename);
                    $attendance->online_image = $filename; 
                }
		       $attendance->save(); // returns false
		      }
		    
        if($Provider->service) {
            
            $provider = $Provider->id;
            $OfflineOpenRequest = RequestFilter::with(['request.provider','request'])
                ->where('provider_id', $provider)
                ->whereHas('request', function($query) use ($provider){
                    $query->where('status','SEARCHING');
                    $query->where('current_provider_id','<>',$provider);
                    $query->orWhereNull('current_provider_id');
                    })->pluck('id');

            if(count($OfflineOpenRequest)>0) {
                RequestFilter::whereIn('id',$OfflineOpenRequest)->delete();
            }   
           
            $Provider->service->update(['status' => $request->service_status]);
           

        } else {
            if($Provider->type == 2){
              $service_type_id = 2 ;
            }
            else {
              $a=array("1"=>"1","3"=>"3","4"=>"4");
              $service_type_id = array_rand($a,1);   
            }

            
            
           $productservice =  ProviderService::create([
                        'provider_id' => $Provider->id,
                        'service_type_id' => $service_type_id,
                        'service_number' => '122',
                        'service_model' => 'test',
                        'status'  =>  $request->service_status,
                        'address' => $request->address
                    ]);

             
              
          
            //return response()->json(['error' => trans('api.provider.not_approved')]);
        }

        
         return response()->json([
                        'status' => true,
                        'message' => 'Success.',
                        'Provider'=>$Provider
                    ], 200);
        //  return $Provider;
    }

    private function isOnLeave($provider_id)
    {
        return Leave::where('provider_id', $provider_id)
            ->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->where('type','leave')
            ->where('status', 'approved')
            ->exists();
    }


    /**
     * Update password of the provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function password(Request $request)
    {
        $this->validate($request, [
                'password' => 'required|confirmed',
                'password_old' => 'required',
            ]);

        $Provider = Auth::user();

        if(password_verify($request->password_old, $Provider->password))
        {
            $Provider->password = bcrypt($request->password);
            $Provider->save();

            return response()->json(['message' => trans('api.provider.password_updated')]);
        } else {
            return response()->json(['error' => trans('api.provider.change_password')], 422);
        }
    }

    /**
     * Show providers daily target.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function target(Request $request)
    {
        try {
            
            $Rides = UserRequests::where('provider_id', Auth::user()->id)
                    ->where('status', 'COMPLETED')
                    ->where('created_at', '>=', Carbon::today())
                    ->with('payment', 'service_type')
                    ->get();

            return response()->json([
                    'rides' => $Rides,
                    'rides_count' => $Rides->count(),
                    'target' => Setting::get('daily_target','0')
                ]);

        } catch(Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }

    public function chatPush(Request $request){

        $this->validate($request,[
                'user_id' => 'required|numeric',
                'message' => 'required',
            ]);       

        try{

            $user_id=$request->user_id;
            $message=$request->message;
           
            $message = \PushNotification::Message($message,array(
            'badge' => 1,
            'sound' => 'default',
            'custom' => array('type' => 'chat')
            ));

           
            (new SendPushNotification)->sendPushToProvider($user_id, $message);          

            return response()->json(['success' => 'true']);

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    //provider document list
    public function documents(Request $request)
    {
        try {

            $provider_id=Auth::user()->id;

            $Documents=Document::select('id','name','type')
                        ->with(['providerdocuments' => function ($query) use ($provider_id) {
                        $query->where('provider_id', $provider_id);
                        }])->get();

            return response()->json(['documents' => $Documents]);

        } catch(Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }

    //provider document list
    public function documentstore(Request $request)
    {
        $this->validate($request, [
            'document' => 'required',
            'document.*' => 'mimes:jpg,jpeg,png|max:2048'
        ]);
        try {           

            if ($request->hasFile('document')) {

                foreach($request->file('document') as $ikey=> $image)
                {
                    $ids=$request->input('id');
                    $doc_id=$ids[$ikey];
                    $provider_id=Auth::user()->id;
                    (new DocumentController)->documentupdate($image, $doc_id,$provider_id);                    
                }                
                
                if(Setting::get('CARD', 0) == 1) {
                    Provider::where('id', Auth::user()->id)->where('status','document')->update(['status'=>'card']);
                }
                else{
                    if(Setting::get('demo_mode', 0) == 1) {
                        Provider::where('id', Auth::user()->id)->where('status','document')->update(['status'=>'approved']);
                    }
                    else{                        
                        Provider::where('id', Auth::user()->id)->where('status','document')->update(['status'=>'onboarding']);
                    }    
                }    

                return $this->documents($request); 
            }

        } catch(Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 422);
        }
    }

    public function stripe(Request $request)
    {
        if(isset($request->code)){
            $post = [
                'client_secret' => Setting::get('stripe_secret_key'),
                'code' => $request->code,
                'grant_type' => 'authorization_code'
            ];
            $curl = curl_init("https://connect.stripe.com/oauth/token");
            curl_setopt($curl, CURLOPT_HEADER, 0); 
            curl_setopt($curl, CURLOPT_POST, 1); 
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
            $result = curl_exec($curl); 
            $curl_error = curl_error($curl);
            curl_close($curl);
            $stripe = json_decode($result);

            if($stripe->stripe_user_id){
                $provider = Provider::where('id', Auth::user()->id)->first();
                $provider->stripe_acc_id = $stripe->stripe_user_id;
                $provider->save();

                if($request->ajax()){
                    return response()->json(['message' => 'Your stripe account connected successfully']);
                }else{
                    return redirect('/provider')->with('flash_success', 'Your stripe account connected successfully');
                }
            }else{
                if($request->ajax()){
                    return response()->json(['message' => $curl_error]);
                }else{
                    return redirect('/provider')->with('flash_error', $curl_error);
                }
            }

        }else{
            if($request->ajax()){
                return response()->json(['message' => $request->error_description]);
            }else{
                return redirect('/provider')->with('flash_error', $request->error_description);
            }
        }
    }


}
