@extends('admin.layout.base')

@section('title', 'ONT Uptime')

@section('content')

<style>
    table.dataTable thead th {
        background-color: #d9d9d9f5 !important;
        border-bottom: none !important;
    }
    .buttons-html5 {
        border-radius: 10px;
    }
    table.display tbody tr:hover td {
        background-color: #f1eeeef5 !important;
    }
    .dataTables_scrollBody thead {
        visibility: hidden;
    }
    .filter-box {
        border-radius: 25px;
        height: 30px !important;
    }
    .sample-table {
        font-size: 13px;
        border: 1px solid #ccc;
        margin-bottom: 20px;
    }
      .nav-cstm .nav-link-cstm:not(.active):hover{
        color: #333333 !important;
        border-bottom:3px solid #edf1f2;
        transition: none !important;
    }

    .nav-cstm .nav-link-cstm{
        font-weight: 600;
        color: #636f73 !important;
    }

    .nav-link-cstm.active{
        background-color: transparent !important;
        color: #2b3eb1 !important;
        border-bottom: 3px solid #2b3eb1;        
    }

</style>

<div class="content-area py-1" id="main_content">
    <div class="container-fluid">
        <div class="box box-block bg-white">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade in">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade in">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            {{-- CSV Upload Button --}}
            <a href="#" onclick="document.getElementById('csvUpload').click();" 
               class="btn btn-success pull-right b-a-radius-0-5 mb-2">
               <i class="fa fa-upload"></i> Upload CSV
            </a>

            <form id="csvUploadForm" 
                  action="{{ route('admin.ont-uptime.upload') }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  style="display:none;">
                {{ csrf_field() }}
                <input type="file" name="csv_file" id="csvUpload" accept=".csv" 
                       onchange="document.getElementById('csvUploadForm').submit();">
            </form>

            <h4 class="mb-2">ONT Uptime</h4>
              <ul class="nav nav-pills mb-1 b-b nav-cstm">
              <li class="nav-item mr-0-5">
                <a href="{{ route('admin.ont-uptime') }}" class="nav-link nav-link-cstm pb-1 {{ Route::is('admin.ont-uptime*') ? 'active' : '' }}">Dashboard</a>
              </li>
              <li class="nav-item mr-0-5">
                <a href="{{ route('admin.ont-uptime.index') }}" class="nav-link nav-link-cstm pb-1">ONT Data</a>
              </li>
            
            </ul>
            <form action="{{ route('admin.ont-uptime') }}" method="GET">
            <ul class="nav nav-pills mb-2 pb-1 b-b">
                <li class="nav-item mr-0-75">
                    <input type="month" name="month" id="month"
                    value="{{ @Request::get('month') }}" class="form-control  filter-box filter">
                </li>
                <li class="nav-item mr-0-75">
                    <input class="form-control filter-box filter" type="date" id="from_date" name="fromDate" placeholder="From Date" value="{{ @Request::get('fromDate') }}"  onclick="this.showPicker()">
                </li>
                <li class="nav-item mr-0-75">
                    <input class="form-control filter-box filter" type="date" id="to_date" name="toDate" placeholder="To Date" value="{{ @Request::get('toDate') }}"  onclick="this.showPicker()">
                </li>

                <li class="nav-item mr-0-75 pull-right">
                    <button type="submit" class="form-control btn btn-primary btn-cstm" style="height:30px">Apply</button>
                </li>
            </ul>
            </form>    

            {{-- Data Table --}}
            
                <table class="table row-bordered dataTable nowrap display">
    <thead>
        <tr class="bg-yellow-100">
            <th class="p-2 border">SL.NO</th>
            <th class="p-2 border">GP UPTIME BIFURCATION</th>
            @foreach($groupedData as $date => $values)
                <th class="p-2 border">{{ \Carbon\Carbon::parse($date)->format('d M') }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php $sl=1; @endphp

        {{-- >=98 --}}
        <tr>
            <td class="p-2 border">{{ $sl++ }}</td>
            <td class="p-2 border">GPs with (>=98) to 100%</td>
            @foreach($groupedData as $values)
                <td class="p-2 border text-green-500">{{ $values['>=98'] }}</td>
            @endforeach
        </tr>

        {{-- >=90 --}}
        <tr>
            <td class="p-2 border">{{ $sl++ }}</td>
            <td class="p-2 border">GPs with (>=90) to <98%</td>
            @foreach($groupedData as $values)
                <td class="p-2 border">{{ $values['>=90'] }}</td>
            @endforeach
        </tr>

        {{-- >=75 --}}
        <tr>
            <td class="p-2 border">{{ $sl++ }}</td>
            <td class="p-2 border">GPs with (>=75) to <90%</td>
            @foreach($groupedData as $values)
                <td class="p-2 border">{{ $values['>=75'] }}</td>
            @endforeach
        </tr>
        <tr>
            <td class="p-2 border">{{ $sl++ }}</td>
            <td class="p-2 border">GPs with (>=50) to <75%</td>
            @foreach($groupedData as $values)
                <td class="p-2 border">{{ $values['>=50'] }}</td>
            @endforeach
        </tr>

        <tr>
            <td class="p-2 border">{{ $sl++ }}</td>
            <td class="p-2 border">GPs with (>=20) to <50%</td>
            @foreach($groupedData as $values)
                <td class="p-2 border">{{ $values['>=20'] }}</td>
            @endforeach
        </tr>
         <tr>
            <td class="p-2 border">{{ $sl++ }}</td>
            <td class="p-2 border">GPs with (0) to <20%</td>
            @foreach($groupedData as $values)
                <td class="p-2 border">{{ $values['<20'] }}</td>
            @endforeach
        </tr>

        <tr class="font-weight-bold bg-gray-100">
            <td></td>
            <td>Total</td>
            @foreach($totals as $total)
                <td class="p-2 border">{{ $total }}</td>
            @endforeach
        </tr>

        {{-- >98% Percentage Row --}}
        <tr class="text-primary font-bold">
            <td></td>
            <td>>98%</td>
            @foreach($percentages as $perc)
                <td class="p-2 border">{{ $perc }}%</td>
            @endforeach
        </tr>

    </tbody>
</table>
           

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#table-uptime').DataTable({
            scrollX: true,
            searching: true,
            responsive: false,
            paging: false,
            info: false,
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
        });
    });
</script>
@endsection
