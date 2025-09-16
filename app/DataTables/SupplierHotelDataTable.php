<?php

namespace App\DataTables;

use App\Models\SupplierHotel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SupplierHotelDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('image', function (SupplierHotel $hotel) {
                if ($hotel->featured_image) {
                    return '<img src="' . asset('storage/' . $hotel->featured_image) . '" alt="' . ($hotel->name ?? 'Hotel') . '" class="rounded" width="50" height="50" style="object-fit: cover;">';
                }
                return '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="fa fa-hotel text-muted"></i></div>';
            })
            ->editColumn('stars', function (SupplierHotel $hotel) {
                $stars = '';
                $hotelStars = $hotel->stars ?? 0;
                for ($i = 1; $i <= 5; $i++) {
                    $stars .= $i <= $hotelStars ? '<i class="fa fa-star text-warning"></i>' : '<i class="fa fa-star-o text-muted"></i>';
                }
                return $stars;
            })
            ->editColumn('enabled', function (SupplierHotel $hotel) {
                return $hotel->enabled ? 
                    '<span class="badge bg-success">Active</span>' : 
                    '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('approved', function (SupplierHotel $hotel) {
                return $hotel->approved
                    ? '<span class="badge bg-success">Approved</span>'
                    : '<span class="badge bg-warning">Pending</span>';
            })
            ->addColumn('bookings_count', function (SupplierHotel $hotel) {
                return '<span class="badge bg-secondary">' . ((int) ($hotel->bookings_count ?? 0)) . '</span>';
            })
            ->addColumn('revenue', function (SupplierHotel $hotel) {
                $sum = (float) ($hotel->bookings_sum_total_price ?? 0);
                return '<strong>$' . number_format($sum, 2) . '</strong>';
            })
            ->editColumn('created_at', function (SupplierHotel $hotel) {
                return $hotel->created_at ? $hotel->created_at->format('M d, Y') : 'N/A';
            })
            ->editColumn('name', fn(SupplierHotel $hotel) => $hotel->name ?? 'N/A')
            ->editColumn('city', fn(SupplierHotel $hotel) => $hotel->city ?? 'N/A')
            ->filterTranslatedColumn('name')
            ->filterTranslatedColumn('city')
            ->orderColumn('name', fn($query, $dir) => $query->orderByTranslation('name', $dir))
            ->orderColumn('city', fn($query, $dir) => $query->orderByTranslation('city', $dir))
            ->addColumn('action', 'dashboard.supplier.hotels.action')
            ->setRowId('id')
            ->rawColumns(['image', 'stars', 'enabled', 'approved', 'bookings_count', 'revenue', 'action']);
    }

    public function query(SupplierHotel $model): QueryBuilder
    {
        return $model->newQuery()
            ->where('supplier_id', auth()->user()->supplier->id)
            ->with(['supplier', 'bookings'])
            ->withCount('bookings')
            ->withSum('bookings', 'total_price');
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
            Column::make('id')->title('ID'),
            Column::computed('image')
                ->title('Image')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
            Column::make('name')->title('Hotel Name'),
            Column::make('city')->title('City'),
            Column::make('stars')->title('Stars'),
            Column::make('enabled')->title('Status'),
            Column::computed('approved')->title('Admin Approval')->exportable(false)->printable(false),
            Column::computed('bookings_count')->title('Bookings')->exportable(false)->printable(false),
            Column::computed('revenue')->title('Revenue')->exportable(false)->printable(false),
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
        return 'SupplierHotels_' . date('YmdHis');
    }
}



