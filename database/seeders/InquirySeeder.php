<?php

namespace Database\Seeders;

use App\Models\Inquiry;
use App\Models\Client;
use App\Models\User;
use App\Enums\InquiryStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InquirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        $users = User::all();
        
        if ($clients->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Clients or Users not found. Please run ClientSeeder and AdminSeeder first.');
            return;
        }
        
        $inquiries = [
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@email.com',
                'phone' => '+20 10 1234 5678',
                'arrival_date' => now()->addDays(30),
                'departure_date' => now()->addDays(37),
                'number_pax' => 4,
                'tour_name' => 'Classic Egypt Tour - Cairo, Luxor & Aswan',
                'nationality' => 'Egyptian',
                'subject' => 'Family vacation to explore ancient Egypt',
                'tour_itinerary' => 'Day 1: Arrival in Cairo, visit Giza Pyramids and Sphinx
Day 2: Egyptian Museum and Islamic Cairo
Day 3: Fly to Luxor, visit Valley of the Kings and Karnak Temple
Day 4: Luxor Temple and West Bank sites
Day 5: Fly to Aswan, visit Philae Temple
Day 6: Abu Simbel day trip
Day 7: Nile cruise from Aswan to Luxor
Day 8: Return to Cairo and departure',
                'status' => InquiryStatus::CONFIRMED,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 2500.00,
                'paid_amount' => 1250.00,
                'remaining_amount' => 1250.00,
                'payment_method' => 'Bank Transfer',
                'confirmed_at' => now()->subDays(5),
                'completed_at' => null,
            ],
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@email.com',
                'phone' => '+44 20 1234 5678',
                'arrival_date' => now()->addDays(45),
                'departure_date' => now()->addDays(52),
                'number_pax' => 2,
                'tour_name' => 'Luxury Nile Cruise Experience',
                'nationality' => 'British',
                'subject' => 'Anniversary trip with luxury accommodations',
                'tour_itinerary' => 'Day 1: Arrival in Luxor
Day 2: Valley of the Kings and Hatshepsut Temple
Day 3: Karnak Temple and Luxor Temple
Day 4: Sail to Edfu, visit Edfu Temple
Day 5: Sail to Kom Ombo, visit Kom Ombo Temple
Day 6: Arrive in Aswan, visit Philae Temple
Day 7: Abu Simbel day trip
Day 8: Return to Luxor and departure',
                'status' => InquiryStatus::PENDING,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 3200.00,
                'paid_amount' => 0.00,
                'remaining_amount' => 3200.00,
                'payment_method' => 'Credit Card',
                'confirmed_at' => null,
                'completed_at' => null,
            ],
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'Mohammed Ali',
                'email' => 'mohammed.ali@email.com',
                'phone' => '+20 10 2345 6789',
                'arrival_date' => now()->addDays(60),
                'departure_date' => now()->addDays(67),
                'number_pax' => 6,
                'tour_name' => 'Red Sea Adventure Package',
                'nationality' => 'Egyptian',
                'subject' => 'Group diving trip to Hurghada and Sharm El Sheikh',
                'tour_itinerary' => 'Day 1: Arrival in Hurghada
Day 2: Diving at Giftun Island
Day 3: Desert safari and quad biking
Day 4: Transfer to Sharm El Sheikh
Day 5: Diving at Ras Mohammed National Park
Day 6: Snorkeling at Tiran Island
Day 7: Mount Sinai sunrise tour
Day 8: Departure',
                'status' => InquiryStatus::CONFIRMED,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 1800.00,
                'paid_amount' => 900.00,
                'remaining_amount' => 900.00,
                'payment_method' => 'Cash',
                'confirmed_at' => now()->subDays(10),
                'completed_at' => null,
            ],
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'Emma Wilson',
                'email' => 'emma.wilson@email.com',
                'phone' => '+33 1 23 45 67 89',
                'arrival_date' => now()->addDays(15),
                'departure_date' => now()->addDays(22),
                'number_pax' => 2,
                'tour_name' => 'Alexandria Cultural Tour',
                'nationality' => 'French',
                'subject' => 'Cultural exploration of Alexandria and Mediterranean coast',
                'tour_itinerary' => 'Day 1: Arrival in Alexandria
