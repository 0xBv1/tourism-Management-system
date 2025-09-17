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
