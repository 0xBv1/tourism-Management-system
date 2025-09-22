<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\InquiryDataTable;
use App\Events\InquiryConfirmed;
use App\Events\NewInquiryCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\InquiryRequest;
use App\Models\Inquiry;
use App\Models\User;
use App\Enums\InquiryStatus;
use Illuminate\Http\Request;

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
        // Get users with specific roles only
        $users = User::with('roles')
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['Reservation', 'Sales', 'Operation', 'Admin']);
            })
            ->get();
        
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
        $data = $request->getSanitized();

        // Payment fields are not required in create form, so we don't need to calculate remaining amount here
        // This will be handled in the confirmation process

        $inquiry = Inquiry::create($data);

        // Fire event for new inquiry notification
        event(new NewInquiryCreated($inquiry));

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
        $inquiry->load(['client', 'assignedUser.roles']);
        $users = User::with('roles')
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['Reservation', 'Sales', 'Operation', 'Admin']);
            })
            ->get();
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
        // Get users with specific roles only
        $users = User::with('roles')
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['Reservation', 'Sales', 'Operation', 'Admin']);
            })
            ->get();
        
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
        $data = $request->getSanitized();
        
        // Payment fields are not required in edit form, so we don't need to calculate remaining amount here
        // This will be handled in the confirmation process
        
        $inquiry->update($data);
        
        // Fire event if status changed to confirmed
        if ($oldStatus !== InquiryStatus::CONFIRMED && $inquiry->status === InquiryStatus::CONFIRMED) {
            $inquiry->update(['confirmed_at' => now()]);
            event(new InquiryConfirmed($inquiry));
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

    /**
     * Show confirmation form with payment details
     *
     * @param  \App\Models\Inquiry  $inquiry
     * @return \Illuminate\Http\Response
     */
    public function showConfirmForm(Inquiry $inquiry)
    {
        $inquiry->load(['client', 'assignedUser.roles']);
        $users = User::with('roles')->get();
        $statuses = InquiryStatus::options();
        return view('dashboard.inquiries.confirm', compact('inquiry', 'users', 'statuses'));
    }

    /**
     * Process confirmation with payment details
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inquiry  $inquiry
     * @return \Illuminate\Http\Response
     */
    public function processConfirmation(Request $request, Inquiry $inquiry)
    {
        $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0|max:' . $request->total_amount,
            'payment_method' => 'required|string|max:50',
        ]);

        $remainingAmount = $request->total_amount - $request->paid_amount;

        $inquiry->update([
            'status' => InquiryStatus::CONFIRMED,
            'total_amount' => $request->total_amount,
            'paid_amount' => $request->paid_amount,
            'remaining_amount' => $remainingAmount,
            'payment_method' => $request->payment_method,
            'confirmed_at' => now()
        ]);
        
        event(new InquiryConfirmed($inquiry));
        
        session()->flash('message', 'Inquiry Confirmed with Payment Details Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.inquiries.show', $inquiry);
    }
}
