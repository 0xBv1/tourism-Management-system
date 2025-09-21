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
use App\Notifications\InquiryUserConfirmationNotification;

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
        $data = $request->getSanitized();
        
        // Check if confirmation users are being changed
        $confirmationUsersChanged = false;
        if (isset($data['user1_id']) || isset($data['user2_id'])) {
            $newUser1Id = $data['user1_id'] ?? $inquiry->user1_id;
            $newUser2Id = $data['user2_id'] ?? $inquiry->user2_id;
            
            if ($newUser1Id !== $inquiry->user1_id || $newUser2Id !== $inquiry->user2_id) {
                $confirmationUsersChanged = true;
                // Reset confirmation status when users change
                $data['user1_confirmed_at'] = null;
                $data['user2_confirmed_at'] = null;
            }
        }
        
        $inquiry->update($data);
        
        // Fire event if status changed to confirmed
        if ($oldStatus !== InquiryStatus::CONFIRMED && $inquiry->status === InquiryStatus::CONFIRMED) {
            $inquiry->update(['confirmed_at' => now()]);
            event(new InquiryConfirmed($inquiry));
        }
        
        // Update completed_at if status is completed
        if ($inquiry->status === InquiryStatus::COMPLETED) {
            $inquiry->update(['completed_at' => now()]);
        }
        
        $message = 'Inquiry Updated Successfully!';
        if ($confirmationUsersChanged) {
            $message .= ' Confirmation users have been updated and confirmation status has been reset.';
        }
        
        session()->flash('message', $message);
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
     * Confirm an inquiry by current user
     *
     * @param  \App\Models\Inquiry  $inquiry
     * @return \Illuminate\Http\Response
     */
    public function confirm(Inquiry $inquiry)
    {
        $userId = auth()->id();
        
        // Check if confirmation users are set
        if (!$inquiry->user1_id || !$inquiry->user2_id) {
            session()->flash('message', 'Confirmation users have not been assigned to this inquiry. Please contact an administrator to assign confirmation users.');
            session()->flash('type', 'warning');
            return back();
        }
        
        // Check if user is assigned to this inquiry
        if ($inquiry->user1_id !== $userId && $inquiry->user2_id !== $userId) {
            session()->flash('message', 'You are not authorized to confirm this inquiry! Only the assigned confirmation users can confirm this inquiry.');
            session()->flash('type', 'error');
            return back();
        }
        
        // Check if user has already confirmed
        if ($inquiry->hasUserConfirmed($userId)) {
            session()->flash('message', 'You have already confirmed this inquiry!');
            session()->flash('type', 'warning');
            return back();
        }
        
        // Confirm by user
        $confirmed = $inquiry->confirmByUser($userId);
        
        if ($confirmed) {
            // Get the other user to notify
            $otherUserId = ($inquiry->user1_id === $userId) ? $inquiry->user2_id : $inquiry->user1_id;
            $otherUser = User::find($otherUserId);
            
            // Check if both users have confirmed
            if ($inquiry->isFullyConfirmed()) {
                $inquiry->update([
                    'status' => InquiryStatus::CONFIRMED,
                    'confirmed_at' => now()
                ]);
                
                event(new InquiryConfirmed($inquiry));
                
                // Notify both users that inquiry is fully confirmed
                if ($otherUser) {
                    $otherUser->notify(new InquiryUserConfirmationNotification($inquiry, auth()->user(), true));
                }
                auth()->user()->notify(new InquiryUserConfirmationNotification($inquiry, auth()->user(), true));
                
                session()->flash('message', 'Inquiry fully confirmed by both users!');
                session()->flash('type', 'success');
            } else {
                // Notify the other user about the confirmation
                if ($otherUser) {
                    $otherUser->notify(new InquiryUserConfirmationNotification($inquiry, auth()->user(), false));
                }
                
                session()->flash('message', 'Your confirmation has been recorded. Waiting for the other user to confirm.');
                session()->flash('type', 'info');
            }
        } else {
            session()->flash('message', 'Failed to confirm inquiry!');
            session()->flash('type', 'error');
        }
        
        return back();
    }

    /**
     * Set users for confirmation (admin only)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inquiry  $inquiry
     * @return \Illuminate\Http\Response
     */
    public function setConfirmationUsers(Request $request, Inquiry $inquiry)
    {
        $request->validate([
            'user1_id' => 'required|exists:users,id',
            'user2_id' => 'required|exists:users,id|different:user1_id',
        ]);
        
        $inquiry->update([
            'user1_id' => $request->user1_id,
            'user2_id' => $request->user2_id,
            'user1_confirmed_at' => null,
            'user2_confirmed_at' => null,
        ]);
        
        session()->flash('message', 'Confirmation users set successfully!');
        session()->flash('type', 'success');
        return back();
    }
}
