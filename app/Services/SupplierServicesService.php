<?php

namespace App\Services;

use App\Models\SupplierHotel;
use App\Models\SupplierTour;
use App\Models\SupplierTrip;
use App\Models\SupplierTransport;
use App\Models\SupplierRoom;
use Illuminate\Support\Collection;

class SupplierServicesService
{
    public function getAllServices($filters = []): Collection
    {
        $query = $filters['query'] ?? null;
        $status = $filters['status'] ?? null;
        $type = $filters['type'] ?? null;
        $supplier = $filters['supplier'] ?? null;
        $supplierId = $filters['supplier_id'] ?? null;
        $onlyApproved = $filters['only_approved'] ?? true;
        $onlyEnabled = $filters['only_enabled'] ?? true;

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
                        $sq->where('company_name', 'like', "%{$supplier}%")
                           ->where('is_active', true);
                    });
                })
                ->when($supplierId, function ($q) use ($supplierId) {
                    $q->where('supplier_hotels.supplier_id', $supplierId)
                      ->whereHas('supplier', function ($sq) {
                          $sq->where('is_active', true);
                      });
                })
                ->when($onlyApproved, function ($q) {
                    $q->where('supplier_hotels.approved', true);
                })
                ->when($onlyEnabled, function ($q) {
                    $q->where('supplier_hotels.enabled', true);
                })
                ->get()
                ->map(function ($hotel) {
                    $hotel->service_type = 'Hotel';
                    $hotel->service_name = $hotel->name;
                    $hotel->service_price = $hotel->price_per_night ?? 0;
                    $hotel->service_currency = $hotel->currency ?? 'EGP';
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
                        $sq->where('company_name', 'like', "%{$supplier}%")
                           ->where('is_active', true);
                    });
                })
                ->when($supplierId, function ($q) use ($supplierId) {
                    $q->where('supplier_tours.supplier_id', $supplierId)
                      ->whereHas('supplier', function ($sq) {
                          $sq->where('is_active', true);
                      });
                })
                ->when($onlyApproved, function ($q) {
                    $q->where('approved', true);
                })
                ->when($onlyEnabled, function ($q) {
                    $q->where('enabled', true);
                })
                ->get()
                ->map(function ($tour) {
                    $tour->service_type = 'Tour';
                    $tour->service_name = $tour->title;
                    $tour->service_price = $tour->adult_price ?? 0;
                    $tour->service_currency = $tour->currency ?? 'EGP';
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
                        $sq->where('company_name', 'like', "%{$supplier}%")
                           ->where('is_active', true);
                    });
                })
                ->when($supplierId, function ($q) use ($supplierId) {
                    $q->where('supplier_trips.supplier_id', $supplierId)
                      ->whereHas('supplier', function ($sq) {
                          $sq->where('is_active', true);
                      });
                })
                ->when($onlyApproved, function ($q) {
                    $q->where('approved', true);
                })
                ->when($onlyEnabled, function ($q) {
                    $q->where('enabled', true);
                })
                ->get()
                ->map(function ($trip) {
                    $trip->service_type = 'Trip';
                    $trip->service_name = $trip->trip_name;
                    $trip->service_price = $trip->seat_price ?? 0;
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
                        $sq->where('company_name', 'like', "%{$supplier}%")
                           ->where('is_active', true);
                    });
                })
                ->when($supplierId, function ($q) use ($supplierId) {
                    $q->where('supplier_transports.supplier_id', $supplierId)
                      ->whereHas('supplier', function ($sq) {
                          $sq->where('is_active', true);
                      });
                })
                ->when($onlyApproved, function ($q) {
                    $q->where('approved', true);
                })
                ->when($onlyEnabled, function ($q) {
                    $q->where('enabled', true);
                })
                ->get()
                ->map(function ($transport) {
                    $transport->service_type = 'Transport';
                    $transport->service_name = $transport->name;
                    $transport->service_price = $transport->price ?? 0;
                    $transport->service_currency = $transport->currency ?? 'EGP';
                    return $transport;
                });

            $allServices = $allServices->concat($transports);
        }

        // Get rooms
        if (!$type || $type === 'Room') {
            $rooms = SupplierRoom::with('supplierHotel.supplier.user')
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
                    $q->whereHas('supplierHotel.supplier', function ($sq) use ($supplier) {
                        $sq->where('company_name', 'like', "%{$supplier}%")
                           ->where('is_active', true);
                    });
                })
                ->when($supplierId, function ($q) use ($supplierId) {
                    $q->whereHas('supplierHotel', function ($sq) use ($supplierId) {
                        $sq->where('supplier_id', $supplierId)
                          ->whereHas('supplier', function ($ssq) {
                              $ssq->where('is_active', true);
                          });
                    });
                })
                ->when($onlyApproved, function ($q) {
                    $q->where('supplier_rooms.approved', true);
                })
                ->when($onlyEnabled, function ($q) {
                    $q->where('supplier_rooms.enabled', true);
                })
                ->get()
                ->map(function ($room) {
                    $room->service_type = 'Room';
                    $room->service_name = $room->name;
                    $room->service_price = $room->night_price ?? 0;
                    $room->service_currency = 'EGP';
                    return $room;
                });

            $allServices = $allServices->concat($rooms);
        }

        return $allServices->sortByDesc('created_at');
    }
}
