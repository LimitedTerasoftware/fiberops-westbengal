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

<style>
/* ── Stat cards ──────────────────────────────────────────────────── */
.pt-stats-row {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 18px;
}
.pt-stat-card {
    flex: 1;
    min-width: 150px;
    background: #fff;
    border-radius: 10px;
    padding: 14px 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    text-align: center;
}
.pt-stat-card h3 {
    font-size: 26px;
    margin: 0 0 4px;
    font-weight: 700;
    color: #1a1a2e;
}
.pt-stat-card p {
    margin: 0;
    font-size: 13px;
    color: #6c757d;
}
.pt-stat-card .sub-counts {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 6px;
}
.pt-stat-card .sub-item {
    font-size: 12px;
    color: #555;
}
.pt-stat-card .sub-item span {
    font-weight: 700;
}
.pt-card-total   { border-top: 3px solid #4361ee; }
.pt-card-today   { border-top: 3px solid #2ec4b6; }
.pt-card-yest    { border-top: 3px solid #ff9f1c; }

/* ── Issue breakdown card ────────────────────────────────────────── */
.pt-issue-card {
    background: #fff;
    border-radius: 10px;
    padding: 14px 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    margin-bottom: 18px;
}
.pt-issue-card .card-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 12px;
}
.pt-issue-card h6 {
    margin: 0;
    font-weight: 700;
    font-size: 14px;
    color: #333;
}
.pt-issue-date-form {
    display: flex;
    align-items: center;
    gap: 6px;
}
.pt-issue-date-form input[type="date"] {
    border: 1px solid #dde1e7;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 12px;
}
.pt-issue-date-form button {
    padding: 4px 12px;
    font-size: 12px;
    border-radius: 6px;
    border: none;
    background: #4361ee;
    color: #fff;
    cursor: pointer;
}
.pt-issue-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.pt-issue-pill {
    background: #f0f2ff;
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 12px;
    color: #4361ee;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}
.pt-issue-pill .pill-count {
    background: #4361ee;
    color: #fff;
    border-radius: 12px;
    padding: 1px 7px;
    font-size: 11px;
}
.pt-issue-pill.empty {
    background: #f5f5f5;
    color: #aaa;
}
.pt-issue-pill.empty .pill-count {
    background: #ccc;
}

/* ── Export button ───────────────────────────────────────────────── */
.btn-export-excel {
    background: #1d6f42;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.btn-export-excel:hover {
    background: #155434;
    color: #fff;
}
</style>
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
             <div class="filter-group">
             <a href="{{ route('admin.patrollertickets.export', request()->query()) }}" 
                               class="btn-export-excel" title="Export to Excel / CSV">
                                <i class="fa fa-file-excel-o"></i> Export
                            </a>
            </div>
        </div>
    </form>
</div>
        {{-- ─────────────── WIDGET 1: Totals ─────────────── --}}
            <div class="pt-stats-row">
                <div class="pt-stat-card pt-card-total">
                    <h3>{{ $totalCount }}</h3>
                    <p>Total Tickets</p>
                    <div class="sub-counts">
                        <div class="sub-item">Today <span style="color:#2ec4b6">{{ $todayCount }}</span></div>
                        <div class="sub-item">Yesterday <span style="color:#ff9f1c">{{ $yesterdayCount }}</span></div>
                    </div>
                </div>
                <div class="pt-stat-card pt-card-today">
                    <h3>{{ $todayCount }}</h3>
                    <p>Today's Tickets</p>
                </div>
                <div class="pt-stat-card pt-card-yest">
                    <h3>{{ $yesterdayCount }}</h3>
                    <p>Yesterday's Tickets</p>
                </div>
            </div>

            {{-- ─────────────── WIDGET 2: Issue Breakdown ─────────────── --}}
            <div class="pt-issue-card">
                <div class="card-header-row">
                    <h6><i class="fa fa-bar-chart text-primary"></i>&nbsp; Issues Breakdown</h6>
                    <form method="GET" action="{{ route('admin.patrollertickets') }}" id="stat-date-form">
                        {{-- carry over all current filters --}}
                        @foreach(request()->except('stat_date') as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <div class="pt-issue-date-form">
                            <label style="font-size:12px;margin:0;">From Date:</label>
                            <input type="date" name="stat_date" value="{{ $statDate }}">
                            <label style="font-size:12px;margin:0;">To Date:</label>
                            <input type="date" name="end_date" value="{{ $endDate }}">
                            <button type="submit">Go</button>
                        </div>
                    </form>
                </div>

                <div class="pt-issue-pills">
                    @if(count($issueCounts) > 0)
                        @foreach($issueCounts as $issue => $cnt)
                            <div class="pt-issue-pill{{ $cnt == 0 ? ' empty' : '' }}">
                                {{ $issue ?: 'Unspecified' }}
                                <span class="pill-count">{{ $cnt }}</span>
                            </div>
                        @endforeach
                    @else
                        <span style="font-size:13px;color:#aaa;">No tickets found for {{ \Carbon\Carbon::parse($statDate)->format('d M Y') }}.</span>
                    @endif
                </div>
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
                            <th>Ticket Id</th>
                            <th>Status</th>
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
                                <td>{{$ticket->ticket_id}}</td>
                                <td>{{$ticket->status}}</td>
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



$('#tickets-table').DataTable({
    responsive: false,
    paging: false,
    info: false,
    dom: 'Bfrtip',
    scrollX: true,
    autoWidth: false,
    // buttons: [
    //     'copyHtml5',
    //     'excelHtml5',
    //     'csvHtml5',
    //     'pdfHtml5'
    // ]
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
