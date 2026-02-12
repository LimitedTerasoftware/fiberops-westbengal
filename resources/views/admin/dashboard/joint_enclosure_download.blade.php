@extends('admin.layout.base')

@section('title', 'Joint Enclosure Image Download')

@section('content')
<div class="content-area py-3">
    <div class="container-fluid">

        <div class="card p-3 shadow-sm">
            <h5 class="mb-3">Download Joint Enclosure Images</h5>

            {{-- ? ERROR MESSAGE --}}
            @if(session('error'))
                <div class="alert alert-warning">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ? FORM START --}}
            <form method="GET" action="{{ route('admin.joint_enclouser_img_download') }}">

                <div class="row g-2">

                    {{-- ZONE --}}
                    <div class="col-md-2">
                        <select name="zone_id" id="zone_id" class="form-control">
                            <option value="">All Zones</option>
                            @foreach($zonals as $z)
                                <option value="{{ $z->id }}"
                                    {{ request('zone_id') == $z->id ? 'selected' : '' }}>
                                    {{ $z->Name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- DISTRICT --}}
                    <div class="col-md-2">
                        <select name="district_id" id="district_id" class="form-control">
                            <option value="">All Districts</option>
                        </select>
                    </div>

                    {{-- BLOCK --}}
                    <div class="col-md-2">
                        <select name="block_id" id="block_id" class="form-control">
                            <option value="">All Blocks</option>
                        </select>
                    </div>

                    {{-- FROM DATE --}}
                    <div class="col-md-2">
                        <input type="date"
                               name="from_date"
                               class="form-control"
                               value="{{ request('from_date') }}">
                    </div>

                    {{-- TO DATE --}}
                    <div class="col-md-2">
                        <input type="date"
                               name="to_date"
                               class="form-control"
                               value="{{ request('to_date') }}">
                    </div>

                    {{-- SUBMIT --}}
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">
                            Download ZIP
                        </button>
                    </div>

                </div>
            </form>
            {{-- ? FORM END --}}

        </div>

    </div>
</div>
@endsection
@section('scripts')
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

@section('styles')
<style>
/* ================= PAGE BASE ================= */
body {
    font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    background-color: #f8fafc;
}

/* ================= CARD ================= */
.filter-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    padding: 18px 20px;
}

/* ================= TITLE ================= */
.filter-card h5 {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 14px;
}

/* ================= ALERT ================= */
.alert-warning {
    font-size: 13px;
    border-radius: 8px;
    padding: 10px 14px;
}

/* ================= FORM ELEMENTS ================= */
.form-control {
    height: 38px;
    font-size: 13px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    color: #374151;
}

.form-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
}

/* ================= SELECT ================= */
select.form-control {
    cursor: pointer;
    background-color: #fff;
}

/* ================= BUTTON ================= */
.btn-success {
    height: 38px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border: none;
}

.btn-success:hover {
    background: linear-gradient(135deg, #16a34a, #15803d);
}

/* ================= GRID FIX ================= */
.row.g-2 > [class*="col-"] {
    padding-top: 6px;
    padding-bottom: 6px;
}

/* ================= MOBILE ================= */
@media (max-width: 768px) {
    .filter-card {
        padding: 14px;
    }

    .btn-success {
        width: 100%;
    }
}
</style>
@endsection

