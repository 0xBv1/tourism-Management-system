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
