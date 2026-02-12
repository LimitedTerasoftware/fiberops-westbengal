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

use \Carbon\Carbon;
use App\Provider;
use App\UserRequestPayment;
use App\UserRequests;
use App\Helpers\Helper;
use App\Document;
use App\Http\Controllers\SendPushNotification;
use Mail;
use App\ProviderTrackingHistory;
use DateTime;
use App\District;
use App\Block;
use App\Zonalmanger;
use App\Leave;
use Excel;
use Illuminate\Support\Facades\Validator;


class ProviderResource extends Controller
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
    public function index_old(Request $request)
    {

      
        $providers = Provider::get();
        
        if(isset($request->status) && !empty($request->status)){      
            $attend = DB::table('attendance')
                        ->where('created_at', '>=', Carbon::today()->toDateString())
                        ->get()->pluck('provider_id')->toArray();      
            if($request->status == 'active')
                $providers = collect($providers)->whereIn('id', $attend)->all();
            else
                $providers = Provider::whereNotIn('id', $attend)->get();
        }

         $total_documents=Document::count();        
                                             
            return view('admin.providers.index', compact('providers','total_documents'));
       
    }


public function index(Request $request)
{
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

    // $zonals = Zonalmanger::get();
    $districtQuery = District::query();
      if (!empty($district_id)) {
        $districtQuery->where('id', $district_id);
    }
    $districts = $districtQuery->get();


    $query = Provider::join('zonal_managers', 'providers.zone_id', '=', 'zonal_managers.id')
        ->join('districts', 'providers.district_id', '=', 'districts.id')
        ->leftJoin('leaves', function($join) {
                $join->on('providers.id', '=', 'leaves.provider_id')
                    ->whereDate('leaves.start_date', '<=', Carbon::today())
                    ->whereDate('leaves.end_date', '>=', Carbon::today())
                    ->where('leaves.status', 'approved');
            })
        ->select(
            'providers.*',
            'zonal_managers.Name as zone_name',
            'districts.name as district_name',
            DB::raw('CASE WHEN leaves.id IS NULL THEN "Not on Leave" ELSE "On Leave" END as leave_status')
        )
        ->where('providers.company_id', $company_id)
        ->where('providers.state_id', $state_id)
        ->distinct('providers.id');
    if (!empty($district_id)) {
        $query->where('providers.district_id', $district_id);
    }

    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('providers.first_name', 'like', "%{$search}%")
              ->orwhere('providers.last_name', 'like', "%{$search}%")
              ->orwhere('providers.mobile', 'like', "%{$search}%")
              ->orwhere('providers.email', 'like', "%{$search}%")
               ->orWhere('leaves.type', 'like', "%{$search}%")
               ->orWhere('leaves.status', 'like', "%{$search}%");
              

        });
    }

    if ($request->has('zone_id') && $request->zone_id != '') {
        $query->where('providers.zone_id', $request->zone_id);
    }

    if ($request->has('district_id') && $request->district_id != '') {
        $query->where('providers.district_id', $request->district_id);
    }

    if ($request->has('role') && $request->role != '') {
        $query->where('providers.type', $request->role);
    }

    $providers = $query->orderBy('providers.id', 'desc')->paginate(10)->appends($request->all());

    return view('admin.providers.provider_list', compact('providers', 'zonals', 'districts'));
}


