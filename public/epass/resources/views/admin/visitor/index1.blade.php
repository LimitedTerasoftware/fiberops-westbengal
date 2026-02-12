@extends('admin.layouts.master')

@section('main-content')

<section class="section">
    <div class="section-header shadow">
        <h1>{{ __('visitor.visitors') }}</h1>
        {{ Breadcrumbs::render('visitors') }}
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">

                    @can('visitors_create')
                        <div class="row">
                       <div class="col-md-6 col-sm-6">
                        <div class="card-header">
                            <a href="{{ route('admin.visitors.create') }}" class="btn btn-icon icon-left btn-primary"><i
                                    class="fas fa-plus"></i> {{ __('visitor.add_visitor') }}</a>
                        </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                        <div class="card-header">
                            <a href="{{route('check-in.return')}}" class="btn btn-icon icon-left btn-primary"><i
                                    class="fas fa-plus"></i> {{ __('Been Here Before') }}</a>
                        </div>
                        </div>
                       </div>    
                    @endcan
                   <!--filter-->
                   <!--
                   <form action="{route('admin.visitors.filter') }}" method="GET" enctype="multipart/form-data">
                            @csrf
                            
                    <div class="form-group col">
                                        <label for="employee_id" class="col-3">{{ __('visitor.select_employee') }}</label> <span class="text-danger">*</span>
                                        <select id="employee_id " class="col-6" name="employee_id" class="form-control select2 @error('employee_id') is-invalid @enderror">
                                           <option>--Select Center--</option>
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
                                    <button class="btn btn-primary mr-1" type="submit">{{ __('visitor.update') }}</button>
                                
                                </form>
                                -->
                    <!--end filter-->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="maintable"
                                data-url="{{ route('admin.visitors.get-visitors1') }}"
                                data-status="{{ \App\Enums\Status::ACTIVE }}" data-hidecolumn="{{ auth()->user()->can('visitors_show') || auth()->user()->can('visitors_edit') || auth()->user()->can('visitors_delete') }}">
                                <thead>
                                    <tr>
                                        <th>{{ __('levels.id') }}</th>
                                        <th>{{ __('levels.image') }}</th>
                                        <th>{{ __('visitor.visitor_id') }}</th>
                                        <th>{{ __('levels.name') }}</th>
                                        <th>{{ __('visitor.employee') }}</th>
                                        <th>{{ __('visitor.checkin') }}</th>
                                        <th>{{ __('visitor.check_out') }}</th>
                                        <th>{{ __('levels.status') }}</th>
                                        <th class="col-md-2">{{ __('levels.actions') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection



@section('css')
<link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
@endsection

@section('scripts')
<script src="{{ asset('assets/modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/visitor/index.js') }}"></script>
@endsection
