<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected $attendances;

    public function __construct($attendances)
    {
        $this->attendances = $attendances;
    }

    public function collection()
    {
        // Add a serial number to each attendance record
        $serialNumber = 1;
        return $this->attendances->map(function ($attendance) use (&$serialNumber) {
            return [
                'No' => $serialNumber++,
                'center' => $attendance->user->name ?? 'N/A',
                'working' => $attendance->title ?? 'N/A',
                'date' => $attendance->date ?? 'N/A',
                'clock_in' => $attendance->checkin_time ?? 'N/A',
                'clock_out' => $attendance->checkout_time ?? 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        // Define the column headings
        return [
            'No',
            'Center',
            'Working',
            'Date',
            'Clock In',
            'Clock Out',
        ];
    }
}
