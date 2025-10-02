<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\TicketDataTable;
use App\Http\Requests\Dashboard\TicketRequest;
use App\Models\Ticket;
use App\Models\City;
use App\Enums\ResourceStatus;

class TicketController extends Controller
{
    public function index(TicketDataTable $dataTable)
    {
        return $dataTable->render('dashboard.tickets.index');
    }

    public function create()
    {
        $cities = City::all();
        return view('dashboard.tickets.create', compact('cities'));
    }

    public function store(TicketRequest $request)
    {
        $ticket = Ticket::create($request->getSanitized());
        
        session()->flash('message', 'Ticket Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.tickets.edit', $ticket);
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['city', 'bookings.bookingFile']);
        return view('dashboard.tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $cities = City::all();
        return view('dashboard.tickets.edit', compact('ticket', 'cities'));
    }

    public function update(TicketRequest $request, Ticket $ticket)
    {
        $ticket->update($request->getSanitized());
        
        session()->flash('message', 'Ticket Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return response()->json([
            'message' => 'Ticket Deleted Successfully!'
        ]);
    }
}