Day 2: Library of Alexandria and Corniche
Day 3: Catacombs of Kom el Shoqafa
Day 4: Pompey\'s Pillar and Roman Theater
Day 5: Montazah Palace and Gardens
Day 6: Day trip to El Alamein
Day 7: Free day for shopping and relaxation
Day 8: Departure',
                'status' => InquiryStatus::CONFIRMED,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 1200.00,
                'paid_amount' => 1200.00,
                'remaining_amount' => 0.00,
                'payment_method' => 'Bank Transfer',
                'confirmed_at' => now()->subDays(3),
                'completed_at' => null,
            ],
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'Fatima Zahra',
                'email' => 'fatima.zahra@email.com',
                'phone' => '+20 10 3456 7890',
                'arrival_date' => now()->addDays(90),
                'departure_date' => now()->addDays(97),
                'number_pax' => 8,
                'tour_name' => 'Siwa Oasis Desert Experience',
                'nationality' => 'Moroccan',
                'subject' => 'Desert adventure and oasis exploration',
                'tour_itinerary' => 'Day 1: Arrival in Siwa Oasis
Day 2: Temple of the Oracle and Cleopatra\'s Bath
Day 3: Desert safari to Great Sand Sea
Day 4: Salt lakes and hot springs
Day 5: Traditional Berber village visit
Day 6: Mountain of the Dead and Shali Fortress
Day 7: Free day for relaxation
Day 8: Departure',
                'status' => InquiryStatus::PENDING,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 1600.00,
                'paid_amount' => 0.00,
                'remaining_amount' => 1600.00,
                'payment_method' => 'Credit Card',
                'confirmed_at' => null,
                'completed_at' => null,
            ],
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'John Smith',
                'email' => 'john.smith@email.com',
                'phone' => '+1 555 123 4567',
                'arrival_date' => now()->addDays(20),
                'departure_date' => now()->addDays(27),
                'number_pax' => 3,
                'tour_name' => 'Cairo Highlights Tour',
                'nationality' => 'American',
                'subject' => 'First-time visit to Egypt focusing on Cairo attractions',
                'tour_itinerary' => 'Day 1: Arrival in Cairo
Day 2: Giza Pyramids, Sphinx, and Solar Boat Museum
Day 3: Egyptian Museum and Islamic Cairo
Day 4: Coptic Cairo and Khan el-Khalili
Day 5: Saqqara and Memphis
Day 6: Day trip to Alexandria
Day 7: Free day for shopping
Day 8: Departure',
                'status' => InquiryStatus::CONFIRMED,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 1400.00,
                'paid_amount' => 700.00,
                'remaining_amount' => 700.00,
                'payment_method' => 'Credit Card',
                'confirmed_at' => now()->subDays(7),
                'completed_at' => null,
            ],
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'Aisha Rahman',
                'email' => 'aisha.rahman@email.com',
                'phone' => '+92 21 1234 5678',
                'arrival_date' => now()->addDays(75),
                'departure_date' => now()->addDays(82),
                'number_pax' => 5,
                'tour_name' => 'Dahab Adventure Package',
                'nationality' => 'Pakistani',
                'subject' => 'Diving and adventure activities in Dahab',
                'tour_itinerary' => 'Day 1: Arrival in Dahab
Day 2: Blue Hole diving
Day 3: Snorkeling at Lighthouse Reef
Day 4: Desert safari and camel riding
Day 5: Mount Sinai sunrise tour
Day 6: Free diving and relaxation
Day 7: St. Catherine\'s Monastery
Day 8: Departure',
                'status' => InquiryStatus::PENDING,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 1100.00,
                'paid_amount' => 0.00,
                'remaining_amount' => 1100.00,
                'payment_method' => 'Bank Transfer',
                'confirmed_at' => null,
                'completed_at' => null,
            ],
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'Carlos Rodriguez',
                'email' => 'carlos.rodriguez@email.com',
                'phone' => '+34 91 123 4567',
                'arrival_date' => now()->addDays(105),
                'departure_date' => now()->addDays(112),
                'number_pax' => 2,
                'tour_name' => 'Marsa Alam Marine Experience',
                'nationality' => 'Spanish',
                'subject' => 'Marine life exploration and diving in Marsa Alam',
                'tour_itinerary' => 'Day 1: Arrival in Marsa Alam
