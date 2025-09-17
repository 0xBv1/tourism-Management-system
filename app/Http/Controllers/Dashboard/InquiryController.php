<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\InquiryDataTable;
use App\Events\InquiryConfirmed;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\InquiryRequest;
use App\Models\Inquiry;
use App\Models\User;
use App\Enums\InquiryStatus;

class InquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InquiryDataTable $dataTable)
    {
        return $dataTable->render('dashboard.inquiries.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        $statuses = InquiryStatus::options();
        return view('dashboard.inquiries.create', compact('users', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Dashboard\InquiryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InquiryRequest $request)
    {
        $inquiry = Inquiry::create($request->getSanitized());
        
        session()->flash('message', 'Inquiry Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.inquiries.show', $inquiry);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inquiry  $inquiry
     * @return \Illuminate\Http\Response
     */
    public function show(Inquiry $inquiry)
    {
        $inquiry->load(['client', 'assignedUser']);
        $users = User::all();
        $statuses = InquiryStatus::options();
        return view('dashboard.inquiries.show', compact('inquiry', 'users', 'statuses'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inquiry  $inquiry
     * @return \Illuminate\Http\Response
     */
    public function edit(Inquiry $inquiry)
    {
        $users = User::all();
        $statuses = InquiryStatus::options();
        return view('dashboard.inquiries.edit', compact('inquiry', 'users', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Dashboard\InquiryRequest  $request
     * @param  \App\Models\Inquiry  $inquiry
     * @return \Illuminate\Http\Response
     */
    public function update(InquiryRequest $request, Inquiry $inquiry)
    {
        $oldStatus = $inquiry->status;
        $inquiry->update($request->getSanitized());
        
        // Fire event if status changed to confirmed
        if ($oldStatus !== InquiryStatus::CONFIRMED && $inquiry->status === InquiryStatus::CONFIRMED) {
            $inquiry->update(['confirmed_at' => now()]);
            event(new InquiryConfirmed($inquiry));
        }
        
        // Update completed_at if status is completed
        if ($inquiry->status === InquiryStatus::COMPLETED) {
            $inquiry->update(['completed_at' => now()]);
        }
        
        session()->flash('message', 'Inquiry Updated Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.inquiries.show', $inquiry);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inquiry  $inquiry
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();
        return response()->json([
            'message' => 'Inquiry Deleted Successfully!'
        ]);
    }

    /**
     * Confirm an inquiry
     *
     * @param  \App\Models\Inquiry  $inquiry
     * @return \Illuminate\Http\Response
     */
    public function confirm(Inquiry $inquiry)
    {
        $inquiry->update([
            'status' => InquiryStatus::CONFIRMED,
            'confirmed_at' => now()
        ]);
        
        event(new InquiryConfirmed($inquiry));
        
        session()->flash('message', 'Inquiry Confirmed Successfully!');
        session()->flash('type', 'success');
        return back();
    }
}
