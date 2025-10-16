# Tourism Management System - Project Structure Documentation

## Project Overview

This document provides a comprehensive overview of the Tourism Management System project structure, including file organization, directory structure, and key components.

## Root Directory Structure

```
T-m/
├── app/                          # Application core files
├── bootstrap/                    # Application bootstrap files
├── config/                       # Configuration files
├── database/                     # Database files (migrations, seeders, factories)
├── lang/                         # Language files (multilingual support)
├── operations/                   # One-time operations
├── public/                       # Public web files
├── resources/                    # Views, assets, and language files
├── routes/                       # Route definitions
├── storage/                      # File storage and logs
├── tests/                        # Test files
├── vendor/                       # Composer dependencies
├── composer.json                 # PHP dependencies
├── package.json                  # Node.js dependencies
├── artisan                       # Laravel command-line interface
├── README.md                     # Project documentation
└── .env.example                  # Environment configuration template
```

## Application Structure (`app/`)

### Controllers (`app/Http/Controllers/`)
```
Controllers/
├── Api/                          # API controllers
│   ├── AuthController.php        # Authentication API
│   ├── CountryController.php     # Country management API
│   ├── ProfileController.php     # Profile management API
│   └── Payment/                  # Payment API controllers
├── Auth/                         # Authentication controllers
├── Dashboard/                    # Dashboard controllers
│   ├── InquiryController.php     # Inquiry management
│   ├── BookingController.php     # Booking management
│   ├── HotelController.php       # Hotel management
│   ├── VehicleController.php     # Vehicle management
│   ├── GuideController.php       # Guide management
│   ├── RepresentativeController.php # Representative management
│   ├── PaymentController.php     # Payment management
│   ├── UserController.php        # User management
│   ├── RoleController.php        # Role management
│   └── NotificationController.php # Notification management
└── Controller.php                # Base controller
```

### Models (`app/Models/`)
```
Models/
├── Inquiry.php                   # Inquiry model
├── BookingFile.php               # Booking file model
├── Hotel.php                     # Hotel model
├── Vehicle.php                   # Vehicle model
├── Guide.php                     # Guide model
├── Representative.php            # Representative model
├── Payment.php                   # Payment model
├── ResourceBooking.php           # Resource booking model
├── InquiryResource.php           # Inquiry resource model
├── Chat.php                      # Chat model
├── Client.php                    # Client model
├── User.php                      # User model
└── Translations/                 # Translation models
    ├── PageMetaTranslation.php
    └── SeoTranslation.php
```

### Enums (`app/Enums/`)
```
Enums/
├── InquiryStatus.php             # Inquiry status enum
├── BookingStatus.php             # Booking status enum
├── ResourceStatus.php            # Resource status enum
├── PaymentStatus.php             # Payment status enum
├── BlogStatus.php                # Blog status enum
└── CartItemType.php              # Cart item type enum
```

### Services (`app/Services/`)
```
Services/
├── BookingService.php            # Booking management service
├── FinanceService.php            # Financial management service
├── ResourceAssignmentService.php # Resource assignment service
├── Cache/                        # Cache services
│   └── AppCache.php
├── Client/                       # Client services
├── Dashboard/                    # Dashboard services
│   └── Currency.php
├── Email/                        # Email services
│   └── MailValidator.php
├── Query/                        # Query services
│   └── QueryBuilder.php
├── Recaptcha/                    # Recaptcha services
│   └── RecaptchaService.php
├── Request/                      # Request services
│   └── UriParser.php
├── Seo/                          # SEO services
├── Translation/                  # Translation services
│   ├── Google/                   # Google translation
│   ├── TranslationFactory.php
│   └── Translator.php
├── Whatsapp/                     # WhatsApp services
│   └── WhatsappMessaging.php
└── Wordpress/                    # WordPress services
```

### Events (`app/Events/`)
```
Events/
├── ChatMessageSent.php           # Chat message sent event
├── InquiryConfirmed.php          # Inquiry confirmed event
└── NewInquiryCreated.php         # New inquiry created event
```

### Listeners (`app/Listeners/`)
```
Listeners/
├── GenerateBookingFileListener.php # Generate booking file listener
├── NewCustomTripRequestListener.php # New custom trip request listener
└── SendChatMessageNotification.php # Send chat message notification
```

