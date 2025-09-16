<?php

namespace App\DataTables;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SupplierDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($supplier) {
                return view('dashboard.suppliers.partials.actions', compact('supplier'))->render();
            })
            ->addColumn('status', function ($supplier) {
                return view('dashboard.suppliers.partials.status', compact('supplier'))->render();
            })
            ->editColumn('commission_rate', function ($supplier) {
                return number_format((float) $supplier->commission_rate, 2) . '%';
            })
            ->editColumn('wallet_balance', function ($supplier) {
                return $supplier->formatted_wallet_balance;
            })
            ->addColumn('user_name', function ($supplier) {
                return $supplier->user->name ?? 'N/A';
            })
            ->addColumn('user_email', function ($supplier) {
                return $supplier->user->email ?? 'N/A';
            })
            ->addColumn('services_count', function ($supplier) {
                return $supplier->hotels()->count() + $supplier->trips()->count() + 
                       $supplier->tours()->count() + $supplier->transports()->count();
            })
            ->addColumn('pending_approvals', function ($supplier) {
                $hotelsPending = Schema::hasColumn('supplier_hotels', 'approved')
                    ? $supplier->hotels()->where('approved', false)->count()
                    : 0;

                $tripsPending = Schema::hasColumn('supplier_trips', 'approved')
                    ? $supplier->trips()->where('approved', false)->count()
                    : 0;

                $toursPending = Schema::hasColumn('supplier_tours', 'approved')
                    ? $supplier->tours()->where('approved', false)->count()
                    : 0;

                $transportsPending = Schema::hasColumn('supplier_transports', 'approved')
                    ? $supplier->transports()->where('approved', false)->count()
                    : 0;

                return $hotelsPending + $tripsPending + $toursPending + $transportsPending;
            })
            ->rawColumns(['action', 'status'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Supplier $model): QueryBuilder
    {
        return $model->with('user')->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
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
                Button::make('reload')->className('btn btn-sm float-right ms-1 p-1 text-light btn-info'),
            ]));
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('company_name')->title('Company Name'),
            Column::make('user_name')->title('User Name'),
            Column::make('user_email')->title('User Email'),
            Column::make('company_email')->title('Company Email'),
            Column::make('phone')->title('Phone'),
            Column::make('commission_rate')->title('Commission Rate'),
            Column::make('wallet_balance')->title('Wallet Balance'),
            Column::make('services_count')->title('Services'),
            Column::make('pending_approvals')->title('Pending'),
            Column::make('status')->title('Status'),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center')
                  ->title('Actions'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Suppliers_' . date('YmdHis');
    }
}
