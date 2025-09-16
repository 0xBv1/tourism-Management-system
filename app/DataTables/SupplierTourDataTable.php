<?php

namespace App\DataTables;

use App\Models\SupplierTour;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SupplierTourDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('image', function (SupplierTour $tour) {
                if ($tour->featured_image) {
                    return '<img src="' . asset('storage/' . $tour->featured_image) . '" alt="' . $tour->title . '" class="rounded" width="50" height="50" style="object-fit: cover;">';
                }
                return '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="fa fa-map text-muted"></i></div>';
            })
            ->editColumn('duration', function (SupplierTour $tour) {
                return '<span class="badge bg-info">' . ($tour->duration ?? 'N/A') . '</span>';
            })
            ->editColumn('adult_price', function (SupplierTour $tour) {
                return '<strong>$' . number_format($tour->adult_price, 2) . '</strong>';
            })
            ->editColumn('enabled', function (SupplierTour $tour) {
                return $tour->enabled ? 
                    '<span class="badge bg-success">Active</span>' : 
                    '<span class="badge bg-danger">Inactive</span>';
            })
            ->editColumn('approved', function (SupplierTour $tour) {
                if ($tour->approved) {
                    return '<span class="badge bg-success"><i class="fa fa-check"></i> Approved</span>';
                } else {
                    return '<span class="badge bg-warning"><i class="fa fa-clock"></i> Pending</span>';
                }
            })
            ->editColumn('created_at', function (SupplierTour $tour) {
                return $tour->created_at->format('M d, Y');
            })
            ->addColumn('action', 'dashboard.supplier.tours.action')
            ->setRowId('id')
            ->rawColumns(['image', 'duration', 'adult_price', 'enabled', 'approved', 'action']);
    }

    public function query(SupplierTour $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->where('supplier_id', auth()->user()->supplier->id);
            
        // Handle approval status filtering
        if (request()->has('approval_status') && request('approval_status') !== '') {
            $status = request('approval_status');
            if ($status === 'approved') {
                $query->where('approved', true);
            } elseif ($status === 'pending') {
                $query->where('approved', false);
            }
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
            ]))
            ->parameters([
                'initComplete' => "function () {
                    this.api().columns().every(function () {
                        var column = this;
                        var input = document.createElement(\"input\");
                        $(input).appendTo($(column.footer()).empty())
                        .on('change', function () {
                            column.search($(this).val(), false, false, true).draw();
                        });
                    });
                }",
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID'),
            Column::computed('image')
                ->title('Image')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
            Column::make('title')->title('Tour Name'),
            Column::make('duration')->title('Duration'),
            Column::make('adult_price')->title('Price'),
            Column::make('enabled')->title('Status'),
            Column::make('approved')->title('Admin Approval'),
            Column::make('created_at')->title('Created'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'SupplierTours_' . date('YmdHis');
    }
}
