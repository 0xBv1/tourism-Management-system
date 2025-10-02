<?php

namespace App\DataTables;

use App\Models\NileCruise;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class NileCruiseDataTable extends DataTable
{
    /**
     * Build DataTable class.
     */
    public function dataTable(DataTables $dataTable)
    {
        return $dataTable
            ->eloquent($this->query())
            ->addColumn('status_badge', function ($nileCruise) {
                $colorClass = $nileCruise->status_color ?? 'secondary';
                $statusText = $nileCruise->status_label ?? 'Unknown';
                return '<span class="badge bg-' . $colorClass . '">' . $statusText . '</span>';
            })
            ->addColumn('capacity_display', function ($nileCruise) {
                return $nileCruise->capacity . ' passengers';
            })
            ->addColumn('duration_display', function ($nileCruise) {
                return $nileCruise->duration_nights . ' night(s)';
            })
            ->addColumn('price_display', function ($nileCruise) {
                if ($nileCruise->price_per_person && $nileCruise->price_per_cabin) {
                    return '<div class="small">Person: ' . $nileCruise->currency . ' ' . number_format($nileCruise->price_per_person, 2) . '</div>' .
                           '<div class="small">Cabin: ' . $nileCruise->currency . ' ' . number_format($nileCruise->price_per_cabin, 2) . '</div>';
                } elseif ($nileCruise->price_per_person) {
                    return $nileCruise->currency . ' ' . number_format($nileCruise->price_per_person, 2) . ' /person';
                } elseif ($nileCruise->price_per_cabin) {
                    return $nileCruise->currency . ' ' . number_format($nileCruise->price_per_cabin, 2) . ' /cabin';
                }
                return 'No pricing';
            })
            ->addColumn('city_name', function ($nileCruise) {
                return $nileCruise->city->name ?? 'Unknown City';
            })
            ->addColumn('action', 'dashboard.nile-cruises.action')
            ->rawColumns(['status_badge', 'price_display', 'action'])
            ->filter(function ($query) {
                if (request()->has('search.value') && request('search.value')) {
                    $searchValue = request('search.value');
                    $query->where(function($q) use ($searchValue) {
                        $q->where('name', 'like', '%' . $searchValue . '%')
                          ->orWhere('description', 'like', '%' . $searchValue . '%')
                          ->orWhere('vessel_type', 'like', '%' . $searchValue . '%')
                          ->orWhereHas('city', function($q) use ($searchValue) {
                              $q->where('name', 'like', '%' . $searchValue . '%');
                          });
                    });
                }
            });
    }

    /**
     * Get query source of dataTable.
     */
    public function query()
    {
        return NileCruise::with(['city'])->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('nile-cruises-data-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1, 'asc')
                    ->buttons([
                        'excel',
                        'csv',
                        'pdf'
                    ])
                    ->parameters([
                        'responsive' => true,
                        'autoWidth' => false,
                        'lengthMenu' => [[10, 25, 50, 100], [10, 25, 50, 100]]
                    ]);
    }

    /**
     * Get columns.
     */
    protected function getColumns()
    {
        return [
            Column::make('id')->title('#'),
            Column::make('name')->title('Name'),
            Column::make('city_name', 'city.name')->title('City'),
            Column::make('vessel_type')->title('Vessel Type'),
            Column::make('capacity_display')->title('Capacity')->searchable(false),
            Column::make('duration_display')->title('Duration')->searchable(false),
            Column::make('price_display')->title('Pricing')->searchable(false),
            Column::make('status_badge')->title('Status')->searchable(false),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(120)
                  ->addClass('text-center')
        ];
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string
    {
        return 'NileCruises_' . date('YmdHis');
    }
}
