<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\InquiryDataTable;
use App\Events\InquiryConfirmed;
use App\Events\NewInquiryCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\InquiryRequest;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Guide;
use App\Models\Representative;
use App\Models\Extra;
use App\Models\Ticket;
use App\Models\Dahabia;
use App\Models\Restaurant;
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
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        // Get users with specific roles only
        $users = User::with('roles')
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['Reservation', 'Sales', 'Operator', 'Admin']);
            })
            ->get();
        
        // Group users by their roles for better organization
        $usersByRole = collect();
        foreach($users as $user) {
            foreach($user->roles as $role) {
                if (!isset($usersByRole[$role->name])) {
                    $usersByRole[$role->name] = collect();
                }
                $usersByRole[$role->name]->push($user);
            }
        }
        
        $statuses = InquiryStatus::options();
        return view('dashboard.inquiries.create', compact('users', 'usersByRole', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Dashboard\InquiryRequest  $request
     * @return \Illuminate\Http\RedirectResponse
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
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Inquiry $inquiry)
    {
        $inquiry->load(['client', 'assignedUser.roles', 'assignedReservation.roles', 'assignedOperator.roles', 'assignedAdmin.roles', 'resources.resource', 'resources.addedBy']);
        
        // Load all available resources for the navigation tabs
        $availableResources = [
            'hotels' => Hotel::active()->with('city')->get(['id', 'name', 'city_id', 'price_per_night', 'currency']),
            'vehicles' => Vehicle::active()->with('city')->get(['id', 'name', 'type', 'city_id', 'price_per_day', 'price_per_hour', 'currency']),
            'guides' => Guide::active()->with('city')->get(['id', 'name', 'city_id']),
            'representatives' => Representative::active()->with('city')->get(['id', 'name', 'city_id']),
            'extras' => Extra::active()->get(['id', 'name', 'category', 'price', 'currency']),
            'tickets' => Ticket::active()->with('city')->get(['id', 'name', 'city_id', 'price_per_person', 'currency']),
            'dahabias' => Dahabia::active()->with('city')->get(['id', 'name', 'city_id', 'price_per_person', 'price_per_charter', 'currency']),
            'restaurants' => Restaurant::active()->with('city')->get(['id', 'name', 'city_id', 'currency']),
        ];
        $users = User::with('roles')
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['Reservation', 'Sales', 'Operator', 'Admin']);
            })
            ->get();
        
        // Group users by their roles for better organization
        $usersByRole = collect();
        foreach($users as $user) {
            foreach($user->roles as $role) {
                if (!isset($usersByRole[$role->name])) {
                    $usersByRole[$role->name] = collect();
                }
                $usersByRole[$role->name]->push($user);
            }
        }
        
        $statuses = InquiryStatus::options();
        return view('dashboard.inquiries.show', compact('inquiry', 'users', 'usersByRole', 'statuses', 'availableResources'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inquiry  $inquiry
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Inquiry $inquiry)
    {
        // Get users with specific roles only
        $users = User::with('roles')
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['Reservation', 'Sales', 'Operator', 'Admin']);
            })
            ->get();
        
        // Group users by their roles for better organization
        $usersByRole = collect();
        foreach($users as $user) {
            foreach($user->roles as $role) {
                if (!isset($usersByRole[$role->name])) {
                    $usersByRole[$role->name] = collect();
                }
                $usersByRole[$role->name]->push($user);
            }
        }
        
        $statuses = InquiryStatus::options();
        return view('dashboard.inquiries.edit', compact('inquiry', 'users', 'usersByRole', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Dashboard\InquiryRequest  $request
     * @param  \App\Models\Inquiry  $inquiry
     * @return \Illuminate\Http\RedirectResponse
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
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\RedirectResponse
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
     * @return \Illuminate\Contracts\View\View
     */
    public function showConfirmForm(Inquiry $inquiry)
    {
        $inquiry->load(['client', 'assignedUser.roles', 'assignedReservation.roles', 'assignedOperator.roles', 'assignedAdmin.roles']);
        $users = User::with('roles')
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['Reservation', 'Sales', 'Operator', 'Admin']);
            })
            ->get();
        
        // Group users by their roles for better organization
        $usersByRole = collect();
        foreach($users as $user) {
            foreach($user->roles as $role) {
                if (!isset($usersByRole[$role->name])) {
                    $usersByRole[$role->name] = collect();
                }
                $usersByRole[$role->name]->push($user);
            }
        }
        
        $statuses = InquiryStatus::options();
        return view('dashboard.inquiries.confirm', compact('inquiry', 'users', 'usersByRole', 'statuses'));
    }

    /**
     * Process confirmation with payment details
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inquiry  $inquiry
     * @return \Illuminate\Http\RedirectResponse
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
