<?php

namespace App\Exports;

use App\Models\BookingFile;
use App\Enums\BookingStatus;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BookingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return BookingFile::with(['inquiry.client', 'payments'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'File Name',
            'Client',
            'Status',
            'Total Amount',
            'Paid Amount',
            'Remaining Amount',
            'Currency',
            'Generated At',
            'Sent At',
            'Downloaded At',
            'Created At',
            'Updated At'
        ];
    }

    public function map($booking): array
    {
        return [
            $booking->id,
            $booking->file_name,
            $booking->inquiry?->client?->name ?? 'N/A',
            $booking->status->getLabel(),
            $booking->total_amount,
            $booking->total_paid,
            $booking->remaining_amount,
            $booking->currency,
            $booking->generated_at?->format('Y-m-d H:i:s') ?? 'N/A',
            $booking->sent_at?->format('Y-m-d H:i:s') ?? 'N/A',
            $booking->downloaded_at?->format('Y-m-d H:i:s') ?? 'N/A',
            $booking->created_at->format('Y-m-d H:i:s'),
            $booking->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 30,  // File Name
            'C' => 20,  // Client
            'D' => 12,  // Status
            'E' => 15,  // Total Amount
            'F' => 15,  // Paid Amount
            'G' => 15,  // Remaining Amount
            'H' => 10,  // Currency
            'I' => 20,  // Generated At
            'J' => 20,  // Sent At
            'K' => 20,  // Downloaded At
            'L' => 20,  // Created At
            'M' => 20,  // Updated At
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '366092'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Add title
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:M1');
                $sheet->setCellValue('A1', 'BOOKINGS REPORT');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Add date range
                $sheet->mergeCells('A2:M2');
                $sheet->setCellValue('A2', 'Period: ' . $this->startDate->format('M d, Y') . ' - ' . $this->endDate->format('M d, Y'));
                $sheet->getStyle('A2')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Add summary
                $totalBookings = $this->collection()->count();
                $confirmedBookings = $this->collection()->where('status', BookingStatus::CONFIRMED)->count();
                $totalRevenue = $this->collection()->sum('total_amount');
                $totalPaid = $this->collection()->sum('total_paid');
                
                $sheet->insertNewRowBefore(4, 1);
                $sheet->mergeCells('A4:M4');
                $sheet->setCellValue('A4', "Summary: {$totalBookings} Total Bookings | {$confirmedBookings} Confirmed | $" . number_format($totalRevenue, 2) . " Total Revenue | $" . number_format($totalPaid, 2) . " Paid");
                $sheet->getStyle('A4')->getFont()->setBold(true);
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Apply borders to all data
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                $sheet->getStyle('A5:' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                
                // Auto-fit columns
                foreach (range('A', $lastColumn) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
