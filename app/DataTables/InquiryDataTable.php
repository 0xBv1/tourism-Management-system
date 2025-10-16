<?php

namespace App\DataTables;

use App\Enums\UserRole;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

/**
 * InquiryDataTable Class
 * 
 * This class handles the DataTable functionality for the Inquiry management system.
 * It provides server-side processing, filtering, and formatting for inquiry data
 * displayed in the admin dashboard.
 * 
 * Features:
 * - Role-based data filtering (restricted access for certain roles)
 * - Custom column formatting (dates, status badges, assigned users)
 * - Export functionality (Excel, CSV, Print)
 * - Responsive design with Bootstrap styling
 */
class InquiryDataTable extends BaseDataTable
{
    /**
     * Process and format the data for the DataTable
     * 
     * This method takes the raw query results and applies formatting to each column.
     * It handles role-based restrictions, date formatting, and HTML generation
     * for status badges and assigned users.
     * 
     * @param QueryBuilder $query The Eloquent query builder instance
     * @return EloquentDataTable The formatted DataTable instance
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        /** @var User $user */
        $user = auth()->user();
        $isRestrictedRole = $user->hasRole(UserRole::restrictedRoles());
        
        return (new EloquentDataTable($query))
            ->editColumn('status', fn(Inquiry $inquiry) => '<span class="badge badge-' . $this->getStatusColor($inquiry->status->value) . '">' . ucfirst($inquiry->status->value) . '</span>')
            ->editColumn('created_at', fn(Inquiry $inquiry) => $inquiry->created_at->format('M Y, d'))
            ->editColumn('assigned_to', fn(Inquiry $inquiry) => $this->formatAssignedUsers($inquiry))
            ->editColumn('arrival_date', function(Inquiry $inquiry) {
                /** @var \Carbon\Carbon|null $arrivalDate */
                $arrivalDate = $inquiry->arrival_date;
                return $arrivalDate ? $arrivalDate->format('M d, Y') : 'Not set';
            })
            ->editColumn('departure_date', function(Inquiry $inquiry) {
                /** @var \Carbon\Carbon|null $departureDate */
                $departureDate = $inquiry->departure_date;
                return $departureDate ? $departureDate->format('M d, Y') : 'Not set';
            })
            ->editColumn('guest_name', fn(Inquiry $inquiry) => $isRestrictedRole ? '<span class="text-muted">*** Restricted ***</span>' : $inquiry->guest_name)
            ->editColumn('email', fn(Inquiry $inquiry) => $isRestrictedRole ? '<span class="text-muted">*** Restricted ***</span>' : $inquiry->email)
            ->editColumn('phone', fn(Inquiry $inquiry) => $isRestrictedRole ? '<span class="text-muted">*** Restricted ***</span>' : $inquiry->phone)
            ->addColumn('action', 'dashboard.inquiries.action')
            ->setRowId('id')
            ->rawColumns(['action', 'status', 'guest_name', 'email', 'phone', 'assigned_to']);
    }

    /**
     * Build the base query for the DataTable with role-based filtering
     * 
     * This method applies role-based access control to filter inquiries based on
     * the current user's role. Different roles see different sets of inquiries:
     * 
     * - Reservation/Operator: Only inquiries assigned to them
     * - Finance: Only confirmed inquiries
     * - Admin/Sales: All inquiries
     * 
     * @param Inquiry $model The Inquiry model instance
     * @return QueryBuilder The filtered query builder
     */
    public function query(Inquiry $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['client', 'assignedUser', 'assignedReservation', 'assignedOperator', 'assignedAdmin']);
        
        /** @var User $user */
        $user = auth()->user();
        
        // Filter inquiries based on user role
        if ($user->hasRole(['Reservation', 'Operator'])) {
            // For Reservation and Operation roles, show only inquiries assigned to the current user
            $query->where(function($q) {
                $q->where('assigned_to', auth()->id())
                  ->orWhere('assigned_reservation_id', auth()->id())
                  ->orWhere('assigned_operator_id', auth()->id())
                  ->orWhere('assigned_admin_id', auth()->id());
            });
        } elseif ($user->hasRole('Finance')) {
            // For Finance role, show only confirmed inquiries
            $query->where('status', 'confirmed');
        }
        // For other roles (Admin, Administrator, Sales), show all inquiries
        
        return $query;
    }

    /**
     * Configure the HTML structure and features of the DataTable
     * 
     * This method sets up the DataTable's HTML configuration including:
     * - Table ID and columns
     * - AJAX processing for server-side operations
     * - DOM layout (buttons, length, filter, table, info, pagination)
     * - Export buttons (Excel, CSV, Print, Reload)
     * - Default sorting and selection behavior
     * 
     * @return HtmlBuilder The configured HTML builder instance
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('data-table')
            ->columns($this->getColumns())
            ->ajax($this->getAjaxUrl())
            ->dom('Blfrtip')
            ->orderBy(0)
            ->selectStyleSingle()
            ->buttons(array_reverse([
                Button::make('excel')->className('btn btn-sm float-right ms-1 p-1 text-light btn-success'),
                Button::make('csv')->className('btn btn-sm float-right ms-1 p-1 text-light btn-primary'),
                Button::make('print')->className('btn btn-sm float-right ms-1 p-1 text-light btn-secondary'),
                Button::make('reload')->className('btn btn-sm float-right ms-1 p-1 text-light btn-info')
            ]));
    }

    /**
     * Define the columns to be displayed in the DataTable
     * 
     * This method returns an array of column definitions that specify:
     * - Column data source (database field or computed value)
     * - Column titles for display
     * - Export and print settings
     * - Column width and styling
     * - Action column configuration
     * 
     * @return array Array of Column objects defining the table structure
     */
    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('guest_name')->title('Guest Name'),
            Column::make('email'),
            Column::make('phone'),
            Column::make('tour_name')->title('Tour Name'),
            Column::make('arrival_date')->title('Arrival Date'),
            Column::make('departure_date')->title('Departure Date'),
            Column::make('number_pax')->title('Pax'),
            Column::make('nationality'),
            Column::make('status'),
            Column::make('assigned_to'),
            Column::make('created_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    /**
     * Generate filename for exported files
     * 
     * Creates a unique filename for exported DataTable files (Excel, CSV)
     * using the current timestamp to ensure uniqueness.
     * 
     * @return string The generated filename with timestamp
     */
    protected function filename(): string
    {
        return 'Inquiry_' . date('YmdHis');
    }

    /**
     * Get the appropriate Bootstrap color class for inquiry status
     * 
     * Maps inquiry status values to Bootstrap color classes for consistent
     * visual representation in the DataTable.
     * 
     * @param string $status The inquiry status value
     * @return string The corresponding Bootstrap color class
     */
    private function getStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Format assigned users for display in the DataTable
     * 
     * This method processes the assigned users for an inquiry and generates
     * HTML markup showing user roles and names with appropriate color coding.
     * It filters out non-user assignments and creates Bootstrap badges for
     * each role assignment.
     * 
     * @param Inquiry $inquiry The inquiry instance
     * @return string HTML markup for displaying assigned users
     */
    private function formatAssignedUsers(Inquiry $inquiry): string
    {
        $assignedUsers = $inquiry->getAllAssignedUsers();
        
        if (empty($assignedUsers)) {
            return '<span class="text-muted">Unassigned</span>';
        }
        
        $html = '<div class="assigned-users">';
        foreach ($assignedUsers as $assignment) {
            // Only show user assignments, skip resource assignments
            if ($assignment['type'] !== 'user' || !isset($assignment['user'])) {
                continue;
            }
            
            $roleColor = match($assignment['role']) {
                'Sales' => 'primary',
                'Reservation' => 'info',
                'Operator' => 'warning',
                'Admin' => 'danger',
                'General' => 'secondary',
                default => 'secondary'
            };
            
            $html .= '<div class="mb-1">';
            $html .= '<span class="badge badge-' . $roleColor . ' me-1">' . $assignment['role'] . '</span>';
            $html .= '<small>' . $assignment['user']->name . '</small>';
            $html .= '</div>';
        }
        $html .= '</div>';
        
        return $html;
    }
}





