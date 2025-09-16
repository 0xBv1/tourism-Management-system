<?php

namespace App\DataTables;

use App\Models\Transport;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TransportDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('image', function (Transport $transport) {
                if ($transport->featured_image) {
                    return '<img src="' . asset('storage/' . $transport->featured_image) . '" alt="' . e($transport->name) . '" class="rounded" width="50" height="50" style="object-fit: cover;">';
                }
                return '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="mdi mdi-truck text-muted"></i></div>';
            })
            ->addColumn('route_info', function (Transport $transport) {
                return '<div>
                    <strong>' . e($transport->origin_location) . '</strong><br>
                    <small class="text-muted">→ ' . e($transport->destination_location) . '</small>
                </div>';
            })
            ->addColumn('transport_details', function (Transport $transport) {
                $details = '<div>';
                $details .= '<span class="badge bg-info">' . e(ucfirst($transport->transport_type)) . '</span><br>';
                $details .= '<small class="text-muted">' . e($transport->vehicle_type ?? 'N/A') . '</small>';
                
                if ($transport->seating_capacity) {
                    $details .= '<br><small class="text-muted"><i class="mdi mdi-account-group"></i> ' . e($transport->seating_capacity) . ' seats</small>';
                }
                
                $details .= '</div>';
                return $details;
            })
            ->addColumn('pricing', function (Transport $transport) {
                $pricing = '<div>';
                $pricing .= '<strong class="text-success">' . e($transport->formatted_price) . '</strong><br>';
                $pricing .= '<small class="text-muted">' . e($transport->formatted_travel_time) . '</small>';
                
                if ($transport->formatted_distance) {
                    $pricing .= '<br><small class="text-muted"><i class="mdi mdi-map-marker-distance"></i> ' . e($transport->formatted_distance) . '</small>';
                }
                
                $pricing .= '</div>';
                return $pricing;
            })
            ->addColumn('schedule', function (Transport $transport) {
                if ($transport->departure_time && $transport->arrival_time) {
                    return '<div>
                        <small class="text-primary"><i class="mdi mdi-clock-outline"></i> ' . e($transport->departure_time->format('H:i')) . '</small><br>
                        <small class="text-muted"><i class="mdi mdi-clock"></i> ' . e($transport->arrival_time->format('H:i')) . '</small>
                    </div>';
                }
                return '<span class="text-muted">No schedule</span>';
            })
            ->addColumn('status', function (Transport $transport) {
                return '<span class="badge bg-' . $transport->status_color . '">' . e($transport->status_label) . '</span>';
            })
            ->editColumn('created_at', fn(Transport $transport) => $transport->created_at->format('M Y, d'))
            ->addColumn('action', 'dashboard.transports.action')
            ->editColumn('name', fn(Transport $transport) => $transport->name)
            ->filterTranslatedColumn('name')
            ->orderColumn('name', fn($query, $dir) => $query->orderByTranslation('name', $dir))
            ->setRowId('id')
            ->rawColumns(['image', 'route_info', 'transport_details', 'pricing', 'schedule', 'status', 'action']);
    }

    public function query(Transport $model): QueryBuilder
    {
        $query = $model->newQuery();
        
        // Filter by transport type if provided
        if (request()->has('transport_type') && request('transport_type') !== '') {
            $query->where('transport_type', request('transport_type'));
        }
        
        // Filter by vehicle type if provided
        if (request()->has('vehicle_type') && request('vehicle_type') !== '') {
            $query->where('vehicle_type', request('vehicle_type'));
        }
        
        // Filter by status if provided
        if (request()->has('status') && request('status') !== '') {
            $query->where('enabled', request('status') === 'active' ? 1 : 0);
        }
        
        // Filter by route type if provided
        if (request()->has('route_type') && request('route_type') !== '') {
            $query->where('route_type', request('route_type'));
        }
        
        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('data-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
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

    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('image')->title('Image')->orderable(false)->searchable(false),
            Column::make('name'),
            Column::make('route_info')->title('Route')->orderable(false)->searchable(false),
            Column::make('transport_details')->title('Transport Details')->orderable(false)->searchable(false),
            Column::make('pricing')->title('Pricing')->orderable(false)->searchable(false),
            Column::make('schedule')->title('Schedule')->orderable(false)->searchable(false),
            Column::make('status')->title('Status')->orderable(false)->searchable(false),
            Column::make('created_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Transport_' . date('YmdHis');
    }
}
