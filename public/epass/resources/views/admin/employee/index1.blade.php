@extends('admin.layouts.master')

@section('main-content')

    <section class="section">
        <div class="section-header shadow">
        @foreach($employees as $key => $employee)
            <h1>{{ $employee->first_name}}&nbsp;{{__('visitor_report.employee')}}  </h1>
        @endforeach
            {{ Breadcrumbs::render('visitors') }}
        </div>

        <div class="section-body">
            <div class="card shadow">
                <div class="card-body">
                    <form action="{{route('admin.addons.create.post',$ids)}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>{{ __('visitor_report.from_date') }}</label>
                                    <input type="text" name="from_date" class="form-control @error('from_date') is-invalid @enderror datepicker" value="{{ old('from_date', $set_from_date) }}">
                                    @error('from_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>{{ __('visitor_report.to_date') }}</label>
                                    <input type="text" name="to_date" class="form-control @error('to_date') is-invalid @enderror datepicker" value="{{ old('to_date', $set_to_date) }}">
                                    @error('to_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <label for="">&nbsp;</label>
                                <button class="btn btn-primary form-control" type="submit">{{ __('Get Visitors') }}</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            @if($showView)
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                                <i class="far fa-user"></i>
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
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>{{ __('visitor_report.checkin_visitor') }}</h4>
                                </div>
                                <div class="card-body">
                                    {{$checkinVisitor}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning">
                                <i class="fas fa-user-secret"></i>
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
                        <h5>{{ __('Visitors') }}</h5>
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
                                                <td>{{ date('d-m-Y h:i A', strtotime($visitor->checkin_at)) }}</td>
                                                @if($visitor->checkout_at)
                                                    <td>{{ date('d-m-Y h:i A', strtotime($visitor->checkout_at)) }}</td>
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
