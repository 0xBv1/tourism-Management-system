<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\BookingDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\BookingRequest;
use App\Models\BookingFile;
use App\Models\Inquiry;
use App\Enums\BookingStatus;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BookingDataTable $dataTable)
    {
        return $dataTable->render('dashboard.bookings.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BookingFile  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(BookingFile $booking)
    {
        $booking->load(['inquiry.client', 'payments']);
        $statuses = BookingStatus::options();
        $inquiries = Inquiry::with('client')->get();
        
        return view('dashboard.bookings.show', compact('booking', 'statuses', 'inquiries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BookingFile  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BookingFile $booking)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', BookingStatus::all()),
            'notes' => 'nullable|string',
            'total_amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
        ]);

        $booking->update($request->only(['status', 'notes', 'total_amount', 'currency']));
        
        session()->flash('message', 'Booking Updated Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.bookings.show', $booking);
    }

    /**
     * Update checklist item
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BookingFile  $booking
     * @return \Illuminate\Http\Response
     */
    public function updateChecklist(Request $request, BookingFile $booking)
    {
        $request->validate([
            'item' => 'required|string',
            'completed' => 'required|boolean',
        ]);

        $booking->updateChecklistItem($request->item, $request->completed);
        
        return response()->json([
            'message' => 'Checklist updated successfully',
            'progress' => $booking->checklist_progress
        ]);
    }

    /**
     * Download booking file
     *
     * @param  \App\Models\BookingFile  $booking
     * @return \Illuminate\Http\Response
     */
    public function download(BookingFile $booking)
    {
        if (!file_exists($booking->file_path)) {
            session()->flash('message', 'File not found!');
            session()->flash('type', 'error');
            return back();
        }

        $booking->update(['downloaded_at' => now()]);
        
        return response()->download($booking->file_path, $booking->file_name);
    }

    /**
     * Send booking file
     *
     * @param  \App\Models\BookingFile  $booking
     * @return \Illuminate\Http\Response
     */
    public function send(BookingFile $booking)
    {
        // TODO: Implement email sending logic
        $booking->update(['sent_at' => now()]);
        
        session()->flash('message', 'Booking file sent successfully!');
        session()->flash('type', 'success');
        return back();
    }
}
