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
            ->addColumn('city_name', function (Restaurant $restaurant) {
                return $restaurant->city->name ?? '-';
            })
            ->addColumn('meals_count', function (Restaurant $restaurant) {
                $count = $restaurant->meals->count();
                if ($count > 0) {
                    return '<span class="badge bg-info">' . $count . ' meal' . ($count > 1 ? 's' : '') . '</span>';
                }
                return '<span class="text-muted">No meals</span>';
            })
            ->addColumn('meals_preview', function (Restaurant $restaurant) {
                $meals = $restaurant->meals->take(3);
                if ($meals->count() > 0) {
                    $preview = $meals->map(function ($meal) {
                        return '<small class="d-block text-truncate" style="max-width: 150px;" title="' . e($meal->name) . '">' . 
                               '<i class="fas fa-utensils me-1 text-primary"></i>' . 
                               e($meal->name) . ' - ' . $meal->currency . ' ' . number_format($meal->price, 2) . 
                               '</small>';
                    })->join('');
                    
                    if ($restaurant->meals->count() > 3) {
                        $preview .= '<small class="text-muted">+ ' . ($restaurant->meals->count() - 3) . ' more</small>';
                    }
                    
                    return $preview;
                }
                return '<span class="text-muted">No meals added</span>';
            })
            ->addColumn('featured_meals', function (Restaurant $restaurant) {
                $featuredMeals = $restaurant->meals->where('is_featured', true);
                if ($featuredMeals->count() > 0) {
                    return '<span class="badge bg-warning"><i class="fas fa-star me-1"></i>' . $featuredMeals->count() . ' featured</span>';
                }
                return '<span class="text-muted">None</span>';
            })
            ->addColumn('action', 'dashboard.restaurants.action')
            ->setRowId('id')
            ->rawColumns(['action', 'status', 'meals_count', 'meals_preview', 'featured_meals']);
    }

    public function query(Restaurant $model): QueryBuilder
    {
        return $model->newQuery()->with(['city', 'meals']);
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
            Column::make('price_range')->title('Price Range'),
            Column::make('meals_count')->title('Meals')->orderable(false)->searchable(false),
            Column::make('meals_preview')->title('Meal Preview')->orderable(false)->searchable(false),
            Column::make('featured_meals')->title('Featured')->orderable(false)->searchable(false),
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
