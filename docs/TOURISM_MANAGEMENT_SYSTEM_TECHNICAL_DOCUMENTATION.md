# Tourism Management System - Technical Documentation

## System Overview

The Tourism Management System is a comprehensive web application built on Laravel 10, designed to manage tourism and travel operations. The system provides complete management of inquiries, bookings, tourism resources, and payments with an advanced notification system.

## Core Features

### 1. Inquiry Management System
- **CRUD Operations**: Create, read, update, and delete inquiries
- **Status Management**: Track inquiry status (Pending, Confirmed, Cancelled)
- **Assignment System**: Assign inquiries to specific admin users with different roles
- **Client Integration**: Link inquiries to existing clients
- **Resource Management**: Associate tourism resources with inquiries
- **Internal Chat System**: Team communication through internal chat

### 2. Booking Management System
- **Booking File Generation**: Automatic PDF generation for confirmed bookings
- **Progress Tracking**: Interactive checklist system for booking progress
- **Status Management**: Complete booking lifecycle management
- **File Operations**: Download and send booking files
- **Payment Integration**: Monitor payments and remaining amounts

### 3. Resource Management System
- **Hotels**: Complete hotel management with amenities, pricing, and availability
- **Vehicles**: Fleet management with capacity and feature tracking
- **Guides**: Guide profiles with specializations and availability
- **Representatives**: Local representative management and assignment
- **Tickets**: Tourist attraction ticket management
- **Nile Cruises**: Dahabia and Nile cruise management
- **Restaurants**: Restaurant and meal management

### 4. Payment Management System
- **Payment Processing**: Complete payment lifecycle management
- **Multiple Gateways**: Support for various payment methods (PayPal, Fawaterk, Bank Transfer)
- **Payment Tracking**: Real-time payment status monitoring
- **Financial Reporting**: Detailed payment reports with filtering
- **Automated Workflows**: Event-driven payment processing

### 5. Notification System
- **Email Notifications**: Professional HTML email templates
- **Database Notifications**: In-app notification center
- **WhatsApp Integration**: Business API for instant messaging
- **SMS Notifications**: Twilio integration for text alerts
- **Smart Routing**: Automatic channel selection based on availability

### 6. User & Role Management
- **User CRUD**: Complete user lifecycle management
- **Role Assignment**: Flexible role and permission assignment
- **Permission Management**: Granular permission control
- **Client Management**: Customer account management

## Technical Architecture

### Technology Stack
- **Laravel 10**: Core framework
- **PHP 8.1+**: Programming language
- **MySQL**: Database management system
- **Tailwind CSS**: Styling framework
- **Alpine.js**: Frontend interactivity
- **Vite**: Build tools

### Key Libraries and Packages
- **Spatie Laravel Permission**: Permission management
- **Spatie Laravel Activity Log**: Activity logging
- **Yajra DataTables**: Interactive data tables
- **Barryvdh DomPDF**: PDF generation
- **Laravel Passport**: API authentication
- **Twilio SDK**: SMS messaging
- **Google Translate**: Auto-translation

## Database Schema

### Core Tables

#### 1. Inquiries Table
```sql
- id (Primary Key)
- inquiry_id (Unique inquiry identifier)
- guest_name (Guest name)
- email (Email address)
- phone (Phone number)
- arrival_date (Arrival date)
- departure_date (Departure date)
- number_pax (Number of passengers)
- tour_name (Tour name)
- nationality (Nationality)
- subject (Inquiry subject)
- tour_itinerary (Tour itinerary)
- status (Status: pending, confirmed, cancelled)
- client_id (Client reference)
- assigned_to (Assigned user)
- assigned_reservation_id (Reservation staff)
- assigned_operator_id (Operations staff)
- assigned_admin_id (Admin staff)
- booking_file_id (Booking file reference)
- total_amount (Total amount)
- paid_amount (Paid amount)
- remaining_amount (Remaining amount)
- payment_method (Payment method)
- confirmed_at (Confirmation timestamp)
- completed_at (Completion timestamp)
- created_at, updated_at, deleted_at
```

#### 2. Booking Files Table
```sql
- id (Primary Key)
- inquiry_id (Inquiry reference)
- file_name (File name)
- file_path (File path)
- status (Status: pending, confirmed, in_progress, completed, cancelled, refunded)
- generated_at (Generation timestamp)
- sent_at (Sent timestamp)
- downloaded_at (Download timestamp)
- checklist (Task checklist - JSON)
- notes (Notes)
- total_amount (Total amount)
- currency (Currency)
- created_at, updated_at
```

#### 3. Hotels Table
```sql
- id (Primary Key)
- name (Hotel name)
- description (Description)
- address (Address)
- city_id (City reference)
- phone (Phone)
- email (Email)
- website (Website)
- star_rating (Star rating)
- total_rooms (Total rooms)
- available_rooms (Available rooms)
- price_per_night (Price per night)
- currency (Currency)
- amenities (Amenities - JSON)
- images (Images - JSON)
- status (Status: available, occupied, maintenance, out_of_service)
- active (Active flag)
- enabled (Enabled flag)
- check_in_time (Check-in time)
- check_out_time (Check-out time)
- cancellation_policy (Cancellation policy)
- notes (Notes)
- created_at, updated_at, deleted_at
```

#### 4. Vehicles Table
```sql
- id (Primary Key)
- name (Vehicle name)
- type (Vehicle type)
- brand (Brand)
- model (Model)
- year (Year)
- license_plate (License plate)
- capacity (Passenger capacity)
- description (Description)
- city_id (City reference)
- driver_name (Driver name)
- driver_phone (Driver phone)
- driver_license (Driver license)
- price_per_hour (Price per hour)
- price_per_day (Price per day)
- currency (Currency)
- fuel_type (Fuel type)
- transmission (Transmission)
- features (Features - JSON)
- images (Images - JSON)
- status (Status)
- active (Active flag)
- enabled (Enabled flag)
- insurance_expiry (Insurance expiry)
- registration_expiry (Registration expiry)
- last_maintenance (Last maintenance)
- next_maintenance (Next maintenance)
- notes (Notes)
- created_at, updated_at, deleted_at
```

#### 5. Guides Table
```sql
- id (Primary Key)
- name (Name)
- email (Email)
- phone (Phone)
- nationality (Nationality)
- languages (Languages - JSON)
- specializations (Specializations - JSON)
- experience_years (Years of experience)
- city_id (City reference)
- price_per_hour (Price per hour)
- price_per_day (Price per day)
- currency (Currency)
- bio (Biography)
- certifications (Certifications - JSON)
- profile_image (Profile image)
- status (Status)
- active (Active flag)
- enabled (Enabled flag)
- rating (Rating)
- total_ratings (Total ratings)
- availability_schedule (Availability schedule - JSON)
- emergency_contact (Emergency contact)
- emergency_phone (Emergency phone)
- notes (Notes)
- created_at, updated_at, deleted_at
```

#### 6. Payments Table
```sql
- id (Primary Key)
- invoice_id (Invoice reference)
- booking_id (Booking reference)
- gateway (Payment gateway)
- amount (Amount)
- status (Status: paid, pending, not_paid)
- paid_at (Payment timestamp)
- transaction_request (Transaction request - JSON)
- transaction_verification (Transaction verification - JSON)
- notes (Notes)
- reference_number (Reference number)
- created_at, updated_at
```

#### 7. Resource Bookings Table
```sql
- id (Primary Key)
- booking_file_id (Booking file reference)
- resource_type (Resource type: hotel, vehicle, guide, representative)
- resource_id (Resource ID)
- start_date (Start date)
- end_date (End date)
- start_time (Start time)
- end_time (End time)
- quantity (Quantity)
- unit_price (Unit price)
- total_price (Total price)
- currency (Currency)
- status (Status)
- special_requirements (Special requirements - JSON)
- notes (Notes)
- created_at, updated_at
```

#### 8. Inquiry Resources Table
```sql
- id (Primary Key)
- inquiry_id (Inquiry reference)
- resource_type (Resource type)
- resource_id (Resource ID)
- resource_name (Resource name)
- added_by (Added by user)
- start_at (Start time)
- end_at (End time)
- check_in (Check-in date)
- check_out (Check-out date)
- number_of_rooms (Number of rooms)
- number_of_adults (Number of adults)
- number_of_children (Number of children)
- rate_per_adult (Rate per adult)
- rate_per_child (Rate per child)
- price_type (Price type)
- original_price (Original price)
- new_price (New price)
- increase_percent (Increase percentage)
- effective_price (Effective price)
- currency (Currency)
- price_note (Price note)
- resource_details (Resource details - JSON)
- created_at, updated_at
```

#### 9. Chats Table
```sql
- id (Primary Key)
- inquiry_id (Inquiry reference)
- sender_id (Sender ID)
- recipient_id (Recipient ID)
- message (Message)
- read_at (Read timestamp)
- created_at, updated_at
```

## Workflow Processes

### 1. Inquiry Process
1. **Inquiry Creation**: New inquiry created by client or staff
2. **User Assignment**: Inquiry assigned to specific users by role
3. **Resource Addition**: Tourism resources linked to inquiry
4. **Communication**: Internal chat communication
5. **Confirmation**: Upon confirmation, booking file automatically created

### 2. Booking Process
1. **Booking File Creation**: PDF file created for confirmed booking
2. **Progress Tracking**: Task checklist used to track booking progress
3. **Resource Management**: Resources assigned to booking with availability check
4. **Follow-up**: Booking tracked until completion

### 3. Payment Process
1. **Payment Creation**: New payment record created
2. **Payment Processing**: Payment processed through selected gateway
3. **Status Update**: Payment and booking status updated
4. **Notifications**: Notifications sent to client and staff

## Roles and Permissions

### 1. Administrator
- **Full Access**: All system permissions
- **User Management**: Create and manage user accounts
- **Role Management**: Manage roles and permissions
- **Reports**: Access to all reports and statistics

