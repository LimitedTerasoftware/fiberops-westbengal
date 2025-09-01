<?php 

namespace App\Services;

use Illuminate\Http\Request;
use Validator;
use Exception;
use DateTime;
use Auth;
use Lang;
use Setting;
use App\Models\OntUptime;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;



class OntUptimeImport{
    public function model(array $row)
    {
        return new OntUptime([
            'lgd_code'       => $row['lgd_code'],  
            'date'           => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'])->format('Y-m-d'),
            'uptime_percent' => $row['uptime_percent'],
        ]);
    }
  }
