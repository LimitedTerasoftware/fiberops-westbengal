@extends('admin.layout.base')

@section('title', 'Joint Enclosure Tickets')

@section('content')

<div class="content-area py-3" id="main_content">
    <div class="container-fluid">

        <!-- ================= FILTER BAR ================= -->
        <div class="filter-bar">
            <select id="zone_id">
                <option value="">All Zones</option>
                @foreach($zonals as $z)
                    <option value="{{ $z->id }}">{{ $z->Name }}</option>
                @endforeach
            </select>

            <select id="district_id">
                <option value="">All Districts</option>
            </select>

            <select id="role_id">
                <option value="">All Roles</option>
                <option value="2">FRT</option>
                <option value="5">Patroller</option>
            </select>

            <input type="date" id="from_date">
            <input type="date" id="to_date">

            <select id="generated_type">
                <option value="">Generated</option>
                <option value="Auto">Auto</option>
                <option value="Manual">Manual</option>
            </select>

            <select id="purpose">
                <option value="">Purpose</option>
                <option value="ROUTE SURVEY">Route Survey</option>
                <option value="Route Patrolling">Route Patrolling</option>
            </select>
        </div>

        <!-- ================= TABLE ================= -->
        <div class="table-card mt-3">
            <table id="joint-table" class="table table-borderless w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ticket</th>
                        <th>Employee</th>
                        <th>Location</th>
                        <th>Issue</th>
                        <th>Timeline</th>
                        <th>Before Image Latlong</th>
                        <th>After Image Latlong</th>
                        <th>Joint Enclosure</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>
@endsection

{{-- ================= SCRIPTS ================= --}}
@section('scripts')
<script>
$(function () {

    let table = $('#joint-table').DataTable({
        paging: true,
        pageLength: 20,
        searching: false,
        ordering: false,
        ajax: {
            url: "{{ route('admin.joint_enclouser_tickets') }}",
            data: function (d) {
                d.zone_id        = $('#zone_id').val();
                d.district_id    = $('#district_id').val();
                d.role_id        = $('#role_id').val();
                d.from_date      = $('#from_date').val();
                d.to_date        = $('#to_date').val();
                d.generated_type = $('#generated_type').val();
                d.purpose        = $('#purpose').val();
            },
            dataSrc: 'data'
        },
        columns: [

            { data: null, render: (d,t,r,m)=>m.row+1 },

            { data: 'booking_id' },

            {
                data: null,
                render: r => `
                    <div class="emp">
                        <b>${r.first_name} ${r.last_name}</b>
                        <div class="muted">${r.mobile} � ${r.type==2?'FRT':'Patroller'}</div>
                    </div>`
            },

            {
                data: null,
                render: r => `
                    <div class="muted">
                        ${r.zone_name}<br>
                        ${r.district} / ${r.mandal}<br>
                        ${r.gpname}
                    </div>`
            },

            {
                data: null,
                render: r => `
                    <b>${r.downreason}</b>
                    <div class="muted">${r.purpose}</div>`
            },

            {
                data: null,
                render: r => `
                    <div class="muted">
                        Down: ${r.downdate} ${r.downtime}<br>
                        Closed: ${r.finished_at}
                    </div>`
            },
            {
                data:null,
                render:r=>`
                <div>
                  ${r.joint_enclosurebefore_latlong}
                </div>`
            },
              {
                data:null,
                render:r=>`
                <div>
                  ${r.joint_enclosureafter_latlong}
                </div>`
            },

            {
                data: null,
                render: function (r) {

                    let b = getImages(r, 'joint_enclouser_beforeimg');
                    let a = getImages(r, 'joint_enclouser_afterimg');

                    let html = '<div class="img-row">';

                    b.forEach(i => {
                        if (!i) return;

                        const file = encodeURIComponent(i);

                        html += `
                            <a href="{{ asset('uploads/SubmitFiles') }}/${file}" target="_blank">
                                <img src="{{ asset('uploads/SubmitFiles') }}/${file}" class="img before">
                            </a>`;
                    });

                    a.forEach(i => {
                        if (!i) return;

                        const file = encodeURIComponent(i);

                        html += `
                            <a href="{{ asset('uploads/SubmitFiles') }}/${file}" target="_blank">
                                <img src="{{ asset('uploads/SubmitFiles') }}/${file}" class="img after">
                            </a>`;
                    });

                    return html + '</div>';
                }
            },


            {
                data: null,
                render: r => `
                    <a href="{{ url('admin/requests') }}/${r.urid}" 
                       target="_blank" 
                       class="btn btn-sm btn-outline-primary">
                       Details
                    </a>`
            }
        ]
    });

    $('#zone_id,#district_id,#role_id,#from_date,#to_date,#generated_type,#purpose')
        .on('change', ()=>table.ajax.reload());

    $('#zone_id').on('change', function () {
        let id = $(this).val();
        if(!id){ $('#district_id').html('<option value="">All Districts</option>'); return; }
        $.get("{{ url('admin/get_districts') }}/"+id, r=>{
            let h='<option value="">All Districts</option>';
            r.forEach(d=>h+=`<option value="${d.id}">${d.name}</option>`);
            $('#district_id').html(h);
        });
    });

});
function getImages(documents, field) {
    if (!documents || !documents[field]) {
        return [];
    }

    let raw = documents[field];

    // try JSON first
    try {
        let parsed = JSON.parse(raw);
        if (Array.isArray(parsed)) {
            return parsed;
        }
    } catch (e) {
        // not JSON
    }

    // fallback for old comma-separated data
    return raw.split(',');
}

</script>
@endsection

{{-- ================= STYLES ================= --}}
@section('styles')
<style>
body { font-family: Inter, system-ui, sans-serif; }

.filter-bar {
    display:flex; flex-wrap:wrap; gap:10px;
    background:#fff; padding:12px; border-radius:10px;
    border:1px solid #e5e7eb;
}
.filter-bar select, .filter-bar input {
    padding:6px 10px; font-size:13px;
    border:1px solid #d1d5db; border-radius:6px;
}

.table-card {
    background:#fff; border-radius:12px;
    border:1px solid #e5e7eb;
}

table thead th {
    font-size:13px; color:#374151;
    border-bottom:1px solid #e5e7eb;
}

table tbody td {
    font-size:13px;
    padding:14px 10px;
    vertical-align:top;
    border-bottom:1px solid #f1f5f9;
}

.emp b { font-size:14px; }
.muted { font-size:12px; color:#6b7280; }

.img-row {
    display:flex; gap:6px; flex-wrap:wrap;
}

.img {
    width:42px; height:42px;
    border-radius:6px; object-fit:cover;
    border:1px solid #e5e7eb;
}

.img.before { outline:2px solid #fecaca; }
.img.after  { outline:2px solid #bbf7d0; }
</style>
@endsection
