@extends('admin.layouts.master')

@section('main-content')

<section class="section">
    <div class="section-header shadow">
        <h1>{{ __('attendance.attendance') }}</h1>
        {{ Breadcrumbs::render('attendance') }}
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card-header shadow">
                    <!-- Your existing HTML content -->
                </div>
                <div class="card shadow">
                    <div class="card-body">
                    @if(!blank($attendances))
                        <div class="table-responsive">
                            <table class="table table-striped" id="attendanceTable">
                                <thead>
                                    <tr>
                                        <th>{{ __('levels.id') }}</th>
                                        <th>{{ __('levels.image') }}</th>
                                        <th>{{ __('attendance.user') }}</th>
                                        <th>{{ __('attendance.working') }}</th>
                                        <th>{{ __('attendance.date') }}</th>
                                        <th>{{ __('attendance.clock_in') }}</th>
                                        <th>{{ __('attendance.clock_out') }}</th>
                                        <th>{{ __('levels.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 0; @endphp
                                    @foreach($attendances as $attendance)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td><figure class="avatar mr-2"><img src="{{ asset('images/img/'.$attendance->img) }}" alt=""></figure></td>
                                        <td>{{ Str::limit($attendance->first_name.' '.$attendance->last_name, 50) }}</td>
                                        <td>{{ Str::limit($attendance->title, 30) }}</td>
                                        <td>{{ $attendance->date }}</td>
                                        @if ($attendance->checkin_time)
                                        <td>{{ $attendance->checkin_time }}</td>
                                        @else
                                        <td>{{ __('attendance_report.n/a') }}</td>
                                        @endif
                                        @if ($attendance->checkout_time)
                                        <td>{{ $attendance->checkout_time }}</td>
                                        @else
                                        <td>{{ __('attendance_report.n/a') }}</td>
                                        @endif
                                        <td>
                                            <form class="float-left pl-2" action="{{ route('admin.attendance.destroy', $attendance->id) }}" method="POST" enctype="multipart/form-data"> 
                                                @csrf  
                                                @method('DELETE')
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
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <!-- Modal content -->
    </div>
</div>

@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
@endsection

@section('scripts')
<script src="{{ asset('assets/modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/attendance/index.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#attendanceTable').DataTable({
            "paging": true, // Enable pagination
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "paginate": {
                    "previous": "<i class='fas fa-angle-double-left'></i>",
                    "next": "<i class='fas fa-angle-double-right'></i>"
                }
            }
        });
    });
</script>
@endsection
