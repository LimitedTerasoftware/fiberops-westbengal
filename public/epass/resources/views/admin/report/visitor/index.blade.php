@extends('admin.layouts.master')

@section('main-content')

    <section class="section">
        <div class="section-header shadow">
            <h1>{{ __('visitor_report.visitor_report') }}</h1>
            {{ Breadcrumbs::render('visitors') }}
        </div>

        <div class="section-body">
            <div class="card shadow">
                <div class="card-body">
                    <form action="<?=route('admin.admin-visitor-report.post')?>" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>{{ __('visitor_report.from_date') }}</label>
                                    <input type="date" name="from_date" class="form-control @error('from_date') is-invalid @enderror " >
                                    @error('from_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>{{ __('visitor_report.to_date') }}</label>
                                    <input type="date" name="to_date" class="form-control @error('to_date') is-invalid @enderror " >
                                    @error('to_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-3">
                            <div class="form-group">
                            <label>{{ __('visitor_report.employee') }}</label>
                            <select id="employee_id "  name="employee_id" class="form-control select2 @error('employee_id') is-invalid @enderror">
                                           <option value="">--Select Center--</option>
                                        @foreach($employees as $key => $employee)
                                               
                                                <option value="{{ $employee->id }}" {{ (old('employee_id') == $employee->id) ? 'selected' : '' }}>{{ $employee->name }} ( {{$employee->department->name}} )</option>
                                            @endforeach
                                        </select>
                                        @error('employee_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                             </div></div> 
                             <div class="col-sm-3">
                             <div class="form-group">
                            <label>{{ __('Repeated Visitors') }}</label>   
                             <select id="repeated_id"  name="repeated_id" class="form-control select2 @error('repeated_id') is-invalid @enderror">
                                           <option value="">--Select--</option>
                                            <option value="repeated">Repeated Vistors</option>
                                        </select>
                                        @error('repeated_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                             </div></div> 
                            <div class="col-sm-2">
                                <label for="">&nbsp;</label>
                                <button class="btn btn-primary form-control" type="submit">{{ __('visitor_report.get_report') }}</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            @if($showView)
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1 shadow">
                            <div class="card-icon">
                            <img  src="{{ asset('images/totalvisitors.png') }}" alt="">
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>{{ __('visitor_report.total_visitor') }}</h4>
                                </div>
                                <div class="card-body">
                                    {{$totalVisitor}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1 shadow">
                            <div class="card-icon">
                            <img  src="{{ asset('images/checkin.png') }}" alt="">
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>{{ __('visitor_report.checkin_visitor') }}</h4>
                                </div>
                                <div class="card-body">
                                    {{$checkinVisitor - $checkoutVisitor}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1 shadow">
                            <div class="card-icon">
                            <img  src="{{ asset('images/checkout.png') }}" alt="">
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>{{ __('visitor_report.checkout_visitor') }}</h4>
                                </div>
                                <div class="card-body">
                                    {{$checkoutVisitor}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header">
                        <h4>{{ __('visitor_report.visitor_report') }} &nbsp; @if($showView1) @if($showView3) @foreach($center_name as $center_names){{ $center_names->first_name }}  {{ $center_names->last_name }} @endforeach @endif {{ $type_data  }}  @endif</h4>
                        <button class="btn btn-success btn-sm report-print-button" onclick="printDiv('printablediv')">{{ __('visitor_report.print') }}</button>
                       <button class="btn btn-primary btn-sm  report-excel-button" id="export_button">{{ __('excel') }}</button> 
                    </div>
                    <div class="card-body" id="printablediv">
                        @if(!blank($visitors))
                            <div class="table-responsive">
                                <table class="table table-striped" id="visitor_data">
                                    <thead>
                                        <tr>
                                            <th>{{ __('levels.id') }}</th>
                                          <!--  <th>{{ __('levels.image') }}</th> -->
                                            <th>{{ __('visitor_report.visitor_id') }}</th>
                                            <th>{{ __('levels.name') }}</th>
                                            <th>{{ __('visitor.age') }}</th>
                                            <th>{{ __('levels.email') }}</th>
                                            <th>{{ __('levels.phone') }}</th>
                                            <th>{{ __('visitor_report.employee') }}</th>
                                            <th>{{ __('visitor.purpose') }}</th>
                                            <th>{{ __('visitor.ln') }}</th>
                                            <th>{{ __('visitor_report.checkin') }}</th>
                                            <th>{{ __('visitor_report.check_out') }}</th>
                                            <th>{{ __('Duration') }}</th>
                                        </tr>
                                        @php $i =0;@endphp
                                        @foreach($visitors as $visitor)
                                            <tr>
                                                <td>{{$i+=1 }}</td>
                                               <!-- <td><figure class="avatar mr-2"><img src="{{$visitor->images}}" alt=""></figure></td> -->
                                               
                                                <td>{{$visitor->reg_no }}</td>
                                                <td>{{ Str::limit(optional($visitor->visitor)->name, 50)}}</td>
                                                <td>{{$visitor->visitor->age}}</td>
                                                <td>{{ Str::limit(optional($visitor->visitor)->email, 50) }}</td>
                                                <td>{{optional($visitor->visitor)->phone}}</td>
                                                <td>{{optional($visitor->employee->user)->name}}</td>
                                                <td>{{ $visitor->used_content}}</td>
                                                <td>{{ $visitor->language}}</td>
                                                <td>{{ date('Y-m-d h:i a', strtotime($visitor->checkin_at)) }}</td>
                                                @if($visitor->checkout_at)
                                                    <td>{{ date('Y-m-d h:i a', strtotime($visitor->checkout_at)) }}</td>
                                                @else
                                                    <td>{{ __('visitor_report.n/a') }}</td>
                                                @endif
                                                <td>{{$visitor->time_duration}}</td>
                                            </tr>
                                        @endforeach
                                    </thead>
                                </table>
                                
                            </div>
                        @else
                            <h4 class="text-danger">{{ __('visitor_report.data_not_found') }}</h4>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('scripts')
   
     <script src="{{ asset('assets/modules/excel/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/report/visitor/index.js') }}"></script>

@endsection