public function exportProviders(Request $request)
{
    $user = Session::get('user');
    $company_id = $user->company_id;
    $state_id = $user->state_id;

    $query = Provider::join('zonal_managers', 'providers.zone_id', '=', 'zonal_managers.id')
        ->join('districts', 'providers.district_id', '=', 'districts.id')
        ->select(
            'providers.*',
            'zonal_managers.Name as zone_name',
            'districts.name as district_name'
        )
        ->where('providers.company_id', $company_id)
        ->where('providers.state_id', $state_id);

    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('providers.first_name', 'like', "%{$search}%")
              ->orWhere('providers.last_name', 'like', "%{$search}%")
              ->orWhere('providers.mobile', 'like', "%{$search}%")
              ->orWhere('providers.email', 'like', "%{$search}%");
        });
    }

    if ($request->has('zone_id') && $request->zone_id != '') {
        $query->where('providers.zone_id', $request->zone_id);
    }

    if ($request->has('district_id') && $request->district_id != '') {
        $query->where('providers.district_id', $request->district_id);
    }

    if ($request->has('role') && $request->role != '') {
        $query->where('providers.type', $request->role);
    }

    $providers = $query->orderBy('providers.id', 'desc')->get();

    $data = [];
    $data[] = ['Name', 'Mobile', 'Email', 'Zone', 'District', 'Version', 'Status', 'Created At'];

    foreach ($providers as $p) {
        $data[] = [
            $p->first_name . ' ' . $p->last_name,
            $p->mobile,
            $p->email,
            $p->zone_name ?? 'N/A',
            $p->district_name ?? 'N/A',
            $p->version ?? '�',
            $p->attendance_status == 'active' ? 'Online' : 'Offline',
            Carbon::parse($p->created_at)->format('d-M-Y h:i A'),
        ];
    }

    return Excel::create('providers_' . now()->format('Ymd_His'), function($excel) use ($data) {
        $excel->sheet('Providers', function($sheet) use ($data) {
            $sheet->fromArray($data, null, 'A1', false, false);
        });
    })->download('xlsx');
}
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Session::get('user');
        $state_id = $user->state_id;
        $district_id = $user->district_id;

        $zoneIdsQuery = DB::table('gp_list')->where('state_id', $state_id);
        if (!empty($district_id)) {
            $zoneIdsQuery->where('district_id', $district_id);
        }

        $zoneIds = $zoneIdsQuery->pluck('zonal_id')->unique();
        $zonalmanagers = DB::table('zonal_managers')->whereIn('id',$zoneIds)->get();

      
        $districtQuery = District::where('state_id',$state_id);
        if (!empty($district_id)) {
            $districtQuery->where('id', $district_id);
        }
        $districts = $districtQuery->get();

       
        $blockQuery= Block::query();
          if (!empty($district_id)) {
            $blockQuery->where('district_id', $district_id);
        }
        $blocks = $blockQuery->get();
          // $zonals = Zonalmanger::get();
         // $districts = District::get();
        // $zonalmanagers= Zonalmanger::get();
        $teams= DB::table('teams')->get();
        return view('admin.providers.create',compact('districts','blocks','zonalmanagers','teams'));
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
            'district_id' => 'required',
            'block_id' => 'required',
            'password' => 'required|min:6|confirmed',
            
        ]);
     

        try{
            
            $user = Session::get('user');
            $state_id = $user->state_id;
            $provider = $request->all();
            $provider['state_id'] = $state_id;
           
            $provider['password'] = bcrypt($request->password);
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
            return view('admin.providers.provider-details', compact('provider'));
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
            $user = Session::get('user');
            $state_id = $user->state_id;
            $district_id = $user->district_id;

            $zoneIdsQuery = DB::table('gp_list')->where('state_id', $state_id);
            if (!empty($district_id)) {
                $zoneIdsQuery->where('district_id', $district_id);
            }

            $zoneIds = $zoneIdsQuery->pluck('zonal_id')->unique();
            $zonalmanagers = DB::table('zonal_managers')->whereIn('id',$zoneIds)->get();

        
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
            // $districts = District::get();
            // $blocks = Block::get();
            $provider = Provider::findOrFail($id);
            // $zonalmanagers= Zonalmanger::get();
            $teams= DB::table('teams')->get();
            return view('admin.providers.edit',compact('provider','districts','blocks','zonalmanagers','teams'));
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }

       public function passwordchange($id)
    {
        try {
            $districts = District::get();
            $blocks = Block::get();
            $provider = Provider::findOrFail($id);
            $zonalmanagers= Zonalmanger::get();
            $teams= DB::table('teams')->get();
            return view('admin.providers.password',compact('provider','districts','blocks','zonalmanagers','teams'));
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }


    public function passwordUpdate(Request $request, $id)
{
    // Validate input
    $request->validate([
        'password' => 'required|string|min:6|confirmed',
    ]);

    try {
        $provider = Provider::findOrFail($id);

        // Update password
        $provider->password = Hash::make($request->password);
        $provider->save();

        return redirect()
            ->route('admin.provider.index')
            ->with('success', 'Password changed successfully.');

    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->with('error', 'Failed to change password. Please try again.');
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
            $provider->email= $request->email;
            $provider->district_id= $request->district_id;
            $provider->block_id= $request->block_id;
            $provider->type= $request->type;
            $provider->zone_id= $request->zone_id;
            $provider->team_id= $request->team_id;
             $provider->joiningdate= $request->joindate;
            
            $provider->save();

            return redirect()->route('admin.provider.index')->with('flash_success', trans('admin.provider_msgs.provider_update'));    
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
    public function approve(Request $request, $id)
    {
        try {            
            $Provider = Provider::findOrFail($id);           
            // $total_documents=Document::count();
            // if($Provider->active_documents()==$total_documents && $Provider->service) {
            //     if($Provider->status=='onboarding'){
            //         // Sending push to the provider
            //         (new SendPushNotification)->DocumentsVerfied($id);
            //     }                
                $Provider->update(['status' => 'approved']);
                $url=$request->session()->pull('providerpage');                
                return redirect()->to($url)->with('flash_success', trans('admin.provider_msgs.provider_approve'));
            // } else {
            //     if($Provider->active_documents()!=$total_documents){
            //         $msg=trans('admin.provider_msgs.document_pending');
            //     }
            //     if(!$Provider->service){
            //         $msg=trans('admin.provider_msgs.service_type_pending');
            //     }

            //     if(!$Provider->service && $Provider->active_documents()!=$total_documents){
            //         $msg=trans('admin.provider_msgs.provider_pending');
            //     }
            //     return redirect()->route('admin.provider.document.index', $id)->with('flash_error',$msg);
            // }
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
                    ->paginate($this->perpage);

            $pagination=(new Helper)->formatPagination($requests);        

            return view('admin.request.index', compact('requests','pagination'));
        } catch (Exception $e) {
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    /**
     * account statements.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement($id){

        try{
            $listname ='';
            $requests = UserRequests::where('provider_id',$id)
                        ->where('status','COMPLETED')
                        ->with('payment')
                        ->get();

            $rides = UserRequests::where('provider_id',$id)->with('payment')->orderBy('id','desc')->paginate($this->perpage);
            $cancel_rides = UserRequests::where('status','CANCELLED')->where('provider_id',$id)->count();
            $Provider = Provider::find($id);
            $revenue = UserRequestPayment::whereHas('request', function($query) use($id) {
                                    $query->where('provider_id', $id );
                                })->select(\DB::raw(
                                   'SUM(ROUND(provider_pay)) as overall, SUM(ROUND(provider_commission)) as commission' 
                               ))->get();


            $Joined = $Provider->created_at ? '- Joined '.$Provider->created_at->diffForHumans() : '';

            $pagination=(new Helper)->formatPagination($rides);

            return view('admin.providers.statement', compact('rides','cancel_rides','revenue','pagination'))
                        ->with('page',$Provider->first_name."'s Overall Statement ". $Joined)->with('listname',$listname);

        } catch (Exception $e) {
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    public function Accountstatement($id){

        try{

            $requests = UserRequests::where('provider_id',$id)
                        ->where('status','COMPLETED')
                        ->with('payment')
                        ->get();

            $rides = UserRequests::where('provider_id',$id)->with('payment')->orderBy('id','desc')->paginate($this->perpage);
            $cancel_rides = UserRequests::where('status','CANCELLED')->where('provider_id',$id)->count();
            $Provider = Provider::find($id);
            $revenue = UserRequestPayment::whereHas('request', function($query) use($id) {
                                    $query->where('provider_id', $id );
                                })->select(\DB::raw(
                                   'SUM(ROUND(fixed) + ROUND(distance)) as overall, SUM(ROUND(commision)) as commission' 
                               ))->get();


            $Joined = $Provider->created_at ? '- Joined '.$Provider->created_at->diffForHumans() : '';

            $pagination=(new Helper)->formatPagination($rides);

            return view('account.providers.statement', compact('rides','cancel_rides','revenue','pagination'))
                        ->with('page',$Provider->first_name."'s Overall Statement ". $Joined);

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

            $providers = Provider::all();

            return view('admin.providers.tracking', compact('tracking','providers'));

        } catch (Exception $e) { 
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }
   
    
    public function HandleLeaves(Request $request)
    {
       
         $validator = Validator::make($request->all(),[
            'provider_id' => 'required|exists:providers,id',
            'type' =>'required|in:leave,late_login',
            'start_date'  => 'required_if:type,leave|date',
            'end_date'    => 'required_if:type,leave|date|after_or_equal:start_date',
            'reason'=>'nullable|string',
            'status'      => 'required|in:approved,pending,rejected'
         ]);

       
           if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()
                ], 422);
            }
            if ($request->leave_id) {
                // UPDATE
                $leave = Leave::findOrFail($request->leave_id);
                $leave->type = $request->type;
                $leave->reason = $request->reason;
                $leave->start_date = Carbon::today();
                $leave->end_date = Carbon::today();
                $leave->status = 'approved';
                $leave->save();
            }else{


            $already = Leave::where('provider_id', $request->provider_id)
                            ->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date)
                            ->whereIn('type', ['leave', 'late_login'])
                            ->first();

            if ($already) {
                return response()->json([
                    'success' => false,
                     'message' => "Already marked as {$request->type} for today"
                ], 409);
            }

    
        Leave::create([
            'provider_id' => $request->provider_id,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'reason'      => $request->reason,
            'status'      => $request->status,
            'type'=> $request->type
        ]);
    }
         
      return response()->json([
            'success' => true,
            'message' => "{$request->type} added successfully!"
        ]);

    
    }
    public function DeleteLeaves($id){
    Leave::where('id', $id)->delete();
    return response()->json(['success' => true]);
    }

}
