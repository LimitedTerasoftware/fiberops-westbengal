@extends('admin.layout.base')
 
@section('title', 'Mapping')

@section('content')
<style type="text/css">
    .dropify-wrapper .border-dashed{
        border-style: dashed;
    }
    .shadow-gray{
       box-shadow:0 0 5px 1px #3333332e !important;
    }
    .tb-border{
        border-radius: 10px;
        border-style: hidden;
        box-shadow: 0 0 0 2px rgba(0,0,0,.15);
    }
    .tb-border td{
        border:none !important;
    }
    .tb-border tr:first-child td{
        padding:1rem 1rem 0.5rem 1rem !important;
        font-weight: 600;
    }
    .tb-border tr:last-child td{
        padding:0 1rem 1rem 1rem !important;
    }
    #error-alert{
        display: none;
    }

</style>
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <a href="{{ route('admin.gps.index') }}" class="btn btn-default pull-right">
                <i class="fa fa-angle-left"></i> @lang('admin.back')
            </a>

            <h5 class="mb-2">Import GP Data</h5>

           {{-- ? Flash Messages --}}
@if (session()->has('success'))
    <div class="alert alert-success">
        <strong>Success!</strong> {{ session('success') }}
    </div>
@endif

@if (session()->has('error'))
    <div class="alert alert-danger">
        <strong>Error!</strong> {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Validation Error:</strong>
        <ul style="margin-bottom: 0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
            <form action="{{ route('admin.gp_mapping_update') }}" method="POST" enctype="multipart/form-data" id="import_form">
                {{ csrf_field() }}
                <div class="card top-cs box-block shadow-gray">
                    <div class="card-header bg-white b-b-0">
                        <h5>Upload File</h5>
                    </div>

                    <div class="card-body p-2 mb-1">

                        <h6 class="card-subtitle mb-1 text-muted">
    <span><strong>Note<span class="look-a-like">*</span>:</strong>
        Please fill the CSV file with <strong>LDG Code</strong>, <strong>Name</strong>, and 
        <strong>Phone Number</strong> in the same sequence shown in the sample file below.
    </span><a href="{{ asset('uploads/samplefiles/gp_mapping.csv') }}" 
   class="btn btn-sm btn-primary" 
   download>
    Download Sample CSV
</a>
</h6>
                        {{-- Import Type --}}
                        <h6 class="card-subtitle mb-1 text-muted">
                            Import Type <span class="look-a-like">*</span>
                        </h6>
                        <div class="card-text mb-2">
                            <select class="form-control select-box" name="type" id="import_type" required>
                                <option value="">Please Select Import Type</option>
                                <option value="1">FRT</option>
                                <option value="2">Patroller</option>
                            </select>
                        </div>

                        


                        {{-- Import File --}}
                        <h6 class="card-subtitle mb-1 text-muted">
                            Import File <span class="look-a-like">*</span>
                        </h6>
                        <div class="card-text mb-1">
                            <input type="file" accept=".csv"
                                   name="import_file"
                                   class="dropify form-control-file border-dashed"
                                   id="import_file"
                                   data-height="100"
                                   required>
                            <small class="text-muted ml-0-5">Choose a CSV file to import data.</small>
                        </div>

                        {{-- Buttons --}}
                        <div class="mt-3 clearfix">
                            <button type="submit" id="form_continue" class="btn btn-primary btn-cstm pull-right" disabled>
                                <i class="fa fa-upload"></i> Import & Update
                            </button>
                            <a href="{{ route('admin.gps.index') }}" class="btn pull-right mr-2">Cancel</a>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ? JS Section --}}
@section('scripts')
<script>
$(document).ready(function() {
    const importType = $('#import_type');
    const importFile = $('#import_file');
    const submitBtn = $('#form_continue');

    function toggleSubmit() {
        if (importType.val() && importFile.val()) {
            submitBtn.prop('disabled', false);
        } else {
            submitBtn.prop('disabled', true);
        }
    }

    importType.on('change', toggleSubmit);
    importFile.on('change', toggleSubmit);
});
</script>

@endsection

@endsection