@extends('admin.layout.base')

@section('title', 'Raise Tickets')

@section('content')
@php
    $roles = [
        1 => 'OFC',
        2 => 'FRT',
        5 => 'Patroller',
        3 => 'Zonal incharge',
        4 => 'District incharge'
    ];
    $user = Session::get('user');
    $DistId = null; 
    if ($user && isset($user->district_id)) {
        $DistId = $user->district_id;
    }
@endphp
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">

            <h5 class="mb-1">
                Raise Tickets
            </h5>
      <div class="filters-section mb-4">
       <form method="GET" action="{{ route('admin.patrollertickets') }}">

        <div class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">Zone</label>
                <select name="zone_id" id = 'zone_id'class="filter-select">
                    <option value="">Select Zone</option>
                    @foreach($zonals as $zon)
                        <option value="{{ $zon->id }}" {{ request('zone_id') == $zon->id ? 'selected' : '' }}>
                            {{ $zon->Name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">District</label>
                <select name="district_id" id ="district_id" class="filter-select">
                    <option value="">Select District</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" 
                        {{ (request('district_id') == $district->id) || ($DistId && $DistId == $district->id) ? 'selected' : '' }}>

                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Block</label>
                <select name="block_id" id="block_id" class="filter-select">
                    <option value="">Select Block</option>
                    @foreach($blocks as $block)
                        <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>
                            {{ $block->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Issue</label>
                <select name="issue_type"  class="filter-select">
                    <option value="">Select Issue</option>
                    @foreach($serviceType as $type)
                    <option value="{{$type->name}}" {{request('issue_type') == $type->name ? "selected":""}}>{{$type->name}}</option>
                    @endforeach
                </select>

            </div>
            <div class="filter-group">
                <label class="filter-label">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-input">
            </div>
            <div class="filter-group">
                <label class="filter-label">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-input">
            </div>
            <div class="filter-group" style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="action-btn action-btn-primary">Apply</button>
                        <a href="{{ route('admin.patrollertickets') }}" class="action-btn action-btn-secondary">Clear</a>
            </div>
        </div>
    </form>
</div>
            <div class="table-responsive">

                <table class="table table-striped table-bordered dataTable" id="tickets-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patroller</th>
                            <th>Contact</th>
                            <th>GP Name</th>
                            <th>Date & Time</th>
                            <th>Issue</th>
                            <th>Priority</th>
                            <th>Map</th>
                            <th>Coordinates</th>
                            <th>Image</th>
                            <th>Details</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php($page = ($pagination->currentPage-1)*$pagination->perPage)
                        @foreach($tickets as $index => $ticket)
                            @php($page++)
                            <tr>
                                <td>{{ $page }}</td>

                                <td>
                            {{ $ticket->patroller_name ?? 'N/A' }}
                            </td>

                            <td>
                            {{ $ticket->patroller_mobile ?? 'N/A' }}
                            </td>
                                <td>{{ $ticket->gp_name }}</td>

                                <td>
                                    {{ $ticket->date }}<br>
                                    <small>{{ $ticket->time }}</small>
                                </td>

                                <td>
                                    <strong>{{ $ticket->issue_type }}</strong><br>
                                    <small>{{ $ticket->issue_sub_type }}</small>
                                </td>

                                <td>
                                    @if($ticket->priority == 'High')
                                        <span class="label label-danger">High</span>
                                    @elseif($ticket->priority == 'Medium')
                                        <span class="label label-warning">Medium</span>
                                    @else
                                        <span class="label label-success">Low</span>
                                    @endif
                                </td>

                                <td>
                                    @if($ticket->latitude && $ticket->longitude)
                                        <a target="_blank"
                                        href="https://www.google.com/maps?q={{ $ticket->latitude }},{{ $ticket->longitude }}">
                                        View Map
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            <td>
                                <?php
                                $latlongs = json_decode($ticket->attachment_latlong, true);

                                if (is_array($latlongs)) {
                                    foreach ($latlongs as $item) {

                                        // remove [, ], quotes, spaces
                                        $clean = str_replace(['[', ']', '"', "'"], '', $item);
                                        $clean = preg_replace('/\s+/', '', $clean);

                                        // final safety check
                                        if (str_contains($clean, ',')) {
                                            echo htmlspecialchars($clean) . '<br>';
                                        }
                                    }
                                } else {
                                    echo '-';
                                }
                                ?>
                                </td>


                                <td>
                                <?php
                                $images = json_decode($ticket->attachments, true);
                                $latlongs = json_decode($ticket->attachment_latlong, true);
                                ?>

                                @if(is_array($images) && count($images) > 0)
                                <a href="javascript:void(0)"
                                onclick='showImages(
                                        <?php echo json_encode($images); ?>,
                                        <?php echo json_encode($latlongs); ?>
                                )'>
                                View Images ({{ count($images) }})
                                </a>
                                @else
                                No Image
                                @endif

                            </td>

                                <td title="{{ $ticket->details }}">
                                {{ str_limit($ticket->details, 40) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @include('common.pagination')

        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Ticket Image</h4>
            </div>
                       <div class="modal-body">
                        <div id="imageGallery" class="row"></div>
                         <hr>
                         <div class="text-center">
                      <img id="modalImage" class="img-responsive img-thumbnail" style="margin:auto;">
                     </div>
                  </div>
        </div>
    </div>
</div>

@endsection
<link rel="stylesheet" href="{{ asset('/css/filter.css')}}">
<style>
    #tickets-table {
    width: 100% !important;
}

.dataTables_wrapper {
    overflow-x: auto;
}

</style>
@section('scripts')
<script>
function showImage(src){
    $('#modalImage').attr('src', src);
    $('#imageModal').modal('show');
}

function showImages(images, latlongs)
{
    $('#imageGallery').html('');
    $('#modalImage').attr('src', '');

    images.forEach(function(path, index) {

        var imgUrl = "{{ asset('') }}" + path;
        var latlongText = '-';

        if (latlongs && latlongs[index]) {
            latlongText = latlongs[index]
                .replace('[','')
                .replace(']','')
                .replace(/\s+/g,'');
        }

        $('#imageGallery').append(
            '<div class="col-xs-4 col-md-3" style="margin-bottom:15px;">' +
                '<img src="'+imgUrl+'" class="img-thumbnail" ' +
                'style="cursor:pointer;height:80px;width:100%;" ' +
                'onclick="selectImage(\''+imgUrl+'\')">' +
                '<div style="font-size:11px;margin-top:4px;text-align:center;">' +
                    latlongText +
                '</div>' +
            '</div>'
        );

        if(index === 0){
            $('#modalImage').attr('src', imgUrl);
        }
    });

    $('#imageModal').modal('show');
}

function selectImage(src)
{
    $('#modalImage').attr('src', src);
}

jQuery.fn.DataTable.Api.register('buttons.exportData()', function () {
    var data = [];
    $.ajax({
        url: "{{ url('admin/tickets') }}?page=all",
        async: false,
        success: function (result) {
            $.each(result.data, function (i, d) {
                data.push([
                    d.id,
                    d.patroller_name,
                    d.patroller_mobile,
                    d.gp_name,
                    d.issue_type,
                    d.issue_sub_type,
                    d.priority,
                    d.date + ' ' + d.time
                ]);
            });
        }
    });

    return {
        header: [
            "ID",
            "Patroller Name",
            "Mobile",
            "GP Name",
            "Issue Type",
            "Sub Issue",
            "Priority",
            "Date & Time"
        ],
        body: data
    };
});

$('#tickets-table').DataTable({
    responsive: false,
    paging: false,
    info: false,
    dom: 'Bfrtip',
    scrollX: true,
    autoWidth: false,
    buttons: [
        'copyHtml5',
        'excelHtml5',
        'csvHtml5',
        'pdfHtml5'
    ]
});
</script>
<script>
$(function () {

    // ===== ZONE ? DISTRICT =====
    $('#zone_id').on('change', function () {

        let zoneId = $(this).val();
        $('#district_id').html('<option value="">All Districts</option>');
        $('#block_id').html('<option value="">All Blocks</option>');

        if (!zoneId) return;

        $.get("{{ url('admin/get_districts') }}/" + zoneId, function (res) {
            let h = '<option value="">All Districts</option>';
            res.forEach(d => {
                h += `<option value="${d.id}">${d.name}</option>`;
            });
            $('#district_id').html(h);
        });
    });

    // ===== DISTRICT ? BLOCK =====
    $('#district_id').on('change', function () {

        let districtId = $(this).val();
        $('#block_id').html('<option value="">All Blocks</option>');

        if (!districtId) return;

        $.get("{{ url('admin/get_blocks') }}/" + districtId, function (res) {
            let h = '<option value="">All Blocks</option>';
            res.forEach(b => {
                h += `<option value="${b.id}">${b.name}</option>`;
            });
            $('#block_id').html(h);
        });
    });

});
</script>
@endsection
