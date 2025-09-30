<?php

namespace App\DataTables;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PaymentDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('status', function (Payment $payment) {
                $color = $payment->status_color;
                $label = $payment->status_label;
                return '<span class="badge bg-' . $color . '">' . $label . '</span>';
            })
            ->editColumn('amount', function (Payment $payment) {
                return $payment->formatted_amount;
            })
            ->editColumn('gateway', function (Payment $payment) {
                return ucfirst(str_replace('_', ' ', $payment->gateway));
            })
            ->editColumn('paid_at', function (Payment $payment) {
                return $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : '-';
            })
            ->editColumn('created_at', function (Payment $payment) {
                return $payment->created_at->format('M d, Y H:i');
            })
            ->addColumn('booking_file', function (Payment $payment) {
                return $payment->booking?->file_name ?? '-';
            })
        
            ->addColumn('action', 'dashboard.payments.action')
            ->setRowId('id')
            ->rawColumns(['status', 'action']);
    }

    public function query(Payment $model): QueryBuilder
    {
        return $model->newQuery()->with(['booking.inquiry.client']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('payment-data-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Blfrtip')
            ->orderBy(0)
            ->selectStyleSingle()
            ->buttons(array_reverse([
                Button::make('excel')->className('btn btn-sm float-right ms-1 p-1 text-light btn-success'),
                Button::make('csv')->className('btn btn-sm float-right ms-1 p-1 text-light btn-primary'),
                Button::make('print')->className('btn btn-sm float-right ms-1 p-1 text-light btn-secondary'),
                Button::make('reload')->className('btn btn-sm float-right ms-1 p-1 p-1 text-light btn-info')
            ]));
    }

    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('reference_number')->title('Reference'),
            Column::make('booking_file')->title('Booking File'),
            Column::make('gateway'),
            Column::make('amount'),
            Column::make('status'),
            Column::make('paid_at')->title('Paid At'),
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
        return 'Payments_' . date('YmdHis');
    }
}

