<?php

namespace App\DataTables;

use App\Models\SupplierHotelBooking;
use App\Models\SupplierTripBooking;
use App\Models\SupplierTourBooking;
use App\Models\SupplierTransportBooking;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class SupplierWalletTransactionDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('reference', function ($transaction) {
                return '<a href="' . route('supplier.wallet.transaction', [$transaction->type, $transaction->id]) . '">' . $transaction->reference . '</a>';
            })
            ->editColumn('type', function ($transaction) {
                return '<span class="badge bg-info">' . ucfirst($transaction->type) . '</span>';
            })
            ->editColumn('service_name', function ($transaction) {
                return '<span class="badge bg-secondary">' . $transaction->service_name . '</span>';
            })
            ->editColumn('amount', function ($transaction) {
                return '<span class="text-success fw-bold">' . number_format($transaction->amount, 2) . ' EGP</span>';
            })
            ->editColumn('commission', function ($transaction) {
                return '<span class="text-warning fw-bold">' . number_format($transaction->commission, 2) . ' EGP</span>';
            })
            ->editColumn('status', function ($transaction) {
                $statusColors = [
                    'completed' => 'success',
                    'confirmed' => 'success',
                    'pending' => 'warning',
                    'failed' => 'danger',
                    'cancelled' => 'secondary'
                ];
                $color = $statusColors[$transaction->status] ?? 'info';
                return '<span class="badge bg-' . $color . '">' . ucfirst($transaction->status) . '</span>';
            })
            ->editColumn('date', function ($transaction) {
                return $transaction->date ? $transaction->date->format('Y-m-d H:i') : '-';
            })
            ->rawColumns(['reference', 'type', 'service_name', 'amount', 'commission', 'status'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(): QueryBuilder
    {
        $supplier = auth()->user()->supplier;
        
        // Get hotel bookings
        $hotelBookings = SupplierHotelBooking::where('supplier_id', $supplier->id)
            ->with(['hotel', 'client'])
            ->select([
                'id',
                DB::raw("'Hotel Booking' as type"),
                DB::raw("COALESCE(supplier_hotel_translations.name, 'N/A') as service_name"),
                DB::raw("COALESCE(users.name, 'N/A') as client_name"),
                'supplier_amount as amount',
                'commission_amount as commission',
                'status',
                'created_at as date',
                DB::raw("CONCAT('HB-', id) as reference")
            ])
            ->leftJoin('supplier_hotels', 'supplier_hotel_bookings.hotel_id', '=', 'supplier_hotels.id')
            ->leftJoin('supplier_hotel_translations', function($join) {
                $join->on('supplier_hotels.id', '=', 'supplier_hotel_translations.supplier_hotel_id')
                     ->where('supplier_hotel_translations.locale', '=', app()->getLocale());
            })
            ->leftJoin('users', 'supplier_hotel_bookings.client_id', '=', 'users.id');

        // Get trip bookings
        $tripBookings = SupplierTripBooking::join('supplier_trips', 'supplier_trip_bookings.supplier_trip_id', '=', 'supplier_trips.id')
        ->where('supplier_trips.supplier_id', $supplier->id)
        ->with(['supplierTrip', 'user'])
        ->select([
            'supplier_trip_bookings.id',
            DB::raw("'Trip Booking' as type"),
            DB::raw("COALESCE(supplier_trips.trip_name, 'N/A') as service_name"),
            DB::raw("COALESCE(users.name, 'N/A') as client_name"),
            'supplier_trip_bookings.total_price as amount',
            DB::raw("0 as commission"),
            'supplier_trip_bookings.status',
            'supplier_trip_bookings.created_at as date',
            DB::raw("CONCAT('TB-', supplier_trip_bookings.id) as reference")
        ])
        ->leftJoin('users', 'supplier_trip_bookings.user_id', '=', 'users.id');

        // Get tour bookings
        $tourBookings = SupplierTourBooking::where('supplier_id', $supplier->id)
            ->with(['tour', 'client'])
            ->select([
                'id',
                DB::raw("'Tour Booking' as type"),
                DB::raw("COALESCE(supplier_tours.title, 'N/A') as service_name"),
                DB::raw("COALESCE(users.name, 'N/A') as client_name"),
                'supplier_amount as amount',
                'commission_amount as commission',
                'status',
                'created_at as date',
                DB::raw("CONCAT('TOB-', id) as reference")
            ])
            ->leftJoin('supplier_tours', 'supplier_tour_bookings.tour_id', '=', 'supplier_tours.id')
            ->leftJoin('users', 'supplier_tour_bookings.client_id', '=', 'users.id');

        // Get transport bookings
        $transportBookings = SupplierTransportBooking::where('supplier_id', $supplier->id)
            ->with(['transport', 'client'])
            ->select([
                'id',
                DB::raw("'Transport Booking' as type"),
                DB::raw("CONCAT(COALESCE(supplier_transports.origin_location, ''), ' to ', COALESCE(supplier_transports.destination_location, '')) as service_name"),
                DB::raw("COALESCE(users.name, 'N/A') as client_name"),
                'supplier_amount as amount',
                'commission_amount as commission',
                'status',
                'created_at as date',
                DB::raw("CONCAT('TRB-', id) as reference")
            ])
            ->leftJoin('supplier_transports', 'supplier_transport_bookings.transport_id', '=', 'supplier_transports.id')
            ->leftJoin('users', 'supplier_transport_bookings.client_id', '=', 'users.id');

        // Union all bookings and order by date
        return $hotelBookings->union($tripBookings)
                            ->union($tourBookings)
                            ->union($transportBookings)
                            ->orderBy('date', 'desc');
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('data-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Blfrtip')
                    ->orderBy(0, 'desc')
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    ])
                    ->parameters([
                        'scrollX' => true,
                        'responsive' => true,
                        'autoWidth' => false,
                        'pageLength' => 25,
                        'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                        'language' => [
                            'search' => 'Search transactions:',
                            'lengthMenu' => 'Show _MENU_ transactions per page',
                            'info' => 'Showing _START_ to _END_ of _TOTAL_ transactions',
                            'infoEmpty' => 'Showing 0 to 0 of 0 transactions',
                            'infoFiltered' => '(filtered from _MAX_ total transactions)',
                        ]
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('reference')->title('Reference')->width(120),
            Column::make('type')->title('Type')->width(100),
            Column::make('service_name')->title('Service')->width(120),
            Column::make('client_name')->title('Client')->width(150),
            Column::make('amount')->title('Amount')->width(120),
            Column::make('commission')->title('Commission')->width(120),
            Column::make('status')->title('Status')->width(100),
            Column::make('date')->title('Date')->width(120),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'SupplierWalletTransactions_' . date('YmdHis');
    }
}
