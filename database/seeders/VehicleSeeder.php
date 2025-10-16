<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\City;
use App\Enums\ResourceStatus;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = City::all()->keyBy('name');
        
        $vehicles = [
            // Cairo Vehicles
            [
                'name' => 'Mercedes-Benz Sprinter Van',
                'type' => 'Van',
                'brand' => 'Mercedes-Benz',
                'model' => 'Sprinter',
                'year' => 2022,
                'license_plate' => 'CAI-1234',
                'capacity' => 12,
                'description' => 'Luxury van perfect for group tours with comfortable seating and air conditioning.',
                'city_id' => $cities['Cairo']->id,
                'driver_name' => 'Ahmed Hassan',
                'driver_phone' => '+20 10 1234 5678',
                'driver_license' => 'DL-2022-001',
                'price_per_hour' => 25.00,
                'price_per_day' => 180.00,
                'currency' => 'USD',
                'fuel_type' => 'Diesel',
                'transmission' => 'Manual',
                'features' => [
                    'Air Conditioning',
                    'WiFi',
                    'USB Charging',
                    'Comfortable Seating',
                    'Luggage Space',
                    'GPS Navigation',
                    'Safety Equipment'
                ],
                'images' => [
                    'vehicle1_exterior.jpg',
                    'vehicle1_interior.jpg',
                    'vehicle1_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2024-12-31',
                'registration_expiry' => '2024-12-31',
                'last_maintenance' => '2024-01-15',
                'next_maintenance' => '2024-04-15',
                'notes' => 'Excellent condition, experienced driver'
            ],
            [
                'name' => 'Toyota Hiace Minibus',
                'type' => 'Minibus',
                'brand' => 'Toyota',
                'model' => 'Hiace',
                'year' => 2021,
                'license_plate' => 'CAI-5678',
                'capacity' => 16,
                'description' => 'Reliable minibus ideal for medium-sized groups with good fuel efficiency.',
                'city_id' => $cities['Cairo']->id,
                'driver_name' => 'Mohammed Ali',
                'driver_phone' => '+20 10 2345 6789',
                'driver_license' => 'DL-2021-002',
                'price_per_hour' => 20.00,
                'price_per_day' => 150.00,
                'currency' => 'USD',
                'fuel_type' => 'Diesel',
                'transmission' => 'Manual',
                'features' => [
                    'Air Conditioning',
                    'Comfortable Seating',
                    'Luggage Space',
                    'GPS Navigation',
                    'Safety Equipment'
                ],
                'images' => [
                    'vehicle2_exterior.jpg',
                    'vehicle2_interior.jpg',
                    'vehicle2_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2024-11-30',
                'registration_expiry' => '2024-11-30',
                'last_maintenance' => '2024-01-10',
                'next_maintenance' => '2024-04-10',
                'notes' => 'Reliable and fuel-efficient'
            ],
            [
                'name' => 'BMW X5 SUV',
                'type' => 'SUV',
                'brand' => 'BMW',
                'model' => 'X5',
                'year' => 2023,
                'license_plate' => 'CAI-9012',
                'capacity' => 7,
                'description' => 'Luxury SUV perfect for small groups or VIP transfers with premium comfort.',
                'city_id' => $cities['Cairo']->id,
                'driver_name' => 'Omar Khalil',
                'driver_phone' => '+20 10 3456 7890',
                'driver_license' => 'DL-2023-003',
                'price_per_hour' => 35.00,
                'price_per_day' => 250.00,
                'currency' => 'USD',
                'fuel_type' => 'Gasoline',
                'transmission' => 'Automatic',
                'features' => [
                    'Air Conditioning',
                    'Leather Seats',
                    'WiFi',
                    'USB Charging',
                    'Premium Sound System',
                    'GPS Navigation',
                    'Safety Equipment',
                    'Sunroof'
                ],
                'images' => [
                    'vehicle3_exterior.jpg',
                    'vehicle3_interior.jpg',
                    'vehicle3_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2025-01-31',
                'registration_expiry' => '2025-01-31',
                'last_maintenance' => '2024-02-01',
                'next_maintenance' => '2024-05-01',
                'notes' => 'Premium luxury vehicle for VIP clients'
            ],
            
            // Luxor Vehicles
            [
                'name' => 'Ford Transit Van',
                'type' => 'Van',
                'brand' => 'Ford',
                'model' => 'Transit',
                'year' => 2022,
                'license_plate' => 'LUX-3456',
                'capacity' => 14,
                'description' => 'Comfortable van perfect for archaeological site tours with good visibility.',
                'city_id' => $cities['Luxor']->id,
                'driver_name' => 'Hassan Mahmoud',
                'driver_phone' => '+20 95 1234 5678',
                'driver_license' => 'DL-2022-004',
                'price_per_hour' => 22.00,
                'price_per_day' => 160.00,
                'currency' => 'USD',
                'fuel_type' => 'Diesel',
                'transmission' => 'Manual',
                'features' => [
                    'Air Conditioning',
                    'Comfortable Seating',
                    'Luggage Space',
                    'GPS Navigation',
                    'Safety Equipment',
                    'High Visibility'
                ],
                'images' => [
                    'vehicle4_exterior.jpg',
                    'vehicle4_interior.jpg',
                    'vehicle4_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2024-10-31',
                'registration_expiry' => '2024-10-31',
                'last_maintenance' => '2024-01-20',
                'next_maintenance' => '2024-04-20',
                'notes' => 'Perfect for archaeological tours'
            ],
            [
                'name' => 'Toyota Land Cruiser',
                'type' => 'SUV',
                'brand' => 'Toyota',
                'model' => 'Land Cruiser',
                'year' => 2021,
                'license_plate' => 'LUX-7890',
                'capacity' => 8,
                'description' => 'Rugged SUV ideal for desert excursions and off-road adventures.',
                'city_id' => $cities['Luxor']->id,
                'driver_name' => 'Karim Abdel',
                'driver_phone' => '+20 95 2345 6789',
                'driver_license' => 'DL-2021-005',
                'price_per_hour' => 30.00,
                'price_per_day' => 200.00,
                'currency' => 'USD',
                'fuel_type' => 'Diesel',
                'transmission' => 'Automatic',
                'features' => [
                    'Air Conditioning',
                    '4WD',
                    'Comfortable Seating',
                    'GPS Navigation',
                    'Safety Equipment',
                    'Off-road Capability'
                ],
                'images' => [
                    'vehicle5_exterior.jpg',
                    'vehicle5_interior.jpg',
                    'vehicle5_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2024-09-30',
                'registration_expiry' => '2024-09-30',
                'last_maintenance' => '2024-01-25',
                'next_maintenance' => '2024-04-25',
                'notes' => 'Excellent for desert and off-road tours'
            ],
            
            // Aswan Vehicles
            [
                'name' => 'Hyundai H1 Van',
                'type' => 'Van',
                'brand' => 'Hyundai',
                'model' => 'H1',
                'year' => 2022,
                'license_plate' => 'ASW-1234',
                'capacity' => 11,
                'description' => 'Modern van with excellent fuel efficiency and comfortable seating.',
                'city_id' => $cities['Aswan']->id,
                'driver_name' => 'Tarek Mansour',
                'driver_phone' => '+20 97 1234 5678',
                'driver_license' => 'DL-2022-006',
                'price_per_hour' => 18.00,
                'price_per_day' => 130.00,
                'currency' => 'USD',
                'fuel_type' => 'Diesel',
                'transmission' => 'Manual',
                'features' => [
                    'Air Conditioning',
                    'Comfortable Seating',
                    'Luggage Space',
                    'GPS Navigation',
                    'Safety Equipment',
                    'Fuel Efficient'
                ],
                'images' => [
                    'vehicle6_exterior.jpg',
                    'vehicle6_interior.jpg',
                    'vehicle6_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2024-08-31',
                'registration_expiry' => '2024-08-31',
                'last_maintenance' => '2024-02-05',
                'next_maintenance' => '2024-05-05',
                'notes' => 'Modern and fuel-efficient'
            ],
            
            // Hurghada Vehicles
            [
                'name' => 'Mercedes-Benz Vito Van',
                'type' => 'Van',
                'brand' => 'Mercedes-Benz',
                'model' => 'Vito',
                'year' => 2023,
                'license_plate' => 'HRG-5678',
                'capacity' => 8,
                'description' => 'Compact luxury van perfect for airport transfers and city tours.',
                'city_id' => $cities['Hurghada']->id,
                'driver_name' => 'Youssef Ibrahim',
                'driver_phone' => '+20 65 1234 5678',
                'driver_license' => 'DL-2023-007',
                'price_per_hour' => 28.00,
                'price_per_day' => 190.00,
                'currency' => 'USD',
                'fuel_type' => 'Diesel',
                'transmission' => 'Automatic',
                'features' => [
                    'Air Conditioning',
                    'WiFi',
                    'USB Charging',
                    'Comfortable Seating',
                    'Luggage Space',
                    'GPS Navigation',
                    'Safety Equipment'
                ],
                'images' => [
                    'vehicle7_exterior.jpg',
                    'vehicle7_interior.jpg',
                    'vehicle7_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2025-02-28',
                'registration_expiry' => '2025-02-28',
                'last_maintenance' => '2024-02-10',
                'next_maintenance' => '2024-05-10',
                'notes' => 'Perfect for airport transfers'
            ],
            [
                'name' => 'Chevrolet Suburban',
                'type' => 'SUV',
                'brand' => 'Chevrolet',
                'model' => 'Suburban',
                'year' => 2022,
                'license_plate' => 'HRG-9012',
                'capacity' => 9,
                'description' => 'Large SUV with spacious interior and powerful engine for long-distance travel.',
                'city_id' => $cities['Hurghada']->id,
                'driver_name' => 'Nour El Din',
                'driver_phone' => '+20 65 2345 6789',
                'driver_license' => 'DL-2022-008',
                'price_per_hour' => 32.00,
                'price_per_day' => 220.00,
                'currency' => 'USD',
                'fuel_type' => 'Gasoline',
                'transmission' => 'Automatic',
                'features' => [
                    'Air Conditioning',
                    'Leather Seats',
                    'WiFi',
                    'USB Charging',
                    'Premium Sound System',
                    'GPS Navigation',
                    'Safety Equipment',
                    'Spacious Interior'
                ],
                'images' => [
                    'vehicle8_exterior.jpg',
                    'vehicle8_interior.jpg',
                    'vehicle8_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2024-07-31',
                'registration_expiry' => '2024-07-31',
                'last_maintenance' => '2024-02-15',
                'next_maintenance' => '2024-05-15',
                'notes' => 'Spacious and comfortable for long trips'
            ],
            
            // Sharm El Sheikh Vehicles
            [
                'name' => 'Volkswagen Crafter Van',
                'type' => 'Van',
                'brand' => 'Volkswagen',
                'model' => 'Crafter',
                'year' => 2023,
                'license_plate' => 'SHS-3456',
                'capacity' => 15,
                'description' => 'Modern van with excellent build quality and comfortable ride.',
                'city_id' => $cities['Sharm El Sheikh']->id,
                'driver_name' => 'Ahmed Hassan',
                'driver_phone' => '+20 69 1234 5678',
                'driver_license' => 'DL-2023-009',
                'price_per_hour' => 24.00,
                'price_per_day' => 170.00,
                'currency' => 'USD',
                'fuel_type' => 'Diesel',
                'transmission' => 'Manual',
                'features' => [
                    'Air Conditioning',
                    'Comfortable Seating',
                    'Luggage Space',
                    'GPS Navigation',
                    'Safety Equipment',
                    'Modern Design'
                ],
                'images' => [
                    'vehicle9_exterior.jpg',
                    'vehicle9_interior.jpg',
                    'vehicle9_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2025-03-31',
                'registration_expiry' => '2025-03-31',
                'last_maintenance' => '2024-02-20',
                'next_maintenance' => '2024-05-20',
                'notes' => 'Modern design with excellent build quality'
            ],
            [
                'name' => 'Range Rover Sport',
                'type' => 'SUV',
                'brand' => 'Land Rover',
                'model' => 'Range Rover Sport',
                'year' => 2023,
                'license_plate' => 'SHS-7890',
                'capacity' => 5,
                'description' => 'Luxury SUV perfect for VIP transfers and premium tours.',
                'city_id' => $cities['Sharm El Sheikh']->id,
                'driver_name' => 'Mohammed Ali',
                'driver_phone' => '+20 69 2345 6789',
                'driver_license' => 'DL-2023-010',
                'price_per_hour' => 45.00,
                'price_per_day' => 300.00,
                'currency' => 'USD',
                'fuel_type' => 'Gasoline',
                'transmission' => 'Automatic',
                'features' => [
                    'Air Conditioning',
                    'Leather Seats',
                    'WiFi',
                    'USB Charging',
                    'Premium Sound System',
                    'GPS Navigation',
                    'Safety Equipment',
                    'Sunroof',
                    '4WD'
                ],
                'images' => [
                    'vehicle10_exterior.jpg',
                    'vehicle10_interior.jpg',
                    'vehicle10_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2025-04-30',
                'registration_expiry' => '2025-04-30',
                'last_maintenance' => '2024-02-25',
                'next_maintenance' => '2024-05-25',
                'notes' => 'Ultimate luxury for VIP clients'
            ],
            
            // Alexandria Vehicles
            [
                'name' => 'Peugeot Boxer Van',
                'type' => 'Van',
                'brand' => 'Peugeot',
                'model' => 'Boxer',
                'year' => 2022,
                'license_plate' => 'ALX-1234',
                'capacity' => 13,
                'description' => 'Reliable van with good fuel economy and comfortable seating.',
                'city_id' => $cities['Alexandria']->id,
                'driver_name' => 'Omar Khalil',
                'driver_phone' => '+20 3 1234 5678',
                'driver_license' => 'DL-2022-011',
                'price_per_hour' => 20.00,
                'price_per_day' => 140.00,
                'currency' => 'USD',
                'fuel_type' => 'Diesel',
                'transmission' => 'Manual',
                'features' => [
                    'Air Conditioning',
                    'Comfortable Seating',
                    'Luggage Space',
                    'GPS Navigation',
                    'Safety Equipment',
                    'Fuel Efficient'
                ],
                'images' => [
                    'vehicle11_exterior.jpg',
                    'vehicle11_interior.jpg',
                    'vehicle11_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2024-06-30',
                'registration_expiry' => '2024-06-30',
                'last_maintenance' => '2024-03-01',
                'next_maintenance' => '2024-06-01',
                'notes' => 'Reliable and fuel-efficient'
            ],
            
            // Dahab Vehicles
            [
                'name' => 'Nissan Patrol',
                'type' => 'SUV',
                'brand' => 'Nissan',
                'model' => 'Patrol',
                'year' => 2021,
                'license_plate' => 'DAH-5678',
                'capacity' => 7,
                'description' => 'Rugged SUV perfect for desert adventures and off-road tours.',
                'city_id' => $cities['Dahab']->id,
                'driver_name' => 'Hassan Mahmoud',
                'driver_phone' => '+20 69 3456 7890',
                'driver_license' => 'DL-2021-012',
                'price_per_hour' => 28.00,
                'price_per_day' => 190.00,
                'currency' => 'USD',
                'fuel_type' => 'Diesel',
                'transmission' => 'Automatic',
                'features' => [
                    'Air Conditioning',
                    '4WD',
                    'Comfortable Seating',
                    'GPS Navigation',
                    'Safety Equipment',
                    'Off-road Capability',
                    'Desert Ready'
                ],
                'images' => [
                    'vehicle12_exterior.jpg',
                    'vehicle12_interior.jpg',
                    'vehicle12_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2024-05-31',
                'registration_expiry' => '2024-05-31',
                'last_maintenance' => '2024-03-05',
                'next_maintenance' => '2024-06-05',
                'notes' => 'Perfect for desert adventures'
            ],
            
            // Marsa Alam Vehicles
            [
                'name' => 'Isuzu NPR Van',
                'type' => 'Van',
                'brand' => 'Isuzu',
                'model' => 'NPR',
                'year' => 2022,
                'license_plate' => 'MAL-9012',
                'capacity' => 16,
                'description' => 'Large van with excellent reliability and spacious interior.',
                'city_id' => $cities['Marsa Alam']->id,
                'driver_name' => 'Karim Abdel',
                'driver_phone' => '+20 65 4567 8901',
                'driver_license' => 'DL-2022-013',
                'price_per_hour' => 26.00,
                'price_per_day' => 180.00,
                'currency' => 'USD',
                'fuel_type' => 'Diesel',
                'transmission' => 'Manual',
                'features' => [
                    'Air Conditioning',
                    'Comfortable Seating',
                    'Luggage Space',
                    'GPS Navigation',
                    'Safety Equipment',
                    'Spacious Interior'
                ],
                'images' => [
                    'vehicle13_exterior.jpg',
                    'vehicle13_interior.jpg',
                    'vehicle13_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2024-04-30',
                'registration_expiry' => '2024-04-30',
                'last_maintenance' => '2024-03-10',
                'next_maintenance' => '2024-06-10',
                'notes' => 'Large capacity and reliable'
            ],
            
            // El Gouna Vehicles
            [
                'name' => 'Audi Q7',
                'type' => 'SUV',
                'brand' => 'Audi',
                'model' => 'Q7',
                'year' => 2023,
                'license_plate' => 'EGO-3456',
                'capacity' => 7,
                'description' => 'Premium SUV with luxury features and excellent performance.',
                'city_id' => $cities['El Gouna']->id,
                'driver_name' => 'Tarek Mansour',
                'driver_phone' => '+20 65 5678 9012',
                'driver_license' => 'DL-2023-014',
                'price_per_hour' => 40.00,
                'price_per_day' => 280.00,
                'currency' => 'USD',
                'fuel_type' => 'Gasoline',
                'transmission' => 'Automatic',
                'features' => [
                    'Air Conditioning',
                    'Leather Seats',
                    'WiFi',
                    'USB Charging',
                    'Premium Sound System',
                    'GPS Navigation',
                    'Safety Equipment',
                    'Sunroof',
                    '4WD'
                ],
                'images' => [
                    'vehicle14_exterior.jpg',
                    'vehicle14_interior.jpg',
                    'vehicle14_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2025-05-31',
                'registration_expiry' => '2025-05-31',
                'last_maintenance' => '2024-03-15',
                'next_maintenance' => '2024-06-15',
                'notes' => 'Premium luxury SUV'
            ],
            
            // Siwa Vehicles
            [
                'name' => 'Toyota Hilux Pickup',
                'type' => 'Pickup',
                'brand' => 'Toyota',
                'model' => 'Hilux',
                'year' => 2022,
                'license_plate' => 'SIW-7890',
                'capacity' => 5,
                'description' => 'Rugged pickup truck perfect for desert adventures and off-road tours.',
                'city_id' => $cities['Siwa']->id,
                'driver_name' => 'Youssef Ibrahim',
                'driver_phone' => '+20 46 6789 0123',
                'driver_license' => 'DL-2022-015',
                'price_per_hour' => 25.00,
                'price_per_day' => 160.00,
                'currency' => 'USD',
                'fuel_type' => 'Diesel',
                'transmission' => 'Manual',
                'features' => [
                    'Air Conditioning',
                    '4WD',
                    'Comfortable Seating',
                    'GPS Navigation',
                    'Safety Equipment',
                    'Off-road Capability',
                    'Desert Ready',
                    'Cargo Space'
                ],
                'images' => [
                    'vehicle15_exterior.jpg',
                    'vehicle15_interior.jpg',
                    'vehicle15_driver.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'insurance_expiry' => '2024-03-31',
                'registration_expiry' => '2024-03-31',
                'last_maintenance' => '2024-03-20',
                'next_maintenance' => '2024-06-20',
                'notes' => 'Perfect for desert and oasis tours'
            ]
        ];

        foreach ($vehicles as $vehicleData) {
            // Check if vehicle already exists
            $existingVehicle = Vehicle::where('license_plate', $vehicleData['license_plate'])->first();
            
            if (!$existingVehicle) {
                Vehicle::create($vehicleData);
            }
        }

        $this->command->info('Vehicles seeded successfully!');
    }
}

