<?php

namespace App\DataTables;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class HotelDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('status', function (Hotel $hotel) {
                $color = $hotel->status_color;
                $label = $hotel->status_label;
                return '<span class="badge bg-' . $color . '">' . $label . '</span>';
            })
            ->editColumn('price_per_night', function (Hotel $hotel) {
                return $hotel->price_per_night ? $hotel->currency . ' ' . number_format($hotel->price_per_night, 2) : '-';
            })
            ->editColumn('utilization', function (Hotel $hotel) {
                return $hotel->utilization_percentage . '%';
            })
            ->addColumn('city_name', function (Hotel $hotel) {
                return $hotel->city->name ?? '-';
            })
            ->addColumn('action', 'dashboard.hotels.action')
            ->setRowId('id')
            ->rawColumns(['action', 'status']);
    }

    public function query(Hotel $model): QueryBuilder
    {
        return $model->newQuery()->with('city');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('hotel-data-table')
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
            Column::make('name'),
            Column::make('city_name')->title('City'),
            Column::make('star_rating')->title('Stars'),
            Column::make('total_rooms')->title('Total Rooms'),
            Column::make('available_rooms')->title('Available Rooms'),
            Column::make('utilization')->title('Utilization %'),
            Column::make('price_per_night')->title('Price/Night'),
            Column::make('status')->title('Status'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Hotels_' . date('YmdHis');
    }
}
