<?php

namespace App\DataTables;

use App\Models\Dahabia;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DahabiaDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('status', function (Dahabia $dahabia) {
                $color = $dahabia->status_color;
                $label = $dahabia->status_label;
                return '<span class="badge bg-' . $color . '">' . $label . '</span>';
            })
            ->editColumn('price_per_person', function (Dahabia $dahabia) {
                return $dahabia->price_per_person ? $dahabia->currency . ' ' . number_format($dahabia->price_per_person, 2) : '-';
            })
            ->editColumn('price_per_charter', function (Dahabia $dahabia) {
                return $dahabia->price_per_charter ? $dahabia->currency . ' ' . number_format($dahabia->price_per_charter, 2) : '-';
            })
            ->editColumn('vessel_length', function (Dahabia $dahabia) {
                return $dahabia->vessel_length ? $dahabia->vessel_length . ' m' : '-';
            })
            ->addColumn('city_name', function (Dahabia $dahabia) {
                return $dahabia->city->name ?? '-';
            })
            ->addColumn('action', 'dashboard.dahabias.action')
            ->setRowId('id')
            ->rawColumns(['action', 'status']);
    }

    public function query(Dahabia $model): QueryBuilder
    {
        return $model->newQuery()->with('city', 'bookings.bookingFile');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('dahabia-data-table')
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
            Column::make('vessel_length')->title('Vessel Length (m)'),
            Column::make('capacity'),
            Column::make('duration_nights')->title('Duration (Nights)'),
            Column::make('price_per_person')->title('Price/Person'),
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
        return 'Dahabias_' . date('YmdHis');
    }
}
