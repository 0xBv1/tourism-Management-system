# Comprehensive Tourism Management System Seeders

## Overview
I have created comprehensive seeders with real data for your tourism management system. These seeders populate your database with realistic Egyptian tourism data including hotels, vehicles, guides, representatives, inquiries, bookings, and payments.

## Created Seeders

### 1. HotelSeeder (`database/seeders/HotelSeeder.php`)
**Real Egyptian Hotels with Complete Details:**
- **15 Luxury Hotels** across major Egyptian destinations
- **Locations:** Cairo, Luxor, Aswan, Hurghada, Sharm El Sheikh, Alexandria, Dahab, Marsa Alam, El Gouna, Siwa
- **Features:** Star ratings (4-5 stars), amenities, pricing, availability, policies
- **Real Hotels Include:**
  - Four Seasons Hotel Cairo at Nile Plaza
  - Cairo Marriott Hotel & Omar Khayyam Casino
  - Sofitel Winter Palace Luxor
  - Hilton Luxor Resort & Spa
  - Sofitel Legend Old Cataract Aswan
  - Grand Resort Hurghada
  - Four Seasons Resort Sharm El Sheikh
  - And many more...

### 2. VehicleSeeder (`database/seeders/VehicleSeeder.php`)
**15 Professional Vehicles with Drivers:**
- **Types:** Vans, Minibuses, SUVs, Pickups
- **Brands:** Mercedes-Benz, Toyota, BMW, Ford, Chevrolet, Volkswagen, Audi, Range Rover, Nissan, Isuzu, Peugeot, Hyundai
- **Features:** Capacity, pricing, driver details, maintenance schedules, insurance
- **Locations:** All major Egyptian cities
- **Real Details:** License plates, driver names, phone numbers, certifications

### 3. GuideSeeder (`database/seeders/GuideSeeder.php`)
**13 Professional Tour Guides:**
- **Specializations:** Egyptology, Cultural Tours, Diving, Archaeological Sites, Desert Adventures, Nubian Culture
- **Languages:** Arabic, English, French, German, Italian, Spanish, Chinese, Russian, Urdu, Hindi, Greek, Berber
- **Locations:** All major Egyptian destinations
- **Features:** Ratings, certifications, availability schedules, emergency contacts
- **Real Profiles:** Names, contact details, experience levels, specializations

### 4. RepresentativeSeeder (`database/seeders/RepresentativeSeeder.php`)
**13 Tour Representatives:**
- **Services:** City tours, airport transfers, VIP services, cultural experiences, adventure coordination
- **Companies:** Real company names with licenses
- **Locations:** All major Egyptian cities
- **Features:** Service areas, pricing, availability, company details
- **Specializations:** Resort services, diving coordination, cultural tours, family services

### 5. InquirySeeder (`database/seeders/InquirySeeder.php`)
**16 Realistic Tour Inquiries:**
- **Tour Types:** Classic Egypt tours, Nile cruises, Red Sea adventures, cultural tours, desert experiences
- **Destinations:** All major Egyptian attractions
- **Features:** Complete itineraries, pricing, payment status, client details
- **Real Tours Include:**
  - Classic Egypt Tour - Cairo, Luxor & Aswan
  - Luxury Nile Cruise Experience
  - Red Sea Adventure Package
  - Alexandria Cultural Tour
  - Siwa Oasis Desert Experience
  - And many more...

### 6. BookingFileSeeder (`database/seeders/BookingFileSeeder.php`)
**Comprehensive Booking Files:**
- **Statuses:** Pending, Confirmed, In Progress, Completed, Cancelled, Refunded
- **Features:** Checklists, notes, confirmation numbers, cancellation policies
- **Real Data:** File names, paths, generation dates, download tracking
- **Integration:** Links to inquiries with realistic booking scenarios

