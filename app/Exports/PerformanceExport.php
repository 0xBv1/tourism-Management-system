<?php

namespace App\Exports;

use App\Models\Inquiry;
use App\Models\BookingFile;
use App\Models\Payment;
use App\Enums\InquiryStatus;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
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

class PerformanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
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
        // Get KPIs data
        $inquiries = Inquiry::whereBetween('created_at', [$this->startDate, $this->endDate])->get();
        $bookings = BookingFile::whereBetween('created_at', [$this->startDate, $this->endDate])->get();
        $payments = Payment::whereBetween('created_at', [$this->startDate, $this->endDate])->get();

        $kpis = [
            'inquiry_to_booking_conversion' => $inquiries->count() > 0 ? round(($bookings->count() / $inquiries->count()) * 100, 2) : 0,
            'booking_to_payment_conversion' => $bookings->count() > 0 ? round(($payments->count() / $bookings->count()) * 100, 2) : 0,
            'average_inquiry_value' => $inquiries->count() > 0 ? round($inquiries->avg('total_amount'), 2) : 0,
            'average_booking_value' => $bookings->count() > 0 ? round($bookings->avg('total_amount'), 2) : 0,
            'revenue_per_inquiry' => $inquiries->count() > 0 ? round($payments->sum('amount') / $inquiries->count(), 2) : 0,
        ];

        return collect([
            [
                'metric' => 'Inquiry to Booking Conversion',
                'value' => $kpis['inquiry_to_booking_conversion'] . '%',
                'description' => 'Percentage of inquiries converted to bookings'
            ],
            [
                'metric' => 'Booking to Payment Conversion',
                'value' => $kpis['booking_to_payment_conversion'] . '%',
                'description' => 'Percentage of bookings with payments'
            ],
            [
                'metric' => 'Average Inquiry Value',
                'value' => '$' . number_format($kpis['average_inquiry_value'], 2),
                'description' => 'Average monetary value per inquiry'
            ],
            [
                'metric' => 'Average Booking Value',
                'value' => '$' . number_format($kpis['average_booking_value'], 2),
                'description' => 'Average monetary value per booking'
            ],
            [
                'metric' => 'Revenue per Inquiry',
                'value' => '$' . number_format($kpis['revenue_per_inquiry'], 2),
                'description' => 'Average revenue generated per inquiry'
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Performance Metric',
            'Value',
            'Description'
        ];
    }

    public function map($item): array
    {
        return [
            $item['metric'],
            $item['value'],
            $item['description']
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 20,
            'C' => 50,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
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
                $sheet->mergeCells('A1:C1');
                $sheet->setCellValue('A1', 'PERFORMANCE REPORT');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Add date range
                $sheet->mergeCells('A2:C2');
                $sheet->setCellValue('A2', 'Period: ' . $this->startDate->format('M d, Y') . ' - ' . $this->endDate->format('M d, Y'));
                $sheet->getStyle('A2')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Apply borders to all data
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A3:C' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                
                // Auto-fit columns
                foreach (range('A', 'C') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
