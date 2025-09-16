<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Supplier;
use App\Models\Hotel;
use App\Models\Trip;
use App\Models\Tour;
use App\Models\SupplierHotel;
use App\Models\SupplierTrip;
use App\Models\SupplierTour;
use App\Models\SupplierTransport;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        $suppliers = Supplier::all();
        $hotels = Hotel::all();
        $trips = Trip::all();
        $tours = Tour::all();
        $supplierHotels = SupplierHotel::all();
        $supplierTrips = SupplierTrip::all();
        $supplierTours = SupplierTour::all();
        $supplierTransports = SupplierTransport::all();

        // Create Hotel Bookings
        $this->createHotelBookings($clients, $suppliers, $hotels, $supplierHotels);

        // Create Trip Bookings
        $this->createTripBookings($clients, $suppliers, $trips, $supplierTrips);

        // Create Tour Bookings
        $this->createTourBookings($clients, $suppliers, $tours, $supplierTours);

        // Create Transport Bookings
        $this->createTransportBookings($clients, $suppliers, $supplierTransports);

        $this->command->info('All Bookings seeded successfully!');
    }

    private function createHotelBookings($clients, $suppliers, $hotels, $supplierHotels)
    {
        $bookingStatuses = ['confirmed', 'pending', 'cancelled', 'completed'];
        $paymentStatuses = ['paid', 'pending', 'failed', 'refunded'];

        for ($i = 0; $i < 30; $i++) {
            $client = $clients->random();
            $supplier = $suppliers->random();
            
            // Randomly choose between system hotel or supplier hotel
            if (rand(0, 1) && $hotels->count() > 0) {
                $hotel = $hotels->random();
                $hotelType = 'system';
                $hotelId = $hotel->id;
                $hotelName = $hotel->name;
                $price = rand(800, 2500);
            } else {
                $supplierHotel = $supplierHotels->where('supplier_id', $supplier->id)->first();
                if (!$supplierHotel) continue;
                
                $hotelType = 'supplier';
                $hotelId = $supplierHotel->id;
                $hotelName = $supplierHotel->hotel_name;
                $price = rand(600, 2000);
            }

            $checkIn = Carbon::now()->addDays(rand(7, 60));
            $checkOut = $checkIn->copy()->addDays(rand(1, 7));
            $nights = $checkIn->diffInDays($checkOut);

            Booking::create([
                'client_id' => $client->id,
                'supplier_id' => $supplier->id,
                'booking_type' => 'hotel',
                'hotel_type' => $hotelType,
                'hotel_id' => $hotelId,
                'hotel_name' => $hotelName,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'nights' => $nights,
                'adults' => rand(1, 4),
                'children' => rand(0, 3),
                'infants' => rand(0, 2),
                'total_amount' => $price * $nights,
                'currency' => 'EGP',
                'status' => $bookingStatuses[array_rand($bookingStatuses)],
                'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                'special_requests' => rand(0, 1) ? 'Early check-in requested' : null,
                'booking_date' => Carbon::now()->subDays(rand(1, 30)),
                'notes' => rand(0, 1) ? 'Customer prefers high floor room' : null,
            ]);
        }
    }

    private function createTripBookings($clients, $suppliers, $trips, $supplierTrips)
    {
        $bookingStatuses = ['confirmed', 'pending', 'cancelled', 'completed'];
        $paymentStatuses = ['paid', 'pending', 'failed', 'refunded'];

        for ($i = 0; $i < 25; $i++) {
            $client = $clients->random();
            $supplier = $suppliers->random();
            
            // Randomly choose between system trip or supplier trip
            if (rand(0, 1) && $trips->count() > 0) {
                $trip = $trips->random();
                $tripType = 'system';
                $tripId = $trip->id;
                $tripName = $trip->trip_name;
                $price = $trip->price;
            } else {
                $supplierTrip = $supplierTrips->where('supplier_id', $supplier->id)->first();
                if (!$supplierTrip) continue;
                
                $tripType = 'supplier';
                $tripId = $supplierTrip->id;
                $tripName = $supplierTrip->trip_name;
                $price = $supplierTrip->price;
            }

            $tripDate = Carbon::now()->addDays(rand(5, 45));
            $passengers = rand(1, 6);

            Booking::create([
                'client_id' => $client->id,
                'supplier_id' => $supplier->id,
                'booking_type' => 'trip',
                'trip_type' => $tripType,
                'trip_id' => $tripId,
                'trip_name' => $tripName,
                'trip_date' => $tripDate,
                'passengers' => $passengers,
                'total_amount' => $price * $passengers,
                'currency' => 'EGP',
                'status' => $bookingStatuses[array_rand($bookingStatuses)],
                'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                'special_requests' => rand(0, 1) ? 'Window seat preferred' : null,
                'booking_date' => Carbon::now()->subDays(rand(1, 20)),
                'notes' => rand(0, 1) ? 'Customer has dietary restrictions' : null,
            ]);
        }
    }

    private function createTourBookings($clients, $suppliers, $tours, $supplierTours)
    {
        $bookingStatuses = ['confirmed', 'pending', 'cancelled', 'completed'];
        $paymentStatuses = ['paid', 'pending', 'failed', 'refunded'];

        for ($i = 0; $i < 35; $i++) {
            $client = $clients->random();
            $supplier = $suppliers->random();
            
            // Randomly choose between system tour or supplier tour
            if (rand(0, 1) && $tours->count() > 0) {
                $tour = $tours->random();
                $tourType = 'system';
                $tourId = $tour->id;
                $tourName = $tour->title;
                $adultPrice = $tour->adult_price;
                $childPrice = $tour->child_price;
            } else {
                $supplierTour = $supplierTours->where('supplier_id', $supplier->id)->first();
                if (!$supplierTour) continue;
                
                $tourType = 'supplier';
                $tourId = $supplierTour->id;
                $tourName = $supplierTour->title;
                $adultPrice = $supplierTour->adult_price;
                $childPrice = $supplierTour->child_price;
            }

            $tourDate = Carbon::now()->addDays(rand(3, 30));
            $adults = rand(1, 4);
            $children = rand(0, 3);
            $totalAmount = ($adultPrice * $adults) + ($childPrice * $children);

            Booking::create([
                'client_id' => $client->id,
                'supplier_id' => $supplier->id,
                'booking_type' => 'tour',
                'tour_type' => $tourType,
                'tour_id' => $tourId,
                'tour_name' => $tourName,
                'tour_date' => $tourDate,
                'adults' => $adults,
                'children' => $children,
                'infants' => rand(0, 2),
                'total_amount' => $totalAmount,
                'currency' => 'EGP',
                'status' => $bookingStatuses[array_rand($bookingStatuses)],
                'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                'special_requests' => rand(0, 1) ? 'English speaking guide required' : null,
                'booking_date' => Carbon::now()->subDays(rand(1, 25)),
                'notes' => rand(0, 1) ? 'Pickup from hotel lobby' : null,
            ]);
        }
    }

    private function createTransportBookings($clients, $suppliers, $supplierTransports)
    {
        $bookingStatuses = ['confirmed', 'pending', 'cancelled', 'completed'];
        $paymentStatuses = ['paid', 'pending', 'failed', 'refunded'];

        for ($i = 0; $i < 20; $i++) {
            $client = $clients->random();
            $supplier = $suppliers->random();
            
            $supplierTransport = $supplierTransports->where('supplier_id', $supplier->id)->first();
            if (!$supplierTransport) continue;

            $transportDate = Carbon::now()->addDays(rand(2, 40));
            $passengers = rand(1, $supplierTransport->seating_capacity);

            Booking::create([
                'client_id' => $client->id,
                'supplier_id' => $supplier->id,
                'booking_type' => 'transport',
                'transport_id' => $supplierTransport->id,
                'transport_name' => $supplierTransport->name,
                'transport_date' => $transportDate,
                'passengers' => $passengers,
                'total_amount' => $supplierTransport->price * $passengers,
                'currency' => 'EGP',
                'status' => $bookingStatuses[array_rand($bookingStatuses)],
                'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                'special_requests' => rand(0, 1) ? 'Airport pickup with sign' : null,
                'booking_date' => Carbon::now()->subDays(rand(1, 15)),
                'notes' => rand(0, 1) ? 'Flight number: ' . strtoupper(substr(md5(rand()), 0, 6)) : null,
            ]);
        }
    }
}

