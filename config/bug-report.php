<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the route prefix for bug report API endpoints.
    | The routes will be added to your application's routes/api.php file
    | during installation.
    |
    */

    'route_prefix' => env('BUG_REPORT_ROUTE_PREFIX', 'bug-reports'),

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configure file storage for bug report attachments.
    |
    */

    'storage' => [
        'disk' => env('BUG_REPORT_STORAGE_DISK', 'local'),
        'path' => env('BUG_REPORT_STORAGE_PATH', 'bug-reports'),
        'max_file_size' => env('BUG_REPORT_MAX_FILE_SIZE', 5120), // KB (default 5MB)
        'allowed_mime_types' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'video/mp4',
            'video/webm',
            'application/pdf',
        ],
        'max_files_per_report' => env('BUG_REPORT_MAX_FILES', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Notification Configuration
    |--------------------------------------------------------------------------
    |
    | Configure email notifications for bug reports.
    | Recipients should be a comma-separated list of email addresses.
    |
    */

    'notifications' => [
        'enabled' => env('BUG_REPORT_NOTIFICATIONS_ENABLED', true),
        'recipients' => array_filter(explode(',', env('BUG_REPORT_NOTIFICATION_EMAILS', ''))),
        'from_address' => env('BUG_REPORT_FROM_EMAIL', null),
        'from_name' => env('BUG_REPORT_FROM_NAME', 'Bug Report System'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Provider Fallback
    |--------------------------------------------------------------------------
    |
    | If Laravel's default mail configuration is not set up, the package
    | can use these settings as a fallback.
    |
    */

    'mail_provider' => [
        'driver' => env('BUG_REPORT_MAIL_DRIVER', 'log'),
        'host' => env('BUG_REPORT_MAIL_HOST', null),
        'port' => env('BUG_REPORT_MAIL_PORT', null),
        'username' => env('BUG_REPORT_MAIL_USERNAME', null),
        'password' => env('BUG_REPORT_MAIL_PASSWORD', null),
        'encryption' => env('BUG_REPORT_MAIL_ENCRYPTION', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for bug report submissions to prevent abuse.
    |
    */

    'rate_limit' => [
        'max_reports_per_hour' => env('BUG_REPORT_RATE_LIMIT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | User Model Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the user model used by your application.
    |
    */

    'user_model' => env('BUG_REPORT_USER_MODEL', 'App\\Models\\User'),

    /*
    |--------------------------------------------------------------------------
    | Pagination Configuration
    |--------------------------------------------------------------------------
    |
    | Configure default pagination for bug report listings.
    |
    */

    'pagination' => [
        'per_page' => env('BUG_REPORT_PER_PAGE', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Configuration
    |--------------------------------------------------------------------------
    |
    | Define the available bug report statuses.
    | These can be customized for future expansion.
    |
    */

    'statuses' => [
        'new' => 'New',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ],

];
