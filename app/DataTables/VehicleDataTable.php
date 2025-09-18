<?php

namespace App\DataTables;

use App\Models\Vehicle;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class VehicleDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', 'dashboard.vehicles.action')
            ->addColumn('city_name', function ($vehicle) {
                return $vehicle->city->name ?? '-';
            })
            ->addColumn('status_badge', function ($vehicle) {
                $color = $vehicle->status_color;
                $label = $vehicle->status_label;
                return "<span class='badge badge-{$color}'>{$label}</span>";
            })
            ->addColumn('maintenance_status', function ($vehicle) {
                if ($vehicle->needsMaintenance()) {
                    return "<span class='badge badge-warning'>Due Soon</span>";
                }
                return "<span class='badge badge-success'>OK</span>";
            })
            ->rawColumns(['action', 'status_badge', 'maintenance_status']);
    }

    public function query(Vehicle $model)
    {
        return $model->newQuery()->with('city');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('data-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(array_reverse([
                Button::make('excel')->className('btn btn-sm float-right ms-1 p-1 text-light btn-success'),
                Button::make('csv')->className('btn btn-sm float-right ms-1 p-1 text-light btn-primary'),
                Button::make('print')->className('btn btn-sm float-right ms-1 p-1 text-light btn-secondary'),
                Button::make('reload')->className('btn btn-sm float-right ms-1 p-1 text-light btn-info')
            ]));
    }

    protected function getColumns()
    {
        return [
            Column::make('id'),
            Column::make('name'),
            Column::make('type'),
            Column::make('brand'),
            Column::make('model'),
            Column::make('license_plate'),
            Column::make('capacity'),
            Column::make('city_name')->title('City'),
            Column::make('price_per_day')->title('Price/Day'),
            Column::make('status_badge')->title('Status'),
            Column::make('maintenance_status')->title('Maintenance'),
            Column::make('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Vehicles_' . date('YmdHis');
    }
}
