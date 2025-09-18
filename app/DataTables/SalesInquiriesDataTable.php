<?php

namespace App\DataTables;

use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SalesInquiriesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('status', function (Inquiry $inquiry) {
                $color = $this->getStatusColor($inquiry->status->value);
                $label = $inquiry->status->getLabel();
                return '<span class="badge bg-' . $color . '">' . $label . '</span>';
            })
            ->editColumn('created_at', function (Inquiry $inquiry) {
                return $inquiry->created_at->format('M d, Y H:i');
            })
            ->editColumn('assigned_to', function (Inquiry $inquiry) {
                return $inquiry->assignedUser?->name ?? 'Unassigned';
            })
            ->addColumn('client_name', function (Inquiry $inquiry) {
                return $inquiry->client?->name ?? 'N/A';
            })
            ->addColumn('estimated_value', function (Inquiry $inquiry) {
                return $inquiry->estimated_budget ? '$' . number_format($inquiry->estimated_budget, 2) : 'N/A';
            })
            ->addColumn('days_since_inquiry', function (Inquiry $inquiry) {
                return $inquiry->created_at->diffInDays(now()) . ' days';
            })
            ->addColumn('conversion_status', function (Inquiry $inquiry) {
                if ($inquiry->bookingFile) {
                    return '<span class="badge bg-success">Converted</span>';
                }
                return '<span class="badge bg-warning">Pending</span>';
            })
            ->addColumn('action', 'dashboard.sales.inquiries.action')
            ->setRowId('id')
            ->rawColumns(['action', 'status', 'conversion_status']);
    }

    public function query(Inquiry $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['client', 'assignedUser', 'bookingFile'])
            ->where('status', '!=', 'cancelled');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('sales-inquiries-data-table')
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
            ]))
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'pageLength' => 25,
                'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('client_name')->title('Client'),
            Column::make('name')->title('Contact Name'),
            Column::make('email')->title('Email'),
            Column::make('phone')->title('Phone'),
            Column::make('subject')->title('Subject'),
            Column::make('estimated_value')->title('Est. Value'),
            Column::make('status')->title('Status'),
            Column::make('conversion_status')->title('Conversion'),
            Column::make('assigned_to')->title('Assigned To'),
            Column::make('days_since_inquiry')->title('Days Since'),
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
        return 'Sales_Inquiries_' . date('YmdHis');
    }

    private function getStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'completed' => 'info',
            default => 'secondary',
        };
    }
}
