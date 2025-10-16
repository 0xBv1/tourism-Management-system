<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Roles Configuration
    |--------------------------------------------------------------------------
    |
    | Define the roles and their permissions for the tourism management system
    |
    */
    'roles' => [
        'dashboard' => [
            'Admin',
            'Sales', 
            'Reservation',
            'Operator',
        ],
        'restricted' => [
            'Reservation',
            'Operator',
        ],
        'finance_only' => [
            'Finance',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Booking Configuration
    |--------------------------------------------------------------------------
    |
    | Default settings for booking management
    |
    */
    'booking' => [
        'default_currency' => 'USD',
        'overdue_days' => 30,
        'file_name_prefix' => 'booking',
        'max_filename_length' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for tourism resources (hotels, vehicles, etc.)
    |
    */
    'resources' => [
        'hotels' => [
            'active_scope' => 'active',
            'with_relations' => ['city'],
            'select_fields' => ['id', 'name', 'city_id', 'price_per_night', 'currency'],
        ],
        'vehicles' => [
            'active_scope' => 'active',
            'with_relations' => ['city'],
            'select_fields' => ['id', 'name', 'type', 'city_id', 'price_per_day', 'price_per_hour', 'currency'],
        ],
        'guides' => [
            'active_scope' => 'active',
            'with_relations' => ['city'],
            'select_fields' => ['id', 'name', 'city_id'],
        ],
        'representatives' => [
            'active_scope' => 'active',
            'with_relations' => ['city'],
            'select_fields' => ['id', 'name', 'city_id'],
        ],
        'extras' => [
            'active_scope' => 'active',
            'with_relations' => [],
            'select_fields' => ['id', 'name', 'category', 'price', 'currency'],
        ],
        'tickets' => [
            'active_scope' => 'active',
            'with_relations' => ['city'],
            'select_fields' => ['id', 'name', 'city_id', 'price_per_person', 'currency'],
        ],
        'dahabias' => [
            'active_scope' => 'active',
            'with_relations' => ['city'],
            'select_fields' => ['id', 'name', 'city_id', 'price_per_person', 'price_per_charter', 'currency'],
        ],
        'restaurants' => [
            'active_scope' => 'active',
            'with_relations' => ['city', 'meals'],
            'select_fields' => ['id', 'name', 'city_id', 'currency'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Configuration
    |--------------------------------------------------------------------------
    |
    | Payment processing settings
    |
    */
    'payment' => [
        'methods' => [
            'cash',
            'credit_card',
            'bank_transfer',
            'paypal',
            'fawaterk',
        ],
        'default_method' => 'cash',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for notifications and alerts
    |
    */
    'notifications' => [
        'channels' => [
            'email',
            'whatsapp',
            'database',
        ],
        'default_channels' => ['email', 'database'],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Management Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for file uploads and management
    |
    */
    'files' => [
        'booking_files' => [
            'path' => 'booking-files',
            'max_size' => 10240, // 10MB in KB
            'allowed_extensions' => ['pdf', 'doc', 'docx'],
        ],
        'profile_images' => [
            'path' => 'profile-images',
            'max_size' => 2048, // 2MB in KB
            'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache settings for performance optimization
    |
    */
    'cache' => [
        'ttl' => 3600, // 1 hour in seconds
        'prefix' => 'tourism_',
        'keys' => [
            'available_resources' => 'available_resources',
            'user_roles' => 'user_roles',
            'settings' => 'settings',
        ],
    ],
];
