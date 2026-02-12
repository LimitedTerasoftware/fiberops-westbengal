<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use DB;
use Exception;
use Setting;
use Storage;

use App\Helpers\Helper;
use Mail;
use DateTime;
use App\District;
use App\Block;
use Session;

class LocationResource extends Controller
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

        $districtQuery = District::where('state_id',$state_id);
        if (!empty($district_id)) {
            $districtQuery->where('id', $district_id);
        }
        $districts = $districtQuery->paginate($this->perpage);
        // $blocks= Block::get();
        $pagination=(new Helper)->formatPagination($districts);
        return view('admin.locations.district.index',compact('districts','pagination'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.locations.district.create');
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
            'district_name' => 'required|max:255'            
        ]);

        try{
            $district = array();
            $district['name'] = $request->district_name;
            $district = District::create($district);

            return redirect()
                ->route('admin.location.index')
                ->with('flash_success', trans('admin.location_msgs.district_saved'));
        } 
        catch (Exception $e) {  
            // dd($e->getMessage());
            return back()->with('flash_error', trans('admin.location_msgs.district_not_found'));
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
            $district = District::findOrFail($id);
            return view('admin.locations.district.edit',compact('district'));
        } catch (ModelNotFoundException $e) {
            return $e;
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
            'district_name' => 'required|max:255'
        ]);

        try {

            $district = District::findOrFail($id);
            $district->name = $request->district_name;
            $district->save();

            return redirect()->route('admin.location.index')->with('flash_success', trans('admin.location_msgs.district_update'));    
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.location_msgs.district_not_found'));
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
            District::find($id)->delete();
            return back()->with('message', trans('admin.location_msgs.district_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.location_msgs.district_not_found'));
        }
    }

    // Listing the blocks
    public function list_block(Request $request)
    {
        $user = Session::get('user');
        $company_id = $user->company_id;
        $state_id = $user->state_id;
        $district_id = $user->district_id;

        $districtQuery = District::where('state_id',$state_id);
        if (!empty($district_id)) {
            $districtQuery->where('id', $district_id);
        }
        $districts = $districtQuery->get();

        $blockQuery= Block::select('blocks.name as block_name', 'districts.name as district_name', 'blocks.id as block_id', 'districts.id as districts_id', 'm_s_code')
                    ->leftJoin('districts', 'blocks.district_id', '=', 'districts.id');
                  
                    
          if (!empty($district_id)) {
            $blockQuery->where('blocks.district_id', $district_id);
        }
        $blocks = $blockQuery->paginate($this->perpage);
        $pagination=(new Helper)->formatPagination($blocks);
        return view('admin.locations.block.index',compact('blocks','districts','pagination'));
    }

    // create block screen
    public function create_block(Request $request)
    {
       $user = Session::get('user');
        $company_id = $user->company_id;
        $state_id = $user->state_id;
        $district_id = $user->district_id;

        $districtQuery = District::where('state_id',$state_id);
        if (!empty($district_id)) {
            $districtQuery->where('id', $district_id);
        }
        $districts = $districtQuery->get();
        return view('admin.locations.block.create',compact('districts'));
        // $districts = District::get();
        // $blocks= Block::select('blocks.name as block_name', 'districts.name as district_name', 'blocks.id as block_id', 'districts.id as districts_id', 'm_s_code')
        //             ->leftJoin('districts', 'blocks.district_id', '=', 'districts.id')
        //             ->paginate($this->perpage);
        // $pagination=(new Helper)->formatPagination($blocks);
        // return view('admin.locations.block.index',compact('blocks','districts','pagination'));
    }

    // Store new block
    public function store_block(Request $request)
    {
       
        $this->validate($request, [
            'block_name' => 'required|max:255',
            'district' => 'required'       
        ]);

        try{
            $block = array();
            $block['name'] = $request->block_name;
            $block['m_s_code'] = $request->m_s_code;
            $block['district_id'] = $request->district;
            $block = Block::create($block);

            return redirect()
                ->route('admin.location.block')
                ->with('flash_success', trans('admin.location_msgs.district_saved'));
        } 
        catch (Exception $e) {  
            return back()->with('flash_error', trans('admin.location_msgs.district_not_found'));
        }
    }

    // Block edit screen
    public function edit_block($id)
    {
        try {
            $user = Session::get('user');
            $company_id = $user->company_id;
            $state_id = $user->state_id;
            $district_id = $user->district_id;

            $districtQuery = District::where('state_id',$state_id);
            if (!empty($district_id)) {
                $districtQuery->where('id', $district_id);
            }
            $districts = $districtQuery->get();
            $block = Block::findOrFail($id);
            return view('admin.locations.block.edit',compact('block','districts'));
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }

    // Update Block
    public function update_block(Request $request, $id)
    {

        $this->validate($request, [
            'block_name' => 'required|max:255',
            'district' => 'required' 
        ]);

        try {

            $block = Block::findOrFail($id); 
            $block->name = $request->block_name;
            $block['m_s_code'] = $request->m_s_code;
            $block->district_id = $request->district;
            $block->save();

            return redirect()->route('admin.location.block')->with('flash_success', trans('admin.location_msgs.block_update'));    
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.location_msgs.block_not_found'));
        }
    }

    // Delete block
    public function destroy_block($id)
    {
        try {
            Block::find($id)->delete();
            return back()->with('message', trans('admin.location_msgs.block_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.location_msgs.block_not_found'));
        }
    }

}