### 7. PaymentSeeder (`database/seeders/PaymentSeeder.php`)
**Realistic Payment Records:**
- **Payment Methods:** PayPal, Stripe, Bank Transfer, Cash, Wire Transfer, Credit Card
- **Statuses:** Paid, Pending, Not Paid
- **Features:** Transaction details, verification data, reference numbers
- **Scenarios:** Multiple payments per booking, different payment methods, realistic amounts

### 8. ResourceBookingSeeder (`database/seeders/ResourceBookingSeeder.php`)
**Resource Booking Management:**
- **Resource Types:** Hotels, Vehicles, Guides, Representatives
- **Features:** Start/end dates, pricing, special requests, confirmation numbers
- **Integration:** Links bookings to resources with realistic scenarios
- **Statuses:** All booking statuses with appropriate resource assignments

### 9. InquiryResourceSeeder (`database/seeders/InquiryResourceSeeder.php`)
**Inquiry Resource Assignments:**
- **Resource Matching:** Automatically assigns appropriate resources to inquiries
- **Features:** Availability confirmation, special requests, pricing
- **Integration:** Links inquiries to available resources
- **Realistic Scenarios:** Proper resource allocation based on tour requirements

## Database Seeder Updates

### Updated `DatabaseSeeder.php`
The main database seeder now includes all new seeders in the correct order:

```php
// Core system seeders
$this->call(RolesAndPermissionsSeeder::class);
$this->call(AdminSeeder::class);
$this->call(CountrySeeder::class);
$this->call(CitySeeder::class);
$this->call(SettingsSeeder::class);
$this->call(PermissionsSeeder::class);

// User and resource seeders
$this->call(DefaultUsersSeeder::class);
$this->call(ResourceSeeder::class);

// Tourism resource seeders
$this->call(HotelSeeder::class);
$this->call(VehicleSeeder::class);
$this->call(GuideSeeder::class);
$this->call(RepresentativeSeeder::class);

// Client seeders
$this->call(ClientSeeder::class);

// Business process seeders
$this->call(InquirySeeder::class);
$this->call(BookingFileSeeder::class);
$this->call(PaymentSeeder::class);
$this->call(ResourceBookingSeeder::class);
$this->call(InquiryResourceSeeder::class);

// Additional seeders
$this->call(ChatSeeder::class);
```

## How to Use

### Run All Seeders
```bash
php artisan db:seed
```

### Run Individual Seeders
```bash
php artisan db:seed --class=HotelSeeder
php artisan db:seed --class=VehicleSeeder
php artisan db:seed --class=GuideSeeder
# ... etc
```

### Fresh Database with All Seeders
```bash
php artisan migrate:fresh --seed
```

## Data Quality Features

### Realistic Data
- **Real Hotel Names:** Actual Egyptian hotels with correct details
- **Realistic Pricing:** Market-appropriate pricing in USD
- **Proper Relationships:** All foreign keys properly linked
- **Complete Information:** All required fields populated with realistic data

### Data Integrity
- **Duplicate Prevention:** Checks for existing records before creating
- **Proper Enums:** Uses correct enum values for statuses
- **Date Handling:** Proper Carbon date handling for all date fields
- **Currency Consistency:** All pricing in USD with proper decimal handling

### Comprehensive Coverage
- **All Major Destinations:** Cairo, Luxor, Aswan, Hurghada, Sharm El Sheikh, Alexandria, Dahab, Marsa Alam, El Gouna, Siwa
- **All Resource Types:** Hotels, vehicles, guides, representatives
- **All Business Processes:** Inquiries, bookings, payments, resource assignments
- **All Status Types:** Various booking and payment statuses

## Benefits

1. **Realistic Testing:** Test your application with real-world data
2. **Complete System:** All components populated with related data
3. **Professional Presentation:** Impress clients with realistic data
4. **Development Ready:** Start development immediately with comprehensive data
5. **Scalable:** Easy to add more data or modify existing data

## Next Steps

1. Run the seeders to populate your database
2. Test your application with the realistic data
3. Customize the data as needed for your specific requirements
4. Add more seeders for additional models as your system grows

The seeders are designed to work together and create a complete, realistic tourism management system with proper relationships and data integrity.

