<?php

namespace App\DataTables;

use App\Models\SupplierTrip;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SupplierTripDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($trip) {
                return view('dashboard.supplier.trips.partials.actions', compact('trip'))->render();
            })
            ->addColumn('status', function ($trip) {
                return view('dashboard.supplier.trips.partials.status', compact('trip'))->render();
            })
            ->addColumn('trip_type', function ($trip) {
                return $trip->trip_type_label;
            })
            ->addColumn('route', function ($trip) {
                return $trip->departure_city . ' → ' . $trip->arrival_city;
            })
            ->addColumn('formatted_price', function ($trip) {
                return $trip->formatted_seat_price;
            })
            ->addColumn('seats_info', function ($trip) {
                return $trip->available_seats . '/' . $trip->total_seats;
            })
            ->addColumn('formatted_departure_time', function ($trip) {
                return $trip->formatted_departure_time;
            })
            ->addColumn('formatted_arrival_time', function ($trip) {
                return $trip->formatted_arrival_time;
            })
            ->addColumn('travel_date_formatted', function ($trip) {
                return $trip->travel_date ? $trip->travel_date->format('M d, Y') : 'N/A';
            })
            ->addColumn('return_date_formatted', function ($trip) {
                return $trip->return_date ? $trip->return_date->format('M d, Y') : 'N/A';
            })
            ->rawColumns(['action', 'status'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SupplierTrip $model): QueryBuilder
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        return $model->newQuery()
            ->where('supplier_id', $supplier->id)
            ->orderBy('created_at', 'desc');
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
            ->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons(array_reverse([
                Button::make('excel')->className('btn btn-sm float-right ms-1 p-1 text-light btn-success'),
                Button::make('csv')->className('btn btn-sm float-right ms-1 p-1 text-light btn-primary'),
                Button::make('print')->className('btn btn-sm float-right ms-1 p-1 text-light btn-secondary'),
                Button::make('reload')->className('btn btn-sm float-right ms-1 p-1 text-light btn-info')
            ]));
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID')->width(60),
            Column::make('trip_name')->title('Trip Name')->width(200),
            Column::make('trip_type')->title('Type')->width(100),
            Column::make('route')->title('Route')->width(150),
            Column::make('travel_date_formatted')->title('Travel Date')->width(120),
            Column::make('return_date_formatted')->title('Return Date')->width(120),
            Column::make('formatted_departure_time')->title('Departure')->width(100),
            Column::make('formatted_arrival_time')->title('Arrival')->width(100),
            Column::make('formatted_price')->title('Price')->width(100),
            Column::make('seats_info')->title('Seats')->width(80),
            Column::make('status')->title('Status')->width(120),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center')
                ->title('Actions'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'SupplierTrips_' . date('YmdHis');
    }
}
