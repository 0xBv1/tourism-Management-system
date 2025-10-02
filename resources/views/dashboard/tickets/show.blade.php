@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Ticket Details">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.tickets.index') }}">Tickets</a></li>
            <li class="breadcrumb-item active">{{ $ticket->name }}</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card">
                        <div class="card-header">
                            <h5>Ticket Details: {{ $ticket->name }}</h5>
                            <div class="card-header-right">
                                @if(admin()->can('tickets.edit'))
                                    <a href="{{ route('dashboard.tickets.edit', $ticket) }}" class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit Ticket
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Name:</th>
                                            <td>{{ $ticket->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>City:</th>
                                            <td>{{ $ticket->city->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Price Per Person:</th>
                                            <td>{{ $ticket->currency }} {{ number_format($ticket->price_per_person, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Duration:</th>
                                            <td>{{ $ticket->duration_hours ? $ticket->duration_hours . ' hours' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Max Participants:</th>
                                            <td>{{ $ticket->max_participants ?: 'Unlimited' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Min Age:</th>
                                            <td>{{ $ticket->min_age ? $ticket->min_age . ' years' : 'No restriction' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Max Age:</th>
                                            <td>{{ $ticket->max_age ? $ticket->max_age . ' years' : 'No restriction' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                <span class="badge bg-{{ $ticket->status_color }}">
                                                    {{ $ticket->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Active:</th>
                                            <td>
                                                <span class="badge bg-{{ $ticket->active ? 'success' : 'danger' }}">
                                                    {{ $ticket->active ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Enabled:</th>
                                            <td>
                                                <span class="badge bg-{{ $ticket->enabled ? 'success' : 'danger' }}">
                                                    {{ $ticket->enabled ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($ticket->description)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Description</h6>
                                    <p>{{ $ticket->description }}</p>
                                </div>
                            </div>
                            @endif



                            @if($ticket->bookings && $ticket->bookings->count() > 0)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Recent Bookings ({{ $ticket->bookings->count() }})</h6>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Booking File</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($ticket->bookings->take(5) as $booking)
                                                <tr>
                                                    <td>{{ $booking->bookingFile->booking_reference ?? 'N/A' }}</td>
                                                    <td>{{ $booking->start_at ? $booking->start_at->format('Y-m-d') : 'N/A' }}</td>
                                                    <td>{{ $booking->end_at ? $booking->end_at->format('Y-m-d') : 'N/A' }}</td>
                                                    <td>{{ $booking->currency ?? $ticket->currency }} {{ number_format($booking->amount ?? 0, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($ticket->bookings->count() > 5)
                                        <p class="text-muted">Showing first 5 bookings. Total: {{ $ticket->bookings->count() }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection
