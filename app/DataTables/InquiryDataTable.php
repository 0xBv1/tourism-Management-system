<?php

namespace App\DataTables;

use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InquiryDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('status', fn(Inquiry $inquiry) => '<span class="badge badge-' . $this->getStatusColor($inquiry->status->value) . '">' . ucfirst($inquiry->status->value) . '</span>')
            ->editColumn('created_at', fn(Inquiry $inquiry) => $inquiry->created_at->format('M Y, d'))
            ->editColumn('assigned_to', fn(Inquiry $inquiry) => $inquiry->assignedUser?->name ?? 'Unassigned')
            ->addColumn('action', 'dashboard.inquiries.action')
            ->setRowId('id')
            ->rawColumns(['action', 'status']);
    }

    public function query(Inquiry $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['client', 'assignedUser']);
        
        // Filter inquiries based on user role
        if (auth()->user()->hasRole(['Reservation', 'Operation'])) {
            // For Reservation and Operation roles, show only inquiries assigned to the current user
            $query->where('assigned_to', auth()->id());
        }
        // For other roles (Admin, Administrator, Sales, Finance), show all inquiries
        
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
            ]));
    }

    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('name'),
            Column::make('email'),
            Column::make('phone'),
            Column::make('subject'),
            Column::make('status'),
            Column::make('assigned_to'),
            Column::make('created_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Inquiry_' . date('YmdHis');
    }

    private function getStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'completed' => 'info',
            default => 'secondary'
        };
    }
}





