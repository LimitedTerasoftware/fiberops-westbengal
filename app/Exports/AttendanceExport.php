<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function($p) {
            return [
                'Date'       => $p->date,
                'Check In'   => $p->check_in,
                'Check Out'  => $p->check_out,
                'Duration'   => $p->duration,
                'Status'     => $p->attendance_status,
                'Tickets'    => $p->total_tickets,
                'Completed'  => $p->completed_tickets,
                'Distance'   => $p->total_distance,
                'Images'     => $p->images,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Check In',
            'Check Out',
            'Duration',
            'Status',
            'Tickets',
            'Completed',
            'Distance',
            'Images',
        ];
    }
}

