<?php

namespace App\DataTables;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TicketDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
           
            ->editColumn('price_per_person', function (Ticket $ticket) {
                return $ticket->price_per_person ? $ticket->currency . ' ' . number_format($ticket->price_per_person, 2) : '-';
            })
            ->editColumn('duration_hours', function (Ticket $ticket) {
                return $ticket->duration_hours ? $ticket->duration_hours . ' hrs' : '-';
            })
            ->addColumn('city_name', function (Ticket $ticket) {
                return $ticket->city->name ?? '-';
            })
            ->addColumn('action', 'dashboard.tickets.action')
            ->setRowId('id')
            ->rawColumns(['action', 'status']);
    }

    public function query(Ticket $model): QueryBuilder
    {
        return $model->newQuery()->with('city');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('ticket-data-table')
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
            Column::make('price_per_person')->title('Price/Person'),
            Column::make('duration_hours')->title('Duration'),
            Column::make('max_participants')->title('Max Participants'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Tickets_' . date('YmdHis');
    }
}