### 2. Sales
- **Inquiry Management**: Create and modify inquiries
- **Tour Itinerary**: Ability to modify tour itinerary
- **Resource Addition**: Add resources to inquiries
- **Chat**: Communicate with team via chat

### 3. Reservation
- **Inquiry Confirmation**: Confirm inquiries with payment details
- **Booking Management**: Manage booking files
- **Progress Tracking**: Update booking task checklists
- **Chat**: Communicate with sales team

### 4. Operator
- **Resource Management**: Manage tourism resources
- **Resource Assignment**: Assign resources to bookings
- **Availability Tracking**: Monitor resource availability
- **Chat**: Communicate with team

## Advanced Technical Features

### 1. Event System
- **InquiryConfirmed**: Inquiry confirmation event
- **NewInquiryCreated**: New inquiry creation event
- **ChatMessageSent**: Chat message sent event

### 2. Listeners
- **GenerateBookingFileListener**: Create booking file on confirmation
- **SendChatMessageNotification**: Send chat notifications

### 3. Services
- **BookingService**: Booking management service
- **FinanceService**: Financial management service
- **ResourceAssignmentService**: Resource assignment service

### 4. Traits
- **HasAuditLog**: Automatic activity logging
- **HasStatuses**: Status management
- **CurrencyConversion**: Currency conversion

## API Endpoints

### 1. Authentication API
- **POST /api/auth/login**: User login
- **POST /api/auth/register**: User registration
- **POST /api/auth/password/forget**: Password reset request
- **POST /api/auth/password/reset**: Password reset

### 2. Profile API
- **GET /api/profile/me**: Get profile data
- **PATCH /api/profile**: Update profile
- **POST /api/profile/change/image**: Change profile image

### 3. Payment API
- **GET /api/payments/fawaterk/methods**: Available payment methods
- **GET /api/payments/paypal/capture**: PayPal payment capture
- **GET /api/payments/paypal/cancel**: PayPal payment cancellation

### 4. Calendar API
- **GET /api/calendar/availability**: Check resource availability

## Configuration and Setup

### 1. Environment Variables (.env)
```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tourism_management
DB_USERNAME=root
DB_PASSWORD=

# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# WhatsApp Configuration
WHATSAPP_API_URL=https://graph.facebook.com
WHATSAPP_TOKEN=your-whatsapp-token
WHATSAPP_PHONE_NUMBER_ID=your-phone-number-id

# SMS Configuration
TWILIO_SID=your-twilio-sid
TWILIO_TOKEN=your-twilio-token
TWILIO_FROM=+1234567890

# PayPal Configuration
PAYPAL_CLIENT_ID=your-paypal-client-id
PAYPAL_CLIENT_SECRET=your-paypal-client-secret
PAYPAL_MODE=sandbox
```

### 2. System Installation
```bash
# Install dependencies
composer install
npm install && npm run dev

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Create storage link
php artisan storage:link

# Run the system
php artisan serve
```

## Security and Protection

### 1. Authentication and Authorization
- **Laravel Passport**: API authentication
- **Spatie Permission**: Permission management
- **CSRF Protection**: CSRF attack protection
- **Rate Limiting**: Request rate limiting

### 2. Data Protection
- **Password Encryption**: Secure password hashing
- **Soft Deletes**: Safe data deletion
- **Audit Logging**: Complete activity logging
- **Input Validation**: Data input validation

### 3. Additional Security
- **CORS Middleware**: CORS request management
- **Encrypted Cookies**: Cookie encryption
- **Secure Headers**: Additional security headers

## Maintenance and Support

### 1. Backup
- **Database Backup**: Regular database backups
- **File Backup**: Storage file backups
- **Performance Monitoring**: System performance monitoring

### 2. Updates
- **Laravel Updates**: Security and feature updates
- **Library Updates**: External library updates
- **Database Updates**: Database schema updates

### 3. Monitoring
- **Laravel Telescope**: Performance and error monitoring
- **Queue Monitor**: Background job monitoring
- **Activity Log**: Activity and operation logging

## Future Development

### 1. Planned Features
- **Mobile Application**: Smartphone application
- **Direct Booking System**: Direct customer booking
- **Advanced Analytics**: Smart analytics and automation
- **External System Integration**: Integration with global booking systems

### 2. Technical Improvements
- **Performance Optimization**: System speed improvements
- **Scalability**: Support for larger user base
- **Advanced Security**: Additional security features
- **Enhanced UI**: Improved user experience

## Conclusion

The Tourism Management System is a comprehensive and integrated solution for managing tourism and travel operations. The system provides all necessary tools for managing inquiries, bookings, tourism resources, and payments with an advanced notification system and high security.

The system is built on the latest technologies and provides an easy-to-use interface for users with advanced capabilities for developers. The system can be customized and extended according to the needs of each tourism company.

---

**Documentation Created By**: Automated Documentation System  
**Creation Date**: {{ date('Y-m-d') }}  
**Version**: 1.0  
**Language**: English
