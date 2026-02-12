@extends('admin.layout.base')

@section('title', 'GPs ')

@section('content')
<style type="text/css">
    table.dataTable thead th {
        background-color: #d9d9d9f5 !important;
        border-bottom: none !important;
    }
    .buttons-html5{
        border-radius: 10px;
/*        margin-right: 6px;*/
    }
    table.display tbody tr:hover td{
        background-color: #f1eeeef5 !important;
    }
    .dataTables_scrollBody thead {
        visibility: hidden;
    }
    select.select-box:not([size]):not([multiple]), input.select-box{
        height: 35px;
    }
</style>
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            @if(Setting::get('demo_mode') == 1)
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : @lang('admin.demomode')
                </div>
                @endif
            <h4 class="mb-1">
                @lang('admin.gp.gp')
                @if(Setting::get('demo_mode', 0) == 1)
                <span class="pull-right">(*personal information hidden in demo)</span>
                @endif
            </h4>
            @if(auth()->user()->role == 'admin')
            <a href="{{ route('admin.gps.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right btn-cstm"><i class="fa fa-plus"></i>@lang('admin.gp.add_gp')</a>
            <a href="{{ route('admin.gp_mapping') }}" style="margin-left: 1em;" class="btn btn-success pull-right btn-cstm"><i class="fa fa-map-pin"></i> Gp Mapping</a>
            @endif
            <table class="table row-bordered dataTable nowrap display" id="table-5" style="width:100%">
                <thead>
                    <tr>
                        <th>@lang('admin.id')</th>
                        <th>@lang('GP Name')</th>
                        <th>@lang('District')</th>
                        <th>@lang('Block')</th>
                        <th>Zonal Incharge</th>
                        <th>Gp Percentage</th>
                        <th>@lang('LGD Code')</th>
                        <th>FRT Name</th>
                        <th>@lang('Contact No')</th>
                        <th>Patroller Name</th>
                        <th>@lang('Contact No')</th>
                        @if(auth()->user()->role == 'admin')
                        <th>@lang('admin.action')</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                @foreach($gps as $index => $gp)
                    <tr>
                        <td>{{ $gp->id }}</td>
                        <td class="font-weight-bold">{{ $gp->gp_name }}</td>
                        <td>{{ $gp->district_name }}</td>
                        <td>{{ $gp->block_name }}</td>
                        <td>{{ $gp->zonal_name }}</td>
                        <td>{{$gp->avg_uptime}}</td>
                        <td>{{ $gp->lgd_code }}</td>
                        <td>{{ $gp->provider }}</td>
                        <td>{{ $gp->contact_no }}</td>
                        <td>{{ $gp->petroller}}</td>
                        <td>{{ $gp->petroller_contact_no }}</td>

                        @if(auth()->user()->role == 'admin')
                        <td>
                            <div class="btn-group" style="width:200px">
                                @if( Setting::get('demo_mode') == 0)
                                <form action="{{ route('admin.gps.destroy', $gp->id) }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button class="btn btn-danger b-a-radius-0-5 pull-left mr-1" onclick="return confirm('Are you sure you want to delete this GP?')"><i class="fa fa-trash"></i> @lang('admin.delete')</button>
                                </form>
                                <a href="{{ route('admin.gps.edit', $gp->id) }}" class="btn btn-default"><i class="fa fa-pencil"></i> @lang('admin.edit')</a>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

    $('#table-5').DataTable( {
        scrollX: true,
        searching: true,
        paging:true,
        info:true,
        dom: 'Bfrtip',
        // buttons: [
        //     'copyHtml5',
        //     'excelHtml5',
        //     'csvHtml5',
        //     'pdfHtml5'
        // ]
        buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    modifier: {
                      page: 'all'
                    }
                  }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    modifier: {
                      page: 'all'
                    }
                  }
            },
            {
                extend: 'csvHtml5',
                exportOptions: {
                    modifier: {
                      page: 'all'
                    }
                  }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    modifier: {
                      page: 'all'
                    }
                  }
            }
        ]
    } );
    
    
</script>
@endsection