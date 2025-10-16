<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\InquiryDataTable;
use App\Events\InquiryConfirmed;
use App\Events\NewInquiryCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\InquiryRequest;
use App\Services\UserService;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Guide;
use App\Models\Extra;
use App\Models\Dahabia;
use App\Models\Inquiry;
use App\Enums\InquiryStatus;
use App\Enums\UserRole;
use App\Models\Hotel;
use App\Models\Representative;
use App\Models\Ticket;
use App\Models\Restaurant;
use Illuminate\Http\Request;

/**
 * InquiryController Class
 * 
 * This controller handles all inquiry-related operations in the dashboard.
 * It manages the complete inquiry lifecycle from creation to confirmation,
 * including resource assignment, payment processing, and status updates.
 * 
 * Features:
 * - CRUD operations for inquiries
 * - Role-based access control
 * - Resource assignment management
 * - Payment processing and confirmation
 * - Tour itinerary management
 * - Event-driven notifications
 * 
 * Dependencies:
 * - UserService: For user role management
 * - InquiryDataTable: For data display
 * - Various resource models (Hotel, Vehicle, Guide, etc.)
 */
class InquiryController extends Controller
{
    /**
     * Constructor - Inject UserService dependency
     * 
     * @param UserService $userService Service for user role management
     */
    public function __construct(
        private UserService $userService
    ) {}
    
    /**
     * Display a listing of inquiries using DataTable
     * 
     * Renders the inquiries index page with server-side processing,
     * filtering, and export capabilities through the DataTable component.
     * 
     * @param InquiryDataTable $dataTable The DataTable instance
     * @return mixed The rendered view with DataTable
     */
    public function index(InquiryDataTable $dataTable): mixed
    {
        return $dataTable->render('dashboard.inquiries.index');
    }

    /**
     * Show the form for creating a new inquiry
     * 
     * Prepares the create form with necessary data including:
     * - Users grouped by roles for assignment dropdowns
     * - Available inquiry statuses
     * 
     * @return \Illuminate\Contracts\View\View The create form view
     */
    public function create(): \Illuminate\Contracts\View\View
    {
        $usersByRole = $this->userService->getUsersByRole();
        $statuses = InquiryStatus::options();
        
        return view('dashboard.inquiries.create', compact('usersByRole', 'statuses'));
    }

