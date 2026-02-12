@extends('admin.layouts.master')

@section('main-content')



    <section class="section">
        <div class="section-header shadow">
       
            <h1>{{ $employee->first_name}}&nbsp;{{__('visitor_report.employee')}}  </h1>
       
            {{ Breadcrumbs::render('employees/show') }}
        </div>

        <div class="section-body">
            <div class="row mt-sm-4">
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="card shadow">
                        <div class="profile-dashboard bg-maroon-light">
                            <img src="{{asset('images/img/'.$employee->img)}}" alt="">
                            <h1>{{ $employee->first_name }}&nbsp;{{__('visitor_report.employee')}}</h1>
                          <!--  <p>
                                {{ $employee->user->getrole->name ?? '' }}
                            </p> -->
                        </div>
                        <div class="profile-widget-description profile-widget-employee">
                            <dl class="row">
                                <dt class="col-sm-4">{{ __('employee.name') }}</dt>
                                <dd class="col-sm-8">{{ $employee->user->name }}</dd>
                                <dt class="col-sm-4">{{ __('employee.phone') }}</dt>
                                <dd class="col-sm-8">{{ $employee->user->phone }}</dd>
                                <dt class="col-sm-4">{{ __('employee.email') }}</dt>
                                <dd class="col-sm-8">{{ $employee->user->email }}</dd>
                                <dt class="col-sm-4">{{ __('employee.joining_date') }}</dt>
                                <dd class="col-sm-8">{{ $employee->date_of_joining }}</dd>
                              <!--  <dt class="col-sm-4">{{ __('employee.gender') }}</dt>
                                <dd class="col-sm-8">{{ $employee->mygender }}</dd>
                                <dt class="col-sm-4">{{ __('employee.department') }}</dt>
                                <dd class="col-sm-8">{{ $employee->department->name }}</dd>
                                <dt class="col-sm-4">{{ __('employee.designation') }}</dt>
                                <dd class="col-sm-8">{{ $employee->designation->name }}</dd>-->
                                <dt class="col-sm-4">{{ __('employee.status') }}</dt>
                                <dd class="col-sm-8">{{ $employee->mystatus }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-12 col-sm-12 col-12">
               
                <div class="section-body">
             
                
                <div class="card shadow">

                    @can('employees_create')
                 <div class="card-header">
                 
                 <form action="{{route('admin.addons.create.post',$ids)}}" method="POST">
                        @csrf
                 <div class="form-row">
                 
                                    <div class="form-group col">
                                  <!--  <label>{{ __('visitor_report.from_date') }}</label>-->
                                    <input type="date" name="from_date" class="form-control @error('from_date') is-invalid @enderror" >
                                    </div>
                                    <div class="form-group col">
                                  <!--   <label>{{ __('visitor_report.to_date') }}</label>-->
                                    <input type="date" name="to_date" class="form-control @error('to_date') is-invalid @enderror" >
                                    </div>
                                    <div class="form-group col">
                                 <!--    <label>&nbsp;</label>-->
                                     <button class="btn btn-primary form-control" type="submit">{{ __('Get') }}</button>
                                    </div>
                                   </form>
                                    <div class="form-group col">
                                 <!--    <label>&nbsp;</label>-->
                                    <input type="text" id="visitor_data1" placeholder="search..">
                                    </div>
                                    

                 </div><!--form row end-->    
                 </div><!-- card head end-->
                 <div class="form-row">
                 <div class="form-group col">
                                 <!--    <label>&nbsp;</label>-->  
                  <button class="btn btn-success btn-sm report-print-button" onclick="printDiv('printablediv')">{{ __('visitor_report.print') }}</button>     
                 <button class="btn btn-primary btn-sm  report-excel-button" id="export_button">{{ __('excel') }}</button>
                </div> 
              </div>
                   @endcan
                        
                    <div class="card-body" id="printablediv">
                        <div class="table-responsive">
                             
                            <table class="table table-striped" id="visitor_data" >
                                <thead id="visitor_data1">
                                            <tr>
                                                <th>{{ __('levels.id') }}</th>
                                               <!-- <th>{{ __('levels.image') }}</th>-->
                                                <th>{{ __('levels.name') }}</th>
                                                <th>{{ __('levels.phone') }}</th>
                                                <th>{{ __('employee.checkin') }}</th>
                                                <th>{{ __('levels.actions') }}</th>
                                            </tr>
                                            @php $i =0;@endphp
                                        @foreach($visitors as $visitor)
                                            <tr>
                                            <td>{{$i+=1 }}</td>
                                          <!--  <td><figure class="avatar mr-2"><img src="{{$visitor->images}}" alt=""></figure></td> -->
                                            <td>{{ Str::limit(optional($visitor->visitor)->name, 50)}}</td>
                                            <td>{{optional($visitor->visitor)->phone}}</td>
                                            <td>{{ date('d-m-Y h:i A', strtotime($visitor->checkin_at)) }}</td>
                                            <td>
                                             <a href="{{ route('admin.visitors.show', $visitor) }}" class="btn btn-sm btn-icon btn-info"><i class="far fa-eye"></i></a>
                                             <a href="{{ route('admin.visitors.edit', $visitor) }}" class="btn btn-sm btn-icon btn-primary"><i class="far fa-edit"></i></a>&nbsp;
                                            
                                            <form class="float-left pl-2" action="{{route('admin.visitors.destroy', $visitor)}}" method="POST" enctype="multipart/form-data"> 
                                                @csrf  @method('DELETE')
                                                <button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"> <i class="fa fa-trash"></i>
                                            </button>
                                           </form>

                                            </td>
                                           </tr>   
                                           @endforeach
                                            </thead>
                            </table><!--Table end-->
                        </div><!--Table responce end-->
                    </div><!--card body end-->
                </div><!--card end-->
            </div><!--section body -->
        </div><!---col log-8 end-->
    

</section>


@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/excel/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('assets/modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/employee/view1.js') }}"></script>
  
    <script>
    $(document).ready(function(){
    $("#visitor_data1").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#visitor_data1 tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });
  </script>
  
@endsection