### Notifications (`app/Notifications/`)
```
Notifications/
├── Admin/                        # Admin notifications
│   └── InquiryConfirmedNotification.php
├── Channels/                     # Notification channels
│   ├── SmsChannel.php
│   └── WhatsAppChannel.php
├── Client/                       # Client notifications
│   ├── BookingConfirmedNotification.php
│   ├── ForgetPasswordNotification.php
│   └── PaymentOverdueNotification.php
├── NewChatMessageNotification.php
└── NewInquiryNotification.php
```

### Jobs (`app/Jobs/`)
```
Jobs/
├── SendPaymentReminderJob.php    # Send payment reminder job
├── TranslateChangesJob.php      # Translate changes job
└── TranslateModelByLocaleJob.php # Translate model by locale job
```

### Traits (`app/Traits/`)
```
Traits/
├── CurrencyConversion.php        # Currency conversion trait
├── HasAuditLog.php               # Audit logging trait
├── HasStatuses.php               # Status management trait
├── Models/                       # Model traits
│   ├── Activated.php
│   ├── ActivatedAndEnabled.php
│   ├── AutoTranslate.php
│   └── [Additional model traits]
└── Response/                     # Response traits
    ├── HasApiResponse.php
    └── RequestValidationErrorResponse.php
```

### DataTables (`app/DataTables/`)
```
DataTables/
├── BookingDataTable.php          # Booking data table
├── BookingFilesDataTable.php    # Booking files data table
├── ClientDataTable.php           # Client data table
├── InquiryDataTable.php          # Inquiry data table
├── PaymentDataTable.php          # Payment data table
├── HotelDataTable.php           # Hotel data table
├── VehicleDataTable.php          # Vehicle data table
├── GuideDataTable.php            # Guide data table
├── RepresentativeDataTable.php   # Representative data table
└── [Additional data tables]
```

### Observers (`app/Observers/`)
```
Observers/
├── AmenityObserver.php           # Amenity observer
├── BookingFileObserver.php       # Booking file observer
├── CarRouteObserver.php          # Car route observer
└── [Additional observers]
```

### Policies (`app/Policies/`)
```
Policies/
├── ChatPolicy.php                # Chat policy
└── InquiryPolicy.php             # Inquiry policy
```

### Scopes (`app/Scopes/`)
```
Scopes/
├── Activated.php                 # Activated scope
├── Enabled.php                   # Enabled scope
└── RestrictClient.php            # Restrict client scope
```

## Database Structure (`database/`)

### Migrations (`database/migrations/`)
```
migrations/
├── 2014_10_12_000000_create_users_table.php
├── 2014_10_12_100000_create_password_resets_table.php
├── 2018_02_05_000000_create_queue_monitor_table.php
├── create_inquiries_table.php
├── create_booking_files_table.php
├── create_hotels_table.php
├── create_vehicles_table.php
├── create_guides_table.php
├── create_representatives_table.php
├── create_payments_table.php
├── create_resource_bookings_table.php
├── create_inquiry_resources_table.php
├── create_chats_table.php
└── [Additional migrations]
```

### Seeders (`database/seeders/`)
```
seeders/
├── AdminSeeder.php               # Admin user seeder
├── BookingFileSeeder.php         # Booking file seeder
├── ChatSeeder.php                # Chat seeder
├── ClientSeeder.php              # Client seeder
├── HotelSeeder.php               # Hotel seeder
├── VehicleSeeder.php             # Vehicle seeder
├── GuideSeeder.php               # Guide seeder
├── RepresentativeSeeder.php      # Representative seeder
├── InquirySeeder.php             # Inquiry seeder
├── PaymentSeeder.php             # Payment seeder
├── ResourceBookingSeeder.php     # Resource booking seeder
├── InquiryResourceSeeder.php     # Inquiry resource seeder
├── currencies/                   # Currency data
│   └── currencies.json
└── Permissions/                  # Permission seeders
    ├── BookingFilePermissionSeeder.php
    ├── BookingPermissionSeeder.php
    ├── ClientPermissionSeeder.php
    ├── HotelPermissionSeeder.php
    ├── VehiclePermissionSeeder.php
    ├── GuidePermissionSeeder.php
    ├── RepresentativePermissionSeeder.php
    ├── PaymentPermissionSeeder.php
    ├── InquiryPermissionSeeder.php
    └── [Additional permission seeders]
```

### Factories (`database/factories/`)
```
factories/
├── ChatFactory.php               # Chat factory
├── ClientFactory.php             # Client factory
├── CouponFactory.php             # Coupon factory
└── [Additional factories]
```