Day 2: Diving at Elphinstone Reef
Day 3: Snorkeling at Abu Dabab
Day 4: Dolphin House diving
Day 5: Samadai Reef (Dolphin House)
Day 6: Desert safari and quad biking
Day 7: Relaxation and beach time
Day 8: Departure',
                'status' => InquiryStatus::CONFIRMED,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 1300.00,
                'paid_amount' => 650.00,
                'remaining_amount' => 650.00,
                'payment_method' => 'Credit Card',
                'confirmed_at' => now()->subDays(12),
                'completed_at' => null,
            ],
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'Nour El Din',
                'email' => 'nour.eldin@email.com',
                'phone' => '+20 10 4567 8901',
                'arrival_date' => now()->addDays(120),
                'departure_date' => now()->addDays(127),
                'number_pax' => 4,
                'tour_name' => 'El Gouna Water Sports Package',
                'nationality' => 'Egyptian',
                'subject' => 'Water sports and lagoon activities in El Gouna',
                'tour_itinerary' => 'Day 1: Arrival in El Gouna
Day 2: Kite surfing lessons
Day 3: Sailing and windsurfing
Day 4: Snorkeling and diving
Day 5: Kayaking and paddleboarding
Day 6: Fishing trip
Day 7: Relaxation and spa
Day 8: Departure',
                'status' => InquiryStatus::PENDING,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 900.00,
                'paid_amount' => 0.00,
                'remaining_amount' => 900.00,
                'payment_method' => 'Cash',
                'confirmed_at' => null,
                'completed_at' => null,
            ],
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'Maria Garcia',
                'email' => 'maria.garcia@email.com',
                'phone' => '+34 91 234 5678',
                'arrival_date' => now()->addDays(135),
                'departure_date' => now()->addDays(142),
                'number_pax' => 2,
                'tour_name' => 'Luxor Archaeological Tour',
                'nationality' => 'Spanish',
                'subject' => 'In-depth exploration of Luxor\'s archaeological sites',
                'tour_itinerary' => 'Day 1: Arrival in Luxor
Day 2: Valley of the Kings (multiple tombs)
Day 3: Hatshepsut Temple and Valley of the Queens
Day 4: Karnak Temple complex
Day 5: Luxor Temple and Luxor Museum
Day 6: Medinet Habu and Ramesseum
Day 7: Deir el-Bahari and Tombs of the Nobles
Day 8: Departure',
                'status' => InquiryStatus::CONFIRMED,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 1600.00,
                'paid_amount' => 1600.00,
                'remaining_amount' => 0.00,
                'payment_method' => 'Bank Transfer',
                'confirmed_at' => now()->subDays(2),
                'completed_at' => null,
            ],
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'Omar Khalil',
                'email' => 'omar.khalil@email.com',
                'phone' => '+20 10 5678 9012',
                'arrival_date' => now()->addDays(150),
                'departure_date' => now()->addDays(157),
                'number_pax' => 6,
                'tour_name' => 'Aswan Nubian Culture Tour',
                'nationality' => 'Egyptian',
                'subject' => 'Cultural immersion in Nubian heritage and traditions',
                'tour_itinerary' => 'Day 1: Arrival in Aswan
Day 2: Philae Temple and High Dam
Day 3: Abu Simbel temples
Day 4: Nubian village visit and cultural experience
Day 5: Elephantine Island and Aswan Museum
Day 6: Felucca sailing and Kitchener\'s Island
Day 7: Unfinished Obelisk and Aswan Botanical Garden
Day 8: Departure',
                'status' => InquiryStatus::CONFIRMED,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 1200.00,
                'paid_amount' => 600.00,
                'remaining_amount' => 600.00,
                'payment_method' => 'Cash',
                'confirmed_at' => now()->subDays(8),
                'completed_at' => null,
            ],
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'Lisa Chen',
                'email' => 'lisa.chen@email.com',
                'phone' => '+86 10 1234 5678',
                'arrival_date' => now()->addDays(165),
                'departure_date' => now()->addDays(172),
                'number_pax' => 3,
                'tour_name' => 'Sharm El Sheikh Diving Package',
                'nationality' => 'Chinese',
                'subject' => 'Professional diving certification and reef exploration',
                'tour_itinerary' => 'Day 1: Arrival in Sharm El Sheikh
