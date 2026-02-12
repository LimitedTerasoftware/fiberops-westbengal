@extends('admin.layout.base')

@section('title', 'Raise Tickets')

@section('content')
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">

            <h5 class="mb-1">
                Raise Tickets
            </h5>

            <table class="table table-striped table-bordered dataTable" id="tickets-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patroller</th>
                        <th>GP Name</th>
                        <th>Date & Time</th>
                        <th>Issue</th>
                        <th>Priority</th>
                        <th>Map</th>
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

                            <td>{{ $ticket->patroller_id }}</td>

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
                                @if($ticket->attachments)
                                    <a href="javascript:void(0)"
                                       onclick="showImage('{{ asset('uploads/tickets/'.$ticket->attachments) }}')">
                                       View Image
                                    </a>
                                @else
                                    No Image
                                @endif
                            </td>

                            <td title="{{ $ticket->details }}">
                                {{ Str::limit($ticket->details, 40) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

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
            <div class="modal-body text-center">
                <img id="modalImage" class="img-responsive img-thumbnail">
            </div>
        </div>
    </div>
</div>
@endsection