## Routes Structure (`routes/`)

### Route Files
```
routes/
├── admin.php                     # Admin dashboard routes
├── api.php                       # API routes
├── auth.php                      # Authentication routes
├── web.php                       # Web routes
└── [Additional route files]
```

### Admin Routes (`routes/admin.php`)
- **User Management**: User and role management routes
- **Inquiry Management**: Inquiry CRUD and confirmation routes
- **Booking Management**: Booking file management routes
- **Resource Management**: Hotel, vehicle, guide, representative routes
- **Payment Management**: Payment processing routes
- **Notification Routes**: Notification management routes
- **Settings Routes**: System settings routes

### API Routes (`routes/api.php`)
- **Authentication**: Login, register, password reset
- **Profile Management**: Profile CRUD operations
- **Payment Processing**: Payment gateway integration
- **Calendar API**: Resource availability checking

## Resources Structure (`resources/`)

### Views (`resources/views/`)
```
views/
├── auth/                         # Authentication views
│   ├── confirm-password.blade.php
│   ├── forgot-password.blade.php
│   ├── login.blade.php
│   └── [Additional auth views]
├── components/                   # Reusable components
│   ├── advanced-chart.blade.php
│   ├── application-logo.blade.php
│   ├── auth-session-status.blade.php
│   ├── dashboard/                # Dashboard components
│   └── [Additional components]
├── dashboard/                    # Dashboard views
│   ├── bookings/                # Booking management views
│   ├── inquiries/               # Inquiry management views
│   ├── hotels/                  # Hotel management views
│   ├── vehicles/                # Vehicle management views
│   ├── guides/                  # Guide management views
│   ├── representatives/         # Representative management views
│   ├── payments/                # Payment management views
│   ├── users/                   # User management views
│   ├── roles/                   # Role management views
│   └── settings/               # Settings views
├── emails/                       # Email templates
│   ├── booking-confirmation-pdf.blade.php
│   ├── payment-receipt.blade.php
│   └── [Additional email templates]
├── layouts/                      # Layout templates
│   ├── app.blade.php
│   ├── guest.blade.php
│   └── [Additional layouts]
└── vendor/                       # Vendor views
```

### Assets (`resources/`)
```
resources/
├── css/
│   └── app.css                  # Main CSS file
├── js/
│   ├── app.js                   # Main JavaScript file
│   └── bootstrap.js             # Bootstrap JavaScript
└── views/                       # Blade templates
```

## Configuration Files (`config/`)

### Key Configuration Files
```
config/
├── app.php                       # Application configuration
├── database.php                  # Database configuration
├── mail.php                      # Mail configuration
├── queue.php                     # Queue configuration
├── cache.php                     # Cache configuration
├── session.php                   # Session configuration
├── auth.php                      # Authentication configuration
├── permission.php                # Permission configuration
├── activitylog.php               # Activity log configuration
├── datatables.php                # DataTables configuration
├── dompdf.php                    # PDF configuration
├── twilio.php                    # Twilio configuration
└── [Additional configuration files]
```

## Public Assets (`public/`)

### Public Directory Structure
```
public/
├── assets/                       # Static assets
│   ├── admin/                    # Admin assets
│   │   ├── css/                 # Admin CSS files
│   │   ├── js/                  # Admin JavaScript files
│   │   ├── images/              # Admin images
│   │   └── fonts/               # Admin fonts
│   └── site/                     # Site assets
│       └── images/              # Site images
├── vendor/                       # Vendor assets
│   ├── datatables/              # DataTables assets
│   ├── file-manager/            # File manager assets
│   ├── queue-monitor/           # Queue monitor assets
│   └── telescope/               # Telescope assets
├── index.php                     # Application entry point
├── favicon.ico                   # Site favicon
└── .htaccess                     # Apache configuration
```

## Storage Structure (`storage/`)

### Storage Directory
```
storage/
├── app/                          # Application storage
│   ├── public/                   # Public storage
│   │   ├── booking-files/        # Booking PDF files
│   │   ├── hotels/               # Hotel images
│   │   ├── vehicles/             # Vehicle images
│   │   ├── guides/                # Guide images
│   │   └── representatives/      # Representative images
│   └── [Additional app storage]
├── framework/                    # Framework storage
│   ├── cache/                    # Cache files
│   ├── sessions/                 # Session files
│   ├── testing/                  # Testing files
│   └── views/                    # Compiled views
└── logs/                         # Log files
    └── laravel.log               # Application logs
```

