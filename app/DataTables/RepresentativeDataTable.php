<?php

namespace App\DataTables;

use App\Models\Representative;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class RepresentativeDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', 'dashboard.representatives.action')
            ->addColumn('city_name', function ($representative) {
                return $representative->city->name ?? '-';
            })
            ->addColumn('status_badge', function ($representative) {
                $color = $representative->status_color;
                $label = $representative->status_label;
                return "<span class='badge badge-{$color}'>{$label}</span>";
            })
            ->addColumn('languages_display', function ($representative) {
                return is_array($representative->languages) ? implode(', ', $representative->languages) : '-';
            })
            ->addColumn('rating_display', function ($representative) {
                return $representative->average_rating . ' (' . $representative->total_ratings . ')';
            })
            ->rawColumns(['action', 'status_badge']);
    }

    public function query(Representative $model)
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
            Column::make('email'),
            Column::make('phone'),
            Column::make('city_name')->title('City'),
            Column::make('company_name')->title('Company'),
            Column::make('languages_display')->title('Languages'),
            Column::make('experience_years')->title('Experience'),
            Column::make('rating_display')->title('Rating'),
            Column::make('price_per_day')->title('Price/Day'),
            Column::make('status_badge')->title('Status'),
            Column::make('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Representatives_' . date('YmdHis');
    }
}
