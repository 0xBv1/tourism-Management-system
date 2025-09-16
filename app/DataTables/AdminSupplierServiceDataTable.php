<?php

namespace App\DataTables;

use App\Models\SupplierHotel;
use App\Models\SupplierTour;
use App\Models\SupplierTrip;
use App\Models\SupplierTransport;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Collection;

class AdminSupplierServiceDataTable extends DataTable
{
    public function dataTable($query): EloquentDataTable
    {
        // Get all services data
        $services = $this->getServicesData();
        
        // Create a custom EloquentDataTable with our combined data
        $dataTable = new EloquentDataTable(collect($services));
        
        return $dataTable
            ->addColumn('service_info', function ($service) {
                $image = '';
                if ($service->featured_image) {
                    $image = '<img src="' . asset('storage/' . $service->featured_image) . '" alt="' . $service->service_name . '" class="rounded me-2" width="40" height="40" style="object-fit: cover;">';
                } else {
                    $icon = match($service->service_type) {
                        'Hotel' => 'hotel',
                        'Tour' => 'explore',
                        'Trip' => 'flight',
                        'Transport' => 'directions_car',
                        default => 'package'
                    };
                    $image = '<div class="bg-light rounded d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;"><i class="material-icons-outlined text-muted">' . $icon . '</i></div>';
                }
                
                $details = '<div class="d-flex align-items-center">' . $image;
                $details .= '<div><h6 class="mb-0">' . $service->service_name . '</h6>';
                
                // Add type-specific details
                if ($service->service_type === 'Hotel') {
                    $details .= '<small class="text-muted">' . $service->city . ', ' . $service->country . '</small>';
                } elseif ($service->service_type === 'Trip') {
                    $details .= '<small class="text-muted">' . $service->departure_city . ' → ' . $service->arrival_city . '</small>';
                } elseif ($service->service_type === 'Transport') {
                    $details .= '<small class="text-muted">' . $service->origin_location . ' → ' . $service->destination_location . '</small>';
                }
                
                $details .= '</div></div>';
                return $details;
            })
            ->addColumn('service_type', function ($service) {
                $colors = [
                    'Hotel' => 'info',
                    'Tour' => 'secondary',
                    'Trip' => 'dark',
                    'Transport' => 'light'
                ];
                $color = $colors[$service->service_type] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . $service->service_type . '</span>';
            })
            ->addColumn('supplier_info', function ($service) {
                return '<div><strong>' . $service->supplier->company_name . '</strong><br><small class="text-muted">' . $service->supplier->user->email . '</small></div>';
            })
            ->addColumn('price_info', function ($service) {
                return '<strong>' . number_format($service->service_price, 2) . ' ' . $service->service_currency . '</strong>';
            })
            ->addColumn('status', function ($service) {
                return $service->enabled ? 
                    '<span class="badge bg-success">Active</span>' : 
                    '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('approval', function ($service) {
                if ($service->approved) {
                    return '<span class="badge bg-success">Approved</span>';
                } else {
                    $html = '<span class="badge bg-warning">Pending</span>';
                    if ($service->rejection_reason) {
                        $html .= '<br><small class="text-danger">' . \Str::limit($service->rejection_reason, 30) . '</small>';
                    }
                    return $html;
                }
            })
            ->editColumn('created_at', function ($service) {
                if (!$service->created_at) {
                    return '<small>N/A</small>';
                }
                return '<small>' . $service->created_at->format('M d, Y') . '</small><br><small class="text-muted">' . $service->created_at->format('H:i') . '</small>';
            })
            ->addColumn('action', function ($service) {
                $type = strtolower($service->service_type);
                $actions = '<div class="btn-group" role="group">';
                
                // Edit button
                $actions .= '<a href="' . route('dashboard.supplier-services.edit', [$type, $service->id]) . '" class="btn btn-sm btn-outline-primary" title="Edit"><i class="material-icons-outlined">edit</i></a>';
                
                // Approval toggle button
                $approvalIcon = $service->approved ? 'close' : 'check';
                $approvalColor = $service->approved ? 'warning' : 'success';
                $actions .= '<button type="button" class="btn btn-sm btn-outline-' . $approvalColor . '" onclick="toggleApproval(\'' . $type . '\', ' . $service->id . ', ' . ($service->approved ? 'false' : 'true') . ')" title="' . ($service->approved ? 'Reject' : 'Approve') . '"><i class="material-icons-outlined">' . $approvalIcon . '</i></button>';
                
                // Status toggle button
                $statusIcon = $service->enabled ? 'power_settings_off' : 'power_settings_new';
                $statusColor = $service->enabled ? 'danger' : 'success';
                $actions .= '<button type="button" class="btn btn-sm btn-outline-' . $statusColor . '" onclick="toggleStatus(\'' . $type . '\', ' . $service->id . ', ' . ($service->enabled ? 'false' : 'true') . ')" title="' . ($service->enabled ? 'Disable' : 'Enable') . '"><i class="material-icons-outlined">' . $statusIcon . '</i></button>';
                
                $actions .= '</div>';
                return $actions;
            })
            ->setRowId('id')
            ->rawColumns(['service_info', 'service_type', 'supplier_info', 'price_info', 'status', 'approval', 'created_at', 'action']);
    }

