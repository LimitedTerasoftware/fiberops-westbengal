@extends('admin.layout.base')

@section('title', 'ONT Uptime')

@section('content')

<style type="text/css">
    table.dataTable thead th {
        background-color: #d9d9d9f5 !important;
        border-bottom: none !important;
    }
    .buttons-html5{
        border-radius: 10px;
    }
    table.display tbody tr:hover td{
        background-color: #f1eeeef5 !important;
    }
    .dataTables_scrollBody thead {
        visibility: hidden;
    }
    .filter-box{
        border-radius: 25px;
        height: 30px !important;
    }
</style>

<div class="content-area py-1" id="main_content">
    <div class="container-fluid">
        <div class="box box-block bg-white">
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

            <form id="csvUploadForm" action="{{ route('admin.ont-uptime.upload') }}" method="POST" enctype="multipart/form-data" style="display:none;">
                {{ csrf_field() }}
                <input type="file" name="csv_file" id="csvUpload" accept=".csv" onchange="document.getElementById('csvUploadForm').submit();">
            </form>

            <h4 class="mb-2">ONT Uptime Records</h4>

            {{-- Table --}}
            @if(count($records) > 0)
            <table class="table row-bordered dataTable nowrap display" id="table-uptime" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>LGD Code</th>
                        <th>Uptime %</th>
                        <th>Record Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                     $i = 1;
                    @endphp
                    @foreach($records as $record)
                        <tr>
                            <td>{{ $i}}</td>
                            <td>{{ $record->lgd_code }}</td>
                            <td>{{ $record->uptime_percent }}</td>
                            <td>{{ \Carbon\Carbon::parse($record->record_date)->format('d/m/Y') }}</td>
                            <td>
                                <!-- Edit Button -->
                               
                                <a href="javascript:void(0)" 
                                    class="btn btn-sm btn-primary edit-btn" 
                                    data-id="{{ $record->id }}">
                                   <i class="ti-pencil"></i> Edit
                                </a>


                                <!-- Delete Button -->
                                <form action="{{ route('admin.ont-uptime.delete', $record->id) }}" method="POST" style="display:inline;">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                                       <i class="ti-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                         @php
                            $i++;
                        @endphp
                    @endforeach
                     
                </tbody>
            </table>
            @else
                <h6 class="no-result">No ONT Uptime Records found</h6>
            @endif

        </div>
            <!-- Edit Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                
                <div class="modal-header">
                    <h5 class="modal-title">Edit Uptime Record</h5>
                </div>

                <div class="modal-body">
                    <form id="editForm">
                        {{ csrf_field() }}
                        <input type="hidden" id="record_id">

                        <div class="mb-3">
                            <label>LGD Code</label>
                            <input type="text" id="lgd_code" name="lgd_code" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Record Date</label>
                            <input type="date" id="record_date" name="record_date" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Uptime %</label>
                            <input type="text" id="uptime_percent" name="uptime_percent" class="form-control">
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="saveBtn">Update</button>
                </div>

                </div>
            </div>
            </div>

    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#table-uptime').DataTable({
            scrollX: true,
            searching: true,
            responsive: false,
            paging:true,
            info: false,
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
        });
   $('.datepicker').datepicker({
        format: "dd/mm/yyyy",
        autoclose: true,
        todayHighlight: true
    });

    });
     
    $(document).ready(function () {

    // Open modal & load data
    $('.edit-btn').on('click', function () {
        let id = $(this).data('id');

        $.get("{{ url('admin/ont-uptime/edit') }}/" + id, function (data) {
            $('#record_id').val(data.id);
            $('#lgd_code').val(data.lgd_code);
            $('#record_date').val(data.record_date.split(' ')[0]);
            $('#uptime_percent').val(data.uptime_percent);
            $('#editModal').modal('show');
        });
    });

    // Save updated data
    $('#saveBtn').on('click', function () {
        let id = $('#record_id').val();
        let formData = {
            _token: "{{ csrf_token() }}",
            lgd_code: $('#lgd_code').val(),
            record_date: $('#record_date').val(),
            uptime_percent: $('#uptime_percent').val()
        };

        $.post("{{ url('admin/ont-uptime/update') }}/" + id, formData, function (response) {
            if (response.success) {
                location.reload(); 
            }
        });
    });

});

</script>
@endsection
