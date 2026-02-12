@extends('admin.layout.base')

@section('title', 'Ongoing Intervals ')

@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
                <h5 class="mb-1">Ongoing Intervals</h5>
                @if(count($requests) != 0)
                <table class="table row-bordered dataTable nowrap display" id="table-4" style="width:100%">
                    <thead>
                        <tr>
                            <th>@lang('admin.id')</th>
                            <th>Description</th>
                            <th>@lang('Count')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td >{{$descriptions[0]}}</td>                            
                            <td class="font-weight-bold">
                                <a href="{{ url('/admin/tickets?interval=below_4_hours') }}" class="btn btn-default"> {{ $requests[0]->below_4_hours }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>{{$descriptions[1]}}</td>                            
                            <td>
                            	<a href="{{ url('/admin/tickets?interval=between_4_to_10_hours') }}" class="btn btn-default"> {{ $requests[0]->between_4_to_10_hours }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>{{$descriptions[2]}}</td>                            
                            <td>
                            	<a href="{{ url('/admin/tickets?interval=between_10_to_24_hours') }}" class="btn btn-default"> {{ $requests[0]->between_10_to_24_hours }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>{{$descriptions[3]}}</td>                            
                            <td>
                            	<a href="{{ url('/admin/tickets?interval=above_24_hours') }}" class="btn btn-default"> {{ $requests[0]->above_24_hours }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>All Gps</td>                            
                            <td>
                            	<a href="{{ url('/admin/tickets?status=Open') }}" class="btn btn-default"> {{ $ongoing_tickets }}</a>
                            </td>
                        </tr>

                    </tbody>
                </table>
                @else
                    <h6 class="no-result">No results found</h6>
                @endif 
            </div>
            
        </div>
    </div>
@endsection