<?php

namespace App\DataTables;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RestaurantDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('status', function (Restaurant $restaurant) {
                $color = $restaurant->status_color;
                $label = $restaurant->status_label;
                return '<span class="badge bg-' . $color . '">' . $label . '</span>';
            })
            ->editColumn('price_per_meal', function (Restaurant $restaurant) {
                return $restaurant->price_per_meal ? $restaurant->currency . ' ' . number_format($restaurant->price_per_meal, 2) : '-';
            })
            ->addColumn('city_name', function (Restaurant $restaurant) {
                return $restaurant->city->name ?? '-';
            })
            ->addColumn('action', 'dashboard.restaurants.action')
            ->setRowId('id')
            ->rawColumns(['action', 'status']);
    }

    public function query(Restaurant $model): QueryBuilder
    {
        return $model->newQuery()->with('city');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('restaurant-data-table')
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
            Column::make('cuisine_type')->title('Cuisine'),
            Column::make('price_range')->title('Price Range'),
            Column::make('price_per_meal')->title('Price/Meal'),
            Column::make('capacity'),
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
        return 'Restaurants_' . date('YmdHis');
    }
}
