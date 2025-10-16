<?php

namespace Database\Seeders;

use App\Models\Guide;
use App\Models\City;
use App\Enums\ResourceStatus;
use Illuminate\Database\Seeder;

class GuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = City::all()->keyBy('name');
        
        $guides = [
            // Cairo Guides
            [
                'name' => 'Dr. Ahmed Hassan',
                'email' => 'ahmed.hassan@guide.com',
                'phone' => '+20 10 1234 5678',
                'nationality' => 'Egyptian',
                'languages' => ['Arabic', 'English', 'French'],
                'specializations' => ['Egyptology', 'Islamic History', 'Coptic History', 'Modern Egypt'],
                'experience_years' => 15,
                'city_id' => $cities['Cairo']->id,
                'price_per_hour' => 25.00,
                'price_per_day' => 150.00,
                'currency' => 'USD',
                'bio' => 'Dr. Ahmed Hassan is a renowned Egyptologist with over 15 years of experience guiding tours in Cairo and throughout Egypt. He holds a PhD in Egyptology from Cairo University and specializes in ancient Egyptian history, Islamic architecture, and Coptic heritage.',
                'certifications' => [
                    'PhD in Egyptology - Cairo University',
                    'Licensed Tour Guide - Egyptian Ministry of Tourism',
                    'Certified Egyptologist - Supreme Council of Antiquities',
                    'Advanced English Certificate - Cambridge University'
                ],
                'profile_image' => 'guide1_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.9,
                'total_ratings' => 127,
                'availability_schedule' => [
                    'monday' => ['09:00', '18:00'],
                    'tuesday' => ['09:00', '18:00'],
                    'wednesday' => ['09:00', '18:00'],
                    'thursday' => ['09:00', '18:00'],
                    'friday' => ['09:00', '18:00'],
                    'saturday' => ['09:00', '18:00'],
                    'sunday' => ['09:00', '18:00']
                ],
                'emergency_contact' => 'Fatima Hassan',
                'emergency_phone' => '+20 10 9876 5432',
                'notes' => 'Expert in Giza Pyramids, Egyptian Museum, and Islamic Cairo'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@guide.com',
                'phone' => '+20 10 2345 6789',
                'nationality' => 'British',
                'languages' => ['English', 'Arabic', 'German'],
                'specializations' => ['Cultural Tours', 'Food Tours', 'Shopping Tours', 'Photography Tours'],
                'experience_years' => 8,
                'city_id' => $cities['Cairo']->id,
                'price_per_hour' => 20.00,
                'price_per_day' => 120.00,
                'currency' => 'USD',
                'bio' => 'Sarah Johnson is a British expat who has been living in Cairo for over 8 years. She specializes in cultural experiences, food tours, and helping visitors discover the authentic side of Cairo beyond the typical tourist attractions.',
                'certifications' => [
                    'Licensed Tour Guide - Egyptian Ministry of Tourism',
                    'Cultural Heritage Specialist',
                    'Food Safety Certificate',
                    'Photography Guide License'
                ],
                'profile_image' => 'guide2_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.7,
                'total_ratings' => 89,
                'availability_schedule' => [
                    'monday' => ['10:00', '19:00'],
                    'tuesday' => ['10:00', '19:00'],
                    'wednesday' => ['10:00', '19:00'],
                    'thursday' => ['10:00', '19:00'],
                    'friday' => ['10:00', '19:00'],
                    'saturday' => ['10:00', '19:00'],
                    'sunday' => ['10:00', '19:00']
                ],
                'emergency_contact' => 'John Johnson',
                'emergency_phone' => '+44 20 1234 5678',
                'notes' => 'Perfect for cultural and food experiences'
            ],
            
            // Luxor Guides
            [
                'name' => 'Mohammed Ali',
                'email' => 'mohammed.ali@guide.com',
                'phone' => '+20 95 1234 5678',
                'nationality' => 'Egyptian',
                'languages' => ['Arabic', 'English', 'Italian'],
                'specializations' => ['Valley of the Kings', 'Karnak Temple', 'Luxor Temple', 'West Bank Tours'],
                'experience_years' => 12,
                'city_id' => $cities['Luxor']->id,
                'price_per_hour' => 22.00,
                'price_per_day' => 130.00,
                'currency' => 'USD',
                'bio' => 'Mohammed Ali is a local Luxor guide with deep knowledge of the ancient Theban necropolis. He has been guiding visitors through the Valley of the Kings and the magnificent temples of Luxor for over 12 years.',
                'certifications' => [
                    'Licensed Tour Guide - Egyptian Ministry of Tourism',
                    'Archaeological Guide Certificate',
                    'Valley of the Kings Specialist',
                    'Temple Guide License'
                ],
                'profile_image' => 'guide3_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.8,
                'total_ratings' => 156,
                'availability_schedule' => [
                    'monday' => ['06:00', '17:00'],
                    'tuesday' => ['06:00', '17:00'],
                    'wednesday' => ['06:00', '17:00'],
                    'thursday' => ['06:00', '17:00'],
                    'friday' => ['06:00', '17:00'],
                    'saturday' => ['06:00', '17:00'],
                    'sunday' => ['06:00', '17:00']
                ],
                'emergency_contact' => 'Amina Ali',
                'emergency_phone' => '+20 95 9876 5432',
                'notes' => 'Expert in Valley of the Kings and Luxor temples'
            ],
            [
                'name' => 'Emma Wilson',
                'email' => 'emma.wilson@guide.com',
                'phone' => '+20 95 2345 6789',
                'nationality' => 'Australian',
                'languages' => ['English', 'Arabic', 'Spanish'],
                'specializations' => ['Hot Air Balloon Tours', 'Sunrise Tours', 'Photography Tours', 'Adventure Tours'],
                'experience_years' => 6,
                'city_id' => $cities['Luxor']->id,
                'price_per_hour' => 18.00,
                'price_per_day' => 110.00,
                'currency' => 'USD',
                'bio' => 'Emma Wilson is an adventurous Australian guide who specializes in unique experiences like hot air balloon tours over the Valley of the Kings and sunrise photography tours. She brings energy and enthusiasm to every tour.',
                'certifications' => [
                    'Licensed Tour Guide - Egyptian Ministry of Tourism',
                    'Hot Air Balloon Guide License',
                    'Photography Guide Certificate',
                    'Adventure Tourism License'
                ],
                'profile_image' => 'guide4_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.6,
                'total_ratings' => 73,
                'availability_schedule' => [
                    'monday' => ['05:00', '16:00'],
                    'tuesday' => ['05:00', '16:00'],
                    'wednesday' => ['05:00', '16:00'],
                    'thursday' => ['05:00', '16:00'],
                    'friday' => ['05:00', '16:00'],
                    'saturday' => ['05:00', '16:00'],
                    'sunday' => ['05:00', '16:00']
                ],
                'emergency_contact' => 'James Wilson',
                'emergency_phone' => '+61 2 1234 5678',
                'notes' => 'Specializes in hot air balloon and adventure tours'
            ],
            
            // Aswan Guides
            [
                'name' => 'Fatima Zahra',
                'email' => 'fatima.zahra@guide.com',
                'phone' => '+20 97 1234 5678',
                'nationality' => 'Egyptian',
                'languages' => ['Arabic', 'English', 'French', 'German'],
                'specializations' => ['Nubian Culture', 'Philae Temple', 'Abu Simbel', 'Nile Cruises'],
                'experience_years' => 10,
                'city_id' => $cities['Aswan']->id,
                'price_per_hour' => 20.00,
                'price_per_day' => 120.00,
                'currency' => 'USD',
                'bio' => 'Fatima Zahra is a Nubian guide with deep roots in Aswan. She specializes in Nubian culture, the magnificent temples of Philae and Abu Simbel, and provides authentic insights into the local way of life.',
                'certifications' => [
                    'Licensed Tour Guide - Egyptian Ministry of Tourism',
                    'Nubian Culture Specialist',
                    'Temple Guide License',
                    'Nile Cruise Guide Certificate'
                ],
                'profile_image' => 'guide5_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.9,
                'total_ratings' => 98,
                'availability_schedule' => [
                    'monday' => ['08:00', '18:00'],
                    'tuesday' => ['08:00', '18:00'],
                    'wednesday' => ['08:00', '18:00'],
                    'thursday' => ['08:00', '18:00'],
                    'friday' => ['08:00', '18:00'],
                    'saturday' => ['08:00', '18:00'],
                    'sunday' => ['08:00', '18:00']
                ],
                'emergency_contact' => 'Hassan Zahra',
                'emergency_phone' => '+20 97 9876 5432',
                'notes' => 'Expert in Nubian culture and Abu Simbel'
            ],
            
            // Hurghada Guides
            [
                'name' => 'John Smith',
                'email' => 'john.smith@guide.com',
                'phone' => '+20 65 1234 5678',
                'nationality' => 'American',
                'languages' => ['English', 'Arabic', 'Russian'],
                'specializations' => ['Diving Tours', 'Snorkeling', 'Desert Safaris', 'Water Sports'],
                'experience_years' => 7,
                'city_id' => $cities['Hurghada']->id,
                'price_per_hour' => 25.00,
                'price_per_day' => 150.00,
                'currency' => 'USD',
                'bio' => 'John Smith is a certified diving instructor and adventure guide who specializes in underwater experiences in the Red Sea. He also leads exciting desert safari adventures and water sports activities.',
                'certifications' => [
                    'Licensed Tour Guide - Egyptian Ministry of Tourism',
                    'PADI Dive Master',
                    'Desert Safari Guide License',
                    'Water Sports Instructor',
                    'First Aid Certificate'
                ],
                'profile_image' => 'guide6_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.8,
                'total_ratings' => 134,
                'availability_schedule' => [
                    'monday' => ['07:00', '19:00'],
                    'tuesday' => ['07:00', '19:00'],
                    'wednesday' => ['07:00', '19:00'],
                    'thursday' => ['07:00', '19:00'],
                    'friday' => ['07:00', '19:00'],
                    'saturday' => ['07:00', '19:00'],
                    'sunday' => ['07:00', '19:00']
                ],
                'emergency_contact' => 'Mary Smith',
                'emergency_phone' => '+1 555 123 4567',
                'notes' => 'Certified diving instructor and adventure specialist'
            ],
            [
                'name' => 'Aisha Rahman',
                'email' => 'aisha.rahman@guide.com',
                'phone' => '+20 65 2345 6789',
                'nationality' => 'Pakistani',
                'languages' => ['English', 'Arabic', 'Urdu', 'Hindi'],
                'specializations' => ['Beach Tours', 'Cultural Tours', 'Shopping Tours', 'Family Tours'],
                'experience_years' => 5,
                'city_id' => $cities['Hurghada']->id,
                'price_per_hour' => 15.00,
                'price_per_day' => 90.00,
                'currency' => 'USD',
                'bio' => 'Aisha Rahman is a friendly and patient guide who specializes in family-friendly tours and cultural experiences. She is particularly good with families and provides comfortable, informative tours.',
                'certifications' => [
                    'Licensed Tour Guide - Egyptian Ministry of Tourism',
                    'Family Tour Specialist',
                    'Cultural Heritage Guide',
                    'Child Safety Certificate'
                ],
                'profile_image' => 'guide7_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.7,
                'total_ratings' => 67,
                'availability_schedule' => [
                    'monday' => ['09:00', '17:00'],
                    'tuesday' => ['09:00', '17:00'],
                    'wednesday' => ['09:00', '17:00'],
                    'thursday' => ['09:00', '17:00'],
                    'friday' => ['09:00', '17:00'],
                    'saturday' => ['09:00', '17:00'],
                    'sunday' => ['09:00', '17:00']
                ],
                'emergency_contact' => 'Hassan Rahman',
                'emergency_phone' => '+92 21 1234 5678',
                'notes' => 'Excellent with families and cultural tours'
            ],
            
            // Sharm El Sheikh Guides
            [
                'name' => 'Carlos Rodriguez',
                'email' => 'carlos.rodriguez@guide.com',
                'phone' => '+20 69 1234 5678',
                'nationality' => 'Spanish',
                'languages' => ['Spanish', 'English', 'Arabic', 'Italian'],
                'specializations' => ['Diving Tours', 'Snorkeling', 'Sinai Desert', 'Mount Sinai Tours'],
                'experience_years' => 9,
                'city_id' => $cities['Sharm El Sheikh']->id,
                'price_per_hour' => 22.00,
                'price_per_day' => 130.00,
                'currency' => 'USD',
                'bio' => 'Carlos Rodriguez is a passionate diving instructor and desert guide who specializes in the unique experiences of Sharm El Sheikh, from world-class diving to mystical Mount Sinai adventures.',
                'certifications' => [
                    'Licensed Tour Guide - Egyptian Ministry of Tourism',
                    'PADI Instructor',
                    'Mount Sinai Guide License',
                    'Desert Survival Certificate',
                    'Emergency Response Certificate'
                ],
                'profile_image' => 'guide8_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.8,
                'total_ratings' => 112,
                'availability_schedule' => [
                    'monday' => ['06:00', '20:00'],
                    'tuesday' => ['06:00', '20:00'],
                    'wednesday' => ['06:00', '20:00'],
                    'thursday' => ['06:00', '20:00'],
                    'friday' => ['06:00', '20:00'],
                    'saturday' => ['06:00', '20:00'],
                    'sunday' => ['06:00', '20:00']
                ],
                'emergency_contact' => 'Maria Rodriguez',
                'emergency_phone' => '+34 91 123 4567',
                'notes' => 'Expert in diving and Mount Sinai tours'
            ],
            
            // Alexandria Guides
            [
                'name' => 'Nour El Din',
                'email' => 'nour.eldin@guide.com',
                'phone' => '+20 3 1234 5678',
                'nationality' => 'Egyptian',
                'languages' => ['Arabic', 'English', 'French', 'Greek'],
                'specializations' => ['Alexandria History', 'Mediterranean Culture', 'Library Tours', 'Coastal Tours'],
                'experience_years' => 11,
                'city_id' => $cities['Alexandria']->id,
                'price_per_hour' => 18.00,
                'price_per_day' => 110.00,
                'currency' => 'USD',
                'bio' => 'Nour El Din is a local Alexandrian guide with deep knowledge of the city\'s rich history, from ancient times to the modern era. He specializes in the Library of Alexandria and Mediterranean culture.',
                'certifications' => [
                    'Licensed Tour Guide - Egyptian Ministry of Tourism',
                    'Alexandria History Specialist',
                    'Library Guide Certificate',
                    'Mediterranean Culture Expert'
                ],
                'profile_image' => 'guide9_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.7,
                'total_ratings' => 89,
                'availability_schedule' => [
                    'monday' => ['09:00', '18:00'],
                    'tuesday' => ['09:00', '18:00'],
                    'wednesday' => ['09:00', '18:00'],
                    'thursday' => ['09:00', '18:00'],
                    'friday' => ['09:00', '18:00'],
                    'saturday' => ['09:00', '18:00'],
                    'sunday' => ['09:00', '18:00']
                ],
                'emergency_contact' => 'Amina El Din',
                'emergency_phone' => '+20 3 9876 5432',
                'notes' => 'Expert in Alexandria history and Mediterranean culture'
            ],
            
            // Dahab Guides
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@guide.com',
                'phone' => '+20 69 3456 7890',
                'nationality' => 'Mexican',
                'languages' => ['Spanish', 'English', 'Arabic'],
                'specializations' => ['Diving', 'Snorkeling', 'Desert Adventures', 'Bedouin Culture'],
                'experience_years' => 6,
                'city_id' => $cities['Dahab']->id,
                'price_per_hour' => 20.00,
                'price_per_day' => 120.00,
                'currency' => 'USD',
                'bio' => 'Maria Garcia is a certified diving instructor who specializes in the unique underwater world of Dahab. She also leads desert adventures and provides insights into Bedouin culture.',
                'certifications' => [
                    'Licensed Tour Guide - Egyptian Ministry of Tourism',
                    'PADI Dive Master',
                    'Desert Guide License',
                    'Bedouin Culture Specialist',
                    'Emergency First Response'
                ],
                'profile_image' => 'guide10_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.6,
                'total_ratings' => 76,
                'availability_schedule' => [
                    'monday' => ['07:00', '18:00'],
                    'tuesday' => ['07:00', '18:00'],
                    'wednesday' => ['07:00', '18:00'],
                    'thursday' => ['07:00', '18:00'],
                    'friday' => ['07:00', '18:00'],
                    'saturday' => ['07:00', '18:00'],
                    'sunday' => ['07:00', '18:00']
                ],
                'emergency_contact' => 'Jose Garcia',
                'emergency_phone' => '+52 55 1234 5678',
                'notes' => 'Specializes in diving and Bedouin culture'
            ],
            
            // Marsa Alam Guides
            [
                'name' => 'Omar Khalil',
                'email' => 'omar.khalil@guide.com',
                'phone' => '+20 65 4567 8901',
                'nationality' => 'Egyptian',
                'languages' => ['Arabic', 'English', 'German'],
                'specializations' => ['Diving', 'Snorkeling', 'Coral Reefs', 'Marine Biology'],
                'experience_years' => 8,
                'city_id' => $cities['Marsa Alam']->id,
                'price_per_hour' => 24.00,
                'price_per_day' => 140.00,
                'currency' => 'USD',
                'bio' => 'Omar Khalil is a marine biologist and diving instructor who specializes in the pristine coral reefs of Marsa Alam. He provides educational tours about marine life and conservation.',
                'certifications' => [
                    'Licensed Tour Guide - Egyptian Ministry of Tourism',
                    'PADI Instructor',
                    'Marine Biology Degree',
                    'Coral Reef Specialist',
                    'Conservation Guide License'
                ],
                'profile_image' => 'guide11_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.9,
                'total_ratings' => 95,
                'availability_schedule' => [
                    'monday' => ['07:00', '17:00'],
                    'tuesday' => ['07:00', '17:00'],
                    'wednesday' => ['07:00', '17:00'],
                    'thursday' => ['07:00', '17:00'],
                    'friday' => ['07:00', '17:00'],
                    'saturday' => ['07:00', '17:00'],
                    'sunday' => ['07:00', '17:00']
                ],
                'emergency_contact' => 'Fatima Khalil',
                'emergency_phone' => '+20 65 9876 5432',
                'notes' => 'Marine biologist specializing in coral reefs'
            ],
            
            // El Gouna Guides
            [
                'name' => 'Lisa Chen',
                'email' => 'lisa.chen@guide.com',
                'phone' => '+20 65 5678 9012',
                'nationality' => 'Chinese',
                'languages' => ['Chinese', 'English', 'Arabic'],
                'specializations' => ['Water Sports', 'Lagoon Tours', 'Cultural Tours', 'Photography'],
                'experience_years' => 4,
                'city_id' => $cities['El Gouna']->id,
                'price_per_hour' => 16.00,
                'price_per_day' => 100.00,
                'currency' => 'USD',
                'bio' => 'Lisa Chen is a water sports enthusiast who specializes in the unique lagoon experiences of El Gouna. She provides excellent photography tours and cultural insights.',
                'certifications' => [
                    'Licensed Tour Guide - Egyptian Ministry of Tourism',
                    'Water Sports Instructor',
                    'Photography Guide License',
                    'Cultural Heritage Guide'
                ],
                'profile_image' => 'guide12_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.5,
                'total_ratings' => 58,
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
                'notes' => 'Specializes in water sports and photography'
            ],
            
            // Siwa Guides
            [
                'name' => 'Youssef Ibrahim',
                'email' => 'youssef.ibrahim@guide.com',
                'phone' => '+20 46 1234 5678',
                'nationality' => 'Egyptian',
                'languages' => ['Arabic', 'English', 'Berber'],
                'specializations' => ['Oasis Tours', 'Desert Adventures', 'Berber Culture', 'Archaeological Sites'],
                'experience_years' => 13,
                'city_id' => $cities['Siwa']->id,
                'price_per_hour' => 20.00,
                'price_per_day' => 120.00,
                'currency' => 'USD',
                'bio' => 'Youssef Ibrahim is a local Siwa guide with deep knowledge of the oasis culture, Berber traditions, and the unique archaeological sites of the Western Desert.',
                'certifications' => [
                    'Licensed Tour Guide - Egyptian Ministry of Tourism',
                    'Oasis Specialist',
                    'Desert Survival Expert',
                    'Berber Culture Guide',
                    'Archaeological Site Guide'
                ],
                'profile_image' => 'guide13_profile.jpg',
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'rating' => 4.8,
                'total_ratings' => 82,
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
                'notes' => 'Expert in oasis culture and Berber traditions'
            ]
        ];

        foreach ($guides as $guideData) {
            // Check if guide already exists
            $existingGuide = Guide::where('email', $guideData['email'])->first();
            
            if (!$existingGuide) {
                Guide::create($guideData);
            }
        }

        $this->command->info('Guides seeded successfully!');
    }
}

