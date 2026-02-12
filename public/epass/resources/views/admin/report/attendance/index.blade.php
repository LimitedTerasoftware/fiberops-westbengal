@extends('admin.layouts.master')

@section('main-content')
<style type="text/css">
    table.dataTable thead th {
        background-color: #d9d9d9f5 !important;
        border-bottom: none !important;
    }
    .buttons-html5{
        border-radius: 10px;
/*        margin-right: 6px;*/
    }
    table.display tbody tr:hover td{
        background-color: #f1eeeef5 !important;
    }
    .dataTables_scrollBody thead {
        visibility: hidden;
    }
    select.select-box:not([size]):not([multiple]), input.select-box{
        height: 35px;
    }
</style>


<section class="section">
    <div class="section-header shadow">
        <h1>{{ __('attendance_report.attendance_report') }}</h1>
        {{ Breadcrumbs::render('attendance') }}
    </div>

    <div class="section-body">
        <div class="card shadow">
            <div class="card-body">
                <form action="<?=route('admin.attendance-report.post')?>" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>{{ __('attendance_report.from_date') }}</label>
                                <input type="date" name="from_date" class="form-control @error('from_date') is-invalid @enderror" value="{{ old('from_date', $set_from_date) }}" >
                                @error('from_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>{{ __('attendance_report.to_date') }}</label>
                                <input type="date" name="to_date" class="form-control @error('to_date') is-invalid @enderror" value="{{ old('to_date', $set_to_date) }}">
                                @error('to_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label>{{ __('visitor_report.employee') }}</label>
                            <select id="employee_id" name="employee_id" class="form-control select2 @error('employee_id') is-invalid @enderror">
                                <option value="">--Select Center--</option>
                                <option value="2">Admin</option>
                                <option value="4">Anjaneya Badavane Center</option>
                                <option value="7">DCM Layout Center</option>
                                <option value="5">Nijalingappa Badavane Center</option>
                                <option value="6">Silver Jubilee Center</option>
                                <option value="3">Stadium Center</option>
                            </select>
                            @error('employee_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>  
                        <div class="col-sm-3">
                            <label for="">&nbsp;</label>
                            <button class="btn btn-primary form-control" type="submit">{{ __('attendance_report.get_report') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($showView)
            <div class="card shadow">
                <div class="card-header">
                    <h5>{{ __('attendance_report.attendance_report') }}</h5>
                </div>
                <div class="card-body" id="printablediv">
                    @if(!blank($attendances))
                        <div class="table-responsive">
                            <table class="table row-bordered dataTable nowrap display" id="attendances-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>{{ __('levels.id') }}</th>
                                        <th>{{ __('attendance_report.user') }}</th>
                                        <th>{{ __('attendance_report.working') }}</th>
                                        <th>{{ __('attendance_report.date') }}</th>
                                        <th>{{ __('attendance_report.clock_in') }}</th>
                                        <th>{{ __('attendance_report.clock_out') }}</th>
                                        <th>{{ __('levels.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 0; @endphp
                                    @foreach($attendances as $attendance)
                                        <tr>
                                            <td>{{$i += 1 }}</td>
                                            <td>{{ Str::limit(optional($attendance->user)->name, 50)}}</td>
                                            <td>{{ Str::limit($attendance->title, 30) }}</td>
                                            <td>{{$attendance->date}}</td>
                                            @if ($attendance->checkin_time)
                                                <td>{{$attendance->checkin_time}}</td>
                                            @else
                                                <td>{{ __('attendance_report.n/a') }}</td>
                                            @endif
                                            @if ($attendance->checkout_time)
                                                <td>{{$attendance->checkout_time}}</td>
                                            @else
                                                <td>{{ __('attendance_report.n/a') }}</td>
                                            @endif
                                            <td>
                                                <form class="float-left pl-2" action="{{route('admin.attendance.destroy', $attendance)}}" method="POST" enctype="multipart/form-data"> 
                                                    @csrf  @method('DELETE')
                                                    <button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"> <i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                    @else
                        <h4 class="text-danger">{{ __('attendance_report.data_not_found') }}</h4>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>

@endsection

@section('css')

<link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/modules/buttons/buttons.dataTables.min.css') }}">

@endsection

@section('scripts')

<script src="{{ asset('assets/modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>

<script src="{{ asset('assets/modules/buttons/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/modules/buttons/jszip.min.js') }}"></script>
<script src="{{ asset('assets/modules/buttons/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/modules/buttons/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/modules/buttons/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/modules/buttons/buttons.print.min.js') }}"></script>


<script type="text/javascript">
    $(document).ready(function() {
        $('#attendances-table').DataTable({
            scrollX: true,
            searching: true,
            paging: true, // Enable pagination
            info: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copyHtml5',
                    filename: 'Attendance_Report', // Specify the filename here
                },
                {
                    extend: 'excelHtml5',
                    filename: 'Attendance_Report', // Specify the filename here
                },
                {
                    extend: 'csvHtml5',
                    filename: 'Attendance_Report', // Specify the filename here
                },
                {
                    extend: 'pdfHtml5',
                    filename: 'Attendance_Report', // Specify the filename here
                }
            ],
            // Add pagination settings
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "pageLength": 10, // Set default page length
            "pagingType": "full_numbers" 
        });
    });
</script>

@endsection
