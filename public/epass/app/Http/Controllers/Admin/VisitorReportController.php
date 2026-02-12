<?php

namespace App\Http\Controllers\Admin;
use App\Models\Employee;
use DateTimeImmutable;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Http\Controllers\BackendController;
use App\Models\VisitingDetails;
use App\Models\Visitor;
use Illuminate\Http\Request;
use App\Enums\Status;
use DB;

class VisitorReportController extends BackendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['siteTitle'] = 'Visitor Report';
        $this->data['employees'] = Employee::where('status', Status::ACTIVE)->get();
        $this->middleware(['permission:admin-visitor-report'])->only('index');
    }

    public function index1(Request $request)
    {  
        $this->data['showView']      = true;
        $this->data['employees'] = Employee::where('status', Status::ACTIVE)->get();
        $this->data['set_from_date'] = '';
        $this->data['set_to_date']   = '';
        $this->data['set_to_employee_id']   = '';
        $this->data['contents'] = Employee::where('status', Status::ACTIVE)->get();
        $this->data['u_contents']=DB::table('usedcontents')
        ->select('usedcontents.content', DB::raw('COUNT(visiting_details.used_content) as cnt'))
        ->leftJoin('visiting_details','visiting_details.used_content','=','usedcontents.content')
        ->groupBy('usedcontents.content')
        ->orderBy('cnt','desc')
        ->get();


        if ($_POST) {

            $request->validate([
                'from_date' => 'nullable|date',
                'to_date'   => 'nullable|date|after_or_equal:from_date',
            ]);
         
            $this->data['showView']      = true;
            $this->data['set_from_date'] = $request->from_date;
            $this->data['set_to_date']   = $request->to_date;
            $this->data['set_to_employee_id'] = $request->employee_id;

            $today = date('y-m-d');

            $dateBetween = [];
            if ($request->from_date != '' && $request->to_date != '') {
                $dateBetween['from_date'] = date('Y-m-d', strtotime($request->from_date)) . ' 00:00:00';
                $dateBetween['to_date']   = date('Y-m-d', strtotime($request->to_date)) . ' 23:59:59';
            }

            if ($request->from_date != '' && $request->to_date == '') {
                $dateBetween['from_date'] = date('Y-m-d', strtotime($request->from_date)) . ' 00:00:00';
                $dateBetween['to_date']   = date('Y-m-d', strtotime($today)) . ' 23:59:59';
            }
           
            if ($request->from_date=='' && $request->employee_id=='') {
                $this->data['u_contents']=DB::table('usedcontents')
                ->select('usedcontents.content', DB::raw('COUNT(visiting_details.used_content) as cnt'))
                ->leftJoin('visiting_details','visiting_details.used_content','=','usedcontents.content')
                ->groupBy('usedcontents.content')
                ->orderBy('cnt','desc')
                ->get();
    
              
              } else {

                if ($request->from_date=='' && $request->employee_id !='') {
                    $this->data['u_contents']=DB::table('usedcontents')
                    ->select('usedcontents.content', DB::raw('COUNT(visiting_details.used_content) as cnt'))
                    ->leftJoin('visiting_details','visiting_details.used_content','=','usedcontents.content')
                    ->where('visiting_details.employee_id','=',$request->employee_id)
                    ->groupBy('usedcontents.content')
                    ->orderBy('cnt','desc')
                    ->get();
        
                  
                  } else { 

                    if ($request->from_date !='' && $request->employee_id =='') {
                        $this->data['u_contents']=DB::table('usedcontents')
                        ->select('usedcontents.content', DB::raw('COUNT(visiting_details.used_content) as cnt'))
                        ->leftJoin('visiting_details','visiting_details.used_content','=','usedcontents.content')
                        ->whereBetween('visiting_details.created_at', [$dateBetween['from_date'], $dateBetween['to_date']])
                        ->groupBy('usedcontents.content')
                        ->orderBy('cnt','desc')
                        ->get();
                      } else {

                        if ($request->from_date !='' && $request->employee_id !='') {
                            $this->data['u_contents']=DB::table('usedcontents')
                            ->select('usedcontents.content', DB::raw('COUNT(visiting_details.used_content) as cnt'))
                            ->leftJoin('visiting_details','visiting_details.used_content','=','usedcontents.content')
                            ->whereBetween('visiting_details.created_at', [$dateBetween['from_date'], $dateBetween['to_date']])
                            ->where('visiting_details.employee_id','=',$request->employee_id)
                            ->groupBy('usedcontents.content')
                            ->orderBy('cnt','desc')
                            ->get();
                
                          
                          } else {

                          }

                      }
            
                  }

                

              }


            

        }

        return view('admin.report.content.index', $this->data);
    }

    public function index(Request $request)
    {
       
        /*index*/
        $date=date('y-m-d');
        $date7 = new DateTimeImmutable($date); 
        $newDate30=$date7->sub(new DateInterval('P30D'));
        $last_month=$newDate30->format('Y-m-d H:i:s');

        $this->data['employees'] = Employee::where('status', Status::ACTIVE)->get();
        
        $this->data['showView1']     = true;
        $this->data['showView']      = true;
        $this->data['showView3']     = false;
        $this->data['set_from_date'] = '';
        $this->data['set_to_date']   = '';
        $this->data['set_to_employee_id']   = '';
        
        $this->data['center_name'] = '';
        $type_data1="Last 30 days Data";
        $this->data['type_data']   = $type_data1;
   
        $d=Visitor::orderBy('id', 'DESC')->get();

        $d1 = $d->unique(['phone']);
        $u1 = $d->diff($d1);
        $this->data['visitors'] = VisitingDetails::orderBy('id', 'DESC')->where('created_at','>=',$last_month)->get();

        $this->data['totalVisitor'] = 0;
            $this->data['checkinVisitor'] = 0;
            $this->data['checkoutVisitor'] = 0;
            if(!blank($this->data['visitors'])){
                $checkin = 0;
                $checkout = 0;
                foreach ($this->data['visitors'] as $visitor){
                        if($visitor->checkin_at){
                            $checkin +=1;
                        }
                        if($visitor->checkout_at){
                            $checkout +=1;
                        }
                }
                $this->data['totalVisitor'] = count($this->data['visitors']);
                $this->data['checkinVisitor'] = $checkin;
                $this->data['checkoutVisitor'] = $checkout;
            }


         /*End index*/

        if ($_POST) {
            $request->validate([
                'from_date' => 'nullable|date',
                'to_date'   => 'nullable|date|after_or_equal:from_date',
            ]);
         
            $this->data['showView1']      =false;
            $this->data['showView']      = true;
            $this->data['set_from_date'] = $request->from_date;
            $this->data['set_to_date']   = $request->to_date;
            $this->data['set_to_employee_id'] = $request->employee_id;

           

            $today = date('y-m-d');

            $dateBetween = [];
            if ($request->from_date != '' && $request->to_date != '') {
                $dateBetween['from_date'] = date('Y-m-d', strtotime($request->from_date)) . ' 00:00:00';
                $dateBetween['to_date']   = date('Y-m-d', strtotime($request->to_date)) . ' 23:59:59';
            }

            if ($request->from_date != '' && $request->to_date == '') {
                $dateBetween['from_date'] = date('Y-m-d', strtotime($request->from_date)) . ' 00:00:00';
                $dateBetween['to_date']   = date('Y-m-d', strtotime($today)) . ' 23:59:59';
            }
          
            if ($request->from_date=='' && $request->employee_id=='' && $request->repeated_id=='') {
              $this->data['visitors'] = VisitingDetails::orderBy('id', 'DESC')->get(); 
  
            
            } else {
                if ($request->from_date =='' && $request->employee_id !=''  && $request->repeated_id=='') {
                    $this->data['visitors'] = VisitingDetails::where('employee_id','=',$request->employee_id)->orderBy('id', 'DESC')->get();
                } else {
                    if ($request->from_date !='' && $request->employee_id ==''  && $request->repeated_id=='') {
                        $this->data['visitors'] = VisitingDetails::whereBetween('created_at', [$dateBetween['from_date'], $dateBetween['to_date']])->orderBy('id', 'DESC')->get();
                    } else {
                       
                        if ($request->from_date !='' && $request->employee_id !=''  && $request->repeated_id=='') {
                            $this->data['visitors'] = VisitingDetails::whereBetween('created_at', [$dateBetween['from_date'], $dateBetween['to_date']])->where('employee_id','=',$request->employee_id)->orderBy('id', 'DESC')->get();
                        } else{
                            if ($request->from_date =='' && $request->employee_id ==''  && $request->repeated_id !='') {
                                //$this->data['visitors'] = VisitingDetails::wherein('visitor_id',(DB::table('visitors')->select('id')->wherein('phone',(DB::table('visitors')->select('phone')->groupBy('phone')->havingRaw('count(phone) > ?', [1])))))->get();
                                $visitorIds = DB::table('visitors')->select('phone')->groupBy('phone')->havingRaw('COUNT(phone) > 1')->pluck('phone')->toArray(); // Convert collection to array
                                $visitorIds1 = DB::table('visitors')->select(DB::raw('MAX(id) as id'))->whereIn('phone', $visitorIds)->groupBy('phone')->get()->pluck('id');
                                $this->data['visitors'] = VisitingDetails::whereIn('visitor_id', $visitorIds1)->get();
                           
                            } else{
                                if ($request->from_date !='' && $request->employee_id ==''  && $request->repeated_id !='') {
                                   // $this->data['visitors'] = VisitingDetails::whereBetween('created_at', [$dateBetween['from_date'], $dateBetween['to_date']])->wherein('visitor_id',(DB::table('visitors')->select('id')->wherein('phone',(DB::table('visitors')->select('phone')->whereBetween('created_at', [$dateBetween['from_date'], $dateBetween['to_date']])->groupBy('phone')->havingRaw('count(phone) > ?', [1])))))->get();
                                    $visitorIds = DB::table('visitors')->select('phone')->whereBetween('created_at', [$dateBetween['from_date'], $dateBetween['to_date']])->groupBy('phone')->havingRaw('COUNT(phone) > 1')->pluck('phone')->toArray(); // Convert collection to array
                                    $visitorIds1 = DB::table('visitors')->select(DB::raw('MAX(id) as id'))->whereIn('phone', $visitorIds)->groupBy('phone')->get()->pluck('id');
                                    $this->data['visitors'] = VisitingDetails::whereIn('visitor_id', $visitorIds1)->get();

                                }else{
                                    if ($request->from_date !='' && $request->employee_id !=''  && $request->repeated_id !='') {
                                        //$this->data['visitors'] = VisitingDetails::whereBetween('created_at', [$dateBetween['from_date'], $dateBetween['to_date']])->where('employee_id','=',$request->employee_id)->wherein('visitor_id',(DB::table('visitors')->select('id')->whereBetween('created_at', [$dateBetween['from_date'], $dateBetween['to_date']])->wherein('phone',(DB::table('visitors')->select('phone')->groupBy('phone')->havingRaw('count(phone) > ?', [1])))))->get();
                                        $visitorIds = DB::table('visitors')->select('phone')->whereBetween('created_at', [$dateBetween['from_date'], $dateBetween['to_date']])->groupBy('phone')->havingRaw('COUNT(phone) > 1')->pluck('phone')->toArray(); // Convert collection to array
                                        $visitorIds1 = DB::table('visitors')->select(DB::raw('MAX(id) as id'))->whereIn('phone', $visitorIds)->groupBy('phone')->get()->pluck('id');
                                        $this->data['visitors'] = VisitingDetails::whereIn('visitor_id', $visitorIds1)->where('employee_id','=',$request->employee_id)->get();
                                    }else{
                                        if ($request->from_date =='' && $request->employee_id !=''  && $request->repeated_id !='') {
                                           // $this->data['visitors'] = VisitingDetails::where('employee_id','=',$request->employee_id)->wherein('visitor_id',(DB::table('visitors')->select('id')->wherein('phone',(DB::table('visitors')->select('phone')->groupBy('phone')->havingRaw('count(phone) > ?', [1])))))->get();
                                           $visitorIds = DB::table('visitors')->select('phone')->groupBy('phone')->havingRaw('COUNT(phone) > 1')->pluck('phone')->toArray(); // Convert collection to array
                                           $visitorIds1 = DB::table('visitors')->select(DB::raw('MAX(id) as id'))->whereIn('phone', $visitorIds)->groupBy('phone')->get()->pluck('id');
                                           $this->data['visitors'] = VisitingDetails::where('employee_id','=',$request->employee_id)->whereIn('visitor_id', $visitorIds1)->get();
                                        }else{

                                        }

                                    }

                                }

                            }

                        }
                    }
                    
                }
                
            }


            $this->data['totalVisitor'] = 0;
            $this->data['checkinVisitor'] = 0;
            $this->data['checkoutVisitor'] = 0;
            if(!blank($this->data['visitors'])){
                $checkin = 0;
                $checkout = 0;
                foreach ($this->data['visitors'] as $visitor){
                        if($visitor->checkin_at){
                            $checkin +=1;
                        }
                        if($visitor->checkout_at){
                            $checkout +=1;
                        }
                }
                $this->data['totalVisitor'] = count($this->data['visitors']);
                $this->data['checkinVisitor'] = $checkin;
                $this->data['checkoutVisitor'] = $checkout;
            }

        }

        /*End Post*/
        return view('admin.report.visitor.index', $this->data);
    }

    public function index2(Request $request)
    {

        $date=date('y-m-d');
        $date7 = new DateTimeImmutable($date); 
        $newDate7 = $date7->sub(new DateInterval('P7D'));
        $newDate30=$date7->sub(new DateInterval('P30D'));

        if($request->date == "to_day"){
            $upto_date=$date7->format('Y-m-d H:i:s');
            $type_data1="Todays Data";
        }
        if($request->date == "last_7days"){
            $upto_date=$newDate7->format('Y-m-d H:i:s');
            $type_data1="Last 7 days Data";
        }
        if($request->date == "last_30days"){
            $upto_date=$newDate30->format('Y-m-d H:i:s');   
            $type_data1="Last 30 days Data";
        }
        if($request->date == "repeated_visitors"){
            $upto_date=$newDate30->format('Y-m-d H:i:s');   
            $type_data1="Last 30 days Data";
        }

        $this->data['center_name'] = Employee::select('first_name','last_name')->where('id','=',$request->id)->get();
    
          $this->data['showView1']     = true;
          $this->data['showView']      = true;
          $this->data['set_from_date'] = '';
          $this->data['set_to_date']   = '';
          $this->data['set_to_employee_id']   = '';
           $this->data['type_data']   = $type_data1;

           if($request->date == "repeated_visitors"){
            $this->data['showView3']     = false;
            //$this->data['visitors'] = VisitingDetails::where('created_at','>=',$upto_date)->wherein('visitor_id',(DB::table('visitors')->select('id')->wherein('phone',(DB::table('visitors')->select('phone')->where('created_at','>=',$upto_date)->groupBy('phone')->havingRaw('count(phone) > ?', [1])))))->get();
            $visitorIds = DB::table('visitors')->select('phone')->where('created_at','>=',$upto_date)->groupBy('phone')->havingRaw('COUNT(phone) > 1')->pluck('phone')->toArray(); // Convert collection to array
            $visitorIds1 = DB::table('visitors')->select(DB::raw('MAX(id) as id'))->whereIn('phone', $visitorIds)->groupBy('phone')->get()->pluck('id');
            $this->data['visitors'] = VisitingDetails::whereIn('visitor_id', $visitorIds1)->get();

           } else {
            $this->data['showView3']     = true;
            $this->data['visitors'] = VisitingDetails::orderBy('id', 'DESC')->where('created_at','>=',$upto_date)->where('employee_id','=',$request->id)->get();
           }

          

          $this->data['totalVisitor'] = 0;
            $this->data['checkinVisitor'] = 0;
            $this->data['checkoutVisitor'] = 0;
            if(!blank($this->data['visitors'])){
                $checkin = 0;
                $checkout = 0;
                foreach ($this->data['visitors'] as $visitor){
                        if($visitor->checkin_at){
                            $checkin +=1;
                        }
                        if($visitor->checkout_at){
                            $checkout +=1;
                        }
                }
                $this->data['totalVisitor'] = count($this->data['visitors']);
                $this->data['checkinVisitor'] = $checkin;
                $this->data['checkoutVisitor'] = $checkout;
            }


          
        
        return view('admin.report.visitor.index', $this->data);
    }
}
