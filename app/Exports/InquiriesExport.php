<?php

namespace App\Exports;

use App\Models\Inquiry;
use App\Enums\InquiryStatus;
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

class InquiriesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
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
        return Inquiry::with(['client'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Inquiry ID',
            'Guest Name',
            'Email',
            'Phone',
            'Client',
            'Subject',
            'Status',
            'Total Amount',
            'Paid Amount',
            'Remaining Amount',
            'Arrival Date',
            'Departure Date',
            'Number of Pax',
            'Tour Name',
            'Nationality',
            'Created At',
            'Updated At'
        ];
    }

    public function map($inquiry): array
    {
        return [
            $inquiry->id,
            $inquiry->inquiry_id,
            $inquiry->guest_name,
            $inquiry->email,
            $inquiry->phone,
            $inquiry->client?->name ?? 'N/A',
            $inquiry->subject,
            $inquiry->status->getLabel(),
            $inquiry->total_amount ?? 0,
            $inquiry->paid_amount ?? 0,
            $inquiry->remaining_amount ?? 0,
            $inquiry->arrival_date?->format('Y-m-d') ?? 'N/A',
            $inquiry->departure_date?->format('Y-m-d') ?? 'N/A',
            $inquiry->number_pax ?? 'N/A',
            $inquiry->tour_name ?? 'N/A',
            $inquiry->nationality ?? 'N/A',
            $inquiry->created_at->format('Y-m-d H:i:s'),
            $inquiry->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 15,  // Inquiry ID
            'C' => 20,  // Guest Name
            'D' => 25,  // Email
            'E' => 15,  // Phone
            'F' => 20,  // Client
            'G' => 30,  // Subject
            'H' => 12,  // Status
            'I' => 15,  // Total Amount
            'J' => 15,  // Paid Amount
            'K' => 15,  // Remaining Amount
            'L' => 15,  // Arrival Date
            'M' => 15,  // Departure Date
            'N' => 12,  // Number of Pax
            'O' => 20,  // Tour Name
            'P' => 15,  // Nationality
            'Q' => 20,  // Created At
            'R' => 20,  // Updated At
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
                $sheet->mergeCells('A1:R1');
                $sheet->setCellValue('A1', 'INQUIRIES REPORT');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Add date range
                $sheet->mergeCells('A2:R2');
                $sheet->setCellValue('A2', 'Period: ' . $this->startDate->format('M d, Y') . ' - ' . $this->endDate->format('M d, Y'));
                $sheet->getStyle('A2')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Add summary
                $totalInquiries = $this->collection()->count();
                $confirmedInquiries = $this->collection()->where('status', InquiryStatus::CONFIRMED)->count();
                $totalRevenue = $this->collection()->sum('total_amount');
                
                $sheet->insertNewRowBefore(4, 1);
                $sheet->mergeCells('A4:R4');
                $sheet->setCellValue('A4', "Summary: {$totalInquiries} Total Inquiries | {$confirmedInquiries} Confirmed | $" . number_format($totalRevenue, 2) . " Total Revenue");
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
