<?php

namespace App\DataTables;

use App\Models\SupplierTransport;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Str;

class SupplierTransportDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('route_info', function (SupplierTransport $transport) {
                return '<div class="d-flex flex-column">
                    <strong>' . $transport->origin_location . '</strong>
                    <i class="mdi mdi-arrow-right mx-1"></i>
                    <strong>' . $transport->destination_location . '</strong>
                    ' . ($transport->intermediate_stops ? '<small class="text-muted">' . Str::limit($transport->intermediate_stops, 30) . '</small>' : '') . '
                </div>';
            })
            ->editColumn('route_type', function (SupplierTransport $transport) {
                $colors = [
                    'One Way' => 'primary',
                    'Round Trip' => 'success',
                    'Multi Stop' => 'info',
                    'Shuttle' => 'warning',
                    'Charter' => 'danger'
                ];
                $color = $colors[$transport->route_type] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . $transport->route_type . '</span>';
            })
            ->editColumn('estimated_travel_time', function (SupplierTransport $transport) {
                return '<span class="badge bg-secondary">' . $transport->formatted_travel_time . '</span>';
            })
            ->editColumn('vehicle_type', function (SupplierTransport $transport) {
                if ($transport->vehicle_type) {
                    return '<span class="badge bg-warning">' . $transport->vehicle_type . '</span><br><small class="text-muted">' . ($transport->seating_capacity ?? 'N/A') . ' seats</small>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->editColumn('price', function (SupplierTransport $transport) {
                return '<span class="badge bg-success">' . $transport->formatted_price . '</span>';
            })
            ->editColumn('enabled', function (SupplierTransport $transport) {
                return '<span class="badge bg-' . $transport->status_color . '">' . $transport->status_label . '</span>';
            })
            ->addColumn('approved', function (SupplierTransport $transport) {
                return $transport->approved
                    ? '<span class="badge bg-success">Approved</span>'
                    : '<span class="badge bg-warning">Pending</span>';
            })
            ->addColumn('bookings_count', function (SupplierTransport $transport) {
                return '<span class="badge bg-info">' . ((int) ($transport->bookings_count ?? 0)) . '</span>';
            })
            ->editColumn('created_at', function (SupplierTransport $transport) {
                return $transport->created_at ? $transport->created_at->format('M d, Y') : 'N/A';
            })
            ->addColumn('action', 'dashboard.supplier.transports.action')
            ->setRowId('id')
            ->rawColumns(['route_info', 'route_type', 'estimated_travel_time', 'vehicle_type', 'price', 'enabled', 'approved', 'bookings_count', 'action']);
    }

    public function query(SupplierTransport $model): QueryBuilder
    {
        return $model->newQuery()
            ->where('supplier_id', auth()->user()->supplier->id)
            ->with(['supplier', 'bookings'])
            ->withCount('bookings');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('data-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Brtip')
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->buttons(array_reverse([
                Button::make('excel')->className('btn btn-sm float-right ms-1 p-1 text-light btn-success'),
                Button::make('csv')->className('btn btn-sm float-right ms-1 p-1 text-light btn-primary'),
                Button::make('print')->className('btn btn-sm float-right ms-1 p-1 text-light btn-secondary'),
                Button::make('reload')->className('btn btn-sm float-right ms-1 p-1 text-light btn-info')
            ]))
            ->parameters([
                'dom' => 'Brtip',
                'scrollX' => true,
                'responsive' => true,
                'autoWidth' => false,
                'pageLength' => 15,
                'lengthMenu' => [[10, 25, 50, 100], [10, 25, 50, 100]],
                'language' => [
                    'search' => 'Search:',
                    'lengthMenu' => 'Show _MENU_ entries',
                    'info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
                    'infoEmpty' => 'Showing 0 to 0 of 0 entries',
                    'infoFiltered' => '(filtered from _MAX_ total entries)',
                    'emptyTable' => 'No data available in table',
                    'zeroRecords' => 'No matching records found',
                ]
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID')->width(60)->addClass('text-center'),
            Column::computed('route_info')
                ->title('Route')
                ->searchable(false)
                ->orderable(false)
                ->width(200),
            Column::make('route_type')->title('Type')->width(120),
            Column::computed('vehicle_type')
                ->title('Vehicle')
                ->searchable(false)
                ->orderable(false)
                ->width(120),
            Column::make('price')->title('Price')->width(120),
            Column::make('estimated_travel_time')->title('Travel Time')->width(120),
            Column::make('enabled')->title('Status')->width(100),
            Column::computed('approved')->title('Approval')->width(100)->exportable(false)->printable(false),
            Column::computed('bookings_count')->title('Bookings')->width(100)->exportable(false)->printable(false),
            Column::make('created_at')->title('Created')->width(120),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center')
                ->title('Actions'),
        ];
    }

    protected function filename(): string
    {
        return 'SupplierTransports_' . date('YmdHis');
    }
}


