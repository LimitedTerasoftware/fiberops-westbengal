@extends('admin.layouts.master')

@section('main-content')
@if(auth()->user()->getrole->name == 'Employee')

@else
<head>
    
    <link href="{{ asset('assets/modules/aos/aos.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/modules/aos/aos.js') }}"></script>
    <script>
  AOS.init();
</script>

 <script src="{{ asset('assets/modules/charts/charts_loader.js') }}"></script>
    
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['DCM Layout Center',    {{$dcmcount}}],
          ['Silver Jubilee Center',       {{$silvercount}}],
          ['Stadium Center',  {{$stadiumcount}}],
          ['Anjaneya Badavane Center', {{$anjaneyacount}}],
          ['Nijalingappa Badavane Center',    {{$nijalingappacount}}]
        ]);

        var options = {
            is3D: true,
            backgroundColor:'#fff',
            legend:{textStyle: {color: '#000'}},
            tooltip:{textStyle: {color: '#6777ef'}, showColorCode: true},
            colors: ['#4835f3','#5c83ba','#3396f5','#f8ac10','#df4142'],
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Month', 'DCM Layout Center', 'Silver Jubilee Center', 'Stadium Center','Anjaneya Badavane Center','Nijalingappa Badavane Center',],
          
          ['Jan-24',  {{$dcm_jan24_1}}, {{$sil_jan24_1}}, {{$sta_jan24_1}}, {{$anj_jan24_1}}, {{$nij_jan24_1}}],
          ['Feb-24',  {{$dcm_feb24_1}}, {{$sil_feb24_1}}, {{$sta_feb24_1}}, {{$anj_feb24_1}}, {{$nij_feb24_1}}],
          ['Mar-24',  {{$dcm_mar24_1}}, {{$sil_mar24_1}}, {{$sta_mar24_1}}, {{$anj_mar24_1}}, {{$nij_mar24_1}}],
          ['Apr-24',  {{$dcm_apr24_1}}, {{$sil_apr24_1}}, {{$sta_apr24_1}}, {{$anj_apr24_1}}, {{$nij_apr24_1}}],
          ['May-24',  {{$dcm_may24_1}}, {{$sil_may24_1}}, {{$sta_may24_1}}, {{$anj_may24_1}}, {{$nij_may24_1}}],
          ['Jun-24', {{$dcm_jun24_1}}, {{$sil_jun24_1}}, {{$sta_jun24_1}}, {{$anj_jun24_1}}, {{$nij_jun24_1}}],
          ['Jul-24', {{$dcm_jul1}}, {{$sil_jul1}}, {{$sta_jul1}}, {{$anj_jul1}}, {{$nij_jul1}}],
          ['Aug-24', {{$dcm_aug1}}, {{$sil_aug1}}, {{$sta_aug1}}, {{$anj_aug1}}, {{$nij_aug1}}],
          ['Sep-24', {{$dcm_sep1}}, {{$sil_sep1}}, {{$sta_sep1}}, {{$anj_sep1}}, {{$nij_sep1}}],
          ['Oct-24', {{$dcm_oct1}}, {{$sil_oct1}}, {{$sta_oct1}}, {{$anj_oct1}}, {{$nij_oct1}}],
          ['Nov-24', {{$dcm_nov1}}, {{$sil_nov1}}, {{$sta_nov1}}, {{$anj_nov1}}, {{$nij_nov1}}],
          ['Dec-24', {{$dcm_dec1}}, {{$sil_dec1}}, {{$sta_dec1}}, {{$anj_dec1}}, {{$nij_dec1}}]
        ]);

        var options = {
            colors: ['#4835f3','#5c83ba','#3396f5','#f8ac10','#df4142'],
            backgroundColor:'#fff',
            chartArea:{backgroundColor:'#fff'},
            legend:{textStyle: {color: '#000'},
            tooltip:{textStyle: {color: '#d12ce3'}, showColorCode: true},},
          chart: { is3D: true},
          bars: 'vertical', // Required for Material Bar Charts.
          hAxis: {textStyle:{color: '#7a7a7e'  },
        },
         
        };

        var chart = new google.charts.Bar(document.getElementById('barchart_material'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
    </script>
    
    <script type="text/javascript">
      google.charts.load('current', {packages:['corechart', 'line']});
      google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = new google.visualization.DataTable();
      data.addColumn('number', 'hours');
      data.addColumn('number', 'DCM Layout Center');
      data.addColumn('number', 'Silver Jubilee Center');
      data.addColumn('number', 'Stadium Center');
      data.addColumn('number', 'Anjaneya Badavane Center');
      data.addColumn('number', 'Nijalingappa Badavane Center');
     

      data.addRows([
       [10, 0,0,0,0,0],
        [10.30, {{$tm10m}},{{$si10m}},{{$st10m}},{{$an10m}},{{$ni10m}}],
        [11, {{$tm11}},{{$si11}},{{$st11}},{{$an11}},{{$ni11}}],
        [11.30, {{$tm11m}},{{$si11m}},{{$st11m}},{{$an11m}},{{$ni11m}}],
        [12, {{$tm12}},{{$si12}},{{$st12}},{{$an12}},{{$ni12}}],
        [12.30, {{$tm12m}},{{$si12m}},{{$st12m}},{{$an12m}},{{$ni12m}}],
        [13, {{$tm13}},{{$si13}},{{$st13}},{{$an13}},{{$ni13}}],
        [13.30, {{$tm13m}},{{$si13m}},{{$st13m}},{{$an13m}},{{$ni13m}}],
        [14,  {{$tm14}},{{$si14}},{{$st14}},{{$an14}},{{$ni14}}],
        [14.30,  {{$tm14m}},{{$si14m}},{{$st14m}},{{$an14m}},{{$ni14m}}],
        [15, {{$tm15}},{{$si15}},{{$st15}},{{$an15}},{{$ni15}}],
        [15.30, {{$tm15m}},{{$si15m}},{{$st15m}},{{$an15m}},{{$ni15m}}],
        [16, {{$tm16}},{{$si16}},{{$st16}},{{$an16}},{{$ni16}}],
        [16.30, {{$tm16m}},{{$si16m}},{{$st16m}},{{$an16m}},{{$ni16m}}],
        [17, {{$tm17}},{{$si17}},{{$st17}},{{$an17}},{{$ni17}}],
        [17.30, {{$tm17m}},{{$si17m}},{{$st17m}},{{$an17m}},{{$ni17m}}],
        [18, {{$tm18}},{{$si18}},{{$st18}},{{$an18}},{{$ni18}}],
        [18.30, {{$tm18m}},{{$si18m}},{{$st18m}},{{$an18m}},{{$ni18m}}],
        [19, {{$tm19}},{{$si19}},{{$st19}},{{$an19}},{{$ni19}}],
        [19.30, 0,0,0,0,0]
      ]);

      var options = {
        colors: ['#4835f3','#5c83ba','#3396f5','#f8ac10','#df4142'],
        backgroundColor:'#fff',
        chartArea:{backgroundColor:'#fff'},
        legend:{textStyle: {color: '#000'},
        tooltip:{textStyle: {color: '#d12ce3'}, showColorCode: true},},
        axes: {y: { 0: {side: 'top'}}}, 
        hAxis: {textStyle:{color: '#7a7a7e'  }},  
      };
          
      var chart = new google.charts.Line(document.getElementById('line_top_x'));
      chart.draw(data, google.charts.Line.convertOptions(options));
    }
  </script>

<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = new google.visualization.DataTable();
          data.addColumn('string', 'Content');
          data.addColumn('number', 'Used');
          data.addRows([
          ['School',{{$school}}],
          ['Leisure Reads',{{$leisure}}],
          ['Commerce and Management',{{$commerce}}],
          ['Dictionary and Encyclopedia',{{$dictionary}}],
          ['Illustrated Content',{{$illustrated}}],
          ['Simulation Labs',{{$simulation}}],
          ['Arts and Humanities',{{$arts}}],
          ['Classics and Literature',{{$classics}}],
          ['Science and Technology',{{$science}}],
          ['Question Papers',{{$question}}],
          ['Personality and Skills',{{$personality}}],
          ['Competitive and Entrance',{{$competitive}}],
          ['NCERT Solutions',{{$ncert}}],
          ['Financial and Digital Literacy',{{$financial}}],

         
        ]);

        var options = {
            'is3D':true,
            backgroundColor:'#fff',
            legend:{textStyle: {color: '#000'}},
         
        hAxis: {
            textPosition : 'out',
            slantedText: true,
            slantedTextAngle:60,
            textStyle:{
            color: '#7a7a7e'  }
        },
        colors: ['#7a50ee'],
        tooltip:{textStyle: {color: '#6777ef'}, showColorCode: true},
        
      }

      var chart = new google.visualization.ColumnChart(document.getElementById('barchart_content'));

chart.draw(data, options);

       
      }
    </script>

  

  </head>
 @endif
    <section class="section backimg" >
        <div class="section-header shadow">
            <h1>{{ __('dashboard.dashboard') }}</h1>
            {{ Breadcrumbs::render('dashboard') }}
        </div>
        <div class="row" >
            <div class="col-md-12">
                @if(!blank($attendance))
                <div class="float-right  d-flex text-center" style="margin-left:auto">
                        <p class="mr-2">
                            <span class="clock-span"><i class="fas fa-4x fa-clock"></i> {{ date('g:i A') }}</span><br>
                            @if($attendance->checkin_time)
                            <span class="text-success">
                                {{ __('dashboard.clock_in_at') }} - {{$attendance->checkin_time}} @if($attendance->checkout_time) <span class="text-danger ml-2">  {{ __('dashboard.clock_out_at') }} - {{$attendance->checkout_time}}</span>@endif
                          </span>
                           @endif
                        </p>
                    @if(!$attendance->checkout_time)
                        <form action="{{ route('admin.attendance.clockout')}}" method="post">
                            {{ csrf_field() }}
                            <button   class="btn  d-flex inputbtnclockout align-items-center btn-dark" type="submit"><i class="fas fa-4x fa-sign-out-alt"></i>{{ __('dashboard.clock_out') }}</button>
                        </form>
                        @endif
                </div>
                    @else
                    <div class="float-right  d-flex text-center" style="margin-left:auto">
                        <p class="mt-2 mr-2">
                            <span class="clock-span"><i class="fas fa-4x fa-clock"></i> {{ date('g:i A') }}</span><br>
                        </p>
                        <button  type="button" class="btn  d-flex inputbtnclockin align-items-center btn-success" data-toggle="modal" data-target="#exampleModal"><i class="fas fa-4x fa-sign-out-alt"></i>{{ __('dashboard.clock_in') }}</button>
                    </div>
                    @endif
            </div>
        </div>

        @if(auth()->user()->getrole->name == 'Employee')
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1 shadow cardhover">
                    <div class="card-icon">
                    <img  src="{{ asset('images/visitor2.png') }}" alt="">
                    </div>
                    
                    <div class="card-wrap">
                        <div class="card-header">
                            {{ __('dashboard.total_visitors') }}
                        </div>
                        <div> &nbsp;&nbsp;</div>
                        <div class="card-body">
                            {{$totalVisitor}}
                        </div>
                    </div>
                
                </div>
            </div>
         
          <div class="col-lg-4 col-md-6 col-sm-6 col-12">
              <a href="{{ url('admin/active_visitor') }}">
                    <div class="card card-statistic-1 shadow cardhover">
                        <div class="card-icon">
                        <img  src="{{ asset('images/center1.png') }}" alt="">
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                {{ __('Currently Active Visitors') }}
                            </div>
                            <div> &nbsp;&nbsp;</div>
                            <div class="card-body">
                                {{$active_visitors1}}
                            </div>
                        </div>
                    </div></a>
                </div>
        </div><!--end start div-->
        @else
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12" data-aos="zoom-in-up">
                <a href="{{ url('admin/employees') }}">
                    <div class="card card-statistic-1 shadow cardhover">
                    <div class="card-icon" data-aos="fade-up-right">
                                <img  src="{{ asset('images/center3.png') }}" alt="">
                                 </div>
                        <div class="card-wrap"  data-aos="fade-up-left">
                            <div class="card-header">
                                {{ __('dashboard.total_employees') }} 
                            </div>
                            <div> &nbsp;&nbsp;</div>
                            <div class="card-body">
                                {{$totalEmployees}}
                              
                            </div>
                        </div>  
                    </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-12" data-aos="zoom-in-up">
                <a href="{{ url('admin/admin-visitor-report') }}">
                    <div class="card card-statistic-1 shadow cardhover">
                    <div class="card-icon" data-aos="fade-up-right">
                                 <img  src="{{ asset('images/visitor2.png') }}" alt="">
                                 </div>
                        <div class="card-wrap" data-aos="fade-up-left">
                            <div class="card-header">
                               {{ __('Last 30 days Visitors') }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;   
                            </div>
                            <div class="cardbodypading">&nbsp;</div>
                            <div class="card-body">
                                {{$totalVisitor}}
                                 
                            </div>
                        </div>
                    </div>
                  </a>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12" data-aos="zoom-in-up">
                <a href="{{ url('admin/active_visitor') }}">
                    <div class="card card-statistic-1 shadow cardhover">
                    <div class="card-icon" data-aos="fade-up-right">
                                <img  src="{{ asset('images/center1.png') }}" alt="">
                                </div>
                        <div class="card-wrap" data-aos="fade-up-left">
                            <div class="card-header">
                                {{ __('Currently Active Visitors') }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </div>
                            <div class="cardbodypading">&nbsp;</div>
                            <div class="card-body">
                                {{$active_visitors1}}
                               
                            </div>
                        </div>
                    </div>
                </div>
               </a>
               <div class="col-lg-3 col-md-6 col-sm-6 col-12" data-aos="zoom-in-up">
                
                <div class="card card-statistic-1 shadow cardhover">
                <div class="card-icon" data-aos="fade-up-right">
                            <img  src="{{ asset('images/agegroup.png') }}" alt="">
                            </div>
                    <div class="card-wrap" data-aos="fade-up-left">
                        <div class="card-header">
                            {{ __('Last 30 days Average Age Group Visited') }}&nbsp;&nbsp;
                        </div>
                        <div class="cardbodypading">&nbsp;</div>
                        <div class="card-body">
                            {{$avg_from_age}} - {{$avg_to_age}}
                           
                        </div>
                    </div>
                </div>
            </div>
            </div>
     <div class="row">
              <div class="col-lg-3 col-md-6 col-sm-6 col-12" data-aos="zoom-in-up">
                    <div class="card card-statistic-1 shadow cardhover">
                    <div class="card-icon" data-aos="fade-up-right">
                             <img  src="{{ asset('images/peakhr.png') }}" alt="">
                             </div> 
                        <div class="card-wrap" data-aos="fade-up-left">
                            <div class="card-header">
                               {{ __('Last 30 days Peak Hour') }}   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </div>
                            <div class="cardbodypading">&nbsp;</div>
                            <div class="card-body">
                                @if($peakHour > '11')
                                @if($peakHour == '12')
                                {{$peakHour}} PM
                                @else
                                {{$peakHour - 12}} PM
                                @endif
                                @else 
                                    {{$peakHour}} AM
                             @endif

                              
                            </div>
                        </div>
                    </div>
                </div>
            
               
                <div class="col-lg-3 col-md-6 col-sm-6 col-12" data-aos="zoom-in-up">
                <a href="{{ url('admin/admin-visitor-report/repeated/repeated_visitors') }}">
                    <div class="card card-statistic-1 shadow cardhover">
                    <div class="card-icon " data-aos="fade-up-right">
                                <img  src="{{ asset('images/visitor1.png') }}" alt="">
                                </div>
                        <div class="card-wrap">
                            <div class="card-header" >
                            {{ __('Last 30 days Repeated Visitors') }}&nbsp;&nbsp;&nbsp;&nbsp;
                            </div>
                            <div class="cardbodypading">&nbsp;</div>
                            <div class="card-body">
                                {{$repeatedvisitor}}    
                            </div>  
                        </div>
                    </div></a>
                </div>
       
    </div>  
    <!---table-->
    <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-12 ">
                    <div class="card card-statistic-1 shadow" data-aos="fade-up" data-aos-offset="300" data-aos-easing="ease-in-sine">
                        <div class="card-wrap">
                            <div class="graph-head" data-aos="fade-down" data-aos-offset="300" data-aos-easing="ease-in-sine">
                                {{ __('Today Center Wise Visitor Count') }}
                            </div>
                            <div class="card-body3" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
                             <div class="row">
                                     <div class="col-2 back-ground1">
                                     <a href="{{ url('admin/admin-visitor-report/5/to_day') }}">
                                     <div class="row count4"><div>DCM Layout Center</div></div>    
                                     <div class="row count3"><div>{{$dcm_today1}}</div></div></a>     
                                     </div>
                                     <div class="col-2 back-ground3">
                                     <a href="{{ url('admin/admin-visitor-report/4/to_day') }}">
                                     <div class="row count4"><div>Silver Jubilee Center</div></div>    
                                     <div class="row count3 "> <div>{{$sil_today1}}</div></div> </a> 
                                     </div> 
                                     <div class="col-2 back-ground2" >
                                     <a href="{{ url('admin/admin-visitor-report/3/to_day') }}">
                                     <div class="row count4"><div>Stadium Center
                                      &nbsp;&nbsp;&nbsp;</div></div>    
                                     <div class="row count3 "> <div>{{$sta_today1}}</div></div></a>    
                                     </div>
                                     <div class="col-3 back-ground4">
                                     <a href="{{ url('admin/admin-visitor-report/2/to_day') }}">
                                     <div class="row count4"><div>Anjaneya Badavane Center</div></div>    
                                     <div class="row count3 "> <div>{{$anj_today1}}</div></div></a>    
                                     </div>   
                                     <div class="col-3 back-ground5">
                                     <a href="{{ url('admin/admin-visitor-report/1/to_day') }}">
                                     <div class="row count4"><div>Nijalingappa Badavane Center</div></div>    
                                     <div class="row count3 "> <div>{{$nij_today1}}</div></div></a>   
                                     </div>  
                             </div>
                          </div>
                        </div>
                    </div>
                </div>
    </div>    
    <!--end table-->
    <!---table-->
    <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-12 ">
                    <div class="card card-statistic-1 shadow" data-aos="fade-up" data-aos-offset="300" data-aos-easing="ease-in-sine">
                        <div class="card-wrap">
                            <div class="graph-head" data-aos="fade-down" data-aos-offset="300" data-aos-easing="ease-in-sine">
                                {{ __('Last Week Center Wise Visitor Count') }}
                            </div>
                            <div class="card-body3" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
                             <div class="row">
                                     <div class="col-2 back-ground1">
                                     <a href="{{ url('admin/admin-visitor-report/5/last_7days') }}">
                                     <div class="row count4"><div>DCM Layout Center</div></div>    
                                     <div class="row count3"><div>{{$dcm_7days1}}</div></div></a>     
                                     </div>
                                     <div class="col-2 back-ground3">
                                     <a href="{{ url('admin/admin-visitor-report/4/last_7days') }}">
                                     <div class="row count4"><div>Silver Jubilee Center</div></div>    
                                     <div class="row count3"> <div>{{$sil_7days1}}</div></div> </a> 
                                     </div> 
                                     <div class="col-2 back-ground2" >
                                     <a href="{{ url('admin/admin-visitor-report/3/last_7days') }}">
                                     <div class="row count4"><div>Stadium Center
                                       &nbsp;&nbsp;&nbsp;</div></div>    
                                     <div class="row count3"> <div>{{$sta_7days1}}</div></div></a>    
                                     </div>
                                     <div class="col-3 back-ground4">
                                     <a href="{{ url('admin/admin-visitor-report/2/last_7days') }}">
                                     <div class="row count4"><div>Anjaneya Badavane Center</div></div>    
                                     <div class="row count3"> <div>{{$anj_7days1}}</div></div></a>    
                                     </div>   
                                     <div class="col-3 back-ground5">
                                     <a href="{{ url('admin/admin-visitor-report/1/last_7days') }}">
                                     <div class="row count4"><div>Nijalingappa Badavane Center</div></div>    
                                     <div class="row count3"> <div>{{$nij_7days1}}</div></div></a>   
                                     </div>  
                             </div>
                          </div>
                        </div>
                    </div>
                </div>
    </div>    
    <!--end table-->
    <!---table-->
    <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-12 ">
                    <div class="card card-statistic-1 shadow" data-aos="fade-up" data-aos-offset="300" data-aos-easing="ease-in-sine">
                        <div class="card-wrap">
                            <div class="graph-head" data-aos="fade-down" data-aos-offset="300" data-aos-easing="ease-in-sine">
                                {{ __('Last 30 days Center Wise Visitor Count') }}
                            </div>
                            <div class="card-body3" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
                             <div class="row">
                                     <div class="col-2 back-ground1">
                                     <a href="{{ url('admin/admin-visitor-report/5/last_30days') }}">
                                     <div class="row count4"><div>DCM Layout Center</div></div>    
                                     <div class="row count3"><div>{{$dcm_month1}}</div></div></a>     
                                     </div>
                                     <div class="col-2 back-ground3">
                                     <a href="{{ url('admin/admin-visitor-report/4/last_30days') }}">
                                     <div class="row count4"><div>Silver Jubilee Center</div></div>    
                                     <div class="row count3"> <div>{{$sil_month1}}</div></div> </a> 
                                     </div> 
                                     <div class="col-2 back-ground2" >
                                     <a href="{{ url('admin/admin-visitor-report/3/last_30days') }}">
                                     <div class="row count4"><div>Stadium Center  &nbsp;&nbsp;&nbsp;</div></div>    
                                     <div class="row count3"> <div>{{$sta_month1}}</div></div></a>    
                                     </div>
                                     <div class="col-3 back-ground4">
                                     <a href="{{ url('admin/admin-visitor-report/2/last_30days') }}">
                                     <div class="row count4"><div>Anjaneya Badavane Center</div></div>    
                                     <div class="row count3"> <div>{{$anj_month1}}</div></div></a>    
                                     </div>   
                                     <div class="col-3 back-ground5">
                                     <a href="{{ url('admin/admin-visitor-report/1/last_30days') }}">
                                     <div class="row count4"><div>Nijalingappa Badavane Center</div></div>    
                                     <div class="row count3"> <div>{{$nij_month1}}</div></div></a>   
                                     </div>  
                             </div>
                          </div>
                        </div>
                    </div>
                </div>
    </div>    
    <!--end table-->
    

    <div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-12" data-aos="fade-up"
     data-aos-duration="3000">
        <div class="card shadow mb-4" >
             <div class="graph-head">Last 30 days Visitors</div>
              <div class="card-body"  data-aos="fade-right"
     data-aos-offset="300"
     data-aos-easing="ease-in-sine">
                       <div class="chart-pie pt-4">
                             <div id="piechart" style="width: 100%; height:70%;"></div>
                        </div>
              </div>
         </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-12" data-aos="fade-up"
     data-aos-duration="3000">
           <div class="col-12 " data-aos="zoom-in-up">
           <a href="{{ url('admin/admin-visitor-report/'.$max->employee_id.'/last_30days') }}">
                    <div class="card card-statistic-1 shadow cardhover">
                    <div class="card-icon" data-aos="fade-up-right">
                            <img  src="{{ asset('images/center1.png') }}" alt="">
                             </div>
                        <div class="card-wrap">
                            <div class="card-header" >
                               {{ __('Last 30 days Maximum Visitors Visited Center') }}
                            </div>
                            <div>&nbsp;</div>
                            <div class="card-body">
                            {{$max->first_name}} {{$max->last_name}}
                            </div>  
                        </div>
                    </div></a>
                </div>

                <div class="col-12 " data-aos="zoom-in-up">
                <a href="{{ url('admin/admin-visitor-report/'.$min->employee_id.'/last_30days') }}"> 
                    <div class="card card-statistic-1 shadow cardhover">
                    <div class="card-icon" data-aos="fade-up-right">
                            <img  src="{{ asset('images/center3.png') }}" alt="">
                            </div>
                        <div class="card-wrap">
                            <div class="card-header" >
                                {{ __('Last 30 days Minimum Visitors Visited Center') }}
                            </div>
                            <div>&nbsp;</div>
                            <div class="card-body">
                            {{$min->first_name}} {{$min->last_name}}
                            
                            </div>  
                        </div>
                    </div></a>
                </div>

     
    </div>
    </div>
    <div class="row" data-aos="fade-up"
     data-aos-duration="3000">
    <div class="col-lg-12 col-md-12 col-sm-12 col-12" >
        <div class="card shadow mb-4">
           
             <div class="graph-head">Last 30 days Visitors (10:00am to 7:30pm)</div>
              <div class="card-body" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine" >
                       <div class="chart-pie pt-4">
                       <div id="line_top_x"  style="width: 100%; "></div>
                        </div>
              </div>
         </div>
    </div>
    </div>

    <div class="row" data-aos="fade-up"
     data-aos-duration="3000">
    <div class="col-lg-12 col-md-12 col-sm-12 col-12" >
    
        <div class="card shadow mb-4">
          
             <div class="graph-head">Visitors Month wise</div>
             <a href="{{ url('admin/admin-visitor-report') }}">
              <div class="card-body" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
                       <div class="chart-pie pt-4">
                       <div id="barchart_material" style="width: 100%; height:300px;"></div>
                        </div>
              </div>
         </div></a>
    </div>
    </div>

    <div class="row" data-aos="fade-up"
     data-aos-duration="3000">
    <div class="col-lg-12 col-md-12 col-sm-12 col-12" >
        
        <div class="card shadow mb-4">
       
             <div class="graph-head">Last 30 days Content Wise Used</div>
             <a href="{{ url('admin/content_used_report') }}">
              <div class="card-body" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
                       <div class="chart-pie pt-4" style="color:red;">
                       <div id="barchart_content" style="width: 100%; height:300px;"></div>
                        </div>
              </div>
         </div></a>
    </div>
    </div>

        @endif

    </section>
    <!-- Modal -->
    
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('dashboard.clock_in') }} - <span class="clock-span"><i class="fas fa-4x fa-clock"></i> {{ date('g:i A') }}</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.attendance.clockin') }}" method="POST">
                    @csrf
                <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('dashboard.working_from') }}</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Office, Home, etc.">
                            @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('dashboard.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('dashboard.clock_in') }}</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    
@endsection