    /**
     * Store a newly created inquiry in the database
     * 
     * Processes the form submission for creating a new inquiry:
     * - Validates input data through InquiryRequest
     * - Creates the inquiry record
     * - Fires NewInquiryCreated event for notifications
     * - Redirects to the inquiry details page
     * 
     * Note: Payment fields are handled separately in the confirmation process
     * 
     * @param InquiryRequest $request The validated form request
     * @return \Illuminate\Http\RedirectResponse Redirect to inquiry details
     */
    public function store(InquiryRequest $request): \Illuminate\Http\RedirectResponse
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
     * Display the detailed view of a specific inquiry
     * 
     * Shows comprehensive inquiry details including:
     * - Inquiry information and assigned users
     * - Available resources for assignment (hotels, vehicles, guides, etc.)
     * - Users grouped by roles for reassignment
     * - Current status and available status options
     * 
     * This view allows for resource assignment, status updates, and
     * tour itinerary management.
     * 
     * @param Inquiry $inquiry The inquiry to display
     * @return \Illuminate\Contracts\View\View The inquiry details view
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
            'restaurants' => Restaurant::active()->with(['city', 'meals'])->get(['id', 'name', 'city_id', 'currency']),
        ];
        $usersByRole = $this->userService->getUsersByRole();
        $statuses = InquiryStatus::options();
        
        return view('dashboard.inquiries.show', compact('inquiry', 'usersByRole', 'statuses', 'availableResources'));
    }

    /**
     * Show the form for editing an existing inquiry
     * 
     * Prepares the edit form with current inquiry data and necessary options:
     * - Users grouped by roles for assignment dropdowns
     * - Available inquiry statuses
     * - Current inquiry data for pre-population
     * 
     * @param Inquiry $inquiry The inquiry to edit
     * @return \Illuminate\Contracts\View\View The edit form view
     */
    public function edit(Inquiry $inquiry)
    {
        $usersByRole = $this->userService->getUsersByRole();
        $statuses = InquiryStatus::options();
        
        return view('dashboard.inquiries.edit', compact('inquiry', 'usersByRole', 'statuses'));
    }

    /**
     * Update an existing inquiry in the database
     * 
     * Processes the form submission for updating an inquiry:
     * - Validates input data through InquiryRequest
     * - Updates the inquiry record
     * - Handles status change to confirmed (sets confirmed_at timestamp)
     * - Fires InquiryConfirmed event if status changed to confirmed
     * - Redirects to the inquiry details page
     * 
     * Note: Payment fields are handled separately in the confirmation process
     * 
     * @param InquiryRequest $request The validated form request
     * @param Inquiry $inquiry The inquiry to update
     * @return \Illuminate\Http\RedirectResponse Redirect to inquiry details
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
     * Delete an inquiry from the database
     * 
     * Permanently removes the inquiry record and all associated data.
     * This action cannot be undone.
     * 
     * @param Inquiry $inquiry The inquiry to delete
     * @return \Illuminate\Http\JsonResponse JSON response confirming deletion
     */
    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();
        return response()->json([
            'message' => 'Inquiry Deleted Successfully!'
        ]);
    }

    /**
     * Confirm an inquiry and mark it as confirmed
     * 
     * Updates the inquiry status to confirmed and sets the confirmation timestamp.
     * Fires the InquiryConfirmed event for notifications and downstream processing.
     * 
     * @param Inquiry $inquiry The inquiry to confirm
     * @return \Illuminate\Http\RedirectResponse Redirect back to previous page
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
     * Show the confirmation form with payment details
     * 
     * Displays a specialized form for confirming inquiries with payment information.
     * This form includes fields for total amount, paid amount, and payment method.
     * 
     * @param Inquiry $inquiry The inquiry to confirm
     * @return \Illuminate\Contracts\View\View The confirmation form view
     */
    public function showConfirmForm(Inquiry $inquiry)
    {
        $inquiry->load(['client', 'assignedUser.roles', 'assignedReservation.roles', 'assignedOperator.roles', 'assignedAdmin.roles']);
        $usersByRole = $this->userService->getUsersByRole();
        $statuses = InquiryStatus::options();
        
        return view('dashboard.inquiries.confirm', compact('inquiry', 'usersByRole', 'statuses'));
    }

    /**
     * Process inquiry confirmation with payment details
     * 
     * Handles the submission of the confirmation form with payment information:
     * - Validates payment amounts and method
     * - Calculates remaining amount
     * - Updates inquiry status to confirmed
     * - Sets payment details and confirmation timestamp
     * - Fires InquiryConfirmed event
     * 
     * @param Request $request The form request with payment data
     * @param Inquiry $inquiry The inquiry to confirm
     * @return \Illuminate\Http\RedirectResponse Redirect to inquiry details
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

    /**
     * Update the tour itinerary for an inquiry
     * 
     * Allows Sales role users to update the tour itinerary details for an inquiry.
     * This method includes role-based authorization to ensure only Sales users
     * can modify tour itineraries.
     * 
     * @param Request $request The request containing tour itinerary data
     * @param Inquiry $inquiry The inquiry to update
     * @return \Illuminate\Http\JsonResponse JSON response with success status
     */
    public function updateTourItinerary(Request $request, Inquiry $inquiry)
    {
        // Check if user has Sales role
        if (!admin()->hasRole(['Sales'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only Sales role can edit tour itinerary.'
            ], 403);
        }

        $request->validate([
            'tour_itinerary' => ['nullable', 'string']
        ]);

        $inquiry->update([
            'tour_itinerary' => $request->tour_itinerary
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tour itinerary updated successfully!',
            'tour_itinerary' => $inquiry->tour_itinerary
        ]);
    }
}
