<?php

namespace App\DataTables;

use App\Models\SupplierRoom;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SupplierRoomDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', fn(SupplierRoom $room) => $room->created_at->format('M Y, d'))
            ->editColumn('supplier_hotel', fn(SupplierRoom $room) => $room->supplierHotel?->name)
            ->addColumn('action', 'dashboard.supplier.rooms.action')
            ->editColumn('name', fn(SupplierRoom $room) => $room->name)
            ->editColumn('status', fn(SupplierRoom $room) => view('dashboard.supplier.rooms.status', compact('room')))
            ->filterColumn('supplier_hotel', function ($query, $keyword) {
                $query->whereHas('supplierHotel', function ($query) use ($keyword) {
                    $query->whereTranslationLike('name', "%{$keyword}%");
                });
            })
            ->filterTranslatedColumn('name')
            ->orderColumn('name', fn($query, $dir) => $query->orderByTranslation('name', $dir))
            ->setRowId('id')
            ->rawColumns(['action', 'status']);
    }

    public function query(SupplierRoom $model): QueryBuilder
    {
        $user = auth()->user();
        $supplier = $user->supplier;
        
        return $model->newQuery()
            ->with('supplierHotel')
            ->whereIn('supplier_hotel_id', function($query) use ($supplier) {
                $query->select('id')
                      ->from('supplier_hotels')
                      ->where('supplier_id', $supplier->id);
            });
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
            Column::make('name'),
            Column::make('supplier_hotel'),
            Column::make('slug'),
            Column::make('status'),
            Column::make('bed_count'),
            Column::make('room_type'),
            Column::make('night_price'),
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
        return 'SupplierRoom_' . date('YmdHis');
    }
}
