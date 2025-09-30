<?php

namespace App\DataTables;

use App\Models\BookingFile;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BookingDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('status', function (BookingFile $booking) {
                $color = $booking->status->getColor();
                $label = $booking->status->getLabel();
                return '<span class="badge bg-' . $color . '">' . $label . '</span>';
            })
            ->editColumn('total_amount', function (BookingFile $booking) {
                return $booking->total_amount ? $booking->currency . ' ' . number_format($booking->total_amount, 2) : '-';
            })
           
            ->editColumn('generated_at', function (BookingFile $booking) {
                return $booking->generated_at ? $booking->generated_at->format('M d, Y H:i') : '-';
            })
            ->editColumn('sent_at', function (BookingFile $booking) {
                return $booking->sent_at ? $booking->sent_at->format('M d, Y H:i') : '-';
            })
            ->editColumn('downloaded_at', function (BookingFile $booking) {
                return $booking->downloaded_at ? $booking->downloaded_at->format('M d, Y H:i') : '-';
            })
            
            ->addColumn('inquiry_subject', function (BookingFile $booking) {
                return $booking->inquiry->subject ?? '-';
            })
            ->addColumn('action', 'dashboard.bookings.action')
            ->setRowId('id')
            ->rawColumns(['status', 'checklist_progress', 'action']);
    }

    public function query(BookingFile $model): QueryBuilder
    {
        return $model->newQuery()->with(['inquiry.client']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('booking-data-table')
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
            Column::make('file_name'),
            Column::make('inquiry_subject')->title('Inquiry Subject'),
            Column::make('status'),
            Column::make('total_amount')->title('Amount'),
            Column::make('generated_at')->title('Generated'),
            Column::make('sent_at')->title('Sent'),
            Column::make('downloaded_at')->title('Downloaded'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Booking_' . date('YmdHis');
    }
}
