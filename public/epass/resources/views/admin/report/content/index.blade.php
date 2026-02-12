@extends('admin.layouts.master')

@section('main-content')

    <section class="section">
        <div class="section-header shadow">
            <h1>{{ __('visitor_report.content_used_report') }}</h1>
            {{ Breadcrumbs::render('visitors') }}
        </div>


        <div class="section-body">
            <div class="card shadow">
                <div class="card-body">
                    <form action="<?=route('admin.content_used_report.post')?>" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>{{ __('visitor_report.from_date') }}</label>
                                    <input type="date" name="from_date" class="form-control @error('from_date') is-invalid @enderror " value="{{ old('from_date', $set_from_date) }}">
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
                                    <input type="date" name="to_date" class="form-control @error('to_date') is-invalid @enderror " value="{{ old('to_date', $set_to_date) }}">
                                    @error('to_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-3">
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
                             </div> 
                             
                            <div class="col-sm-3">
                                <label for="">&nbsp;</label>
                                <button class="btn btn-primary form-control" type="submit">{{ __('visitor_report.get_report') }}</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>


            @if($showView)
                

                <div class="card shadow">
                    <div class="card-header">
                        <h5>{{ __('visitor_report.content_used_report') }}</h5>
                        <button class="btn btn-success btn-sm report-print-button" onclick="printDiv('printablediv')">{{ __('visitor_report.print') }}</button>
                       <button class="btn btn-primary btn-sm  report-excel-button" id="export_button">{{ __('excel') }}</button> 
                    </div>
                    <div class="card-body" id="printablediv">
                        @if(!blank($u_contents))
                            <div class="table-responsive">
                                <table class="table table-striped" id="visitor_data">
                                    <thead>
                                        <tr>
                                            <th>{{ __('levels.id') }}</th>
                                          
                                            <th>{{ __('visitor_report.content') }}</th>
                                            <th>{{ __('visitor_report.vi_d') }}</th>
                                            
                                        </tr>
                                        @php $i =0;@endphp
                                        @foreach($u_contents as $ucontent)
                                            <tr>
                                                <td>{{$i+=1 }}</td>
                                               
                                               
                                                <td>{{$ucontent->content}}</td>
                                                <td>{{$ucontent->cnt}}</td>
                                                
                                    
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


     
    </section>

@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('scripts')
   
     <script src="{{ asset('assets/modules/excel/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/report/visitor/content.js') }}"></script>

@endsection
