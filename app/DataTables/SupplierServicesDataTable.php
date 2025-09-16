<?php

namespace App\DataTables;

use App\Models\SupplierHotel;
use App\Models\SupplierTour;
use App\Models\SupplierTrip;
use App\Models\SupplierTransport;
use App\Models\SupplierRoom;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SupplierServicesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('service_info', function ($service) {
                return view('dashboard.supplier-services.partials.service_info', compact('service'))->render();
            })
            ->addColumn('type', function ($service) {
                return view('dashboard.supplier-services.partials.type', compact('service'))->render();
            })
            ->addColumn('supplier_info', function ($service) {
                return view('dashboard.supplier-services.partials.supplier_info', compact('service'))->render();
            })
            ->addColumn('price', function ($service) {
                return view('dashboard.supplier-services.partials.price', compact('service'))->render();
            })
            ->addColumn('status', function ($service) {
                return view('dashboard.supplier-services.partials.status', compact('service'))->render();
            })
            ->addColumn('approval', function ($service) {
                return view('dashboard.supplier-services.partials.approval', compact('service'))->render();
            })
            ->editColumn('created_at', function ($service) {
                return $service->created_at->format('M Y, d');
            })
            ->addColumn('action', function ($service) {
                return view('dashboard.supplier-services.partials.actions', compact('service'))->render();
            })
            ->rawColumns(['service_info', 'type', 'supplier_info', 'price', 'status', 'approval', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(): QueryBuilder
    {
        // Return an Eloquent Builder instead of Query Builder
        return SupplierHotel::whereRaw('1 = 0');
    }

    /**
     * Handle AJAX requests for the DataTable.
     */
    public function ajax(): \Illuminate\Http\JsonResponse
    {
        $data = $this->getData();
        
        return datatables()
            ->of($data)
            ->addColumn('service_info', function ($service) {
                return view('dashboard.supplier-services.partials.service_info', compact('service'))->render();
            })
            ->addColumn('type', function ($service) {
                return view('dashboard.supplier-services.partials.type', compact('service'))->render();
            })
            ->addColumn('supplier_info', function ($service) {
                return view('dashboard.supplier-services.partials.supplier_info', compact('service'))->render();
            })
            ->addColumn('price', function ($service) {
                return view('dashboard.supplier-services.partials.price', compact('service'))->render();
            })
            ->addColumn('status', function ($service) {
                return view('dashboard.supplier-services.partials.status', compact('service'))->render();
            })
            ->addColumn('approval', function ($service) {
                return view('dashboard.supplier-services.partials.approval', compact('service'))->render();
            })
            ->editColumn('created_at', function ($service) {
                return $service->created_at->format('M Y, d');
            })
            ->addColumn('action', function ($service) {
                return view('dashboard.supplier-services.partials.actions', compact('service'))->render();
            })
            ->rawColumns(['service_info', 'type', 'supplier_info', 'price', 'status', 'approval', 'action'])
            ->setRowId('id')
            ->make(true);
    }

    /**
     * Get the data for the DataTable
     */
    protected function getData(): Collection
    {
        $query = request('query');
        $status = request('status');
        $type = request('type');
        $supplier = request('supplier');

        $allServices = collect();

        // Get hotels
        if (!$type || $type === 'Hotel') {
            $hotels = SupplierHotel::with('supplier.user')
                ->join('supplier_hotel_translations', 'supplier_hotels.id', '=', 'supplier_hotel_translations.supplier_hotel_id')
                ->where('supplier_hotel_translations.locale', app()->getLocale())
                ->when($query, function ($q) use ($query) {
                    $q->where('supplier_hotel_translations.name', 'like', "%{$query}%");
                })
                ->when($status, function ($q) use ($status) {
                    if ($status === 'approved') {
                        $q->where('supplier_hotels.approved', true);
                    } elseif ($status === 'pending') {
                        $q->where('supplier_hotels.approved', false);
                    } elseif ($status === 'enabled') {
                        $q->where('supplier_hotels.enabled', true);
                    } elseif ($status === 'disabled') {
                        $q->where('supplier_hotels.enabled', false);
                    }
                })
                ->when($supplier, function ($q) use ($supplier) {
                    $q->whereHas('supplier', function ($sq) use ($supplier) {
                        $sq->where('company_name', 'like', "%{$supplier}%");
                    });
                })
                ->get()
                ->map(function ($hotel) {
                    $hotel->service_type = 'Hotel';
                    $hotel->service_name = $hotel->name;
                    $hotel->service_price = 0; // No price column in current table
                    $hotel->service_currency = 'EGP';
                    return $hotel;
                });

            $allServices = $allServices->concat($hotels);
        }

        // Get tours
        if (!$type || $type === 'Tour') {
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
                ->get()
                ->map(function ($tour) {
                    $tour->service_type = 'Tour';
                    $tour->service_name = $tour->title;
                    $tour->service_price = $tour->adult_price;
                    $tour->service_currency = $tour->currency;
                    return $tour;
                });

            $allServices = $allServices->concat($tours);
        }

        // Get trips
        if (!$type || $type === 'Trip') {
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
                ->get()
                ->map(function ($trip) {
                    $trip->service_type = 'Trip';
                    $trip->service_name = $trip->trip_name;
                    $trip->service_price = $trip->seat_price;
                    $trip->service_currency = 'EGP';
                    return $trip;
                });

            $allServices = $allServices->concat($trips);
        }

        // Get transports
        if (!$type || $type === 'Transport') {
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
                ->get()
                ->map(function ($transport) {
                    $transport->service_type = 'Transport';
                    $transport->service_name = $transport->name;
                    $transport->service_price = $transport->price;
                    $transport->service_currency = $transport->currency;
                    return $transport;
                });

            $allServices = $allServices->concat($transports);
        }

        // Get rooms
        if (!$type || $type === 'Room') {
            $rooms = SupplierRoom::with('supplier.user')
                ->join('supplier_room_translations', 'supplier_rooms.id', '=', 'supplier_room_translations.supplier_room_id')
                ->where('supplier_room_translations.locale', app()->getLocale())
                ->when($query, function ($q) use ($query) {
                    $q->where('supplier_room_translations.name', 'like', "%{$query}%");
                })
                ->when($status, function ($q) use ($status) {
                    if ($status === 'approved') {
                        $q->where('supplier_rooms.approved', true);
                    } elseif ($status === 'pending') {
                        $q->where('supplier_rooms.approved', false);
                    } elseif ($status === 'enabled') {
                        $q->where('supplier_rooms.enabled', true);
                    } elseif ($status === 'disabled') {
                        $q->where('supplier_rooms.enabled', false);
                    }
                })
                ->when($supplier, function ($q) use ($supplier) {
                    $q->whereHas('supplier', function ($sq) use ($supplier) {
                        $sq->where('company_name', 'like', "%{$supplier}%");
                    });
                })
                ->get()
                ->map(function ($room) {
                    $room->service_type = 'Room';
                    $room->service_name = $room->name;
                    $room->service_price = $room->night_price;
                    $room->service_currency = 'EGP';
                    return $room;
                });

            $allServices = $allServices->concat($rooms);
        }

        return $allServices->sortByDesc('created_at');
    }

    /**
     * Optional method if you want to use html builder.
     */
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
                Button::make('reload')->className('btn btn-sm float-right ms-1 p-1 text-light btn-info'),
            ]));
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('service_info')->title('Service'),
            Column::make('type')->title('Type'),
            Column::make('supplier_info')->title('Supplier'),
            Column::make('price')->title('Price'),
            Column::make('status')->title('Status'),
            Column::make('approval')->title('Approval'),
            Column::make('created_at')->title('Created'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center')
                ->title('Actions'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'SupplierServices_' . date('YmdHis');
    }
}
