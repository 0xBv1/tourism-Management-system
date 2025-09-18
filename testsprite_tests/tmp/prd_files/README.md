## New Vision Project Installation Steps

This is a Laravel Application For Tourism Services.

### Installation Steps:
```bash
composer install
```
```bash
npm install && npm run dev
```
- Create .env file
- Put your database connection in env file
- if you have mail-trap credentials place it in env file
```bash
php artisan key:generate
```

```bash
php artisan app:install
```
- app:install run only once after initialization of project
    - This command runs migration, seeders, roles and permissions, storage link...etc
- After first deployment any changes made you may need to run php artisan deploy
- You Can find an Admin Account in database/seeders/AdminSeeder.php

### Notes

Every thing is a component base in this project, You should browse resources/components/dashboard folder to know what's the components created to use it

- Not Allowed to make any changes to the pre created components
- Any Module With Translation should follow [astrotomic/laravel-translatable](https://docs.astrotomic.info/laravel-translatable/installation)

### New Module Specifications
Every new Module should have:
- Model
- Dashboard controller
- Datatable
- Form Request Validation
- views
- permissions for module CRUD
    - Browse database/seeders/Permissions
- Dashboard Resource Routes

## Inquiry Management System

The Inquiry Management System is a comprehensive module for handling customer inquiries in the tourism management platform. It provides a complete workflow from inquiry creation to confirmation with automated processes.

### Features

#### Core Functionality
- **CRUD Operations**: Create, read, update, and delete inquiries
- **Status Management**: Track inquiry status (Pending, Confirmed, Cancelled, Completed)
- **Assignment System**: Assign inquiries to specific admin users
- **Client Integration**: Link inquiries to existing clients
- **Admin Notes**: Add internal notes for inquiry management

#### Workflow Features
- **Confirmation Process**: One-click inquiry confirmation
- **Automated File Generation**: Generate booking files when inquiries are confirmed
- **Notification System**: Email and database notifications for admin users
- **Event-Driven Architecture**: Uses Laravel events for workflow automation

### Database Structure

#### Inquiries Table
```sql
- id (Primary Key)
- name (Customer Name)
- email (Customer Email)
- phone (Customer Phone)
- subject (Inquiry Subject)
- message (Inquiry Message)
- status (Enum: pending, confirmed, cancelled, completed)
- admin_notes (Internal Notes)
- client_id (Foreign Key to clients table)
- assigned_to (Foreign Key to users table)
- booking_file_id (Foreign Key to booking_files table)
- confirmed_at (Timestamp)
- completed_at (Timestamp)
- created_at, updated_at, deleted_at (Timestamps)
```

#### Booking Files Table
```sql
- id (Primary Key)
- inquiry_id (Foreign Key to inquiries table)
- file_name (Generated file name)
- file_path (Storage path)
- status (Enum: generated, sent, downloaded)
- generated_at, sent_at, downloaded_at (Timestamps)
- created_at, updated_at (Timestamps)
```

### API Endpoints

#### Dashboard Routes
```
GET    /dashboard/inquiries              - List all inquiries
GET    /dashboard/inquiries/create       - Show create form
POST   /dashboard/inquiries              - Store new inquiry
GET    /dashboard/inquiries/{id}         - Show inquiry details
GET    /dashboard/inquiries/{id}/edit    - Show edit form
PUT    /dashboard/inquiries/{id}         - Update inquiry
DELETE /dashboard/inquiries/{id}         - Delete inquiry
POST   /dashboard/inquiries/{id}/confirm - Confirm inquiry
```

### Permissions

The system includes the following permissions:
- `inquiries.list` - View inquiries list
- `inquiries.create` - Create new inquiries
- `inquiries.edit` - Edit existing inquiries
- `inquiries.delete` - Delete inquiries
- `inquiries.restore` - Restore soft-deleted inquiries
- `inquiries.show` - View individual inquiry details
- `inquiries.confirm` - Confirm inquiries

### Workflow Process

1. **Inquiry Creation**
   - Admin creates inquiry or customer submits inquiry
   - Inquiry is stored with "pending" status
   - Admin can assign inquiry to specific user

2. **Inquiry Management**
   - Admin can view, edit, and manage inquiry details
   - Add admin notes for internal tracking
   - Update status and assignment

3. **Inquiry Confirmation**
   - Admin confirms inquiry (status changes to "confirmed")
   - `InquiryConfirmed` event is fired
   - `GenerateBookingFileListener` processes the event

4. **Automated Processes**
   - Booking file is automatically generated
   - File is stored in `storage/app/public/booking-files/`
   - Admin users receive email and database notifications
   - Inquiry is linked to generated booking file

5. **Completion**
   - Admin can mark inquiry as completed
   - Completion timestamp is recorded

### Event System

#### InquiryConfirmed Event
- Fired when inquiry status changes to "confirmed"
- Contains inquiry data for processing
- Triggers automated workflow

#### GenerateBookingFileListener
- Processes confirmed inquiries
- Generates booking file content
- Stores file in public storage
- Sends notifications to admin users
- Updates inquiry with booking file reference

### Notifications

#### InquiryConfirmedNotification
- Sent to all admin users when inquiry is confirmed
- Available via email and database channels
- Includes inquiry details and direct link to view

### File Structure

```
app/
├── Models/
│   ├── Inquiry.php
│   └── BookingFile.php
├── Enums/
│   └── InquiryStatus.php
├── Http/
│   ├── Controllers/Dashboard/
│   │   └── InquiryController.php
│   └── Requests/Dashboard/
│       └── InquiryRequest.php
├── DataTables/
│   └── InquiryDataTable.php
├── Events/
│   └── InquiryConfirmed.php
├── Listeners/
│   └── GenerateBookingFileListener.php
└── Notifications/Admin/
    └── InquiryConfirmedNotification.php

resources/views/dashboard/inquiries/
├── index.blade.php
├── create.blade.php
├── show.blade.php
├── edit.blade.php
└── action.blade.php

database/
├── migrations/
│   ├── create_inquiries_table.php
│   ├── create_booking_files_table.php
│   └── add_booking_file_id_to_inquiries_table.php
└── seeders/Permissions/
    └── InquiryPermissionSeeder.php
```

### Usage Examples

#### Creating an Inquiry
```php
$inquiry = Inquiry::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+1234567890',
    'subject' => 'Tour Inquiry',
    'message' => 'I would like to know about your tour packages.',
    'status' => InquiryStatus::PENDING,
    'assigned_to' => $adminUserId
]);
```

#### Confirming an Inquiry
```php
$inquiry->update([
    'status' => InquiryStatus::CONFIRMED,
    'confirmed_at' => now()
]);

event(new InquiryConfirmed($inquiry));
```

#### Querying Inquiries
```php
// Get pending inquiries
$pendingInquiries = Inquiry::pending()->get();

// Get inquiries assigned to specific user
$userInquiries = Inquiry::where('assigned_to', $userId)->get();

// Get inquiries with booking files
$inquiriesWithFiles = Inquiry::with('bookingFile')->get();
```

### Configuration

The system uses Laravel's built-in features:
- **Queue System**: For background processing of file generation and notifications
- **Storage**: Files stored in `storage/app/public/booking-files/`
- **Permissions**: Integrated with Spatie Laravel Permission package
- **Events**: Uses Laravel's event system for workflow automation

### Security

- All routes are protected by authentication middleware
- Permission-based access control for all operations
- CSRF protection on all forms
- Input validation through form request classes
- Soft deletes for data retention

### Maintenance

- Run `php artisan db:seed --class=PermissionsSeeder` to ensure permissions are up to date
- Check queue workers are running for background processing
- Monitor storage space for generated booking files
- Regular cleanup of old booking files if needed

## Booking Management System

The Booking Management System is a comprehensive module for managing booking files generated from confirmed inquiries. It provides complete booking lifecycle management with audit trails, checklist functionality, and payment tracking.

### Features

#### Core Functionality
- **Booking File Management**: View, update, and manage booking files
- **Status Tracking**: Track booking status through various stages
- **Payment Integration**: Monitor payments and remaining amounts
- **Checklist System**: Interactive task management with progress tracking
- **Audit Logging**: Complete audit trail for all booking changes
- **File Operations**: Download and send booking files

#### Advanced Features
- **Real-time Updates**: AJAX-powered checklist updates
- **Progress Tracking**: Visual progress bars for checklist completion
- **Payment Calculations**: Automatic calculation of paid and remaining amounts
- **Timeline Management**: Track file generation, sending, and download timestamps
- **Role-based Access**: Different permission levels for different user roles

### Database Structure

#### Enhanced Booking Files Table
```sql
- id (Primary Key)
- inquiry_id (Foreign Key to inquiries table)
- file_name (Generated file name)
- file_path (Storage path)
- status (String: pending, confirmed, in_progress, completed, cancelled, refunded)
- checklist (JSON: Task completion tracking)
- notes (Text: Additional notes)
- total_amount (Decimal: Total booking amount)
- currency (String: Currency code)
- generated_at, sent_at, downloaded_at (Timestamps)
- created_at, updated_at (Timestamps)
```

#### Booking Status Enum
```php
enum BookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
}
```

### API Endpoints

#### Dashboard Routes
```
GET    /dashboard/bookings                    - List all bookings
GET    /dashboard/bookings/{id}               - Show booking details
PUT    /dashboard/bookings/{id}               - Update booking
POST   /dashboard/bookings/{id}/checklist     - Update checklist item
GET    /dashboard/bookings/{id}/download      - Download booking file
POST   /dashboard/bookings/{id}/send          - Send booking file
```

### Permissions

The system includes comprehensive permissions for different user roles:

#### Administrator Permissions
- `bookings.list` - View bookings list
- `bookings.create` - Create new bookings
- `bookings.edit` - Edit existing bookings
- `bookings.delete` - Delete bookings
- `bookings.restore` - Restore soft-deleted bookings
- `bookings.show` - View individual booking details
- `bookings.update` - Update booking information
- `bookings.download` - Download booking files
- `bookings.send` - Send booking files
- `bookings.checklist` - Manage checklist items

#### Manager Permissions
- `bookings.list` - View bookings list
- `bookings.show` - View individual booking details
- `bookings.update` - Update booking information
- `bookings.download` - Download booking files
- `bookings.send` - Send booking files
- `bookings.checklist` - Manage checklist items

#### Staff Permissions
- `bookings.list` - View bookings list
- `bookings.show` - View individual booking details
- `bookings.download` - Download booking files

### Workflow Process

1. **Booking Creation**
   - Booking files are automatically generated when inquiries are confirmed
   - Initial status is set to "pending"
   - Basic checklist items are created

2. **Booking Management**
   - Admin can view and update booking details
   - Status can be changed through various stages
   - Payment information can be added and tracked

3. **Checklist Management**
   - Interactive checklist with real-time updates
   - Progress tracking with visual indicators
   - AJAX-powered updates without page refresh

4. **File Operations**
   - Download booking files with timestamp tracking
   - Send files via email (integration ready)
   - Track file access and usage

5. **Audit Trail**
   - All changes are logged with detailed information
   - Status changes, checklist updates, and payment modifications are tracked
   - Complete history available for compliance and debugging

### Event System

#### BookingFileObserver
- Automatically logs all model events (created, updated, deleted)
- Tracks specific field changes (status, checklist, payments)
- Integrates with Spatie Activity Log package
- Provides detailed audit information

### Notifications

#### Activity Logging
- All booking changes are logged in the activity log
- Detailed information about what changed and when
- User-friendly log messages for different operations
- Integration with Laravel Telescope for debugging

### File Structure

```
app/
├── Models/
│   └── BookingFile.php (Enhanced)
├── Enums/
│   └── BookingStatus.php
├── Http/
│   └── Controllers/Dashboard/
│       └── BookingController.php
├── DataTables/
│   └── BookingDataTable.php
├── Observers/
│   └── BookingFileObserver.php
└── Seeders/Permissions/
    └── BookingPermissionSeeder.php

resources/views/dashboard/bookings/
├── index.blade.php
├── show.blade.php
└── action.blade.php

database/
├── migrations/
│   ├── add_booking_fields_to_booking_files_table.php
│   └── update_booking_files_status_enum.php
└── seeders/
    ├── BookingFileSeeder.php
    └── Permissions/BookingPermissionSeeder.php
```

### Usage Examples

#### Creating a Booking File
```php
$booking = BookingFile::create([
    'inquiry_id' => $inquiry->id,
    'file_name' => 'booking_confirmation_001.pdf',
    'file_path' => '/storage/bookings/booking_confirmation_001.pdf',
    'status' => BookingStatus::PENDING,
    'total_amount' => 1500.00,
    'currency' => 'USD',
    'checklist' => [
        'accommodation_booked' => false,
        'tours_scheduled' => false,
        'transportation_arranged' => false,
        'insurance_processed' => false,
        'final_documents_sent' => false,
    ],
    'generated_at' => now()
]);
```

#### Updating Checklist
```php
$booking->updateChecklistItem('accommodation_booked', true);
// Progress is automatically calculated and updated
```

#### Querying Bookings
```php
// Get bookings by status
$pendingBookings = BookingFile::pending()->get();
$completedBookings = BookingFile::completed()->get();

// Get bookings with payments
$bookingsWithPayments = BookingFile::with('payments')->get();

// Get bookings with inquiry details
$bookingsWithInquiries = BookingFile::with('inquiry.client')->get();
```

#### Payment Tracking
```php
// Check if booking is fully paid
if ($booking->isFullyPaid()) {
    // Handle fully paid booking
}

// Get remaining amount
$remaining = $booking->remaining_amount;

// Get total paid amount
$paid = $booking->total_paid;
```

### Configuration

The system uses Laravel's built-in features:
- **Activity Logging**: Spatie Laravel Activity Log package
- **Permissions**: Spatie Laravel Permission package
- **DataTables**: Yajra DataTables for listing
- **Storage**: Files stored in `storage/app/public/bookings/`
- **AJAX**: Real-time updates for checklist management

### Security

- All routes are protected by authentication middleware
- Permission-based access control for all operations
- CSRF protection on all forms and AJAX requests
- Input validation for all user inputs
- Audit logging for compliance and security

### Maintenance

- Run `php artisan db:seed --class=Database\Seeders\Permissions\BookingPermissionSeeder` to create permissions
- Monitor activity logs for system usage
- Regular cleanup of old booking files if needed
- Check storage space for booking file uploads

## Resource Management System

The Resource Management System is a comprehensive module for managing tourism resources including hotels, vehicles, guides, and representatives. It provides complete resource lifecycle management with assignment capabilities, utilization tracking, and detailed reporting.

### Features

#### Core Functionality
- **Multi-Resource Management**: Hotels, Vehicles, Guides, and Representatives
- **Resource Assignment**: Link resources to booking files with conflict prevention
- **Utilization Tracking**: Real-time utilization percentages and performance metrics
- **Calendar Views**: Visual resource availability and booking management
- **Status Management**: Available, Occupied, Maintenance, Out of Service statuses
- **Capacity Management**: Room availability, vehicle capacity, guide scheduling

#### Advanced Features
- **Smart Assignment**: Automatic availability checking and conflict prevention
- **Dynamic Pricing**: Per night, per hour, per day pricing models
- **Performance Analytics**: Revenue tracking, booking counts, and efficiency metrics
- **Filtering & Search**: Advanced filtering by city, price, capacity, languages, and specializations
- **Maintenance Tracking**: Vehicle maintenance schedules and insurance tracking
- **Rating System**: Guide and representative rating and review system

### Database Structure

#### Hotels Table
```sql
- id (Primary Key)
- name (Hotel Name)
- description (Text Description)
- address (Full Address)
- city_id (Foreign Key to cities table)
- phone, email, website (Contact Information)
- star_rating (1-5 Star Rating)
- total_rooms, available_rooms (Room Management)
- price_per_night (Pricing)
- currency (Currency Code)
- amenities (JSON: Hotel Amenities)
- images (JSON: Hotel Images)
- status (Enum: available, occupied, maintenance, out_of_service)
- active, enabled (Boolean Flags)
- check_in_time, check_out_time (Time Slots)
- cancellation_policy (Text)
- notes (Additional Notes)
- created_at, updated_at, deleted_at (Timestamps)
```

#### Vehicles Table
```sql
- id (Primary Key)
- name (Vehicle Name)
- type, brand, model (Vehicle Details)
- year, license_plate (Registration Info)
- capacity (Passenger Capacity)
- description (Text Description)
- city_id (Foreign Key to cities table)
- driver_name, driver_phone, driver_license (Driver Info)
- price_per_hour, price_per_day (Pricing)
- currency (Currency Code)
- fuel_type, transmission (Technical Specs)
- features (JSON: Vehicle Features)
- images (JSON: Vehicle Images)
- status (Enum: available, occupied, maintenance, out_of_service)
- active, enabled (Boolean Flags)
- insurance_expiry, registration_expiry (Document Expiry)
- last_maintenance, next_maintenance (Maintenance Schedule)
- notes (Additional Notes)
- created_at, updated_at, deleted_at (Timestamps)
```

#### Guides Table
```sql
- id (Primary Key)
- name, email, phone (Contact Information)
- nationality (Nationality)
- languages (JSON: Spoken Languages)
- specializations (JSON: Guide Specializations)
- experience_years (Years of Experience)
- city_id (Foreign Key to cities table)
- price_per_hour, price_per_day (Pricing)
- currency (Currency Code)
- bio (Biography)
- certifications (JSON: Professional Certifications)
- profile_image (Profile Photo)
- status (Enum: available, occupied, maintenance, out_of_service)
- active, enabled (Boolean Flags)
- rating, total_ratings (Rating System)
- availability_schedule (JSON: Weekly Availability)
- emergency_contact, emergency_phone (Emergency Contacts)
- notes (Additional Notes)
- created_at, updated_at, deleted_at (Timestamps)
```

#### Representatives Table
```sql
- id (Primary Key)
- name, email, phone (Contact Information)
- nationality (Nationality)
- languages (JSON: Spoken Languages)
- specializations (JSON: Service Specializations)
- experience_years (Years of Experience)
- city_id (Foreign Key to cities table)
- price_per_hour, price_per_day (Pricing)
- currency (Currency Code)
- bio (Biography)
- certifications (JSON: Professional Certifications)
- profile_image (Profile Photo)
- status (Enum: available, occupied, maintenance, out_of_service)
- active, enabled (Boolean Flags)
- rating, total_ratings (Rating System)
- availability_schedule (JSON: Weekly Availability)
- emergency_contact, emergency_phone (Emergency Contacts)
- company_name, company_license (Company Information)
- service_areas (JSON: Service Coverage Areas)
- notes (Additional Notes)
- created_at, updated_at, deleted_at (Timestamps)
```

#### Resource Bookings Table
```sql
- id (Primary Key)
- booking_file_id (Foreign Key to booking_files table)
- resource_type (String: hotel, vehicle, guide, representative)
- resource_id (Polymorphic Resource ID)
- start_date, end_date (Booking Period)
- start_time, end_time (Time Slots)
- quantity (Booking Quantity)
- unit_price, total_price (Pricing)
- currency (Currency Code)
- status (Enum: available, occupied, maintenance, out_of_service)
- special_requirements (JSON: Special Requirements)
- notes (Additional Notes)
- created_at, updated_at (Timestamps)
```

### API Endpoints

#### Resource Management Routes
```
# Hotels
GET    /dashboard/hotels                    - List all hotels
GET    /dashboard/hotels/create             - Show create form
POST   /dashboard/hotels                    - Store new hotel
GET    /dashboard/hotels/{id}               - Show hotel details
GET    /dashboard/hotels/{id}/edit          - Show edit form
PUT    /dashboard/hotels/{id}               - Update hotel
DELETE /dashboard/hotels/{id}               - Delete hotel
GET    /dashboard/hotels/calendar           - Hotel calendar view

# Vehicles
GET    /dashboard/vehicles                  - List all vehicles
GET    /dashboard/vehicles/create           - Show create form
POST   /dashboard/vehicles                  - Store new vehicle
GET    /dashboard/vehicles/{id}             - Show vehicle details
GET    /dashboard/vehicles/{id}/edit        - Show edit form
PUT    /dashboard/vehicles/{id}             - Update vehicle
DELETE /dashboard/vehicles/{id}             - Delete vehicle
GET    /dashboard/vehicles/calendar         - Vehicle calendar view

# Guides
GET    /dashboard/guides                    - List all guides
GET    /dashboard/guides/create             - Show create form
POST   /dashboard/guides                    - Store new guide
GET    /dashboard/guides/{id}               - Show guide details
GET    /dashboard/guides/{id}/edit          - Show edit form
PUT    /dashboard/guides/{id}               - Update guide
DELETE /dashboard/guides/{id}               - Delete guide
GET    /dashboard/guides/calendar           - Guide calendar view

# Representatives
GET    /dashboard/representatives           - List all representatives
GET    /dashboard/representatives/create    - Show create form
POST   /dashboard/representatives           - Store new representative
GET    /dashboard/representatives/{id}      - Show representative details
GET    /dashboard/representatives/{id}/edit - Show edit form
PUT    /dashboard/representatives/{id}      - Update representative
DELETE /dashboard/representatives/{id}      - Delete representative
GET    /dashboard/representatives/calendar  - Representative calendar view
```

#### Resource Assignment Routes
```
POST   /dashboard/resource-assignments/{bookingFile}     - Assign resource to booking
DELETE /dashboard/resource-assignments/{resourceBooking} - Remove resource assignment
POST   /dashboard/resources/available                    - Get available resources
POST   /dashboard/resources/check-availability           - Check resource availability
GET    /dashboard/resources/utilization                  - Get utilization data
```

#### Reporting Routes
```
GET    /dashboard/reports/resource-utilization           - Utilization reports
GET    /dashboard/reports/resource-details/{type}/{id}   - Resource details report
GET    /dashboard/reports/resource-utilization/export    - Export utilization data
```

### Permissions

The system includes comprehensive permissions for different user roles:

#### Hotel Permissions
- `hotels.list` - View hotels list
- `hotels.create` - Create new hotels
- `hotels.edit` - Edit existing hotels
- `hotels.delete` - Delete hotels
- `hotels.restore` - Restore soft-deleted hotels
- `hotels.show` - View individual hotel details
- `hotels.calendar` - Access hotel calendar view
- `hotels.assign` - Assign hotels to bookings
- `hotels.unassign` - Remove hotel assignments
- `hotels.utilization` - View utilization reports

#### Vehicle Permissions
- `vehicles.list` - View vehicles list
- `vehicles.create` - Create new vehicles
- `vehicles.edit` - Edit existing vehicles
- `vehicles.delete` - Delete vehicles
- `vehicles.restore` - Restore soft-deleted vehicles
- `vehicles.show` - View individual vehicle details
- `vehicles.calendar` - Access vehicle calendar view
- `vehicles.assign` - Assign vehicles to bookings
- `vehicles.unassign` - Remove vehicle assignments
- `vehicles.utilization` - View utilization reports
- `vehicles.maintenance` - Manage vehicle maintenance

#### Guide Permissions
- `guides.list` - View guides list
- `guides.create` - Create new guides
- `guides.edit` - Edit existing guides
- `guides.delete` - Delete guides
- `guides.restore` - Restore soft-deleted guides
- `guides.show` - View individual guide details
- `guides.calendar` - Access guide calendar view
- `guides.assign` - Assign guides to bookings
- `guides.unassign` - Remove guide assignments
- `guides.utilization` - View utilization reports
- `guides.rating` - Manage guide ratings

#### Representative Permissions
- `representatives.list` - View representatives list
- `representatives.create` - Create new representatives
- `representatives.edit` - Edit existing representatives
- `representatives.delete` - Delete representatives
- `representatives.restore` - Restore soft-deleted representatives
- `representatives.show` - View individual representative details
- `representatives.calendar` - Access representative calendar view
- `representatives.assign` - Assign representatives to bookings
- `representatives.unassign` - Remove representative assignments
- `representatives.utilization` - View utilization reports
- `representatives.rating` - Manage representative ratings

#### Resource Assignment Permissions
- `resource-assignments.create` - Create resource assignments
- `resource-assignments.store` - Store resource assignments
- `resource-assignments.destroy` - Remove resource assignments
- `resource-assignments.available` - View available resources
- `resource-assignments.check-availability` - Check resource availability
- `resource-assignments.utilization` - View utilization data
- `resource-assignments.export` - Export assignment data

#### Resource Report Permissions
- `resource-reports.index` - View resource reports
- `resource-reports.show` - View individual resource reports
- `resource-reports.export` - Export report data
- `resource-reports.utilization` - View utilization reports
- `resource-reports.details` - View detailed resource reports

### Service Layer

#### ResourceAssignmentService
The core service for managing resource assignments and availability:

```php
// Assign a resource to a booking
$resourceBooking = $service->assignResource(
    $bookingFile,
    'hotel',
    $hotelId,
    $startDate,
    $endDate,
    $startTime,
    $endTime,
    $quantity,
    $unitPrice,
    $currency,
    $specialRequirements,
    $notes
);

// Check resource availability
$isAvailable = $service->checkAvailability(
    'hotel',
    $hotelId,
    $startDate,
    $endDate,
    $startTime,
    $endTime
);

// Get available resources
$availableHotels = $service->getAvailableResources(
    'hotel',
    $startDate,
    $endDate,
    $startTime,
    $endTime,
    $cityId,
    $filters
);

// Get utilization report
$utilization = $service->getResourceUtilization(
    'hotel',
    $hotelId,
    $startDate,
    $endDate
);
```

### Workflow Process

1. **Resource Creation**
   - Admin creates resources (hotels, vehicles, guides, representatives)
   - Resources are set with initial status and availability
   - Pricing and capacity information is configured

2. **Resource Management**
   - Admin can view, edit, and manage resource details
   - Status can be updated (available, occupied, maintenance, out_of_service)
   - Capacity and availability are tracked in real-time

3. **Resource Assignment**
   - Resources are assigned to booking files through the assignment interface
   - System automatically checks for availability and conflicts
   - Pricing is calculated based on duration and resource type

4. **Utilization Tracking**
   - Real-time utilization percentages are calculated
   - Performance metrics are tracked and displayed
   - Revenue and booking statistics are maintained

5. **Reporting & Analytics**
   - Comprehensive utilization reports for all resource types
   - Visual dashboards with progress indicators
   - Export functionality for further analysis

### File Structure

```
app/
├── Models/
│   ├── Hotel.php
│   ├── Vehicle.php
│   ├── Guide.php
│   ├── Representative.php
│   └── ResourceBooking.php
├── Enums/
│   └── ResourceStatus.php
├── Http/
│   ├── Controllers/Dashboard/
│   │   ├── HotelController.php
│   │   ├── VehicleController.php
│   │   ├── GuideController.php
│   │   ├── RepresentativeController.php
│   │   ├── ResourceAssignmentController.php
│   │   └── ResourceReportController.php
│   └── Requests/Dashboard/
│       ├── HotelRequest.php
│       ├── VehicleRequest.php
│       ├── GuideRequest.php
│       ├── RepresentativeRequest.php
│       └── ResourceAssignmentRequest.php
├── Services/
│   └── ResourceAssignmentService.php
├── DataTables/
│   ├── HotelDataTable.php
│   ├── VehicleDataTable.php
│   ├── GuideDataTable.php
│   └── RepresentativeDataTable.php
└── Seeders/Permissions/
    ├── HotelPermissionSeeder.php
    ├── VehiclePermissionSeeder.php
    ├── GuidePermissionSeeder.php
    ├── RepresentativePermissionSeeder.php
    ├── ResourceAssignmentPermissionSeeder.php
    └── ResourceReportPermissionSeeder.php

resources/views/dashboard/
├── hotels/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── show.blade.php
│   ├── calendar.blade.php
│   └── action.blade.php
├── vehicles/
│   ├── index.blade.php
│   ├── action.blade.php
│   └── calendar.blade.php
├── guides/
│   ├── index.blade.php
│   ├── action.blade.php
│   └── calendar.blade.php
├── representatives/
│   ├── index.blade.php
│   ├── action.blade.php
│   └── calendar.blade.php
├── bookings/
│   └── assign-resources.blade.php
└── reports/
    ├── resource-utilization.blade.php
    └── resource-details.blade.php

database/
├── migrations/
│   ├── create_hotels_table.php
│   ├── create_vehicles_table.php
│   ├── create_guides_table.php
│   ├── create_representatives_table.php
│   └── create_resource_bookings_table.php
└── seeders/Permissions/
    ├── HotelPermissionSeeder.php
    ├── VehiclePermissionSeeder.php
    ├── GuidePermissionSeeder.php
    ├── RepresentativePermissionSeeder.php
    ├── ResourceAssignmentPermissionSeeder.php
    └── ResourceReportPermissionSeeder.php
```

### Usage Examples

#### Creating a Hotel
```php
$hotel = Hotel::create([
    'name' => 'Grand Hotel',
    'description' => 'Luxury hotel in city center',
    'address' => '123 Main Street, City',
    'city_id' => 1,
    'star_rating' => 5,
    'total_rooms' => 100,
    'available_rooms' => 100,
    'price_per_night' => 200.00,
    'currency' => 'USD',
    'amenities' => ['wifi', 'pool', 'spa', 'restaurant'],
    'status' => ResourceStatus::AVAILABLE,
    'active' => true,
    'enabled' => true
]);
```

#### Assigning a Resource
```php
$resourceBooking = ResourceBooking::create([
    'booking_file_id' => $bookingFile->id,
    'resource_type' => 'hotel',
    'resource_id' => $hotel->id,
    'start_date' => '2024-01-15',
    'end_date' => '2024-01-20',
    'quantity' => 2,
    'unit_price' => 200.00,
    'total_price' => 2000.00,
    'currency' => 'USD',
    'status' => ResourceStatus::OCCUPIED
]);
```

#### Querying Resources
```php
// Get available hotels in a city
$availableHotels = Hotel::available()
    ->byCity($cityId)
    ->byStarRating(4)
    ->get();

// Get hotel utilization
$utilization = $hotel->utilization_percentage;

// Get resource bookings for a period
$bookings = ResourceBooking::byDateRange($startDate, $endDate)
    ->byResourceType('hotel')
    ->get();
```

### Configuration

The system uses Laravel's built-in features:
- **Polymorphic Relationships**: Flexible resource booking system
- **JSON Columns**: Flexible storage for amenities, features, and schedules
- **Soft Deletes**: Data preservation with soft deletion
- **Activity Logging**: Spatie Laravel Activity Log package
- **Permissions**: Spatie Laravel Permission package
- **DataTables**: Yajra DataTables for listing
- **FullCalendar**: Interactive calendar views

### Security

- All routes are protected by authentication middleware
- Permission-based access control for all operations
- CSRF protection on all forms and AJAX requests
- Input validation for all user inputs
- Resource conflict prevention and validation
- Audit logging for compliance and security

### Maintenance

- Run `php artisan db:seed --class=Database\Seeders\PermissionsSeeder` to create all permissions
- Monitor resource utilization reports for optimization
- Regular maintenance scheduling for vehicles
- Check resource availability and capacity
- Update resource pricing and information as needed

---

## 📑 Epic 4: Reservation & Operation

The Reservation & Operation module provides comprehensive resource management and booking operations for the tourism management platform. It handles hotels, vehicles, guides, and representatives with advanced scheduling and utilization tracking.

### Features

#### Resource Management
- **Hotel Management**: Complete hotel CRUD with amenities, pricing, and availability
- **Vehicle Management**: Fleet management with capacity and feature tracking
- **Guide Management**: Guide profiles with specializations and availability
- **Representative Management**: Local representative management and assignment

#### Booking Operations
- **Resource Assignment**: Assign resources to booking files
- **Availability Checking**: Real-time availability validation
- **Conflict Prevention**: Automatic conflict detection and resolution
- **Utilization Tracking**: Monitor resource usage and performance

#### Calendar Integration
- **Interactive Calendars**: FullCalendar integration for each resource type
- **Visual Scheduling**: Drag-and-drop booking management
- **Availability Views**: Clear visualization of resource availability
- **Booking Timeline**: Track bookings across time periods

### Database Structure

#### Hotels Table
```sql
- id (Primary Key)
- name (Hotel Name)
- city_id (Foreign Key to cities table)
- address (Hotel Address)
- star_rating (1-5 stars)
- total_rooms (Total room count)
- available_rooms (Available room count)
- price_per_night (Base price)
- currency (Currency code)
- amenities (JSON array)
- features (JSON array)
- status (Enum: available, occupied, maintenance)
- active (Boolean)
- enabled (Boolean)
- created_at, updated_at, deleted_at (Timestamps)
```

#### Vehicles Table
```sql
- id (Primary Key)
- name (Vehicle Name)
- type (Vehicle Type)
- capacity (Passenger capacity)
- features (JSON array)
- price_per_day (Daily rate)
- currency (Currency code)
- status (Enum: available, occupied, maintenance)
- active (Boolean)
- enabled (Boolean)
- created_at, updated_at, deleted_at (Timestamps)
```

#### Resource Bookings Table
```sql
- id (Primary Key)
- booking_file_id (Foreign Key to booking_files table)
- resource_type (Polymorphic type: hotel, vehicle, guide, representative)
- resource_id (Polymorphic ID)
- start_date (Booking start date)
- end_date (Booking end date)
- quantity (Number of units)
- unit_price (Price per unit)
- total_price (Total booking price)
- currency (Currency code)
- status (Enum: available, occupied, cancelled)
- created_at, updated_at (Timestamps)
```

### API Endpoints

#### Resource Management Routes
```
GET    /dashboard/hotels              - List all hotels
POST   /dashboard/hotels              - Create new hotel
GET    /dashboard/hotels/{id}         - Show hotel details
PUT    /dashboard/hotels/{id}         - Update hotel
DELETE /dashboard/hotels/{id}         - Delete hotel
GET    /dashboard/hotels/{id}/calendar - Hotel calendar view

GET    /dashboard/vehicles            - List all vehicles
POST   /dashboard/vehicles            - Create new vehicle
GET    /dashboard/vehicles/{id}       - Show vehicle details
PUT    /dashboard/vehicles/{id}       - Update vehicle
DELETE /dashboard/vehicles/{id}       - Delete vehicle
GET    /dashboard/vehicles/{id}/calendar - Vehicle calendar view

GET    /dashboard/guides              - List all guides
POST   /dashboard/guides              - Create new guide
GET    /dashboard/guides/{id}         - Show guide details
PUT    /dashboard/guides/{id}         - Update guide
DELETE /dashboard/guides/{id}         - Delete guide
GET    /dashboard/guides/{id}/calendar - Guide calendar view

GET    /dashboard/representatives     - List all representatives
POST   /dashboard/representatives     - Create new representative
GET    /dashboard/representatives/{id} - Show representative details
PUT    /dashboard/representatives/{id} - Update representative
DELETE /dashboard/representatives/{id} - Delete representative
GET    /dashboard/representatives/{id}/calendar - Representative calendar view
```

#### Resource Assignment Routes
```
POST   /dashboard/resource-assignments/available - Get available resources
POST   /dashboard/resource-assignments/check-availability - Check availability
GET    /dashboard/resource-assignments/utilization - Get utilization report
```

### Permissions

The system includes comprehensive permissions for each resource type:
- `hotels.list`, `hotels.create`, `hotels.edit`, `hotels.delete`, `hotels.show`, `hotels.calendar`
- `vehicles.list`, `vehicles.create`, `vehicles.edit`, `vehicles.delete`, `vehicles.show`, `vehicles.calendar`
- `guides.list`, `guides.create`, `guides.edit`, `guides.delete`, `guides.show`, `guides.calendar`
- `representatives.list`, `representatives.create`, `representatives.edit`, `representatives.delete`, `representatives.show`, `representatives.calendar`
- `resource-assignments.available`, `resource-assignments.check-availability`, `resource-assignments.utilization`

---

## 💵 Epic 5: Finance Module

The Finance Module provides comprehensive financial management capabilities including payment processing, financial reporting, and automated workflows for the tourism management platform.

### Features

#### Payment Management
- **Payment Processing**: Complete payment lifecycle management
- **Multiple Gateways**: Support for various payment methods
- **Payment Tracking**: Real-time payment status monitoring
- **Receipt Generation**: Automated payment receipts

#### Financial Reporting
- **Payment Statements**: Detailed payment reports with filtering
- **Aging Buckets**: Overdue payment analysis and tracking
- **Revenue Analytics**: Comprehensive revenue reporting
- **Export Functionality**: Excel/CSV export capabilities

#### Workflow Automation
- **Payment Events**: Automated event-driven payment processing
- **Status Updates**: Automatic booking status updates on payment
- **Reminder System**: Automated payment reminder notifications
- **Audit Logging**: Complete payment audit trail

### Database Structure

#### Payments Table
```sql
- id (Primary Key)
- invoice_id (Invoice reference)
- booking_id (Foreign Key to booking_files table)
- gateway (Payment gateway)
- amount (Payment amount)
- status (Enum: paid, not_paid, pending)
- paid_at (Payment completion timestamp)
- transaction_request (JSON: Gateway request data)
- transaction_verification (JSON: Gateway response data)
- reference_number (Payment reference)
- notes (Payment notes)
- created_at, updated_at (Timestamps)
```

### API Endpoints

#### Payment Management Routes
```
GET    /dashboard/payments              - List all payments
POST   /dashboard/payments              - Create new payment
GET    /dashboard/payments/create       - Show create form
GET    /dashboard/payments/{id}         - Show payment details
GET    /dashboard/payments/{id}/edit    - Show edit form
PUT    /dashboard/payments/{id}         - Update payment
DELETE /dashboard/payments/{id}         - Delete payment
POST   /dashboard/payments/{id}/mark-as-paid - Mark payment as paid
GET    /dashboard/payments/statements   - Payment statements
GET    /dashboard/payments/aging-buckets - Aging buckets report
```

### Permissions

The system includes comprehensive payment permissions:
- `payments.list` - View payments list
- `payments.create` - Create new payments
- `payments.edit` - Edit existing payments
- `payments.delete` - Delete payments
- `payments.show` - View payment details
- `payments.mark-as-paid` - Mark payments as paid
- `payments.statements` - Access payment statements
- `payments.aging-buckets` - Access aging buckets report

### Workflow Process

1. **Payment Creation**
   - Payment is created with booking reference
   - Status is set to "pending" by default
   - Payment details are stored with gateway information

2. **Payment Processing**
   - Payment is processed through selected gateway
   - Transaction data is stored for audit purposes
   - Status is updated based on gateway response

3. **Payment Confirmation**
   - Payment status changes to "paid"
   - `PaymentReceived` event is fired
   - Booking status is automatically updated
   - Receipt is generated and sent to client

4. **Automated Workflows**
   - Overdue payment detection and notifications
   - Payment reminder scheduling
   - Financial reporting and analytics

---

## 🔑 Epic 6: Admin & Reports

The Admin & Reports module provides comprehensive user management and advanced reporting capabilities for the tourism management platform.

### Features

#### User Management
- **User CRUD**: Complete user lifecycle management
- **Role Assignment**: Flexible role and permission assignment
- **Permission Management**: Granular permission control
- **User Profiles**: Detailed user profile management

#### Advanced Reporting
- **Inquiries Report**: Lead analysis and conversion tracking
- **Bookings Report**: Booking patterns and revenue analysis
- **Finance Report**: Payment tracking and financial performance
- **Operational Report**: Resource utilization and staff performance
- **Performance Report**: KPIs and business metrics
- **Resource Utilization**: Hotel, vehicle, guide, and representative usage

#### Dashboard Analytics
- **Real-time Statistics**: Live dashboard with key metrics
- **Interactive Charts**: Visual data representation
- **Export Capabilities**: CSV/Excel export for all reports
- **Date Range Filtering**: Flexible reporting periods

### Database Structure

#### Users Table
```sql
- id (Primary Key)
- name (User Name)
- email (Email Address)
- email_verified_at (Email verification timestamp)
- password (Hashed password)
- remember_token (Remember me token)
- created_at, updated_at (Timestamps)
```

#### Roles Table
```sql
- id (Primary Key)
- name (Role Name)
- guard_name (Guard name)
- created_at, updated_at (Timestamps)
```

#### Permissions Table
```sql
- id (Primary Key)
- name (Permission Name)
- guard_name (Guard name)
- created_at, updated_at (Timestamps)
```

### API Endpoints

#### User Management Routes
```
GET    /dashboard/users              - List all users
POST   /dashboard/users              - Create new user
GET    /dashboard/users/create       - Show create form
GET    /dashboard/users/{id}         - Show user details
GET    /dashboard/users/{id}/edit    - Show edit form
PUT    /dashboard/users/{id}         - Update user
DELETE /dashboard/users/{id}         - Delete user
```

#### Role Management Routes
```
GET    /dashboard/roles              - List all roles
POST   /dashboard/roles              - Create new role
GET    /dashboard/roles/create       - Show create form
GET    /dashboard/roles/{id}         - Show role details
GET    /dashboard/roles/{id}/edit    - Show edit form
PUT    /dashboard/roles/{id}         - Update role
DELETE /dashboard/roles/{id}         - Delete role
```

#### Reports Routes
```
GET    /dashboard/reports            - Reports dashboard
GET    /dashboard/reports/inquiries  - Inquiries report
GET    /dashboard/reports/bookings   - Bookings report
GET    /dashboard/reports/finance    - Finance report
GET    /dashboard/reports/operational - Operational report
GET    /dashboard/reports/performance - Performance report
GET    /dashboard/reports/export/{type} - Export reports
```

### Permissions

The system includes comprehensive reporting permissions:
- `reports.index` - Access reports dashboard
- `reports.inquiries` - Access inquiries report
- `reports.bookings` - Access bookings report
- `reports.finance` - Access finance report
- `reports.operational` - Access operational report
- `reports.performance` - Access performance report
- `reports.export` - Export report data

### Report Types

#### Inquiries Report
- Lead analysis and conversion rates
- Status breakdown and trends
- Monthly performance tracking
- Client inquiry patterns

#### Bookings Report
- Booking patterns and revenue analysis
- Status tracking and completion rates
- Average booking values
- Outstanding payment tracking

#### Finance Report
- Payment status analysis
- Gateway distribution
- Revenue trends and forecasting
- Financial performance metrics

#### Operational Report
- Resource utilization analysis
- Staff performance tracking
- Resource booking details
- Efficiency metrics

#### Performance Report
- KPI calculations and monitoring
- Conversion funnel analysis
- Top performer identification
- Trend analysis and forecasting

---

## 🌐 Epic 7: Communication & Notifications

The Communication & Notifications module provides comprehensive multi-channel communication capabilities including email, WhatsApp, SMS, and in-app notifications for the tourism management platform.

### Features

#### Email System
- **Professional Templates**: Responsive HTML email templates
- **Booking Confirmations**: Automated booking confirmation emails
- **Payment Receipts**: Detailed payment receipt emails
- **Monthly Statements**: Client financial summary emails
- **Template Management**: Reusable email template system

#### Multi-Channel Notifications
- **Email Notifications**: Detailed formatted messages
- **WhatsApp Integration**: Business API for instant messaging
- **SMS Notifications**: Twilio integration for text alerts
- **Database Notifications**: In-app notification center
- **Smart Routing**: Automatic channel selection based on availability

#### Notification Management
- **Event-Driven**: Automatic notifications based on system events
- **Queue Support**: Background processing for better performance
- **Template System**: Consistent messaging across channels
- **Delivery Tracking**: Notification delivery status monitoring

### Database Structure

#### Notifications Table
```sql
- id (Primary Key)
- type (Notification type)
- notifiable_type (Notifiable model type)
- notifiable_id (Notifiable model ID)
- data (JSON: Notification data)
- read_at (Read timestamp)
- created_at, updated_at (Timestamps)
```

### API Endpoints

#### Notification Routes
```
GET    /notifications              - List user notifications
POST   /notifications/{id}/mark-as-read - Mark notification as read
DELETE /notifications/{id}         - Delete notification
```

### Configuration

#### Email Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### WhatsApp Configuration
```env
WHATSAPP_API_URL=https://graph.facebook.com
WHATSAPP_TOKEN=your-whatsapp-token
WHATSAPP_PHONE_NUMBER_ID=your-phone-number-id
```

#### SMS Configuration
```env
TWILIO_SID=your-twilio-sid
TWILIO_TOKEN=your-twilio-token
TWILIO_FROM=+1234567890
```

### Notification Types

#### BookingConfirmedNotification
- Sent when booking is confirmed
- Available via email, WhatsApp, and database
- Includes booking details and confirmation information

#### PaymentOverdueNotification
- Sent for overdue payments
- Available via email, WhatsApp, SMS, and database
- Includes payment details and overdue information

### Usage Examples

#### Sending Notifications
```php
// Booking confirmation
$client->notify(new BookingConfirmedNotification($bookingFile));

// Payment overdue
$client->notify(new PaymentOverdueNotification($payment, 15));
```

#### Sending Emails
```php
// Booking confirmation email
Mail::to($client->email)->send(new BookingConfirmationMail($bookingFile));

// Payment receipt email
Mail::to($client->email)->send(new PaymentReceiptMail($payment));
```

---

## 🧩 Epic 8: Core Infrastructure

The Core Infrastructure module provides essential building blocks and services that power the entire tourism management platform.

### Features

#### Service Layer
- **BookingService**: Complete booking lifecycle management
- **FinanceService**: Payment and financial operations
- **ReportService**: Analytics and reporting capabilities
- **Dependency Injection**: Proper service container binding

#### Traits System
- **HasAuditLog**: Automatic activity logging for models
- **HasStatuses**: Status management with scopes and helpers
- **CurrencyConversion**: Multi-currency support with live rates

#### DataTables Integration
- **Sales Inquiries DataTable**: Lead management and conversion tracking
- **Booking Files DataTable**: Complete booking lifecycle management
- **Payments DataTable**: Financial transaction management
- **Export Functionality**: Excel, CSV, PDF export options

### Service Layer

#### BookingService
```php
// Create booking from inquiry
$booking = app(BookingService::class)->createBookingFromInquiry($inquiry, $data);

// Update booking status
app(BookingService::class)->updateBookingStatus($booking, BookingStatus::CONFIRMED);

// Get booking statistics
$stats = app(BookingService::class)->getBookingStatistics($startDate, $endDate);
```

#### FinanceService
```php
// Process payment
app(FinanceService::class)->processPayment($payment, $data);

// Send overdue notifications
$sentCount = app(FinanceService::class)->sendOverduePaymentNotifications();

// Generate monthly statement
$statement = app(FinanceService::class)->generateMonthlyStatement($client, $startDate, $endDate);
```

#### ReportService
```php
// Get dashboard data
$dashboardData = app(ReportService::class)->getDashboardData();

// Get monthly performance
$performance = app(ReportService::class)->getMonthlyPerformanceData(12);

// Generate export data
$exportData = app(ReportService::class)->generateExportData('inquiries', $startDate, $endDate);
```

### Traits Usage

#### HasAuditLog Trait
```php
use App\Traits\HasAuditLog;

class YourModel extends Model
{
    use HasAuditLog;
    
    // Automatic logging for create, update, delete operations
}
```

#### HasStatuses Trait
```php
use App\Traits\HasStatuses;

class YourModel extends Model
{
    use HasStatuses;
    
    protected $statusField = 'status';
    protected $statusEnum = 'App\Enums\YourStatusEnum';
    
    // Use provided methods
    $model->isActive();
    $model->isPending();
    $model->changeStatus('active');
}
```

#### CurrencyConversion Trait
```php
use App\Traits\CurrencyConversion;

class YourModel extends Model
{
    use CurrencyConversion;
    
    // Convert currency
    $convertedAmount = $this->convertCurrency(100, 'USD', 'EUR');
    
    // Format currency
    $formatted = $this->formatCurrency(100, 'USD');
    
    // Get exchange rate
    $rate = $this->getExchangeRate('USD', 'EUR');
}
```

### DataTables

#### Sales Inquiries DataTable
- Enhanced inquiry listing with conversion tracking
- Client information and estimated values
- Status indicators and action buttons
- Export functionality (Excel, CSV, PDF)

#### Booking Files DataTable
- Complete booking file management
- Payment status tracking
- Progress indicators and client information
- Comprehensive filtering and sorting

#### Payments DataTable
- Enhanced payment listing with multi-currency support
- Status tracking and client information
- Gateway information and transaction details
- Export and reporting capabilities

### Configuration

The system uses Laravel's built-in features:
- **Service Container**: Dependency injection for services
- **Event System**: Event-driven architecture
- **Queue System**: Background job processing
- **Cache System**: Performance optimization
- **Activity Logging**: Spatie Laravel Activity Log package
- **Permissions**: Spatie Laravel Permission package
- **DataTables**: Yajra DataTables for listing
- **Currency API**: Live exchange rate integration

### Security

- All services are properly bound in the service container
- Traits provide consistent functionality across models
- DataTables include proper authorization checks
- Export functionality includes data sanitization
- Audit logging provides complete activity tracking

### Maintenance

- Services are automatically resolved by Laravel's container
- Traits can be easily added to new models
- DataTables are automatically updated with new data
- Currency rates are cached for performance
- Regular maintenance of service dependencies
