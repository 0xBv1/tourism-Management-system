<?php

namespace Database\Seeders;

use App\Models\Representative;
use App\Models\City;
use App\Enums\ResourceStatus;
use Illuminate\Database\Seeder;

class RepresentativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = City::all()->keyBy('name');
        
        $representatives = [
            // Cairo Representatives
            [
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@representative.com',
                'phone' => '+20 10 1234 5678',
                'nationality' => 'Egyptian',
                'languages' => ['Arabic', 'English', 'French'],
                'specializations' => ['City Tours', 'Airport Transfers', 'VIP Services', 'Group Tours'],
                'experience_years' => 8,
                'city_id' => $cities['Cairo']->id,
                'price_per_hour' => 15.00,
                'price_per_day' => 100.00,
                'currency' => 'USD',
                'bio' => 'Ahmed Hassan is a professional representative specializing in city tours and VIP services. He has extensive knowledge of Cairo and provides excellent customer service.',
                'certifications' => [
                    'Licensed Tour Representative - Egyptian Ministry of Tourism',
                    'VIP Service Certificate',
                    'Customer Service Excellence',
                    'Airport Transfer Specialist'
                ],
                'profile_image' => 'rep1_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.7,
                'total_ratings' => 89,
                'availability_schedule' => [
                    'monday' => ['08:00', '20:00'],
                    'tuesday' => ['08:00', '20:00'],
                    'wednesday' => ['08:00', '20:00'],
                    'thursday' => ['08:00', '20:00'],
                    'friday' => ['08:00', '20:00'],
                    'saturday' => ['08:00', '20:00'],
                    'sunday' => ['08:00', '20:00']
                ],
                'emergency_contact' => 'Fatima Hassan',
                'emergency_phone' => '+20 10 9876 5432',
                'company_name' => 'Cairo Tourism Services',
                'company_license' => 'CT-2023-001',
                'service_areas' => ['Cairo', 'Giza', 'Saqqara', 'Memphis'],
                'notes' => 'Professional representative with excellent customer service'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@representative.com',
                'phone' => '+20 10 2345 6789',
                'nationality' => 'British',
                'languages' => ['English', 'Arabic', 'German'],
                'specializations' => ['Cultural Tours', 'Shopping Assistance', 'Restaurant Recommendations', 'Event Planning'],
                'experience_years' => 6,
                'city_id' => $cities['Cairo']->id,
                'price_per_hour' => 18.00,
                'price_per_day' => 120.00,
                'currency' => 'USD',
                'bio' => 'Sarah Johnson is a British expat who specializes in cultural experiences and shopping assistance. She helps visitors discover authentic local experiences.',
                'certifications' => [
                    'Licensed Tour Representative - Egyptian Ministry of Tourism',
                    'Cultural Heritage Specialist',
                    'Shopping Guide Certificate',
                    'Event Planning License'
                ],
                'profile_image' => 'rep2_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.6,
                'total_ratings' => 67,
                'availability_schedule' => [
                    'monday' => ['09:00', '19:00'],
                    'tuesday' => ['09:00', '19:00'],
                    'wednesday' => ['09:00', '19:00'],
                    'thursday' => ['09:00', '19:00'],
                    'friday' => ['09:00', '19:00'],
                    'saturday' => ['09:00', '19:00'],
                    'sunday' => ['09:00', '19:00']
                ],
                'emergency_contact' => 'John Johnson',
                'emergency_phone' => '+44 20 1234 5678',
                'company_name' => 'Cairo Cultural Services',
                'company_license' => 'CCS-2023-002',
                'service_areas' => ['Cairo', 'Islamic Cairo', 'Coptic Cairo', 'Khan el-Khalili'],
                'notes' => 'Specializes in cultural experiences and shopping'
            ],
            
            // Luxor Representatives
            [
                'name' => 'Mohammed Ali',
                'email' => 'mohammed.ali@representative.com',
                'phone' => '+20 95 1234 5678',
                'nationality' => 'Egyptian',
                'languages' => ['Arabic', 'English', 'Italian'],
                'specializations' => ['Archaeological Tours', 'Temple Visits', 'Nile Cruises', 'Group Coordination'],
                'experience_years' => 10,
                'city_id' => $cities['Luxor']->id,
                'price_per_hour' => 16.00,
                'price_per_day' => 110.00,
                'currency' => 'USD',
                'bio' => 'Mohammed Ali is a local Luxor representative with deep knowledge of the archaeological sites. He specializes in coordinating temple visits and Nile cruise arrangements.',
                'certifications' => [
                    'Licensed Tour Representative - Egyptian Ministry of Tourism',
                    'Archaeological Site Coordinator',
                    'Nile Cruise Specialist',
                    'Group Tour Management'
                ],
                'profile_image' => 'rep3_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.8,
                'total_ratings' => 124,
                'availability_schedule' => [
                    'monday' => ['07:00', '19:00'],
                    'tuesday' => ['07:00', '19:00'],
                    'wednesday' => ['07:00', '19:00'],
                    'thursday' => ['07:00', '19:00'],
                    'friday' => ['07:00', '19:00'],
                    'saturday' => ['07:00', '19:00'],
                    'sunday' => ['07:00', '19:00']
                ],
                'emergency_contact' => 'Amina Ali',
                'emergency_phone' => '+20 95 9876 5432',
                'company_name' => 'Luxor Archaeological Services',
                'company_license' => 'LAS-2023-003',
                'service_areas' => ['Luxor', 'Valley of the Kings', 'Karnak', 'West Bank'],
                'notes' => 'Expert in archaeological site coordination'
            ],
            
            // Aswan Representatives
            [
                'name' => 'Emma Wilson',
                'email' => 'emma.wilson@representative.com',
                'phone' => '+20 97 1234 5678',
                'nationality' => 'Australian',
                'languages' => ['English', 'Arabic', 'Spanish'],
                'specializations' => ['Nubian Culture', 'Abu Simbel Tours', 'Nile Cruises', 'Cultural Experiences'],
                'experience_years' => 5,
                'city_id' => $cities['Aswan']->id,
                'price_per_hour' => 14.00,
                'price_per_day' => 95.00,
                'currency' => 'USD',
                'bio' => 'Emma Wilson is an enthusiastic representative who specializes in Nubian culture and Abu Simbel tours. She provides excellent cultural experiences and local insights.',
                'certifications' => [
                    'Licensed Tour Representative - Egyptian Ministry of Tourism',
                    'Nubian Culture Specialist',
                    'Cultural Experience Guide',
                    'Nile Cruise Coordinator'
                ],
                'profile_image' => 'rep4_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.5,
                'total_ratings' => 78,
                'availability_schedule' => [
                    'monday' => ['08:00', '18:00'],
                    'tuesday' => ['08:00', '18:00'],
                    'wednesday' => ['08:00', '18:00'],
                    'thursday' => ['08:00', '18:00'],
                    'friday' => ['08:00', '18:00'],
                    'saturday' => ['08:00', '18:00'],
                    'sunday' => ['08:00', '18:00']
                ],
                'emergency_contact' => 'James Wilson',
                'emergency_phone' => '+61 2 1234 5678',
                'company_name' => 'Aswan Cultural Services',
                'company_license' => 'ACS-2023-004',
                'service_areas' => ['Aswan', 'Abu Simbel', 'Philae', 'Nubian Villages'],
                'notes' => 'Specializes in Nubian culture and Abu Simbel'
            ],
            
            // Hurghada Representatives
            [
                'name' => 'Fatima Zahra',
                'email' => 'fatima.zahra@representative.com',
                'phone' => '+20 65 1234 5678',
                'nationality' => 'Egyptian',
                'languages' => ['Arabic', 'English', 'French', 'German'],
                'specializations' => ['Beach Tours', 'Water Sports', 'Desert Safaris', 'Resort Services'],
                'experience_years' => 7,
                'city_id' => $cities['Hurghada']->id,
                'price_per_hour' => 17.00,
                'price_per_day' => 115.00,
                'currency' => 'USD',
                'bio' => 'Fatima Zahra is a professional representative specializing in beach tours and water sports. She provides excellent service for resort guests and adventure seekers.',
                'certifications' => [
                    'Licensed Tour Representative - Egyptian Ministry of Tourism',
                    'Water Sports Coordinator',
                    'Desert Safari Specialist',
                    'Resort Service Excellence'
                ],
                'profile_image' => 'rep5_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.7,
                'total_ratings' => 92,
                'availability_schedule' => [
                    'monday' => ['07:00', '20:00'],
                    'tuesday' => ['07:00', '20:00'],
                    'wednesday' => ['07:00', '20:00'],
                    'thursday' => ['07:00', '20:00'],
                    'friday' => ['07:00', '20:00'],
                    'saturday' => ['07:00', '20:00'],
                    'sunday' => ['07:00', '20:00']
                ],
                'emergency_contact' => 'Hassan Zahra',
                'emergency_phone' => '+20 65 9876 5432',
                'company_name' => 'Hurghada Beach Services',
                'company_license' => 'HBS-2023-005',
                'service_areas' => ['Hurghada', 'El Gouna', 'Sahl Hasheesh', 'Makadi Bay'],
                'notes' => 'Expert in beach tours and water sports'
            ],
            [
                'name' => 'John Smith',
                'email' => 'john.smith@representative.com',
                'phone' => '+20 65 2345 6789',
                'nationality' => 'American',
                'languages' => ['English', 'Arabic', 'Russian'],
                'specializations' => ['Diving Tours', 'Snorkeling', 'Desert Adventures', 'Adventure Sports'],
                'experience_years' => 6,
                'city_id' => $cities['Hurghada']->id,
                'price_per_hour' => 19.00,
                'price_per_day' => 125.00,
                'currency' => 'USD',
                'bio' => 'John Smith is an adventure specialist who coordinates diving tours, snorkeling trips, and desert adventures. He provides excellent service for thrill-seekers.',
                'certifications' => [
                    'Licensed Tour Representative - Egyptian Ministry of Tourism',
                    'Adventure Sports Coordinator',
                    'Diving Tour Specialist',
                    'Desert Adventure Guide'
                ],
                'profile_image' => 'rep6_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.6,
                'total_ratings' => 85,
                'availability_schedule' => [
                    'monday' => ['06:00', '21:00'],
                    'tuesday' => ['06:00', '21:00'],
                    'wednesday' => ['06:00', '21:00'],
                    'thursday' => ['06:00', '21:00'],
                    'friday' => ['06:00', '21:00'],
                    'saturday' => ['06:00', '21:00'],
                    'sunday' => ['06:00', '21:00']
                ],
                'emergency_contact' => 'Mary Smith',
                'emergency_phone' => '+1 555 123 4567',
                'company_name' => 'Hurghada Adventure Services',
                'company_license' => 'HAS-2023-006',
                'service_areas' => ['Hurghada', 'Giftun Island', 'Desert', 'Red Sea'],
                'notes' => 'Specializes in adventure sports and diving'
            ],
            
            // Sharm El Sheikh Representatives
            [
                'name' => 'Aisha Rahman',
                'email' => 'aisha.rahman@representative.com',
                'phone' => '+20 69 1234 5678',
                'nationality' => 'Pakistani',
                'languages' => ['English', 'Arabic', 'Urdu', 'Hindi'],
                'specializations' => ['Resort Services', 'Beach Activities', 'Shopping Tours', 'Family Services'],
                'experience_years' => 4,
                'city_id' => $cities['Sharm El Sheikh']->id,
                'price_per_hour' => 15.00,
                'price_per_day' => 100.00,
                'currency' => 'USD',
                'bio' => 'Aisha Rahman is a friendly representative who specializes in resort services and family-friendly activities. She provides excellent customer service and local assistance.',
                'certifications' => [
                    'Licensed Tour Representative - Egyptian Ministry of Tourism',
                    'Resort Service Specialist',
                    'Family Service Coordinator',
                    'Customer Service Excellence'
                ],
                'profile_image' => 'rep7_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.5,
                'total_ratings' => 63,
                'availability_schedule' => [
                    'monday' => ['08:00', '20:00'],
                    'tuesday' => ['08:00', '20:00'],
                    'wednesday' => ['08:00', '20:00'],
                    'thursday' => ['08:00', '20:00'],
                    'friday' => ['08:00', '20:00'],
                    'saturday' => ['08:00', '20:00'],
                    'sunday' => ['08:00', '20:00']
                ],
                'emergency_contact' => 'Hassan Rahman',
                'emergency_phone' => '+92 21 1234 5678',
                'company_name' => 'Sharm Resort Services',
                'company_license' => 'SRS-2023-007',
                'service_areas' => ['Sharm El Sheikh', 'Naama Bay', 'Old Market', 'Resort Areas'],
                'notes' => 'Excellent for resort services and families'
            ],
            [
                'name' => 'Carlos Rodriguez',
                'email' => 'carlos.rodriguez@representative.com',
                'phone' => '+20 69 2345 6789',
                'nationality' => 'Spanish',
                'languages' => ['Spanish', 'English', 'Arabic', 'Italian'],
                'specializations' => ['Diving Coordination', 'Mount Sinai Tours', 'Desert Safaris', 'Adventure Tours'],
                'experience_years' => 8,
                'city_id' => $cities['Sharm El Sheikh']->id,
                'price_per_hour' => 18.00,
                'price_per_day' => 120.00,
                'currency' => 'USD',
                'bio' => 'Carlos Rodriguez is an adventure coordinator who specializes in diving tours, Mount Sinai expeditions, and desert adventures. He provides excellent service for adventure seekers.',
                'certifications' => [
                    'Licensed Tour Representative - Egyptian Ministry of Tourism',
                    'Adventure Tour Coordinator',
                    'Mount Sinai Specialist',
                    'Diving Tour Manager'
                ],
                'profile_image' => 'rep8_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.7,
                'total_ratings' => 97,
                'availability_schedule' => [
                    'monday' => ['06:00', '22:00'],
                    'tuesday' => ['06:00', '22:00'],
                    'wednesday' => ['06:00', '22:00'],
                    'thursday' => ['06:00', '22:00'],
                    'friday' => ['06:00', '22:00'],
                    'saturday' => ['06:00', '22:00'],
                    'sunday' => ['06:00', '22:00']
                ],
                'emergency_contact' => 'Maria Rodriguez',
                'emergency_phone' => '+34 91 123 4567',
                'company_name' => 'Sharm Adventure Services',
                'company_license' => 'SAS-2023-008',
                'service_areas' => ['Sharm El Sheikh', 'Mount Sinai', 'Desert', 'Red Sea'],
                'notes' => 'Expert in adventure tours and Mount Sinai'
            ],
            
            // Alexandria Representatives
            [
                'name' => 'Nour El Din',
                'email' => 'nour.eldin@representative.com',
                'phone' => '+20 3 1234 5678',
                'nationality' => 'Egyptian',
                'languages' => ['Arabic', 'English', 'French', 'Greek'],
                'specializations' => ['City Tours', 'Library Visits', 'Mediterranean Culture', 'Historical Sites'],
                'experience_years' => 9,
                'city_id' => $cities['Alexandria']->id,
                'price_per_hour' => 16.00,
                'price_per_day' => 110.00,
                'currency' => 'USD',
                'bio' => 'Nour El Din is a local Alexandrian representative with deep knowledge of the city\'s history and Mediterranean culture. He specializes in cultural tours and historical site visits.',
                'certifications' => [
                    'Licensed Tour Representative - Egyptian Ministry of Tourism',
                    'Historical Site Coordinator',
                    'Cultural Heritage Specialist',
                    'Library Tour Guide'
                ],
                'profile_image' => 'rep9_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.6,
                'total_ratings' => 81,
                'availability_schedule' => [
                    'monday' => ['09:00', '19:00'],
                    'tuesday' => ['09:00', '19:00'],
                    'wednesday' => ['09:00', '19:00'],
                    'thursday' => ['09:00', '19:00'],
                    'friday' => ['09:00', '19:00'],
                    'saturday' => ['09:00', '19:00'],
                    'sunday' => ['09:00', '19:00']
                ],
                'emergency_contact' => 'Amina El Din',
                'emergency_phone' => '+20 3 9876 5432',
                'company_name' => 'Alexandria Cultural Services',
                'company_license' => 'ACS-2023-009',
                'service_areas' => ['Alexandria', 'Library of Alexandria', 'Corniche', 'Historical Sites'],
                'notes' => 'Expert in Alexandria history and culture'
            ],
            
            // Dahab Representatives
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@representative.com',
                'phone' => '+20 69 3456 7890',
                'nationality' => 'Mexican',
                'languages' => ['Spanish', 'English', 'Arabic'],
                'specializations' => ['Diving Services', 'Beach Activities', 'Bedouin Experiences', 'Adventure Tours'],
                'experience_years' => 5,
                'city_id' => $cities['Dahab']->id,
                'price_per_hour' => 17.00,
                'price_per_day' => 115.00,
                'currency' => 'USD',
                'bio' => 'Maria Garcia is a diving specialist who coordinates diving services and beach activities. She also arranges authentic Bedouin experiences and adventure tours.',
                'certifications' => [
                    'Licensed Tour Representative - Egyptian Ministry of Tourism',
                    'Diving Service Coordinator',
                    'Bedouin Experience Specialist',
                    'Adventure Tour Manager'
                ],
                'profile_image' => 'rep10_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.5,
                'total_ratings' => 72,
                'availability_schedule' => [
                    'monday' => ['07:00', '19:00'],
                    'tuesday' => ['07:00', '19:00'],
                    'wednesday' => ['07:00', '19:00'],
                    'thursday' => ['07:00', '19:00'],
                    'friday' => ['07:00', '19:00'],
                    'saturday' => ['07:00', '19:00'],
                    'sunday' => ['07:00', '19:00']
                ],
                'emergency_contact' => 'Jose Garcia',
                'emergency_phone' => '+52 55 1234 5678',
                'company_name' => 'Dahab Adventure Services',
                'company_license' => 'DAS-2023-010',
                'service_areas' => ['Dahab', 'Blue Hole', 'Desert', 'Bedouin Camps'],
                'notes' => 'Specializes in diving and Bedouin experiences'
            ],
            
            // Marsa Alam Representatives
            [
                'name' => 'Omar Khalil',
                'email' => 'omar.khalil@representative.com',
                'phone' => '+20 65 4567 8901',
                'nationality' => 'Egyptian',
                'languages' => ['Arabic', 'English', 'German'],
                'specializations' => ['Marine Tours', 'Coral Reef Tours', 'Diving Services', 'Eco-Tourism'],
                'experience_years' => 6,
                'city_id' => $cities['Marsa Alam']->id,
                'price_per_hour' => 18.00,
                'price_per_day' => 120.00,
                'currency' => 'USD',
                'bio' => 'Omar Khalil is a marine specialist who coordinates diving tours and coral reef experiences. He specializes in eco-tourism and marine conservation awareness.',
                'certifications' => [
                    'Licensed Tour Representative - Egyptian Ministry of Tourism',
                    'Marine Tour Coordinator',
                    'Coral Reef Specialist',
                    'Eco-Tourism Guide'
                ],
                'profile_image' => 'rep11_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.7,
                'total_ratings' => 88,
                'availability_schedule' => [
                    'monday' => ['07:00', '18:00'],
                    'tuesday' => ['07:00', '18:00'],
                    'wednesday' => ['07:00', '18:00'],
                    'thursday' => ['07:00', '18:00'],
                    'friday' => ['07:00', '18:00'],
                    'saturday' => ['07:00', '18:00'],
                    'sunday' => ['07:00', '18:00']
                ],
                'emergency_contact' => 'Fatima Khalil',
                'emergency_phone' => '+20 65 9876 5432',
                'company_name' => 'Marsa Alam Marine Services',
                'company_license' => 'MAMS-2023-011',
                'service_areas' => ['Marsa Alam', 'Coral Reefs', 'Marine Reserve', 'Diving Sites'],
                'notes' => 'Expert in marine tours and coral reefs'
            ],
            
            // El Gouna Representatives
            [
                'name' => 'Lisa Chen',
                'email' => 'lisa.chen@representative.com',
                'phone' => '+20 65 5678 9012',
                'nationality' => 'Chinese',
                'languages' => ['Chinese', 'English', 'Arabic'],
                'specializations' => ['Lagoon Tours', 'Water Sports', 'Cultural Experiences', 'Photography Tours'],
                'experience_years' => 3,
                'city_id' => $cities['El Gouna']->id,
                'price_per_hour' => 15.00,
                'price_per_day' => 100.00,
                'currency' => 'USD',
                'bio' => 'Lisa Chen is a water sports enthusiast who specializes in lagoon tours and water activities. She provides excellent service for photography tours and cultural experiences.',
                'certifications' => [
                    'Licensed Tour Representative - Egyptian Ministry of Tourism',
                    'Water Sports Coordinator',
                    'Photography Tour Guide',
                    'Cultural Experience Specialist'
                ],
                'profile_image' => 'rep12_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.4,
                'total_ratings' => 56,
                'availability_schedule' => [
                    'monday' => ['08:00', '18:00'],
                    'tuesday' => ['08:00', '18:00'],
                    'wednesday' => ['08:00', '18:00'],
                    'thursday' => ['08:00', '18:00'],
                    'friday' => ['08:00', '18:00'],
                    'saturday' => ['08:00', '18:00'],
                    'sunday' => ['08:00', '18:00']
                ],
                'emergency_contact' => 'Wei Chen',
                'emergency_phone' => '+86 10 1234 5678',
                'company_name' => 'El Gouna Water Services',
                'company_license' => 'EGWS-2023-012',
                'service_areas' => ['El Gouna', 'Lagoons', 'Water Sports', 'Cultural Sites'],
                'notes' => 'Specializes in water sports and photography'
            ],
            
            // Siwa Representatives
            [
                'name' => 'Youssef Ibrahim',
                'email' => 'youssef.ibrahim@representative.com',
                'phone' => '+20 46 1234 5678',
                'nationality' => 'Egyptian',
                'languages' => ['Arabic', 'English', 'Berber'],
                'specializations' => ['Oasis Tours', 'Desert Adventures', 'Berber Culture', 'Archaeological Sites'],
                'experience_years' => 11,
                'city_id' => $cities['Siwa']->id,
                'price_per_hour' => 16.00,
                'price_per_day' => 110.00,
                'currency' => 'USD',
                'bio' => 'Youssef Ibrahim is a local Siwa representative with deep knowledge of the oasis culture and Berber traditions. He specializes in authentic desert experiences.',
                'certifications' => [
                    'Licensed Tour Representative - Egyptian Ministry of Tourism',
                    'Oasis Tour Coordinator',
                    'Berber Culture Specialist',
                    'Desert Adventure Guide'
                ],
                'profile_image' => 'rep13_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.8,
                'total_ratings' => 94,
                'availability_schedule' => [
                    'monday' => ['08:00', '17:00'],
                    'tuesday' => ['08:00', '17:00'],
                    'wednesday' => ['08:00', '17:00'],
                    'thursday' => ['08:00', '17:00'],
                    'friday' => ['08:00', '17:00'],
                    'saturday' => ['08:00', '17:00'],
                    'sunday' => ['08:00', '17:00']
                ],
                'emergency_contact' => 'Amina Ibrahim',
                'emergency_phone' => '+20 46 9876 5432',
                'company_name' => 'Siwa Oasis Services',
                'company_license' => 'SOS-2023-013',
                'service_areas' => ['Siwa Oasis', 'Desert', 'Berber Villages', 'Archaeological Sites'],
                'notes' => 'Expert in oasis culture and Berber traditions'
            ]
        ];

        foreach ($representatives as $representativeData) {
            // Check if representative already exists
            $existingRepresentative = Representative::where('email', $representativeData['email'])->first();
            
            if (!$existingRepresentative) {
                Representative::create($representativeData);
            }
        }

        $this->command->info('Representatives seeded successfully!');
    }
}

