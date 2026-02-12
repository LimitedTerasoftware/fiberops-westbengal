<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BackendController;
use App\Models\Employee;
use App\Models\Attendance;
use App\Exports\AttendanceExport;
use App\Models\VisitingDetails;
use Illuminate\Http\Request;
use App\Enums\Status;
use Maatwebsite\Excel\Facades\Excel;


class AttendanceReportController extends BackendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['siteTitle'] = 'Attendance Report';
        $this->data['employees'] = Employee::where('status', Status::ACTIVE)->get();
        $this->middleware(['permission:attendance-report'])->only('index');
    }

    public function index(Request $request)
{
    $this->data['showView'] = true;
    $this->data['set_from_date'] = '';
    $this->data['set_to_date'] = '';

    // Store filter parameters in session
    $request->session()->put('from_date', $request->from_date);
    $request->session()->put('to_date', $request->to_date);
    $request->session()->put('employee_id', $request->employee_id);

    $attendances = Attendance::orderBy('id', 'DESC');

    if ($request->has('from_date') && $request->has('to_date')) {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $this->data['set_from_date'] = $request->from_date;
        $this->data['set_to_date'] = $request->to_date;

        if ($request->from_date && $request->to_date) {
            $fromDate = date('Y-m-d', strtotime($request->from_date)) . ' 00:00:00';
            $toDate = date('Y-m-d', strtotime($request->to_date)) . ' 23:59:59';
            $attendances->whereBetween('date', [$fromDate, $toDate]);
        }
    }

    if ($request->filled('employee_id')) {
        $attendances->where('user_id', $request->employee_id);
    }

    $this->data['attendances'] = $attendances->get();


    return view('admin.report.attendance.index', $this->data);
}
public function export1(Request $request)
{
    // Retrieve filter parameters from session
    $fromDate = $request->session()->get('from_date');
    $toDate = $request->session()->get('to_date');
    $employeeId = $request->session()->get('employee_id');

    // Build query based on filter parameters
    $attendances = Attendance::orderBy('id', 'DESC');

    if ($fromDate && $toDate) {
        $fromDateTime = date('Y-m-d', strtotime($fromDate)) . ' 00:00:00';
        $toDateTime = date('Y-m-d', strtotime($toDate)) . ' 23:59:59';
        $attendances->whereBetween('date', [$fromDateTime, $toDateTime]);
    }

    if ($employeeId) {
        $attendances->where('user_id', $employeeId);
    }

    // Fetch filtered data
    $attendances = $attendances->get();

    // Format the data for export
    $exportData = $attendances->map(function ($attendance) {
        return [
            'user' => $attendance->user->name ?? 'N/A',
            'working' => $attendance->title ?? 'N/A',
            'date' => $attendance->date ?? 'N/A',
            'clock_in' => $attendance->checkin_time ?? 'N/A',
            'clock_out' => $attendance->checkout_time ?? 'N/A',
        ];
    });

    // Return the attendance data in JSON format
    return response()->json($exportData);
}

public function export(Request $request)
{

    // Retrieve filter parameters from session
    $fromDate = $request->session()->get('from_date');
    $toDate = $request->session()->get('to_date');
    $employeeId = $request->session()->get('employee_id');

    // Build query based on filter parameters
    $attendances = Attendance::orderBy('id', 'DESC');

    if ($fromDate && $toDate) {
        $fromDateTime = date('Y-m-d', strtotime($fromDate)) . ' 00:00:00';
        $toDateTime = date('Y-m-d', strtotime($toDate)) . ' 23:59:59';
        $attendances->whereBetween('date', [$fromDateTime, $toDateTime]);
    }

    if ($employeeId) {
        $attendances->where('user_id', $employeeId);
    }

    // Fetch filtered data
    $attendances = $attendances->get();
    return Excel::download(new AttendanceExport($attendances), 'attendance_report.xlsx');
    
}


}