    protected function getServicesData()
    {
        $query = request()->get('query');
        $status = request()->get('status');
        $type = request()->get('type');
        $supplier = request()->get('supplier');

        // Get all services with their suppliers
        $hotels = SupplierHotel::with('supplier.user')
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->when($status, function ($q) use ($status) {
                if ($status === 'approved') {
                    $q->where('approved', true);
                } elseif ($status === 'pending') {
                    $q->where('approved', false);
                } elseif ($status === 'enabled') {
                    $q->where('enabled', true);
                } elseif ($status === 'disabled') {
                    $q->where('enabled', false);
                }
            })
            ->when($supplier, function ($q) use ($supplier) {
                $q->whereHas('supplier', function ($sq) use ($supplier) {
                    $sq->where('company_name', 'like', "%{$supplier}%");
                });
            })
            ->latest()
            ->get()
            ->map(function ($hotel) {
                $hotel->service_type = 'Hotel';
                $hotel->service_name = $hotel->name;
                $hotel->service_price = $hotel->price_per_night;
                $hotel->service_currency = $hotel->currency;
                return $hotel;
            });

        $tours = SupplierTour::with('supplier.user')
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            })
            ->when($status, function ($q) use ($status) {
                if ($status === 'approved') {
                    $q->where('approved', true);
                } elseif ($status === 'pending') {
                    $q->where('approved', false);
                } elseif ($status === 'enabled') {
                    $q->where('enabled', true);
                } elseif ($status === 'disabled') {
                    $q->where('enabled', false);
                }
            })
            ->when($supplier, function ($q) use ($supplier) {
                $q->whereHas('supplier', function ($sq) use ($supplier) {
                    $sq->where('company_name', 'like', "%{$supplier}%");
                });
            })
            ->latest()
            ->get()
            ->map(function ($tour) {
                $tour->service_type = 'Tour';
                $tour->service_name = $tour->title;
                $tour->service_price = $tour->adult_price;
                $tour->service_currency = $tour->currency;
                return $tour;
            });

        $trips = SupplierTrip::with('supplier.user')
            ->when($query, function ($q) use ($query) {
                $q->where('trip_name', 'like', "%{$query}%");
            })
            ->when($status, function ($q) use ($status) {
                if ($status === 'approved') {
                    $q->where('approved', true);
                } elseif ($status === 'pending') {
                    $q->where('approved', false);
                } elseif ($status === 'enabled') {
                    $q->where('enabled', true);
                } elseif ($status === 'disabled') {
                    $q->where('enabled', false);
                }
            })
            ->when($supplier, function ($q) use ($supplier) {
                $q->whereHas('supplier', function ($sq) use ($supplier) {
                    $sq->where('company_name', 'like', "%{$supplier}%");
                });
            })
            ->latest()
            ->get()
            ->map(function ($trip) {
                $trip->service_type = 'Trip';
                $trip->service_name = $trip->trip_name;
                $trip->service_price = $trip->seat_price;
                $trip->service_currency = 'EGP';
                return $trip;
            });

        $transports = SupplierTransport::with('supplier.user')
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->when($status, function ($q) use ($status) {
                if ($status === 'approved') {
                    $q->where('approved', true);
                } elseif ($status === 'pending') {
                    $q->where('approved', false);
                } elseif ($status === 'enabled') {
                    $q->where('enabled', true);
                } elseif ($status === 'disabled') {
                    $q->where('enabled', false);
                }
            })
            ->when($supplier, function ($q) use ($supplier) {
                $q->whereHas('supplier', function ($sq) use ($supplier) {
                    $sq->where('company_name', 'like', "%{$supplier}%");
                });
            })
            ->latest()
            ->get()
            ->map(function ($transport) {
                $transport->service_type = 'Transport';
                $transport->service_name = $transport->name;
                $transport->service_price = $transport->price;
                $transport->service_currency = $transport->currency;
                return $transport;
            });

        // Combine all services
        $allServices = $hotels->concat($tours)->concat($trips)->concat($transports);

        // Filter by type if specified
        if ($type) {
            $allServices = $allServices->where('service_type', $type);
        }

        // Sort by created_at
        return $allServices->sortByDesc('created_at')->values();
    }

    public function query(): QueryBuilder
    {
        // This is a custom query that combines all service types
        // We'll handle the actual data fetching in the getServicesData method
        return \DB::table('supplier_hotels')->whereRaw('1 = 0'); // Dummy query
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('data-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Blfrtip')
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->buttons(array_reverse([
                Button::make('excel')->className('btn btn-sm float-right ms-1 p-1 text-light btn-success'),
                Button::make('csv')->className('btn btn-sm float-right ms-1 p-1 text-light btn-primary'),
                Button::make('print')->className('btn btn-sm float-right ms-1 p-1 text-light btn-secondary'),
                Button::make('reload')->className('btn btn-sm float-right ms-1 p-1 text-light btn-info')
            ]))
            ->parameters([
                'scrollX' => true,
                'responsive' => true,
                'autoWidth' => false,
                'pageLength' => 15,
                'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID')->width(60),
            Column::computed('service_info')
                ->title('Service')
                ->exportable(false)
                ->printable(false)
                ->width(250)
                ->addClass('text-start'),
            Column::computed('service_type')
                ->title('Type')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
            Column::computed('supplier_info')
                ->title('Supplier')
                ->exportable(false)
                ->printable(false)
                ->width(200)
                ->addClass('text-start'),
            Column::computed('price_info')
                ->title('Price')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-end'),
            Column::computed('status')
                ->title('Status')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
            Column::computed('approval')
                ->title('Approval')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
            Column::computed('created_at')
                ->title('Created')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
            Column::computed('action')
                ->title('Actions')
                ->exportable(false)
                ->printable(false)
                ->width(150)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'SupplierServices_' . date('YmdHis');
    }
}


