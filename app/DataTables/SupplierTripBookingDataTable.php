<?php

namespace App\DataTables;

use App\Models\SupplierTripBooking;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SupplierTripBookingDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('booking_reference', function ($booking) {
                return '<strong>' . $booking->booking_reference . '</strong>';
            })
            ->addColumn('passenger_info', function ($booking) {
                return '<div>
                    <strong>' . $booking->guest_name . '</strong><br>
                    <small class="text-muted">' . $booking->guest_email . '</small><br>
                    <small class="text-muted">' . $booking->guest_phone . '</small>
                </div>';
            })
            ->addColumn('passengers', function ($booking) {
                return '<span class="badge bg-info">' . $booking->passengers_count . ' Passengers</span>';
            })
            ->editColumn('total_price', function ($booking) {
                return '<span class="text-success fw-bold">' . number_format($booking->total_price, 2) . ' EGP</span>';
            })
            ->editColumn('status', function ($booking) {
                $statusColors = [
                    'confirmed' => 'success',
                    'pending' => 'warning',
                    'cancelled' => 'danger',
                    'completed' => 'info'
                ];
                $color = $statusColors[$booking->status] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucfirst($booking->status) . '</span>';
            })
            ->editColumn('created_at', function ($booking) {
                return $booking->created_at->format('M d, Y H:i');
            })
            ->addColumn('actions', function ($booking) {
                return '<div class="btn-group" role="group">
                    <a href="' . route('supplier.trips.bookings', $booking->supplierTrip) . '" class="btn btn-sm btn-info" title="View">
                        <i class="fa fa-eye"></i>
                    </a>
                </div>';
            })
            ->rawColumns(['booking_reference', 'passenger_info', 'passengers', 'total_price', 'status', 'actions'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SupplierTripBooking $model): QueryBuilder
    {
        $supplier = auth()->user()->supplier;
        $tripId = request()->route('trip')->id ?? null;
        
        $query = $model->join('supplier_trips', 'supplier_trip_bookings.supplier_trip_id', '=', 'supplier_trips.id')
                      ->where('supplier_trips.supplier_id', $supplier->id)
                      ->with(['supplierTrip', 'user']);
        
        if ($tripId) {
            $query->where('supplier_trip_bookings.supplier_trip_id', $tripId);
        }
        
        return $query->select('supplier_trip_bookings.*')->orderBy('supplier_trip_bookings.created_at', 'desc');
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
                            'search' => 'Search bookings:',
                            'lengthMenu' => 'Show _MENU_ bookings per page',
                            'info' => 'Showing _START_ to _END_ of _TOTAL_ bookings',
                            'infoEmpty' => 'Showing 0 to 0 of 0 bookings',
                            'infoFiltered' => '(filtered from _MAX_ total bookings)',
                        ]
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('booking_reference')->title('Booking Ref')->width(120),
            Column::make('passenger_info')->title('Passenger Info')->width(200),
            Column::make('passengers')->title('Passengers')->width(150),
            Column::make('total_price')->title('Total Price')->width(120),
            Column::make('status')->title('Status')->width(100),
            Column::make('created_at')->title('Created')->width(120),
            Column::make('actions')->title('Actions')->width(100)->orderable(false)->searchable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'SupplierTripBookings_' . date('YmdHis');
    }
}
