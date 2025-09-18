<?php

namespace App\DataTables;

use App\Models\BookingFile;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BookingFilesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('status', function (BookingFile $booking) {
                $color = $this->getStatusColor($booking->status->value);
                $label = $booking->status->getLabel();
                return '<span class="badge bg-' . $color . '">' . $label . '</span>';
            })
            ->editColumn('total_amount', function (BookingFile $booking) {
                return $booking->currency . ' ' . number_format($booking->total_amount, 2);
            })
            ->editColumn('total_paid', function (BookingFile $booking) {
                return $booking->currency . ' ' . number_format($booking->total_paid, 2);
            })
            ->editColumn('remaining_amount', function (BookingFile $booking) {
                $remaining = $booking->remaining_amount;
                $color = $remaining > 0 ? 'text-danger' : 'text-success';
                return '<span class="' . $color . '">' . $booking->currency . ' ' . number_format($remaining, 2) . '</span>';
            })
            ->editColumn('created_at', function (BookingFile $booking) {
                return $booking->created_at->format('M d, Y H:i');
            })
            ->editColumn('generated_at', function (BookingFile $booking) {
                return $booking->generated_at ? $booking->generated_at->format('M d, Y H:i') : 'Not Generated';
            })
            ->editColumn('sent_at', function (BookingFile $booking) {
                return $booking->sent_at ? $booking->sent_at->format('M d, Y H:i') : 'Not Sent';
            })
            ->addColumn('client_name', function (BookingFile $booking) {
                return $booking->inquiry->client?->name ?? 'N/A';
            })
            ->addColumn('inquiry_subject', function (BookingFile $booking) {
                return $booking->inquiry?->subject ?? 'N/A';
            })
            ->addColumn('payment_status', function (BookingFile $booking) {
                if ($booking->isFullyPaid()) {
                    return '<span class="badge bg-success">Fully Paid</span>';
                } elseif ($booking->total_paid > 0) {
                    return '<span class="badge bg-warning">Partially Paid</span>';
                } else {
                    return '<span class="badge bg-danger">Not Paid</span>';
                }
            })
            ->addColumn('checklist_progress', function (BookingFile $booking) {
                $progress = $booking->checklist_progress;
                $color = $progress >= 100 ? 'success' : ($progress >= 50 ? 'warning' : 'danger');
                return '<div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-' . $color . '" role="progressbar" style="width: ' . $progress . '%">
                        ' . $progress . '%
                    </div>
                </div>';
            })
            ->addColumn('action', 'dashboard.bookings.action')
            ->setRowId('id')
            ->rawColumns(['action', 'status', 'remaining_amount', 'payment_status', 'checklist_progress']);
    }

    public function query(BookingFile $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['inquiry.client', 'payments']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('booking-files-data-table')
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
            Column::make('file_name')->title('File Name'),
            Column::make('client_name')->title('Client'),
            Column::make('inquiry_subject')->title('Subject'),
            Column::make('status')->title('Status'),
            Column::make('total_amount')->title('Total Amount'),
            Column::make('total_paid')->title('Paid Amount'),
            Column::make('remaining_amount')->title('Remaining'),
            Column::make('payment_status')->title('Payment Status'),
            Column::make('checklist_progress')->title('Progress'),
            Column::make('generated_at')->title('Generated'),
            Column::make('sent_at')->title('Sent'),
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
        return 'Booking_Files_' . date('YmdHis');
    }

    private function getStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'in_progress' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary',
            default => 'secondary',
        };
    }
}
