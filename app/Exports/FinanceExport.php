<?php

namespace App\Exports;

use App\Models\Payment;
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

class FinanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
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
        return Payment::with(['booking.inquiry.client'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Reference Number',
            'Client',
            'Amount',
            'Currency',
            'Status',
            'Payment Method',
            'Gateway',
            'Transaction ID',
            'Paid At',
            'Created At',
            'Updated At'
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->id,
            $payment->reference_number,
            $payment->booking?->inquiry?->client?->name ?? 'N/A',
            $payment->amount,
            $payment->currency,
            $payment->status->getLabel(),
            $payment->payment_method ?? 'N/A',
            $payment->gateway ?? 'N/A',
            $payment->transaction_id ?? 'N/A',
            $payment->paid_at?->format('Y-m-d H:i:s') ?? 'N/A',
            $payment->created_at->format('Y-m-d H:i:s'),
            $payment->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 20,  // Reference Number
            'C' => 20,  // Client
            'D' => 15,  // Amount
            'E' => 10,  // Currency
            'F' => 12,  // Status
            'G' => 15,  // Payment Method
            'H' => 15,  // Gateway
            'I' => 20,  // Transaction ID
            'J' => 20,  // Paid At
            'K' => 20,  // Created At
            'L' => 20,  // Updated At
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
                $sheet->mergeCells('A1:L1');
                $sheet->setCellValue('A1', 'FINANCE REPORT');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Add date range
                $sheet->mergeCells('A2:L2');
                $sheet->setCellValue('A2', 'Period: ' . $this->startDate->format('M d, Y') . ' - ' . $this->endDate->format('M d, Y'));
                $sheet->getStyle('A2')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Add summary
                $totalPayments = $this->collection()->count();
                $paidPayments = $this->collection()->where('status', PaymentStatus::PAID)->count();
                $totalAmount = $this->collection()->sum('amount');
                $paidAmount = $this->collection()->where('status', PaymentStatus::PAID)->sum('amount');
                
                $sheet->insertNewRowBefore(4, 1);
                $sheet->mergeCells('A4:L4');
                $sheet->setCellValue('A4', "Summary: {$totalPayments} Total Payments | {$paidPayments} Paid | $" . number_format($totalAmount, 2) . " Total Amount | $" . number_format($paidAmount, 2) . " Paid Amount");
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