Day 2: PADI Open Water certification
Day 3: Diving at Ras Mohammed National Park
Day 4: Tiran Island diving
Day 5: Shark\'s Bay diving
Day 6: Mount Sinai sunrise tour
Day 7: Relaxation and shopping
Day 8: Departure',
                'status' => InquiryStatus::PENDING,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 1500.00,
                'paid_amount' => 0.00,
                'remaining_amount' => 1500.00,
                'payment_method' => 'Credit Card',
                'confirmed_at' => null,
                'completed_at' => null,
            ],
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'Youssef Ibrahim',
                'email' => 'youssef.ibrahim@email.com',
                'phone' => '+20 10 6789 0123',
                'arrival_date' => now()->addDays(180),
                'departure_date' => now()->addDays(187),
                'number_pax' => 4,
                'tour_name' => 'Hurghada Family Resort Package',
                'nationality' => 'Egyptian',
                'subject' => 'Family vacation with kids-friendly activities',
                'tour_itinerary' => 'Day 1: Arrival in Hurghada
Day 2: Beach activities and swimming
Day 3: Snorkeling trip for families
Day 4: Desert safari with kids activities
Day 5: Aqua park visit
Day 6: Glass-bottom boat tour
Day 7: Free day for relaxation
Day 8: Departure',
                'status' => InquiryStatus::CONFIRMED,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 1000.00,
                'paid_amount' => 500.00,
                'remaining_amount' => 500.00,
                'payment_method' => 'Bank Transfer',
                'confirmed_at' => now()->subDays(4),
                'completed_at' => null,
            ],
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'Anna Kowalski',
                'email' => 'anna.kowalski@email.com',
                'phone' => '+48 22 123 4567',
                'arrival_date' => now()->addDays(195),
                'departure_date' => now()->addDays(202),
                'number_pax' => 2,
                'tour_name' => 'Cairo and Alexandria Cultural Tour',
                'nationality' => 'Polish',
                'subject' => 'Cultural and historical exploration of northern Egypt',
                'tour_itinerary' => 'Day 1: Arrival in Cairo
Day 2: Giza Pyramids and Egyptian Museum
Day 3: Islamic Cairo and Khan el-Khalili
Day 4: Transfer to Alexandria
Day 5: Library of Alexandria and Corniche
Day 6: Catacombs and Roman Theater
Day 7: Return to Cairo, free time
Day 8: Departure',
                'status' => InquiryStatus::PENDING,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 1100.00,
                'paid_amount' => 0.00,
                'remaining_amount' => 1100.00,
                'payment_method' => 'Credit Card',
                'confirmed_at' => null,
                'completed_at' => null,
            ],
            [
                'inquiry_id' => 'INQ-' . Str::upper(Str::random(8)),
                'guest_name' => 'Hassan Mahmoud',
                'email' => 'hassan.mahmoud@email.com',
                'phone' => '+20 10 7890 1234',
                'arrival_date' => now()->addDays(210),
                'departure_date' => now()->addDays(217),
                'number_pax' => 5,
                'tour_name' => 'Complete Egypt Experience',
                'nationality' => 'Egyptian',
                'subject' => 'Comprehensive tour covering all major Egyptian attractions',
                'tour_itinerary' => 'Day 1: Arrival in Cairo
Day 2: Giza Pyramids and Sphinx
Day 3: Egyptian Museum and Islamic Cairo
Day 4: Fly to Luxor, Valley of the Kings
Day 5: Karnak and Luxor Temples
Day 6: Fly to Aswan, Philae Temple
Day 7: Abu Simbel day trip
Day 8: Return to Cairo and departure',
                'status' => InquiryStatus::CONFIRMED,
                'client_id' => $clients->random()->id,
                'assigned_to' => $users->random()->id,
                'assigned_reservation_id' => $users->random()->id,
                'assigned_operator_id' => $users->random()->id,
                'assigned_admin_id' => $users->random()->id,
                'total_amount' => 2800.00,
                'paid_amount' => 1400.00,
                'remaining_amount' => 1400.00,
                'payment_method' => 'Bank Transfer',
                'confirmed_at' => now()->subDays(6),
                'completed_at' => null,
            ]
        ];

        foreach ($inquiries as $inquiryData) {
            // Check if inquiry already exists
            $existingInquiry = Inquiry::where('inquiry_id', $inquiryData['inquiry_id'])->first();
            
            if (!$existingInquiry) {
                Inquiry::create($inquiryData);
            }
        }

        $this->command->info('Inquiries seeded successfully!');
    }
}