## Language Files (`lang/`)

### Multilingual Support
```
lang/
├── en/                           # English language files
│   ├── auth.php
│   ├── general.php
│   ├── messages.php
│   └── [Additional English files]
├── ar/                           # Arabic language files
├── de/                           # German language files
├── es/                           # Spanish language files
├── fr/                           # French language files
├── it/                           # Italian language files
├── pt/                           # Portuguese language files
└── zh/                           # Chinese language files
```

## Test Structure (`tests/`)

### Test Organization
```
tests/
├── Feature/                      # Feature tests
│   ├── Auth/                     # Authentication tests
│   ├── ChatTest.php              # Chat functionality tests
│   ├── ExampleTest.php           # Example tests
│   ├── InquiryResourceTest.php   # Inquiry resource tests
│   └── [Additional feature tests]
├── Unit/                         # Unit tests
│   └── WhatsappNotificationTest.php
├── CreatesApplication.php         # Test application creation
└── TestCase.php                  # Base test case
```

## Key Configuration Files

### Composer Configuration (`composer.json`)
- **Laravel Framework**: Core framework dependencies
- **Spatie Packages**: Permission and activity logging
- **PDF Generation**: DomPDF and Snappy
- **Translation**: Google Translate integration
- **Payment Gateways**: PayPal and Fawaterk
- **SMS Integration**: Twilio SDK
- **DataTables**: Yajra DataTables
- **API Documentation**: Swagger integration

### Package Configuration (`package.json`)
- **Tailwind CSS**: Styling framework
- **Alpine.js**: Frontend interactivity
- **Vite**: Build tools
- **FontAwesome**: Icon library
- **Axios**: HTTP client

### Environment Configuration (`.env.example`)
- **Database**: MySQL configuration
- **Mail**: SMTP configuration
- **Queue**: Queue configuration
- **Cache**: Cache configuration
- **Payment Gateways**: PayPal, Fawaterk configuration
- **SMS**: Twilio configuration
- **WhatsApp**: WhatsApp Business API configuration

## Development Workflow

### 1. Local Development Setup
```bash
# Clone repository
git clone [repository-url]

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Storage link
php artisan storage:link

# Run development server
php artisan serve
npm run dev
```

### 2. Production Deployment
```bash
# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Environment setup
cp .env.example .env
# Configure production environment variables

# Database setup
php artisan migrate --force
php artisan db:seed --force

# Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link
php artisan storage:link
```

### 3. Maintenance Commands
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Queue management
php artisan queue:work
php artisan queue:restart

# Backup database
php artisan backup:run

# Update permissions
php artisan db:seed --class=PermissionsSeeder
```

## Security Considerations

### 1. File Permissions
- **Storage Directory**: 755 permissions
- **Bootstrap Cache**: 755 permissions
- **Log Files**: 644 permissions
- **Configuration Files**: 644 permissions

### 2. Environment Security
- **Environment Variables**: Never commit .env files
- **API Keys**: Store securely in environment variables
- **Database Credentials**: Use strong passwords
- **Session Security**: Use secure session configuration

### 3. Code Security
- **Input Validation**: Validate all user inputs
- **SQL Injection**: Use Eloquent ORM
- **XSS Protection**: Use Blade templating
- **CSRF Protection**: Enable CSRF middleware

## Performance Optimization

### 1. Caching Strategy
- **Configuration Cache**: Cache configuration files
- **Route Cache**: Cache route definitions
- **View Cache**: Cache compiled views
- **Application Cache**: Cache application data

### 2. Database Optimization
- **Indexes**: Proper database indexing
- **Query Optimization**: Efficient database queries
- **Connection Pooling**: Database connection optimization
- **Query Caching**: Cache frequent queries

### 3. Asset Optimization
- **Asset Minification**: Minify CSS and JavaScript
- **Image Optimization**: Optimize images
- **CDN Integration**: Use CDN for static assets
- **Gzip Compression**: Enable compression

## Conclusion

This project structure documentation provides a comprehensive overview of the Tourism Management System's organization and architecture. The system follows Laravel best practices with a well-organized structure that supports scalability, maintainability, and security.

The modular approach allows for easy extension and customization while maintaining code quality and consistency. Each component has a specific purpose and follows established patterns for better development experience.

---

**Documentation Created By**: Automated Documentation System  
**Creation Date**: {{ date('Y-m-d') }}  
**Version**: 1.0  
**Language**: English

