<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Support\Facades\DB;
use DateTimeImmutable;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Http\Controllers\BackendController;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\PreRegister;
use App\Models\VisitingDetails;
use App\Models\Visitor;


class DashboardController extends BackendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['sitetitle'] = 'Dashboard';
        $this->middleware(['permission:dashboard'])->only('index');
    }
    public function index()
    {
        if (auth()->user()->getrole->name == 'Employee') {
            $visitors       = VisitingDetails::where(['employee_id' => auth()->user()->employee->id])->orderBy('id', 'desc')->get();
            $preregister    = PreRegister::where(['employee_id' => auth()->user()->employee->id])->orderBy('id', 'desc')->get();
            $totalEmployees = 0;
            $active_visitors=VisitingDetails::where(['employee_id' => auth()->user()->employee->id])->whereNull('checkout_at')->whereNotNull('checkin_at')->get();
        } else {

            $date=date('y-m-d');
            $date7 = new DateTimeImmutable($date); 
            $todat=$date7->format('Y-m-d H:i:s');

        
            $newDate7 = $date7->sub(new DateInterval('P7D'));
            $last_7days=$newDate7->format('Y-m-d H:i:s');

            $newDate30=$date7->sub(new DateInterval('P30D'));

            $last_month=$newDate30->format('Y-m-d H:i:s');
            $visitors       = VisitingDetails::orderBy('id', 'desc')->where('created_at','>=',$last_month)->get();
            $preregister    = PreRegister::orderBy('id', 'desc')->get();
            $employees      = Employee::orderBy('id', 'desc')->get();
            $totalEmployees = count($employees);
            $dcm = VisitingDetails::where('employee_id', '=', '5')->where('created_at','>=',$last_month)->get();
            $silver = VisitingDetails::where('employee_id', '=', '4')->where('created_at','>=',$last_month)->get();
            $stadium  = VisitingDetails::where('employee_id', '=', '3')->where('created_at','>=',$last_month)->get();
            $anjaneya  = VisitingDetails::where('employee_id', '=', '2')->where('created_at','>=',$last_month)->get();
            $nijalingappa  = VisitingDetails::where('employee_id', '=', '1')->where('created_at','>=',$last_month)->get();
            //$repeated= VisitingDetails::where('created_at','>=',$last_month)->wherein('visitor_id',(DB::table('visitors')->select('id')->wherein('phone',(DB::table('visitors')->select('phone')->where('created_at','>=',$last_month)->groupBy('phone')->havingRaw('count(phone) > ?', [1])))))->get();
            $visitorIds = DB::table('visitors')->select('phone')->where('created_at', '>=', $last_month)->groupBy('phone')->havingRaw('COUNT(phone) > 1')->pluck('phone')->toArray(); // Convert collection to array
            $visitorIds1 = DB::table('visitors')->select(DB::raw('MAX(id) as id'))->whereIn('phone', $visitorIds)->groupBy('phone')->get()->pluck('id');
            $repeated = VisitingDetails::whereIn('visitor_id', $visitorIds1)->get();
           //dd($repeated);
            $dcmcount = count($dcm);
            $silvercount = count($silver);
            $stadiumcount  = count($stadium);
            $anjaneyacount  = count($anjaneya);
            $nijalingappacount  = count($nijalingappa);
            $repeatedvisitor=count($repeated);
            $dcm_jul=VisitingDetails::whereBetween('created_at',['2024-07-01 00:00:00' ,'2024-07-31 23:59:59'])->where('employee_id', '=', '5')->get();
            $sil_jul=VisitingDetails::whereBetween('created_at',['2024-07-01 00:00:00' ,'2024-07-31 23:59:59'])->where('employee_id', '=', '4')->get();
            $sta_jul=VisitingDetails::whereBetween('created_at',['2024-07-01 00:00:00' ,'2024-07-31 23:59:59'])->where('employee_id', '=', '3')->get();
            $anj_jul=VisitingDetails::whereBetween('created_at',['2024-07-01 00:00:00' ,'2024-07-31 23:59:59'])->where('employee_id', '=', '2')->get();
            $nij_jul=VisitingDetails::whereBetween('created_at',['2024-07-01 00:00:00' ,'2024-07-31 23:59:59'])->where('employee_id', '=', '1')->get();

            $dcm_aug=VisitingDetails::whereBetween('created_at',['2024-08-01 00:00:00' ,'2024-08-31 23:59:59'])->where('employee_id', '=', '5')->get();
            $sil_aug=VisitingDetails::whereBetween('created_at',['2024-08-01 00:00:00' ,'2024-08-31 23:59:59'])->where('employee_id', '=', '4')->get();
            $sta_aug=VisitingDetails::whereBetween('created_at',['2024-08-01 00:00:00' ,'2024-08-31 23:59:59'])->where('employee_id', '=', '3')->get();
            $anj_aug=VisitingDetails::whereBetween('created_at',['2024-08-01 00:00:00' ,'2024-08-31 23:59:59'])->where('employee_id', '=', '2')->get();
            $nij_aug=VisitingDetails::whereBetween('created_at',['2024-08-01 00:00:00' ,'2024-08-31 23:59:59'])->where('employee_id', '=', '1')->get();

            $dcm_sep=VisitingDetails::whereBetween('created_at',['2024-09-01 00:00:00' ,'2024-09-31 23:59:59'])->where('employee_id', '=', '5')->get();
            $sil_sep=VisitingDetails::whereBetween('created_at',['2024-09-01 00:00:00' ,'2024-09-31 23:59:59'])->where('employee_id', '=', '4')->get();
            $sta_sep=VisitingDetails::whereBetween('created_at',['2024-09-01 00:00:00' ,'2024-09-31 23:59:59'])->where('employee_id', '=', '3')->get();
            $anj_sep=VisitingDetails::whereBetween('created_at',['2024-09-01 00:00:00' ,'2024-09-31 23:59:59'])->where('employee_id', '=', '2')->get();
            $nij_sep=VisitingDetails::whereBetween('created_at',['2024-09-01 00:00:00' ,'2024-09-31 23:59:59'])->where('employee_id', '=', '1')->get();

            $dcm_oct=VisitingDetails::whereBetween('created_at',['2024-10-01 00:00:00' ,'2024-10-31 23:59:59'])->where('employee_id', '=', '5')->get();
            $sil_oct=VisitingDetails::whereBetween('created_at',['2024-10-01 00:00:00' ,'2024-10-31 23:59:59'])->where('employee_id', '=', '4')->get();
            $sta_oct=VisitingDetails::whereBetween('created_at',['2024-10-01 00:00:00' ,'2024-10-31 23:59:59'])->where('employee_id', '=', '3')->get();
            $anj_oct=VisitingDetails::whereBetween('created_at',['2024-10-01 00:00:00' ,'2024-10-31 23:59:59'])->where('employee_id', '=', '2')->get();
            $nij_oct=VisitingDetails::whereBetween('created_at',['2024-10-01 00:00:00' ,'2024-10-31 23:59:59'])->where('employee_id', '=', '1')->get(); 

            $dcm_nov=VisitingDetails::whereBetween('created_at',['2024-11-01 00:00:00' ,'2024-11-31 23:59:59'])->where('employee_id', '=', '5')->get();
            $sil_nov=VisitingDetails::whereBetween('created_at',['2024-11-01 00:00:00' ,'2024-11-31 23:59:59'])->where('employee_id', '=', '4')->get();
            $sta_nov=VisitingDetails::whereBetween('created_at',['2024-11-01 00:00:00' ,'2024-11-31 23:59:59'])->where('employee_id', '=', '3')->get();
            $anj_nov=VisitingDetails::whereBetween('created_at',['2024-11-01 00:00:00' ,'2024-11-31 23:59:59'])->where('employee_id', '=', '2')->get();
            $nij_nov=VisitingDetails::whereBetween('created_at',['2024-11-01 00:00:00' ,'2024-11-31 23:59:59'])->where('employee_id', '=', '1')->get();
            
            $dcm_dec=VisitingDetails::whereBetween('created_at',['2024-12-01 00:00:00' ,'2024-12-31 23:59:59'])->where('employee_id', '=', '5')->get();
            $sil_dec=VisitingDetails::whereBetween('created_at',['2024-12-01 00:00:00' ,'2024-12-31 23:59:59'])->where('employee_id', '=', '4')->get();
            $sta_dec=VisitingDetails::whereBetween('created_at',['2024-12-01 00:00:00' ,'2024-12-31 23:59:59'])->where('employee_id', '=', '3')->get();
            $anj_dec=VisitingDetails::whereBetween('created_at',['2024-12-01 00:00:00' ,'2024-12-31 23:59:59'])->where('employee_id', '=', '2')->get();
            $nij_dec=VisitingDetails::whereBetween('created_at',['2024-12-01 00:00:00' ,'2024-12-31 23:59:59'])->where('employee_id', '=', '1')->get();  

            $dcm_jan24=VisitingDetails::whereBetween('created_at',['2024-01-01 00:00:00' ,'2024-01-31 23:59:59'])->where('employee_id', '=', '5')->get();
            $sil_jan24=VisitingDetails::whereBetween('created_at',['2024-01-01 00:00:00' ,'2024-01-31 23:59:59'])->where('employee_id', '=', '4')->get();
            $sta_jan24=VisitingDetails::whereBetween('created_at',['2024-01-01 00:00:00' ,'2024-01-31 23:59:59'])->where('employee_id', '=', '3')->get();
            $anj_jan24=VisitingDetails::whereBetween('created_at',['2024-01-01 00:00:00' ,'2024-01-31 23:59:59'])->where('employee_id', '=', '2')->get();
            $nij_jan24=VisitingDetails::whereBetween('created_at',['2024-01-01 00:00:00' ,'2024-01-31 23:59:59'])->where('employee_id', '=', '1')->get(); 

            $dcm_feb24=VisitingDetails::whereBetween('created_at',['2024-02-01 00:00:00' ,'2024-02-31 23:59:59'])->where('employee_id', '=', '5')->get();
            $sil_feb24=VisitingDetails::whereBetween('created_at',['2024-02-01 00:00:00' ,'2024-02-31 23:59:59'])->where('employee_id', '=', '4')->get();
            $sta_feb24=VisitingDetails::whereBetween('created_at',['2024-02-01 00:00:00' ,'2024-02-31 23:59:59'])->where('employee_id', '=', '3')->get();
            $anj_feb24=VisitingDetails::whereBetween('created_at',['2024-02-01 00:00:00' ,'2024-02-31 23:59:59'])->where('employee_id', '=', '2')->get();
            $nij_feb24=VisitingDetails::whereBetween('created_at',['2024-02-01 00:00:00' ,'2024-02-31 23:59:59'])->where('employee_id', '=', '1')->get(); 

            $dcm_mar24=VisitingDetails::whereBetween('created_at',['2024-03-01 00:00:00' ,'2024-03-31 23:59:59'])->where('employee_id', '=', '5')->get();
            $sil_mar24=VisitingDetails::whereBetween('created_at',['2024-03-01 00:00:00' ,'2024-03-31 23:59:59'])->where('employee_id', '=', '4')->get();
            $sta_mar24=VisitingDetails::whereBetween('created_at',['2024-03-01 00:00:00' ,'2024-03-31 23:59:59'])->where('employee_id', '=', '3')->get();
            $anj_mar24=VisitingDetails::whereBetween('created_at',['2024-03-01 00:00:00' ,'2024-03-31 23:59:59'])->where('employee_id', '=', '2')->get();
            $nij_mar24=VisitingDetails::whereBetween('created_at',['2024-03-01 00:00:00' ,'2024-03-31 23:59:59'])->where('employee_id', '=', '1')->get(); 

            $dcm_apr24=VisitingDetails::whereBetween('created_at',['2024-04-01 00:00:00' ,'2024-04-31 23:59:59'])->where('employee_id', '=', '5')->get();
            $sil_apr24=VisitingDetails::whereBetween('created_at',['2024-04-01 00:00:00' ,'2024-04-31 23:59:59'])->where('employee_id', '=', '4')->get();
            $sta_apr24=VisitingDetails::whereBetween('created_at',['2024-04-01 00:00:00' ,'2024-04-31 23:59:59'])->where('employee_id', '=', '3')->get();
            $anj_apr24=VisitingDetails::whereBetween('created_at',['2024-04-01 00:00:00' ,'2024-04-31 23:59:59'])->where('employee_id', '=', '2')->get();
            $nij_apr24=VisitingDetails::whereBetween('created_at',['2024-04-01 00:00:00' ,'2024-04-31 23:59:59'])->where('employee_id', '=', '1')->get();

            $dcm_may24=VisitingDetails::whereBetween('created_at',['2024-05-01 00:00:00' ,'2024-05-31 23:59:59'])->where('employee_id', '=', '5')->get();
            $sil_may24=VisitingDetails::whereBetween('created_at',['2024-05-01 00:00:00' ,'2024-05-31 23:59:59'])->where('employee_id', '=', '4')->get();
            $sta_may24=VisitingDetails::whereBetween('created_at',['2024-05-01 00:00:00' ,'2024-05-31 23:59:59'])->where('employee_id', '=', '3')->get();
            $anj_may24=VisitingDetails::whereBetween('created_at',['2024-05-01 00:00:00' ,'2024-05-31 23:59:59'])->where('employee_id', '=', '2')->get();
            $nij_may24=VisitingDetails::whereBetween('created_at',['2024-05-01 00:00:00' ,'2024-05-31 23:59:59'])->where('employee_id', '=', '1')->get();

            $dcm_jun24=VisitingDetails::whereBetween('created_at',['2024-06-01 00:00:00' ,'2024-06-31 23:59:59'])->where('employee_id', '=', '5')->get();
            $sil_jun24=VisitingDetails::whereBetween('created_at',['2024-06-01 00:00:00' ,'2024-06-31 23:59:59'])->where('employee_id', '=', '4')->get();
            $sta_jun24=VisitingDetails::whereBetween('created_at',['2024-06-01 00:00:00' ,'2024-06-31 23:59:59'])->where('employee_id', '=', '3')->get();
            $anj_jun24=VisitingDetails::whereBetween('created_at',['2024-06-01 00:00:00' ,'2024-06-31 23:59:59'])->where('employee_id', '=', '2')->get();
            $nij_jun24=VisitingDetails::whereBetween('created_at',['2024-06-01 00:00:00' ,'2024-06-31 23:59:59'])->where('employee_id', '=', '1')->get();
            
            $dcm_jul1=count($dcm_jul);
            $sil_jul1=count($sil_jul);
            $sta_jul1=count($sta_jul);
            $anj_jul1=count($anj_jul);
            $nij_jul1=count($nij_jul);

            $dcm_aug1=count($dcm_aug);
            $sil_aug1=count($sil_aug);
            $sta_aug1=count($sta_aug);
            $anj_aug1=count($anj_aug);
            $nij_aug1=count($nij_aug);

            $dcm_sep1=count($dcm_sep);
            $sil_sep1=count($sil_sep);
            $sta_sep1=count($sta_sep);
            $anj_sep1=count($anj_sep);
            $nij_sep1=count($nij_sep);

            $dcm_oct1=count($dcm_oct);
            $sil_oct1=count($sil_oct);
            $sta_oct1=count($sta_oct);
            $anj_oct1=count($anj_oct);
            $nij_oct1=count($nij_oct);

            $dcm_nov1=count($dcm_nov);
            $sil_nov1=count($sil_nov);
            $sta_nov1=count($sta_nov);
            $anj_nov1=count($anj_nov);
            $nij_nov1=count($nij_nov);

            $dcm_dec1=count($dcm_dec);
            $sil_dec1=count($sil_dec);
            $sta_dec1=count($sta_dec);
            $anj_dec1=count($anj_dec);
            $nij_dec1=count($nij_dec);

            $dcm_jan24_1=count($dcm_jan24);
            $sil_jan24_1=count($sil_jan24);
            $sta_jan24_1=count($sta_jan24);
            $anj_jan24_1=count($anj_jan24);
            $nij_jan24_1=count($nij_jan24);

            $dcm_feb24_1=count($dcm_feb24);
            $sil_feb24_1=count($sil_feb24);
            $sta_feb24_1=count($sta_feb24);
            $anj_feb24_1=count($anj_feb24);
            $nij_feb24_1=count($nij_feb24);

            $dcm_mar24_1=count($dcm_mar24);
            $sil_mar24_1=count($sil_mar24);
            $sta_mar24_1=count($sta_mar24);
            $anj_mar24_1=count($anj_mar24);
            $nij_mar24_1=count($nij_mar24);

            $dcm_apr24_1=count($dcm_apr24);
            $sil_apr24_1=count($sil_apr24);
            $sta_apr24_1=count($sta_apr24);
            $anj_apr24_1=count($anj_apr24);
            $nij_apr24_1=count($nij_apr24);

            $dcm_may24_1=count($dcm_may24);
            $sil_may24_1=count($sil_may24);
            $sta_may24_1=count($sta_may24);
            $anj_may24_1=count($anj_may24);
            $nij_may24_1=count($nij_may24);

            $dcm_jun24_1=count($dcm_jun24);
            $sil_jun24_1=count($sil_jun24);
            $sta_jun24_1=count($sta_jun24);
            $anj_jun24_1=count($anj_jun24);
            $nij_jun24_1=count($nij_jun24);


           
    
            $dcm_today=VisitingDetails::where('created_at','>=',$todat)->where('employee_id', '=', '5')->get();
            $sil_today=VisitingDetails::where('created_at','>=',$todat)->where('employee_id', '=', '4')->get();
            $sta_today=VisitingDetails::where('created_at','>=',$todat)->where('employee_id', '=', '3')->get();
            $anj_today=VisitingDetails::where('created_at','>=',$todat)->where('employee_id', '=', '2')->get();
            $nij_today=VisitingDetails::where('created_at','>=',$todat)->where('employee_id', '=', '1')->get(); 

            $dcm_7days=VisitingDetails::where('created_at','>=',$last_7days)->where('employee_id', '=', '5')->get();
            $sil_7days=VisitingDetails::where('created_at','>=',$last_7days)->where('employee_id', '=', '4')->get();
            $sta_7days=VisitingDetails::where('created_at','>=',$last_7days)->where('employee_id', '=', '3')->get();
            $anj_7days=VisitingDetails::where('created_at','>=',$last_7days)->where('employee_id', '=', '2')->get();
            $nij_7days=VisitingDetails::where('created_at','>=',$last_7days)->where('employee_id', '=', '1')->get();
            
            $dcm_month=VisitingDetails::where('created_at','>=',$last_month)->where('employee_id', '=', '5')->get();
            $sil_month=VisitingDetails::where('created_at','>=',$last_month)->where('employee_id', '=', '4')->get();
            $sta_month=VisitingDetails::where('created_at','>=',$last_month)->where('employee_id', '=', '3')->get();
            $anj_month=VisitingDetails::where('created_at','>=',$last_month)->where('employee_id', '=', '2')->get();
            $nij_month=VisitingDetails::where('created_at','>=',$last_month)->where('employee_id', '=', '1')->get(); 
            

            $dcm_today1=count($dcm_today);
            $sil_today1=count($sil_today);
            $sta_today1=count($sta_today);
            $anj_today1=count($anj_today);
            $nij_today1=count($nij_today);

            $dcm_7days1=count($dcm_7days);
            $sil_7days1=count($sil_7days);
            $sta_7days1=count($sta_7days);
            $anj_7days1=count($anj_7days);
            $nij_7days1=count($nij_7days);

            $dcm_month1=count($dcm_month);
            $sil_month1=count($sil_month);
            $sta_month1=count($sta_month);
            $anj_month1=count($anj_month);
            $nij_month1=count($nij_month);

            $active_visitors=VisitingDetails::whereNull('checkout_at')->whereNotNull('checkin_at')->get();
            
            $ave_age1=Visitor::where('created_at','>=',$last_month)->avg('age');
            $ave_age2=round($ave_age1);
            $ave_age3 = sprintf("%02d", $ave_age2);
            $avg_from_age=$ave_age3-6;
            $avg_to_age=$ave_age3+4;

            
            $this->data['dcmcount']  = $dcmcount;
            $this->data['silvercount']  = $silvercount;
            $this->data['stadiumcount']  = $stadiumcount;
            $this->data['anjaneyacount']  = $anjaneyacount;
            $this->data['nijalingappacount']  = $nijalingappacount;
           

            $this->data['dcm_jul1']  = $dcm_jul1;
            $this->data['sil_jul1']  = $sil_jul1;
            $this->data['sta_jul1']  = $sta_jul1;
            $this->data['anj_jul1']  = $anj_jul1;
            $this->data['nij_jul1']  = $nij_jul1;

            $this->data['dcm_aug1']  = $dcm_aug1;
            $this->data['sil_aug1']  = $sil_aug1;
            $this->data['sta_aug1']  = $sta_aug1;
            $this->data['anj_aug1']  = $anj_aug1;
            $this->data['nij_aug1']  = $nij_aug1;

            $this->data['dcm_sep1']  = $dcm_sep1;
            $this->data['sil_sep1']  = $sil_sep1;
            $this->data['sta_sep1']  = $sta_sep1;
            $this->data['anj_sep1']  = $anj_sep1;
            $this->data['nij_sep1']  = $nij_sep1;

            $this->data['dcm_oct1']  = $dcm_oct1;
            $this->data['sil_oct1']  = $sil_oct1;
            $this->data['sta_oct1']  = $sta_oct1;
            $this->data['anj_oct1']  = $anj_oct1;
            $this->data['nij_oct1']  = $nij_oct1;

            $this->data['dcm_nov1']  = $dcm_nov1;
            $this->data['sil_nov1']  = $sil_nov1;
            $this->data['sta_nov1']  = $sta_nov1;
            $this->data['anj_nov1']  = $anj_nov1;
            $this->data['nij_nov1']  = $nij_nov1;

            $this->data['dcm_dec1']  = $dcm_dec1;
            $this->data['sil_dec1']  = $sil_dec1;
            $this->data['sta_dec1']  = $sta_dec1;
            $this->data['anj_dec1']  = $anj_dec1;
            $this->data['nij_dec1']  = $nij_dec1;

            $this->data['dcm_jan24_1']  = $dcm_jan24_1;
            $this->data['sil_jan24_1']  = $sil_jan24_1;
            $this->data['sta_jan24_1']  = $sta_jan24_1;
            $this->data['anj_jan24_1']  = $anj_jan24_1;
            $this->data['nij_jan24_1']  = $nij_jan24_1;

            $this->data['dcm_feb24_1']  = $dcm_feb24_1;
            $this->data['sil_feb24_1']  = $sil_feb24_1;
            $this->data['sta_feb24_1']  = $sta_feb24_1;
            $this->data['anj_feb24_1']  = $anj_feb24_1;
            $this->data['nij_feb24_1']  = $nij_feb24_1;

            $this->data['dcm_mar24_1']  = $dcm_mar24_1;
            $this->data['sil_mar24_1']  = $sil_mar24_1;
            $this->data['sta_mar24_1']  = $sta_mar24_1;
            $this->data['anj_mar24_1']  = $anj_mar24_1;
            $this->data['nij_mar24_1']  = $nij_mar24_1;

            $this->data['dcm_apr24_1']  = $dcm_apr24_1;
            $this->data['sil_apr24_1']  = $sil_apr24_1;
            $this->data['sta_apr24_1']  = $sta_apr24_1;
            $this->data['anj_apr24_1']  = $anj_apr24_1;
            $this->data['nij_apr24_1']  = $nij_apr24_1;

            $this->data['dcm_may24_1']  = $dcm_may24_1;
            $this->data['sil_may24_1']  = $sil_may24_1;
            $this->data['sta_may24_1']  = $sta_may24_1;
            $this->data['anj_may24_1']  = $anj_may24_1;
            $this->data['nij_may24_1']  = $nij_may24_1;

            $this->data['dcm_jun24_1']  = $dcm_jun24_1;
            $this->data['sil_jun24_1']  = $sil_jun24_1;
            $this->data['sta_jun24_1']  = $sta_jun24_1;
            $this->data['anj_jun24_1']  = $anj_jun24_1;
            $this->data['nij_jun24_1']  = $nij_jun24_1;

            $this->data['dcm_today1']  = $dcm_today1;
            $this->data['sil_today1']  = $sil_today1;
            $this->data['sta_today1']  = $sta_today1;
            $this->data['anj_today1']  = $anj_today1;
            $this->data['nij_today1']  = $nij_today1;

            $this->data['dcm_7days1']  = $dcm_7days1;
            $this->data['sil_7days1']  = $sil_7days1;
            $this->data['sta_7days1']  = $sta_7days1;
            $this->data['anj_7days1']  = $anj_7days1;
            $this->data['nij_7days1']  = $nij_7days1;

            $this->data['dcm_month1']  = $dcm_month1;
            $this->data['sil_month1']  = $sil_month1;
            $this->data['sta_month1']  = $sta_month1;
            $this->data['anj_month1']  = $anj_month1;
            $this->data['nij_month1']  = $nij_month1;

            $this->data['avg_from_age']  = $avg_from_age;
            $this->data['avg_to_age']  = $avg_to_age;


            /**Line Graph */
           $t9='09:00:00';$t10='10:00:00';$t11='11:00:00';$t12='12:00:00';$t13='13:00:00';$t14='14:00:00';
           $t9t='09:59:59';$t10t='10:59:59';$t11t='11:59:59';$t12t='12:59:59';$t13t='13:59:59';$t14t='14:59:59';
           $t15='15:00:00';$t16='16:00:00';$t17='17:00:00';$t18='18:00:00';$t19='19:00:00';$t20='20:00:00';
           $t15t='15:59:59';$t16t='16:59:59';$t17t='17:59:59';$t18t='18:59:59';$t19t='19:59:59';$t20t='20:59:59';
           $t21='21:00:00';$t22='22:00:00';$t23='23:00:00';$t1='1:00:00';$t2='2:00:00';$t3='3:00:00';
           $t21t='21:59:59';$t22t='22:59:59';$t23t='23:59:59';$t1t='1:59:59';$t2t='2:59:59';$t3t='3:59:59';
           $t4='4:00:00';$t5='5:00:00';$t6='6:00:00';$t7='7:00:00';$t8='8:00:00';$t24='00:00:00';
           $t4t='4:59:59';$t5t='5:59:59';$t6t='6:59:59';$t7t='7:59:59';$t8t='8:59:59';$t24t='00:59:59';
           $t9m='09:30:00';$t10m='10:29:59';$t11m='11:29:59';$t12m='12:29:59';$t13m='13:29:59';$t14m='14:29:59';
           $t9tm='09:59:59';$t10tm='10:30:00';$t11tm='11:30:00';$t12tm='12:30:00';$t13tm='13:30:00';$t14tm='14:30:00';
           $t15m='15:29:59';$t16m='16:29:59';$t17m='17:29:59';$t18m='18:29:59';$t19m='19:29:59';
           $t15tm='15:30:00';$t16tm='16:30:00';$t17tm='17:30:00';$t18tm='18:30:00';$t19tm='19:30:00';
           


           $tm1= VisitingDetails::whereBetween('graph_time', [$t1,$t1t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm2= VisitingDetails::whereBetween('graph_time', [$t2,$t2t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm3= VisitingDetails::whereBetween('graph_time', [$t3,$t3t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm4= VisitingDetails::whereBetween('graph_time', [$t4,$t4t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm5= VisitingDetails::whereBetween('graph_time', [$t5,$t5t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm6= VisitingDetails::whereBetween('graph_time', [$t6,$t6t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm7= VisitingDetails::whereBetween('graph_time', [$t7,$t7t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm8= VisitingDetails::whereBetween('graph_time', [$t8,$t8t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm9= VisitingDetails::whereBetween('graph_time', [$t9,$t9t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm10= VisitingDetails::whereBetween('graph_time', [$t10,$t10m])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm11= VisitingDetails::whereBetween('graph_time', [$t11,$t11m])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm12= VisitingDetails::whereBetween('graph_time', [$t12,$t12m])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm13= VisitingDetails::whereBetween('graph_time', [$t13,$t13m])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm14= VisitingDetails::whereBetween('graph_time', [$t14,$t14m])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm15= VisitingDetails::whereBetween('graph_time', [$t15,$t15m])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm16= VisitingDetails::whereBetween('graph_time', [$t16,$t16m])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm17= VisitingDetails::whereBetween('graph_time', [$t17,$t17m])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm18= VisitingDetails::whereBetween('graph_time', [$t18,$t18m])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm19= VisitingDetails::whereBetween('graph_time', [$t19,$t19m])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm20= VisitingDetails::whereBetween('graph_time', [$t20,$t20t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm21= VisitingDetails::whereBetween('graph_time', [$t21,$t21t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm22= VisitingDetails::whereBetween('graph_time', [$t22,$t22t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm23= VisitingDetails::whereBetween('graph_time', [$t23,$t23t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm24= VisitingDetails::whereBetween('graph_time', [$t24,$t24t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();

           $tm10m= VisitingDetails::whereBetween('graph_time', [$t10,$t10t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm11m= VisitingDetails::whereBetween('graph_time', [$t11tm,$t11t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm12m= VisitingDetails::whereBetween('graph_time', [$t12tm,$t12t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm13m= VisitingDetails::whereBetween('graph_time', [$t13tm,$t13t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm14m= VisitingDetails::whereBetween('graph_time', [$t14tm,$t14t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm15m= VisitingDetails::whereBetween('graph_time', [$t15tm,$t15t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm16m= VisitingDetails::whereBetween('graph_time', [$t16tm,$t16t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm17m= VisitingDetails::whereBetween('graph_time', [$t17tm,$t17t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm18m= VisitingDetails::whereBetween('graph_time', [$t18tm,$t18t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();
           $tm19m= VisitingDetails::whereBetween('graph_time', [$t19tm,$t19t])->where('employee_id', '=', '5')->where('created_at','>=',$last_month)->count();

           $si1= VisitingDetails::whereBetween('graph_time', [$t1,$t1t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si2= VisitingDetails::whereBetween('graph_time', [$t2,$t2t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si3= VisitingDetails::whereBetween('graph_time', [$t3,$t3t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si4= VisitingDetails::whereBetween('graph_time', [$t4,$t4t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si5= VisitingDetails::whereBetween('graph_time', [$t5,$t5t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si6= VisitingDetails::whereBetween('graph_time', [$t6,$t6t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si7= VisitingDetails::whereBetween('graph_time', [$t7,$t7t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si8= VisitingDetails::whereBetween('graph_time', [$t8,$t8t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si9= VisitingDetails::whereBetween('graph_time', [$t9,$t9t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si10= VisitingDetails::whereBetween('graph_time', [$t10,$t10m])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si11= VisitingDetails::whereBetween('graph_time', [$t11,$t11m])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si12= VisitingDetails::whereBetween('graph_time', [$t12,$t12m])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si13= VisitingDetails::whereBetween('graph_time', [$t13,$t13m])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si14= VisitingDetails::whereBetween('graph_time', [$t14,$t14m])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si15= VisitingDetails::whereBetween('graph_time', [$t15,$t15m])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si16= VisitingDetails::whereBetween('graph_time', [$t16,$t16m])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si17= VisitingDetails::whereBetween('graph_time', [$t17,$t17m])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si18= VisitingDetails::whereBetween('graph_time', [$t18,$t18m])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si19= VisitingDetails::whereBetween('graph_time', [$t19,$t19m])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si20= VisitingDetails::whereBetween('graph_time', [$t20,$t20t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si21= VisitingDetails::whereBetween('graph_time', [$t21,$t21t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si22= VisitingDetails::whereBetween('graph_time', [$t22,$t22t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si23= VisitingDetails::whereBetween('graph_time', [$t23,$t23t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si24= VisitingDetails::whereBetween('graph_time', [$t24,$t24t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();

           $si10m= VisitingDetails::whereBetween('graph_time', [$t10,$t10t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si11m= VisitingDetails::whereBetween('graph_time', [$t11tm,$t11t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si12m= VisitingDetails::whereBetween('graph_time', [$t12tm,$t12t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si13m= VisitingDetails::whereBetween('graph_time', [$t13tm,$t13t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si14m= VisitingDetails::whereBetween('graph_time', [$t14tm,$t14t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si15m= VisitingDetails::whereBetween('graph_time', [$t15tm,$t15t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si16m= VisitingDetails::whereBetween('graph_time', [$t16tm,$t16t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si17m= VisitingDetails::whereBetween('graph_time', [$t17tm,$t17t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si18m= VisitingDetails::whereBetween('graph_time', [$t18tm,$t18t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           $si19m= VisitingDetails::whereBetween('graph_time', [$t19tm,$t19t])->where('employee_id', '=', '4')->where('created_at','>=',$last_month)->count();
           

           $st1= VisitingDetails::whereBetween('graph_time', [$t1,$t1t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st2= VisitingDetails::whereBetween('graph_time', [$t2,$t2t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st3= VisitingDetails::whereBetween('graph_time', [$t3,$t3t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st4= VisitingDetails::whereBetween('graph_time', [$t4,$t4t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st5= VisitingDetails::whereBetween('graph_time', [$t5,$t5t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st6= VisitingDetails::whereBetween('graph_time', [$t6,$t6t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st7= VisitingDetails::whereBetween('graph_time', [$t7,$t7t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st8= VisitingDetails::whereBetween('graph_time', [$t8,$t8t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st9= VisitingDetails::whereBetween('graph_time', [$t9,$t9t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st10= VisitingDetails::whereBetween('graph_time', [$t10,$t10m])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st11= VisitingDetails::whereBetween('graph_time', [$t11,$t11m])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st12= VisitingDetails::whereBetween('graph_time', [$t12,$t12m])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st13= VisitingDetails::whereBetween('graph_time', [$t13,$t13m])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st14= VisitingDetails::whereBetween('graph_time', [$t14,$t14m])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st15= VisitingDetails::whereBetween('graph_time', [$t15,$t15m])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st16= VisitingDetails::whereBetween('graph_time', [$t16,$t16m])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st17= VisitingDetails::whereBetween('graph_time', [$t17,$t17m])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st18= VisitingDetails::whereBetween('graph_time', [$t18,$t18m])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st19= VisitingDetails::whereBetween('graph_time', [$t19,$t19m])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st20= VisitingDetails::whereBetween('graph_time', [$t20,$t20t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st21= VisitingDetails::whereBetween('graph_time', [$t21,$t21t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st22= VisitingDetails::whereBetween('graph_time', [$t22,$t22t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st23= VisitingDetails::whereBetween('graph_time', [$t23,$t23t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st24= VisitingDetails::whereBetween('graph_time', [$t24,$t24t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();

           $st10m= VisitingDetails::whereBetween('graph_time', [$t10,$t10t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st11m= VisitingDetails::whereBetween('graph_time', [$t11tm,$t11t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st12m= VisitingDetails::whereBetween('graph_time', [$t12tm,$t12t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st13m= VisitingDetails::whereBetween('graph_time', [$t13tm,$t13t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st14m= VisitingDetails::whereBetween('graph_time', [$t14tm,$t14t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st15m= VisitingDetails::whereBetween('graph_time', [$t15tm,$t15t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st16m= VisitingDetails::whereBetween('graph_time', [$t16tm,$t16t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st17m= VisitingDetails::whereBetween('graph_time', [$t17tm,$t17t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st18m= VisitingDetails::whereBetween('graph_time', [$t18tm,$t18t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           $st19m= VisitingDetails::whereBetween('graph_time', [$t19tm,$t19t])->where('employee_id', '=', '3')->where('created_at','>=',$last_month)->count();
           

           $an1= VisitingDetails::whereBetween('graph_time', [$t1,$t1t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an2= VisitingDetails::whereBetween('graph_time', [$t2,$t2t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an3= VisitingDetails::whereBetween('graph_time', [$t3,$t3t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an4= VisitingDetails::whereBetween('graph_time', [$t4,$t4t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an5= VisitingDetails::whereBetween('graph_time', [$t5,$t5t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an6= VisitingDetails::whereBetween('graph_time', [$t6,$t6t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an7= VisitingDetails::whereBetween('graph_time', [$t7,$t7t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an8= VisitingDetails::whereBetween('graph_time', [$t8,$t8t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an9= VisitingDetails::whereBetween('graph_time', [$t9,$t9t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an10= VisitingDetails::whereBetween('graph_time', [$t10,$t10m])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an11= VisitingDetails::whereBetween('graph_time', [$t11,$t11m])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an12= VisitingDetails::whereBetween('graph_time', [$t12,$t12m])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an13= VisitingDetails::whereBetween('graph_time', [$t13,$t13m])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an14= VisitingDetails::whereBetween('graph_time', [$t14,$t14m])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an15= VisitingDetails::whereBetween('graph_time', [$t15,$t15m])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an16= VisitingDetails::whereBetween('graph_time', [$t16,$t16m])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an17= VisitingDetails::whereBetween('graph_time', [$t17,$t17m])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an18= VisitingDetails::whereBetween('graph_time', [$t18,$t18m])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an19= VisitingDetails::whereBetween('graph_time', [$t19,$t19m])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an20= VisitingDetails::whereBetween('graph_time', [$t20,$t20t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an21= VisitingDetails::whereBetween('graph_time', [$t21,$t21t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an22= VisitingDetails::whereBetween('graph_time', [$t22,$t22t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an23= VisitingDetails::whereBetween('graph_time', [$t23,$t23t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an24= VisitingDetails::whereBetween('graph_time', [$t24,$t24t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();

           $an10m= VisitingDetails::whereBetween('graph_time', [$t10,$t10t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an11m= VisitingDetails::whereBetween('graph_time', [$t11tm,$t11t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an12m= VisitingDetails::whereBetween('graph_time', [$t12tm,$t12t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an13m= VisitingDetails::whereBetween('graph_time', [$t13tm,$t13t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an14m= VisitingDetails::whereBetween('graph_time', [$t14tm,$t14t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an15m= VisitingDetails::whereBetween('graph_time', [$t15tm,$t15t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an16m= VisitingDetails::whereBetween('graph_time', [$t16tm,$t16t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an17m= VisitingDetails::whereBetween('graph_time', [$t17tm,$t17t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an18m= VisitingDetails::whereBetween('graph_time', [$t18tm,$t18t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           $an19m= VisitingDetails::whereBetween('graph_time', [$t19tm,$t19t])->where('employee_id', '=', '2')->where('created_at','>=',$last_month)->count();
           

           $ni1= VisitingDetails::whereBetween('graph_time', [$t1,$t1t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni2= VisitingDetails::whereBetween('graph_time', [$t2,$t2t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni3= VisitingDetails::whereBetween('graph_time', [$t3,$t3t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni4= VisitingDetails::whereBetween('graph_time', [$t4,$t4t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni5= VisitingDetails::whereBetween('graph_time', [$t5,$t5t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni6= VisitingDetails::whereBetween('graph_time', [$t6,$t6t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni7= VisitingDetails::whereBetween('graph_time', [$t7,$t7t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni8= VisitingDetails::whereBetween('graph_time', [$t8,$t8t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni9= VisitingDetails::whereBetween('graph_time', [$t9,$t9t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni10= VisitingDetails::whereBetween('graph_time', [$t10,$t10m])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni11= VisitingDetails::whereBetween('graph_time', [$t11,$t11m])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni12= VisitingDetails::whereBetween('graph_time', [$t12,$t12m])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni13= VisitingDetails::whereBetween('graph_time', [$t13,$t13m])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni14= VisitingDetails::whereBetween('graph_time', [$t14,$t14m])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni15= VisitingDetails::whereBetween('graph_time', [$t15,$t15m])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni16= VisitingDetails::whereBetween('graph_time', [$t16,$t16m])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni17= VisitingDetails::whereBetween('graph_time', [$t17,$t17m])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni18= VisitingDetails::whereBetween('graph_time', [$t18,$t18m])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni19= VisitingDetails::whereBetween('graph_time', [$t19,$t19m])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni20= VisitingDetails::whereBetween('graph_time', [$t20,$t20t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni21= VisitingDetails::whereBetween('graph_time', [$t21,$t21t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni22= VisitingDetails::whereBetween('graph_time', [$t22,$t22t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni23= VisitingDetails::whereBetween('graph_time', [$t23,$t23t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni24= VisitingDetails::whereBetween('graph_time', [$t24,$t24t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();

           $ni10m= VisitingDetails::whereBetween('graph_time', [$t10,$t10t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni11m= VisitingDetails::whereBetween('graph_time', [$t11tm,$t11t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni12m= VisitingDetails::whereBetween('graph_time', [$t12tm,$t12t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni13m= VisitingDetails::whereBetween('graph_time', [$t13tm,$t13t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni14m= VisitingDetails::whereBetween('graph_time', [$t14tm,$t14t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni15m= VisitingDetails::whereBetween('graph_time', [$t15tm,$t15t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni16m= VisitingDetails::whereBetween('graph_time', [$t16tm,$t16t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni17m= VisitingDetails::whereBetween('graph_time', [$t17tm,$t17t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni18m= VisitingDetails::whereBetween('graph_time', [$t18tm,$t18t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           $ni19m= VisitingDetails::whereBetween('graph_time', [$t19tm,$t19t])->where('employee_id', '=', '1')->where('created_at','>=',$last_month)->count();
           
           $pa=DB::TABLE('visiting_details')
           ->SELECT(DB::raw('HOUR(checkin_at) as pk'))->where('created_at','>=',$last_month)
           ->groupBy(DB::raw('HOUR(checkin_at)'))
           ->orderBy(DB::raw('COUNT(checkin_at)'),'desc')->limit(1)
           ->get(); 

           /**Content Graph */

           $this->data['school']=VisitingDetails::where('created_at','>=',$last_month)->where('used_content', '=', 'School')->count();
           $this->data['leisure']=VisitingDetails::where('created_at','>=',$last_month)->where('used_content', '=', 'Leisure Reads')->count();
           $this->data['commerce']=VisitingDetails::where('created_at','>=',$last_month)->where('used_content', '=', 'Commerce and Management')->count();
           $this->data['dictionary']=VisitingDetails::where('created_at','>=',$last_month)->where('used_content', '=', 'Dictionary and Encyclopedia')->count();
           $this->data['illustrated']=VisitingDetails::where('created_at','>=',$last_month)->where('used_content', '=', 'Illustrated Content')->count();
           $this->data['simulation']=VisitingDetails::where('created_at','>=',$last_month)->where('used_content', '=', 'Simulation Labs')->count();
           $this->data['arts']=VisitingDetails::where('created_at','>=',$last_month)->where('used_content', '=', 'Arts and Humanities')->count();
           $this->data['classics']=VisitingDetails::where('created_at','>=',$last_month)->where('used_content', '=', 'Classics and Literature')->count();
           $this->data['science']=VisitingDetails::where('created_at','>=',$last_month)->where('used_content', '=', 'Science and Technology')->count();
           $this->data['question']=VisitingDetails::where('created_at','>=',$last_month)->where('used_content', '=', 'Question Papers')->count();
           $this->data['personality']=VisitingDetails::where('created_at','>=',$last_month)->where('used_content', '=', 'Personality and Skills')->count();
           $this->data['competitive']=VisitingDetails::where('created_at','>=',$last_month)->where('used_content', '=', 'Competitive and Entrance')->count();
           $this->data['ncert']=VisitingDetails::where('created_at','>=',$last_month)->where('used_content', '=', 'NCERT Solutions')->count();
           $this->data['financial']=VisitingDetails::where('created_at','>=',$last_month)->where('used_content', '=', 'Financial and Digital Literacy')->count();
           
          

           $this->data['max']=DB::table('visiting_details')
                    ->select('visiting_details.employee_id','employees.first_name','employees.last_name', DB::raw('COUNT(visiting_details.employee_id) as cnt'))
                    ->Join('employees','visiting_details.employee_id','=','employees.id')
                    ->where('visiting_details.created_at','>=',$last_month)
                    ->groupBy('employees.first_name','employees.last_name','visiting_details.employee_id')
                    ->orderBy('cnt','desc')
                    ->latest('cnt')->first();
          $this->data['min']=DB::table('visiting_details')
                    ->select('visiting_details.employee_id','employees.first_name','employees.last_name', DB::raw('COUNT(visiting_details.employee_id) as cnt'))
                    ->Join('employees','visiting_details.employee_id','=','employees.id')
                    ->where('visiting_details.created_at','>=',$last_month)
                    ->groupBy('employees.first_name','employees.last_name','visiting_details.employee_id')
                    ->orderBy('cnt','asc')
                    ->latest('cnt')->first();        

        
           foreach ($pa as $pa1)
               $peakHour=$pa1->pk;
    
           $this->data['peakHour']  = $peakHour;
           $this->data['repeatedvisitor']  = $repeatedvisitor;

           $this->data['tm1']  = $tm1;$this->data['tm2']  = $tm2;$this->data['tm3']  = $tm3;
           $this->data['tm4']  = $tm4;$this->data['tm5']  = $tm5;$this->data['tm6']  = $tm6;
           $this->data['tm7']  = $tm7;$this->data['tm8']  = $tm8;$this->data['tm9']  = $tm9;
           $this->data['tm10']  = $tm10;$this->data['tm11']  = $tm11;$this->data['tm12']  = $tm12;
           $this->data['tm13']  = $tm13;$this->data['tm14']  = $tm14;$this->data['tm15']  = $tm15;
           $this->data['tm16']  = $tm16;$this->data['tm17']  = $tm17;$this->data['tm18']  = $tm18;
           $this->data['tm19']  = $tm19;$this->data['tm20']  = $tm20;$this->data['tm21']  = $tm21;
           $this->data['tm22']  = $tm22;$this->data['tm23']  = $tm23;$this->data['tm24']  = $tm24;
           $this->data['tm10m']  = $tm10m;$this->data['tm11m']  = $tm11m;$this->data['tm12m']  = $tm12m;
           $this->data['tm13m']  = $tm13m;$this->data['tm14m']  = $tm14m;$this->data['tm15m']  = $tm15m;
           $this->data['tm16m']  = $tm16m;$this->data['tm17m']  = $tm17m;$this->data['tm18m']  = $tm18m;
           $this->data['tm19m']  = $tm19m;

           $this->data['si1']  = $si1;$this->data['si2']  = $si2;$this->data['si3']  = $si3;
           $this->data['si4']  = $si4;$this->data['si5']  = $si5;$this->data['si6']  = $si6;
           $this->data['si7']  = $si7;$this->data['si8']  = $si8;$this->data['si9']  = $si9;
           $this->data['si10']  = $si10;$this->data['si11']  = $si11;$this->data['si12']  = $si12;
           $this->data['si13']  = $si13;$this->data['si14']  = $si14;$this->data['si15']  = $si15;
           $this->data['si16']  = $si16;$this->data['si17']  = $si17;$this->data['si18']  = $si18;
           $this->data['si19']  = $si19;$this->data['si20']  = $si20;$this->data['si21']  = $si21;
           $this->data['si22']  = $si22;$this->data['si23']  = $si23;$this->data['si24']  = $si24;
           $this->data['si10m']  = $si10m;$this->data['si11m']  = $si11m;$this->data['si12m']  = $si12m;
           $this->data['si13m']  = $si13m;$this->data['si14m']  = $si14m;$this->data['si15m']  = $si15m;
           $this->data['si16m']  = $si16m;$this->data['si17m']  = $si17m;$this->data['si18m']  = $si18m;
           $this->data['si19m']  = $si19m;

           $this->data['st1']  = $st1;$this->data['st2']  = $st2;$this->data['st3']  = $st3;
           $this->data['st4']  = $st4;$this->data['st5']  = $st5;$this->data['st6']  = $st6;
           $this->data['st7']  = $st7;$this->data['st8']  = $st8;$this->data['st9']  = $st9;
           $this->data['st10']  = $st10;$this->data['st11']  = $st11;$this->data['st12']  = $st12;
           $this->data['st13']  = $st13;$this->data['st14']  = $st14;$this->data['st15']  = $st15;
           $this->data['st16']  = $st16;$this->data['st17']  = $st17;$this->data['st18']  = $st18;
           $this->data['st19']  = $st19;$this->data['st20']  = $st20;$this->data['st21']  = $st21;
           $this->data['st22']  = $st22;$this->data['st23']  = $st23;$this->data['st24']  = $st24;
           $this->data['st10m']  = $st10m;$this->data['st11m']  = $st11m;$this->data['st12m']  = $st12m;
           $this->data['st13m']  = $st13m;$this->data['st14m']  = $st14m;$this->data['st15m']  = $st15m;
           $this->data['st16m']  = $st16m;$this->data['st17m']  = $st17m;$this->data['st18m']  = $st18m;
           $this->data['st19m']  = $st19m;

           $this->data['an1']  = $an1;$this->data['an2']  = $an2;$this->data['an3']  = $an3;
           $this->data['an4']  = $an4;$this->data['an5']  = $an5;$this->data['an6']  = $an6;
           $this->data['an7']  = $an7;$this->data['an8']  = $an8;$this->data['an9']  = $an9;
           $this->data['an10']  = $an10;$this->data['an11']  = $an11;$this->data['an12']  = $an12;
           $this->data['an13']  = $an13;$this->data['an14']  = $an14;$this->data['an15']  = $an15;
           $this->data['an16']  = $an16;$this->data['an17']  = $an17;$this->data['an18']  = $an18;
           $this->data['an19']  = $an19;$this->data['an20']  = $an20;$this->data['an21']  = $an21;
           $this->data['an22']  = $an22;$this->data['an23']  = $an23;$this->data['an24']  = $an24;
           $this->data['an10m']  = $an10m;$this->data['an11m']  = $an11m;$this->data['an12m']  = $an12m;
           $this->data['an13m']  = $an13m;$this->data['an14m']  = $an14m;$this->data['an15m']  = $an15m;
           $this->data['an16m']  = $an16m;$this->data['an17m']  = $an17m;$this->data['an18m']  = $an18m;
           $this->data['an19m']  = $an19m;

           $this->data['ni1']  = $ni1;$this->data['ni2']  = $ni2;$this->data['ni3']  = $ni3;
           $this->data['ni4']  = $ni4;$this->data['ni5']  = $ni5;$this->data['ni6']  = $ni6;
           $this->data['ni7']  = $ni7;$this->data['ni8']  = $ni8;$this->data['ni9']  = $ni9;
           $this->data['ni10']  = $ni10;$this->data['ni11']  = $ni11;$this->data['ni12']  = $ni12;
           $this->data['ni13']  = $ni13;$this->data['ni14']  = $ni14;$this->data['ni15']  = $ni15;
           $this->data['ni16']  = $ni16;$this->data['ni17']  = $ni17;$this->data['ni18']  = $ni18;
           $this->data['ni19']  = $ni19;$this->data['ni20']  = $ni20;$this->data['ni21']  = $ni21;
           $this->data['ni22']  = $ni22;$this->data['ni23']  = $ni23;$this->data['ni24']  = $ni24;
           $this->data['ni10m']  = $ni10m;$this->data['ni11m']  = $ni11m;$this->data['ni12m']  = $ni12m;
           $this->data['ni13m']  = $ni13m;$this->data['ni14m']  = $ni14m;$this->data['ni15m']  = $ni15m;
           $this->data['ni16m']  = $ni16m;$this->data['ni17m']  = $ni17m;$this->data['ni18m']  = $ni18m;
           $this->data['ni19m']  = $ni19m;
       
        }
        

        

        
        


        $totalVisitor   = count($visitors);
        $totalPrerigister = count($preregister);
        $active_visitors1=count($active_visitors);
     /*  $pa=DB::TABLE('visiting_details')
       ->SELECT(DB::raw('HOUR(checkin_at)'), DB::raw('COUNT(checkin_at)'))
       ->groupBy(DB::raw('HOUR(checkin_at)'))
       ->orderBy('COUNT(checkin_at)','desc')->limit(1)
       ->get();  */

      
        $attendance = Attendance::where(['user_id' => auth()->user()->id, 'date' => date('Y-m-d')])->first();
        $this->data['attendance']    = $attendance;
        $this->data['totalVisitor']    = $totalVisitor;
        $this->data['totalEmployees'] = $totalEmployees;
        $this->data['totalPrerigister']     = $totalPrerigister;
        $this->data['visitors']  = $visitors;
        $this->data['active_visitors1']  = $active_visitors1;
        

        return view('admin.dashboard.index', $this->data);
    }
}
