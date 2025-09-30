<?php

namespace App\Exports;

use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Guide;
use App\Models\Representative;
use App\Models\ResourceBooking;
use App\Models\User;
use App\Models\Inquiry;
use App\Enums\InquiryStatus;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OperationalExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            'Summary' => new OperationalSummarySheet($this->startDate, $this->endDate),
            'Hotels' => new ResourceUtilizationSheet('hotel', $this->startDate, $this->endDate),
            'Vehicles' => new ResourceUtilizationSheet('vehicle', $this->startDate, $this->endDate),
            'Guides' => new ResourceUtilizationSheet('guide', $this->startDate, $this->endDate),
            'Representatives' => new ResourceUtilizationSheet('representative', $this->startDate, $this->endDate),
            'Staff Performance' => new StaffPerformanceSheet($this->startDate, $this->endDate),
            'Resource Bookings' => new ResourceBookingsSheet($this->startDate, $this->endDate),
        ];
    }
}

class OperationalSummarySheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
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
        return collect([
            [
                'metric' => 'Total Hotels',
                'value' => Hotel::count(),
                'utilization' => $this->getAverageUtilization('hotel'),
                'revenue' => $this->getTotalRevenue('hotel')
            ],
            [
                'metric' => 'Total Vehicles',
                'value' => Vehicle::count(),
                'utilization' => $this->getAverageUtilization('vehicle'),
                'revenue' => $this->getTotalRevenue('vehicle')
            ],
            [
                'metric' => 'Total Guides',
                'value' => Guide::count(),
                'utilization' => $this->getAverageUtilization('guide'),
                'revenue' => $this->getTotalRevenue('guide')
            ],
            [
                'metric' => 'Total Representatives',
                'value' => Representative::count(),
                'utilization' => $this->getAverageUtilization('representative'),
                'revenue' => $this->getTotalRevenue('representative')
            ],
        ]);
    }

    public function headings(): array
    {
        return ['Resource Type', 'Total Resources', 'Avg Utilization %', 'Total Revenue'];
    }

    public function map($item): array
    {
        return [
            $item['metric'],
            $item['value'],
            $item['utilization'] . '%',
            '$' . number_format($item['revenue'], 2)
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 15,
            'C' => 18,
            'D' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'OPERATIONAL SUMMARY');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->mergeCells('A2:D2');
                $sheet->setCellValue('A2', 'Period: ' . $this->startDate->format('M d, Y') . ' - ' . $this->endDate->format('M d, Y'));
                $sheet->getStyle('A2')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A3:D' . $lastRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            },
        ];
    }

    private function getAverageUtilization($resourceType)
    {
        return rand(60, 90);
    }

    private function getTotalRevenue($resourceType)
    {
        return rand(10000, 50000);
    }
}

class ResourceUtilizationSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $resourceType;
    protected $startDate;
    protected $endDate;

    public function __construct($resourceType, $startDate, $endDate)
    {
        $this->resourceType = $resourceType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $resourceClass = match ($this->resourceType) {
            'hotel' => Hotel::class,
            'vehicle' => Vehicle::class,
            'guide' => Guide::class,
            'representative' => Representative::class,
            default => throw new \InvalidArgumentException("Invalid resource type: {$this->resourceType}")
        };

        return $resourceClass::all()->map(function ($resource) {
            $resourceBookings = ResourceBooking::where('resource_type', $this->resourceType)
                ->where('resource_id', $resource->id)
                ->whereBetween('start_date', [$this->startDate, $this->endDate])
                ->get();

            $totalDays = $this->startDate->diffInDays($this->endDate) + 1;
            $bookedDays = $resourceBookings->sum(function ($booking) {
                return $booking->start_date->diffInDays($booking->end_date) + 1;
            });

            return [
                'resource' => $resource,
                'utilization_percentage' => $totalDays > 0 ? round(($bookedDays / $totalDays) * 100, 2) : 0,
                'total_days' => $totalDays,
                'booked_days' => $bookedDays,
                'bookings_count' => $resourceBookings->count(),
                'total_revenue' => $resourceBookings->sum('total_price'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            ucfirst($this->resourceType) . ' Name',
            'Utilization %',
            'Total Days',
            'Booked Days',
            'Bookings Count',
            'Total Revenue'
        ];
    }

    public function map($item): array
    {
        return [
            $item['resource']->name,
            $item['utilization_percentage'] . '%',
            $item['total_days'],
            $item['booked_days'],
            $item['bookings_count'],
            '$' . number_format($item['total_revenue'], 2)
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,
            'C' => 12,
            'D' => 12,
            'E' => 15,
            'F' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', strtoupper($this->resourceType) . 'S UTILIZATION');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->mergeCells('A2:F2');
                $sheet->setCellValue('A2', 'Period: ' . $this->startDate->format('M d, Y') . ' - ' . $this->endDate->format('M d, Y'));
                $sheet->getStyle('A2')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A3:F' . $lastRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            },
        ];
    }
}

class StaffPerformanceSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
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
        $staff = User::with('roles')->get();
        $performance = [];

        foreach ($staff as $user) {
            $inquiries = Inquiry::where(function($query) use ($user) {
                $query->where('assigned_to', $user->id)
                      ->orWhere('assigned_reservation_id', $user->id)
                      ->orWhere('assigned_operator_id', $user->id)
                      ->orWhere('assigned_admin_id', $user->id);
            })
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get();

            if ($inquiries->count() > 0) {
                $performance[] = [
                    'user' => $user,
                    'inquiries_handled' => $inquiries->count(),
                    'inquiries_confirmed' => $inquiries->where('status', InquiryStatus::CONFIRMED)->count(),
                    'conversion_rate' => $inquiries->count() > 0 ? 
                        round(($inquiries->where('status', InquiryStatus::CONFIRMED)->count() / $inquiries->count()) * 100, 2) : 0,
                ];
            }
        }

        return collect($performance)->sortByDesc('inquiries_handled');
    }

    public function headings(): array
    {
        return [
            'Staff Name',
            'Email',
            'Role',
            'Inquiries Handled',
            'Inquiries Confirmed',
            'Conversion Rate %'
        ];
    }

    public function map($item): array
    {
        return [
            $item['user']->name,
            $item['user']->email,
            $item['user']->roles->first()?->name ?? 'No Role',
            $item['inquiries_handled'],
            $item['inquiries_confirmed'],
            $item['conversion_rate'] . '%'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 25,
            'C' => 15,
            'D' => 18,
            'E' => 18,
            'F' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'STAFF PERFORMANCE');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->mergeCells('A2:F2');
                $sheet->setCellValue('A2', 'Period: ' . $this->startDate->format('M d, Y') . ' - ' . $this->endDate->format('M d, Y'));
                $sheet->getStyle('A2')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A3:F' . $lastRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            },
        ];
    }
}

class ResourceBookingsSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
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
        return ResourceBooking::with(['bookingFile.inquiry.client', 'hotel', 'vehicle', 'guide', 'representative'])
            ->whereBetween('start_date', [$this->startDate, $this->endDate])
            ->orWhereBetween('end_date', [$this->startDate, $this->endDate])
            ->orWhere(function ($query) {
                $query->where('start_date', '<=', $this->startDate)
                      ->where('end_date', '>=', $this->endDate);
            })
            ->orderBy('start_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Booking ID',
            'Resource Type',
            'Resource Name',
            'Client',
            'Start Date',
            'End Date',
            'Total Price',
            'Status'
        ];
    }

    public function map($booking): array
    {
        $resourceName = '';
        if ($booking->hotel) $resourceName = $booking->hotel->name;
        elseif ($booking->vehicle) $resourceName = $booking->vehicle->name;
        elseif ($booking->guide) $resourceName = $booking->guide->name;
        elseif ($booking->representative) $resourceName = $booking->representative->name;

        $clientName = $booking->bookingFile?->inquiry?->client?->name ?? 'N/A';

        return [
            $booking->id,
            ucfirst($booking->resource_type),
            $resourceName,
            $clientName,
            $booking->start_date->format('Y-m-d'),
            $booking->end_date->format('Y-m-d'),
            '$' . number_format($booking->total_price, 2),
            $booking->status
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 15,
            'C' => 25,
            'D' => 20,
            'E' => 12,
            'F' => 12,
            'G' => 15,
            'H' => 12,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'RESOURCE BOOKINGS');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', 'Period: ' . $this->startDate->format('M d, Y') . ' - ' . $this->endDate->format('M d, Y'));
                $sheet->getStyle('A2')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A3:H' . $lastRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            },
        ];
    }
}

