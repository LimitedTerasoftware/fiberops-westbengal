@extends('admin.layout.base')
 
@section('title', 'Import ')

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
    		<a href="{{ route('admin.tickets') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

            <h5 class="mb-2">Import Data</h5>
            <form action="{{route('admin.import.process')}}" method="POST" enctype="multipart/form-data" id="import_form">
                {{csrf_field()}}
                <div class="card top-cs box-block shadow-gray">
                    <div class="card-header bg-white b-b-0">
                        <h5>Upload File</h5>
                    </div>
                    <div class="card-body p-2 mb-1">
                        <h6 class="card-subtitle mb-1 text-muted">Import Type<span class="look-a-like">*</span></h6>
                        <div class="card-text mb-2">
                            <select class="form-control select-box" name="type" id="import_type" required>
                                <option value="">Please Select Import Type</option>
                                <option value="1">Tickets</option>
                                <!--<option value="2">Patroller Tickets</option>--->
                            </select>
                        </div>

                        <h6 class="card-subtitle mb-1 text-muted">Import File<span class="look-a-like">*</span></h6>
                        <div class="alert alert-danger" id="error-alert" > Some Error message
                        </div>
                        <div class="card-text mb-1">
                            <input type="file" accept=".csv" name="import_file" class="dropify form-control-file border-dashed" id="import_file" data-height="100" aria-describedby="fileHelp" required>
                            <small class="text-muted ml-0-5">Choose a CSV file to import data.</small>
                        </div>
                        <a href="#" id="form_continue" class="btn btn-primary btn-cstm pull-right disabled">Continue</a>
                        <a href="{{ route('admin.tickets') }}" class="btn pull-right">Cancel</a>
                    </div>
                </div>
                <br>
                <div class="card top-cs box-block shadow-gray">
                    <div class="card-header bg-white b-b-0">
                        <h5>Review & Process</h5>
                    </div>
                    <div class="card-body p-2 mb-1">
                        <table class="table tb-border mb-2">
                          <tbody>
                            <tr>
                              <td width="25%">Columns ready to import</td>
                              <td width="25%">Columns ignored</td>
                              <td width="25%">Records to create</td>
                              <td width="25%">Records to update</td>
                            </tr>
                            <tr id="process_counts">
                              <td width="25%" class="text-muted">-</td>
                              <td width="25%" class="text-muted">-</td>
                              <td width="25%" class="text-muted">-</td>
                              <td width="25%" class="text-muted">-</td>
                            </tr>
                          </tbody>
                        </table>
                        <a href="#" class="btn btn-primary btn-cstm pull-right disabled" id="form_process">Process Import</a>
                        <a href="{{ route('admin.tickets') }}" class="btn pull-right">Cancel</a>
                    </div>
                </div>
            </form>


    	</div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){

        $('#form_continue').click(function(e){
            e.preventDefault();
            var import_typee = document.getElementById('import_type');
            var import_type = import_typee.options[import_typee.selectedIndex].value;
            var files = $('#import_file')[0].files;            
            if(!import_type){
                document.getElementById('import_type').style.border = "1px solid red";
                return false;
            }
            if(files.length <= 0){
                alert("Please select a file.");
                return false;
            }

            var fd = new FormData();
            // Append data 
            fd.append('import_file', files[0]);
            fd.append('_token', "{{ csrf_token() }}");
            $('.preloader').css('display','block');
            $.ajax({
               url: "{{route('admin.import.data')}}",
               method: 'post',
               data: fd,
               contentType: false,
               processData: false,
               dataType: 'json',
               success: function(response){
                    $('.preloader').css('display','none');
                    console.log("Success Response: ");
                    console.log(response);
                    if(response.status == 200 || response.status == '200'){
                        document.getElementById("form_process").classList.remove("disabled");
                        var counts = '<td width="25%" class="text-muted">'+response.ready_to_import+'</td>';
                        counts += '<td width="25%" class="text-muted">'+response.ignored+'</td>';
                        counts += '<td width="25%" class="text-muted">'+response.creates+'</td>';
                        counts += '<td width="25%" class="text-muted">'+response.updates+'</td>';
                        document.getElementById('process_counts').innerHTML = counts;
                    }
                    else {
                        document.getElementById('error-alert').innerHTML = response.error;
                        document.getElementById('error-alert').style.display = "block";
                        $(".alert").delay(4000).slideUp(200, function() {
                            document.getElementById('error-alert').style.display = "none";
                        });
                    }
                },
               error: function(response){
                    $('.preloader').css('display','none');
                  console.log("error : " + JSON.stringify(response) );
                  console.log("error : " , JSON.parse(response.responseText).error );
                  var data = JSON.parse(response.responseText);
                  console.log("error : " , data.error );
                  document.getElementById('error-alert').innerHTML = data.error;
                  document.getElementById('error-alert').style.display = "block";
                  $(".alert").delay(4000).slideUp(200, function() {
                    document.getElementById('error-alert').style.display = "none";
                  });
               }
            });
        });

        $('#import_type').on('change', function(){
            var type = $(this).find('option:selected').val();
            if(type){
                document.getElementById('import_type').style.border = "1px solid rgba(0,0,0,.15)";
            }

        });

        $('input[type=file]').on('change',function(){
            var files = $('#import_file')[0].files;  
            if(files.length > 0){
                document.getElementById("form_continue").classList.remove("disabled");
            }
            
        });

        $('#form_process').click(function(e){
            e.preventDefault();
            var import_typee = document.getElementById('import_type');
            var import_type = import_typee.options[import_typee.selectedIndex].value;
            var files = $('#import_file')[0].files;            
            if(!import_type){
                document.getElementById('import_type').style.border = "1px solid red";
                return false;
            }
            if(files.length <= 0){
                alert("Please select a file.");
                return false;
            }
            $('#import_form').submit();
        });
        
    });
</script>
@endsection