# Bug Report Package - Development Plan

## Project Overview

An open-source Laravel Composer package that provides out-of-the-box bug reporting functionality for Laravel applications. Built with PHP 8.4, fully tested, and designed with security as a priority.

**Core Philosophy**: API-first design with optional Vue 3 components. Authentication and authorization are delegated to the consuming application.

## Core Requirements

### Technical Stack
- **PHP Version**: 8.4
- **Target Framework**: Laravel 10+
- **Frontend**: API-first, optional Vue 3 components (publishable)
- **Storage**: Configurable (filesystem default, S3/bucket support)
- **Authentication**: Delegated to consuming application
- **Database**: Prefixed tables (`bug_report_*`)
- **Testing**: MySQL (for proper foreign key constraint testing)
- **i18n**: Vue i18n for frontend translations

### Security Considerations
- No direct user input to database (use Eloquent ORM with proper mass assignment protection)
- File upload validation (type, size, mime-type checking)
- CSRF protection on all forms
- XSS prevention in user-submitted content
- Rate limiting on bug submission endpoints
- Authentication required (consuming app's responsibility)
- SQL injection prevention through parameter binding
- Sanitization of user-generated content before display
- Custom exception classes for proper error handling

### Authentication & Authorization Approach
- **Package does NOT manage roles/permissions**
- Routes will be registered via install command in consuming app's `api.php`
- README will recommend middleware configuration for admin routes
- Consuming application is responsible for:
  - Determining admin vs user access
  - Protecting admin routes with appropriate gates/middleware
  - Managing user authentication

---

## Iteration 1: Core Functionality

### IMPORTANT: Iteration 1 Scope Clarifications

**INCLUDED in Iteration 1:**
- API endpoints for CRUD operations
- Event system (BugReportCreated, BugReportStatusChanged, CommentAdded)
- Database migrations with soft deletes and foreign key constraints
- Email notifications with array of recipients
- Pagination using Laravel's pagination response objects
- Vue 3 components (optional, publishable)
- i18n support with vue-i18n
- Database factories and seeders
- CI/CD with GitHub Actions
- Code style enforcement with Laravel Pint
- Custom exception classes for error handling
- Bug report submission form with window.location URL capture

**EXCLUDED from Iteration 1 (Future Iterations):**
- Users viewing their own bug reports list
- Priority field functionality (field removed from schema)
- `is_internal` flag for comments (field removed from schema)
- Statistics dashboard endpoint
- API versioning
- User journey tracking

**Important Behavioral Notes:**
- Users submit bug reports but cannot view them after submission (no user-facing list view)
- All soft deletes maintain foreign key constraints (no cascade delete)
- Attachments are uploaded WITH the bug report in a single multipart request
- URL is captured via `window.location.href` and passed as form data

### 1. Package Structure & Installation

**Atomic Tasks (Phase 1A - Initial Setup):**

1. **✅ COMPLETED - Initialize package structure**
   - ✅ Create basic directory structure
   - ✅ Initialize composer.json with package metadata
   - ✅ Set up PSR-4 autoloading configuration
   - ✅ Add required Laravel package dependencies
   - ✅ Add dev dependencies (PHPUnit, Orchestra Testbench, Mockery, Pint)

2. **✅ COMPLETED - Configure Laravel Pint**
   - ✅ Create `pint.json` configuration file
   - ✅ Configure PSR-12 coding standards
   - ✅ Add Pint command to composer scripts

3. **✅ COMPLETED - Set up GitHub Actions CI/CD**
   - ✅ Create `.github/workflows/tests.yml`
   - ✅ Configure MySQL service for tests
   - ✅ Add PHP 8.4 matrix testing
   - ✅ Add Laravel 10 and 11 matrix testing
   - ✅ Include Pint code style check
   - ✅ Run PHPUnit tests

4. **✅ COMPLETED - Create base ServiceProvider**
   - ✅ Create `BugReportServiceProvider` class
   - ✅ Register package config, migrations, routes, views, translations
   - ✅ Set up publishable assets (config, migrations, lang, Vue components - optional)
   - ✅ Register event service provider

5. **✅ COMPLETED - Create BugReportEventServiceProvider**
   - ✅ Define event-to-listener mappings
   - ✅ Will be registered by install command to consuming app's `EventServiceProvider`

**Atomic Tasks (Phase 1A-1 - Frontend Setup):**

5a. **✅ COMPLETED - Set up frontend dependencies**
   - ✅ Initialize package.json for frontend dependencies
   - ✅ Install Vue 3 and core dependencies
   - ✅ Install Axios for API requests
   - ✅ Install development dependencies (Vite, etc.)
   - ✅ Configure for Node 22 LTS

**Atomic Tasks (Phase 1B - Configuration):**

6. **✅ COMPLETED - Create configuration file**
   - ✅ Build `config/bug-report.php` structure
   - ✅ Define all configuration keys with sensible defaults
   - ✅ Document each configuration option
   - ✅ Use env() helper for environment variables

7. **✅ COMPLETED - Create install command - Part 1: Structure**
   - ✅ Create `InstallCommand` class
   - ✅ Extend Laravel's Command class
   - ✅ Define command signature: `bug-report:install`
   - ✅ Add command description

8. **✅ COMPLETED - Create install command - Part 2: Publishing**
   - ✅ Publish config file
   - ✅ Publish migrations (don't run automatically)
   - ✅ Optionally publish Vue components
   - ✅ Optionally publish translations
   - ✅ Display next steps to user

9. **✅ COMPLETED - Create install command - Part 3: Route Registration**
   - ✅ Append API routes to consuming app's `routes/api.php`
   - ✅ Use File::append() to add route group
   - ✅ Include comments explaining admin middleware requirement
   - ✅ Provide example middleware configuration

10. **✅ COMPLETED - Create test email command**
    - ✅ Create `TestEmailCommand` class
    - ✅ Accept email argument
    - ✅ Send test email using package mail configuration
    - ✅ Display success/failure message

**Deliverables:**
- Complete package directory structure
- `composer.json` with all dependencies
- `pint.json` for code style
- `.github/workflows/tests.yml` for CI/CD
- `BugReportServiceProvider` with asset registration
- `BugReportEventServiceProvider` for event handling
- `config/bug-report.php` with full configuration
- `InstallCommand` that publishes assets and registers routes
- `TestEmailCommand` for email verification

---

### 2. Database Schema Design

**Atomic Tasks (Phase 2 - Database Layer):**

11. **✅ COMPLETED - Create bug_report_reports migration**
    - ✅ Migration name: `create_bug_report_reports_table.php`
    - ✅ Define table schema (see below)
    - ✅ Add foreign key constraint: `user_id` references `users.id`
    - ✅ Foreign key: NO CASCADE on delete (maintain referential integrity with soft deletes)
    - ✅ Add indexes for performance
    - ✅ Add soft deletes

12. **✅ COMPLETED - Create bug_report_attachments migration**
    - ✅ Migration name: `create_bug_report_attachments_table.php`
    - ✅ Define table schema (see below)
    - ✅ Add foreign key constraint: `bug_report_id` references `bug_report_reports.id`
    - ✅ Foreign key: NO CASCADE on delete (maintain referential integrity with soft deletes)
    - ✅ Add timestamps (no soft deletes needed)

13. **✅ COMPLETED - Create bug_report_comments migration**
    - ✅ Migration name: `create_bug_report_comments_table.php`
    - ✅ Define table schema (see below)
    - ✅ Add foreign key constraints: `bug_report_id` and `user_id`
    - ✅ Foreign keys: NO CASCADE on delete (maintain referential integrity with soft deletes)
    - ✅ Add indexes for performance
    - ✅ Add soft deletes

**Table Schemas:**

#### `bug_report_reports`
- `id` (bigIncrements, primary key)
- `user_id` (unsignedBigInteger, foreign key to users table, nullable: false)
- `title` (string, 255, required)
- `description` (text, required)
- `status` (string, 50, default: 'new') - Will use enum in model
- `url` (string, 2048, nullable - captured from window.location)
- `timestamps` (created_at, updated_at)
- `deleted_at` (soft deletes)

**Foreign Keys:**
- `user_id` → `users.id` (NO CASCADE, NO SET NULL)

**Indexes:**
- Index on `status`
- Index on `user_id`
- Index on `created_at`
- Index on `deleted_at` (for soft delete queries)

#### `bug_report_attachments`
- `id` (bigIncrements, primary key)
- `bug_report_id` (unsignedBigInteger, foreign key, required)
- `file_path` (string, 500, required - storage path)
- `file_name` (string, 255, required - original filename)
- `file_type` (string, 100, required - mime type)
- `file_size` (unsignedInteger, required - bytes)
- `timestamps` (created_at, updated_at)

**Foreign Keys:**
- `bug_report_id` → `bug_report_reports.id` (NO CASCADE, NO SET NULL)

**Indexes:**
- Index on `bug_report_id`

#### `bug_report_comments`
- `id` (bigIncrements, primary key)
- `bug_report_id` (unsignedBigInteger, foreign key, required)
- `user_id` (unsignedBigInteger, foreign key, required)
- `comment` (text, required)
- `timestamps` (created_at, updated_at)
- `deleted_at` (soft deletes)

**Foreign Keys:**
- `bug_report_id` → `bug_report_reports.id` (NO CASCADE, NO SET NULL)
- `user_id` → `users.id` (NO CASCADE, NO SET NULL)

**Indexes:**
- Composite index on (`bug_report_id`, `created_at`)
- Index on `user_id`
- Index on `deleted_at`

**Note**: Priority and is_internal fields removed from iteration 1

---

### 3. Models, Enums & Factories

**Atomic Tasks (Phase 3 - Eloquent Layer):**

14. **✅ COMPLETED - Create BugReportStatus enum**
    - ✅ Create `src/Enums/BugReportStatus.php`
    - ✅ Define enum cases: New, InProgress, Resolved, Closed
    - ✅ Add `label()` method for display names
    - ✅ Add `values()` static method for validation

15. **✅ COMPLETED - Create BugReport model**
    - ✅ Create `src/Models/BugReport.php`
    - ✅ Extend Eloquent Model
    - ✅ Add SoftDeletes trait
    - ✅ Define relationships: `user()`, `attachments()`, `comments()`
    - ✅ Fillable: `title`, `description`, `url`, `status`, `user_id`
    - ✅ Guarded: `id`
    - ✅ Casts: `status` as `BugReportStatus` enum
    - ✅ Protected: `deleted_at` as datetime
    - ✅ Boot method: dispatch BugReportCreated event on creation

16. **✅ COMPLETED - Create BugReportAttachment model**
    - ✅ Create `src/Models/BugReportAttachment.php`
    - ✅ Extend Eloquent Model
    - ✅ Define relationship: `bugReport()`
    - ✅ Fillable: `bug_report_id`, `file_path`, `file_name`, `file_type`, `file_size`
    - ✅ Guarded: `id`
    - ✅ Accessor: `file_url` (uses Storage::url())
    - ✅ Model event: Delete file from storage when model is deleted

17. **✅ COMPLETED - Create BugReportComment model**
    - ✅ Create `src/Models/BugReportComment.php`
    - ✅ Extend Eloquent Model
    - ✅ Add SoftDeletes trait
    - ✅ Define relationships: `bugReport()`, `user()`
    - ✅ Fillable: `bug_report_id`, `user_id`, `comment`
    - ✅ Guarded: `id`
    - ✅ Protected: `deleted_at` as datetime
    - ✅ Boot method: dispatch CommentAdded event on creation

18. **✅ COMPLETED - Create BugReportFactory**
    - ✅ Create `database/factories/BugReportFactory.php`
    - ✅ Generate fake data for all fields
    - ✅ Use Faker for title, description, URL
    - ✅ Random status from enum
    - ✅ Associate with User factory

19. **✅ COMPLETED - Create BugReportAttachmentFactory**
    - ✅ Create `database/factories/BugReportAttachmentFactory.php`
    - ✅ Generate fake file data
    - ✅ Create fake file path, name, type, size

20. **✅ COMPLETED - Create BugReportCommentFactory**
    - ✅ Create `database/factories/BugReportCommentFactory.php`
    - ✅ Generate fake comment text
    - ✅ Associate with BugReport and User factories

21. **✅ COMPLETED - Create database seeder**
    - ✅ Create `database/seeders/BugReportSeeder.php`
    - ✅ Seed 20 bug reports with random attachments and comments
    - ✅ Use factories for data generation
    - ✅ Document usage in README

---

### 4. Configuration System

**Configuration file created in task #6 above**

**`config/bug-report.php` Structure:**

```php
return [
    // Route configuration
    'route_prefix' => env('BUG_REPORT_ROUTE_PREFIX', 'bug-reports'),

    // Storage configuration
    'storage' => [
        'disk' => env('BUG_REPORT_STORAGE_DISK', 'local'),
        'path' => env('BUG_REPORT_STORAGE_PATH', 'bug-reports'),
        'max_file_size' => env('BUG_REPORT_MAX_FILE_SIZE', 5120), // KB
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

    // Email notification configuration
    'notifications' => [
        'enabled' => env('BUG_REPORT_NOTIFICATIONS_ENABLED', true),
        'recipients' => explode(',', env('BUG_REPORT_NOTIFICATION_EMAILS', '')), // Array support
        'from_address' => env('BUG_REPORT_FROM_EMAIL', null),
        'from_name' => env('BUG_REPORT_FROM_NAME', 'Bug Report System'),
    ],

    // Email provider fallback (if Laravel mail not configured)
    'mail_provider' => [
        'driver' => env('BUG_REPORT_MAIL_DRIVER', 'log'),
        'host' => env('BUG_REPORT_MAIL_HOST', null),
        'port' => env('BUG_REPORT_MAIL_PORT', null),
        'username' => env('BUG_REPORT_MAIL_USERNAME', null),
        'password' => env('BUG_REPORT_MAIL_PASSWORD', null),
        'encryption' => env('BUG_REPORT_MAIL_ENCRYPTION', null),
    ],

    // Rate limiting
    'rate_limit' => [
        'max_reports_per_hour' => env('BUG_REPORT_RATE_LIMIT', 10),
    ],

    // User model configuration
    'user_model' => env('BUG_REPORT_USER_MODEL', 'App\\Models\\User'),

    // Pagination
    'pagination' => [
        'per_page' => env('BUG_REPORT_PER_PAGE', 15),
    ],

    // Status options (configurable for future expansion)
    'statuses' => [
        'new' => 'New',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ],
];
```

**Note**: Email recipients now supports array via comma-separated env variable

---

### 5. Events & Listeners

**Atomic Tasks (Phase 4 - Event System):**

22. **✅ COMPLETED - Create BugReportCreated event**
    - ✅ Create `src/Events/BugReportCreated.php`
    - ✅ Accept BugReport model in constructor
    - ✅ Implement ShouldBroadcast interface (for future real-time updates)

23. **✅ COMPLETED - Create BugReportStatusChanged event**
    - ✅ Create `src/Events/BugReportStatusChanged.php`
    - ✅ Accept BugReport model and old status in constructor

24. **✅ COMPLETED - Create CommentAdded event**
    - ✅ Create `src/Events/CommentAdded.php`
    - ✅ Accept BugReportComment model in constructor

25. **✅ COMPLETED - Create SendBugReportNotification listener**
    - ✅ Create `src/Listeners/SendBugReportNotification.php`
    - ✅ Listen to BugReportCreated event
    - ✅ Send email notification to configured recipients

26. **✅ COMPLETED - Create SendStatusChangeNotification listener**
    - ✅ Create `src/Listeners/SendStatusChangeNotification.php`
    - ✅ Listen to BugReportStatusChanged event
    - ✅ Notify bug reporter of status change

27. **✅ COMPLETED - Create SendCommentNotification listener**
    - ✅ Create `src/Listeners/SendCommentNotification.php`
    - ✅ Listen to CommentAdded event
    - ✅ Notify bug reporter of new comment

---

### 6. API Layer - Services, Exceptions & Middleware

**Atomic Tasks (Phase 5A - Services):**

28. **✅ COMPLETED - Create custom exception classes**
    - ✅ `src/Exceptions/BugReportNotFoundException.php`
    - ✅ `src/Exceptions/UnauthorizedBugReportAccessException.php`
    - ✅ `src/Exceptions/InvalidFileUploadException.php`
    - ✅ `src/Exceptions/RateLimitExceededException.php`
    - ✅ All extend base Exception with JSON response formatting

29. **✅ COMPLETED - Create AttachmentService**
    - ✅ Create `src/Services/AttachmentService.php`
    - ✅ Method: `validateFiles(array $files): void` - validates mime types, size, count
    - ✅ Method: `storeFiles(array $files, BugReport $report): Collection` - stores and creates attachment records
    - ✅ Method: `deleteFile(BugReportAttachment $attachment): void` - removes from storage

30. **✅ COMPLETED - Create BugReportService**
    - ✅ Create `src/Services/BugReportService.php`
    - ✅ Method: `create(array $data, array $files = []): BugReport` - creates report with attachments
    - ✅ Method: `update(BugReport $report, array $data): BugReport` - updates report
    - ✅ Method: `updateStatus(BugReport $report, string $status): BugReport` - changes status, fires event
    - ✅ Method: `delete(BugReport $report): bool` - soft deletes report

31. **✅ COMPLETED - Create EmailService**
    - ✅ Create `src/Services/EmailService.php`
    - ✅ Method: `checkMailConfiguration(): bool` - validates mail config
    - ✅ Method: `getMailer(): Mailer` - returns Laravel mailer or package-specific config
    - ✅ Method: `sendTest(string $email): bool` - sends test email

32. **✅ COMPLETED - Create BugReportRateLimit middleware**
    - ✅ Create `src/Http/Middleware/BugReportRateLimit.php`
    - ✅ Use Laravel's rate limiting features
    - ✅ Check config for max reports per hour
    - ✅ Throw RateLimitExceededException if exceeded

**Atomic Tasks (Phase 5B - Form Requests):**

33. **✅ COMPLETED - Create StoreBugReportRequest**
    - ✅ Create `src/Http/Requests/StoreBugReportRequest.php`
    - ✅ Validation: title (required, max:255), description (required), url (nullable, url, max:2048)
    - ✅ Validation: attachments (optional array, validated by AttachmentService)
    - ✅ Sanitize HTML in description

34. **✅ COMPLETED - Create UpdateBugReportRequest**
    - ✅ Create `src/Http/Requests/UpdateBugReportRequest.php`
    - ✅ Validation: title (sometimes, max:255), description (sometimes), url (sometimes, url)
    - ✅ Sanitize HTML in description

35. **✅ COMPLETED - Create UpdateStatusRequest**
    - ✅ Create `src/Http/Requests/UpdateStatusRequest.php`
    - ✅ Validation: status (required, in BugReportStatus values)

36. **✅ COMPLETED - Create StoreCommentRequest**
    - ✅ Create `src/Http/Requests/StoreCommentRequest.php`
    - ✅ Validation: comment (required, max:5000)
    - ✅ Sanitize HTML in comment

37. **✅ COMPLETED - Create UpdateCommentRequest**
    - ✅ Create `src/Http/Requests/UpdateCommentRequest.php`
    - ✅ Validation: comment (required, max:5000)
    - ✅ Sanitize HTML in comment

---

### 7. API Layer - Controllers & Routes

**Atomic Tasks (Phase 5C - Controllers):**

38. **✅ COMPLETED - Create BugReportController** (User-facing)
    - ✅ Create `src/Http/Controllers/BugReportController.php`
    - ✅ Method: `store(StoreBugReportRequest $request): JsonResponse` - create bug report with attachments
    - ✅ Returns: 201 with bug report resource
    - ✅ Applies: rate limit middleware

39. **✅ COMPLETED - Create BugReportAdminController** (Admin-facing)
    - ✅ Create `src/Http/Controllers/BugReportAdminController.php`
    - ✅ Method: `index(Request $request): JsonResponse` - list all reports with pagination & filtering
    - ✅ Method: `show(int $id): JsonResponse` - view single report with attachments & comments
    - ✅ Method: `updateStatus(UpdateStatusRequest $request, int $id): JsonResponse` - update status
    - ✅ Method: `destroy(int $id): JsonResponse` - soft delete report
    - ✅ Uses Laravel pagination response format

40. **✅ COMPLETED - Create CommentController**
    - ✅ Create `src/Http/Controllers/CommentController.php`
    - ✅ Method: `store(StoreCommentRequest $request, int $reportId): JsonResponse` - add comment
    - ✅ Method: `update(UpdateCommentRequest $request, int $reportId, int $commentId): JsonResponse` - update comment
    - ✅ Method: `destroy(int $reportId, int $commentId): JsonResponse` - soft delete comment

41. **✅ COMPLETED - Create API routes file**
    - ✅ Create `routes/api.php`
    - ✅ Define route group with prefix from config
    - ✅ User routes: POST /bug-reports
    - ✅ Admin routes: GET /bug-reports, GET /bug-reports/{id}, PUT /bug-reports/{id}/status, DELETE /bug-reports/{id}
    - ✅ Comment routes: POST /bug-reports/{id}/comments, PUT /bug-reports/{id}/comments/{commentId}, DELETE /bug-reports/{id}/comments/{commentId}
    - ✅ Add comments indicating where to apply admin middleware
    - ✅ Will be appended to consuming app's api.php by install command

**API Endpoint Summary:**

#### User Routes (authenticated)
- `POST /api/bug-reports` - Create bug report with attachments (multipart/form-data)

#### Admin Routes (authenticated + admin middleware - consuming app's responsibility)
- `GET /api/bug-reports` - List all reports (paginated, filterable by status)
- `GET /api/bug-reports/{id}` - View single report with relationships
- `PUT /api/bug-reports/{id}/status` - Update report status
- `DELETE /api/bug-reports/{id}` - Soft delete report
- `POST /api/bug-reports/{id}/comments` - Add comment to report
- `PUT /api/bug-reports/{id}/comments/{commentId}` - Update comment
- `DELETE /api/bug-reports/{id}/comments/{commentId}` - Soft delete comment

**Standard JSON Response Format:**

Success:
```json
{
    "success": true,
    "data": { ... },
    "message": "Operation successful"
}
```

Error:
```json
{
    "success": false,
    "error": "Error message",
    "code": "ERROR_CODE"
}
```

Pagination (Laravel standard):
```json
{
    "data": [...],
    "links": { ... },
    "meta": { ... }
}
```

---

### 8. Email Notifications

**Atomic Tasks (Phase 6 - Notifications):**

42. **✅ COMPLETED - Create BugReportCreatedNotification**
    - ✅ Create `src/Notifications/BugReportCreatedNotification.php`
    - ✅ Use Mailable class
    - ✅ To: configured admin recipients array
    - ✅ Content: Title, Description, Reporter name, URL, Link to view report
    - ✅ Uses EmailService for mail configuration

43. **✅ COMPLETED - Create BugReportStatusChangedNotification**
    - ✅ Create `src/Notifications/BugReportStatusChangedNotification.php`
    - ✅ Use Mailable class
    - ✅ To: bug reporter email
    - ✅ Content: Old status, New status, Link to view report
    - ✅ Only sent if status actually changed

44. **✅ COMPLETED - Create CommentAddedNotification**
    - ✅ Create `src/Notifications/CommentAddedNotification.php`
    - ✅ Use Mailable class
    - ✅ To: bug reporter email
    - ✅ Content: Commenter name, Comment text, Link to view report

45. **✅ COMPLETED - Create email templates (Blade)**
    - ✅ `resources/views/emails/bug-created.blade.php`
    - ✅ `resources/views/emails/status-changed.blade.php`
    - ✅ `resources/views/emails/comment-added.blade.php`
    - ✅ Simple, responsive HTML email templates
    - ✅ Include package branding (customizable)

---

### 9. Frontend Components (Vue 3 - Optional, Publishable)

**Atomic Tasks (Phase 7 - Frontend):**

46. **✅ COMPLETED - Set up i18n translations**
    - ✅ Create `resources/lang/en/bug-report.php` for English (Laravel)
    - ✅ Create `resources/js/i18n/en.js` for JavaScript translations
    - ✅ Set up vue-i18n v11 with Composition API mode
    - ✅ Define all translatable strings
    - ✅ Create structure for additional languages
    - ✅ Use Laravel's trans() helper for Blade, vue-i18n for Vue
    - ✅ Export i18n instance, addLocale, and setLocale helpers
    - ✅ Create I18N_USAGE.md documentation

47. **✅ COMPLETED - Create ReportBugButton component**
    - ✅ File: `resources/js/components/ReportBugButton.vue`
    - ✅ Features: Generic button that navigates to report form route
    - ✅ Captures `window.location.href` before navigation
    - ✅ Passes URL as query parameter
    - ✅ Styled minimally (easily theme-able)
    - ✅ Props: size, color, text (customizable)

48. **✅ COMPLETED - Create BugReportCreate component**
    - ✅ File: `resources/js/components/BugReportCreate.vue`
    - ✅ Features: Title, description, file upload with preview
    - ✅ URL pre-filled from query parameter
    - ✅ Client-side validation
    - ✅ Submits multipart/form-data to POST /api/bug-reports
    - ✅ Loading states, success/error messages
    - ✅ Uses vue-i18n for translations

49. **Create BugReportDashboard component**
    - File: `resources/js/components/BugReportDashboard.vue`
    - Features: Paginated table of all reports
    - Filter by status, search by title/description
    - Status badges with color coding
    - Click row to navigate to detail view
    - Uses Laravel pagination response

50. **Create BugReportDetail component**
    - File: `resources/js/components/BugReportDetail.vue`
    - Features: Display full report with attachments & comments
    - Image attachments shown inline
    - Other files as download links
    - Status update UI
    - Comment thread

51. **Create BugReportComments sub-component**
    - File: `resources/js/components/BugReportComments.vue`
    - Features: Display comments, add new, edit/delete own
    - Nested inside BugReportDetail

52. **Create shared UI components**
    - `resources/js/components/shared/StatusBadge.vue`
    - `resources/js/components/shared/FileUpload.vue`
    - `resources/js/components/shared/Pagination.vue`
    - `resources/js/components/shared/LoadingSpinner.vue`

53. **✅ COMPLETED - Create API service layer (JavaScript)**
    - ✅ File: `resources/js/services/BugReportApi.js`
    - ✅ Fetch-based API client
    - ✅ Methods: createReport, listReports, getReport, updateStatus, addComment, etc.
    - ✅ Handles CSRF token
    - ✅ Error handling with custom exceptions

54. **Document Vue component usage**
    - Add section to README on publishing Vue components
    - Document mounting components in consuming app
    - Example vite.config.js configuration
    - Example component registration

---

### 10. Testing

**Atomic Tasks (Phase 8 - Testing):**

55. **Set up testing environment**
    - Create `phpunit.xml` configuration
    - Configure MySQL connection for testing
    - Create `tests/TestCase.php` extending Orchestra\Testbench
    - Set up package service provider loading
    - Create test database migration helper

56. **Unit tests - Models**
    - `tests/Unit/Models/BugReportTest.php` - relationships, accessors
    - `tests/Unit/Models/BugReportAttachmentTest.php` - file URL accessor
    - `tests/Unit/Models/BugReportCommentTest.php` - relationships

57. **Unit tests - Services**
    - `tests/Unit/Services/AttachmentServiceTest.php` - file validation
    - `tests/Unit/Services/BugReportServiceTest.php` - CRUD operations
    - `tests/Unit/Services/EmailServiceTest.php` - mail config

58. **Unit tests - Enums**
    - `tests/Unit/Enums/BugReportStatusTest.php` - enum values

59. **Feature tests - Bug Report API**
    - `tests/Feature/BugReportTest.php`
    - Test: create report with attachments (multipart)
    - Test: create report without attachments
    - Test: rate limiting on creation
    - Test: validation errors
    - Test: events dispatched on creation

60. **Feature tests - Admin API**
    - `tests/Feature/BugReportAdminTest.php`
    - Test: list reports with pagination
    - Test: filter reports by status
    - Test: view single report
    - Test: update status (fires event)
    - Test: soft delete report

61. **Feature tests - Comments**
    - `tests/Feature/CommentTest.php`
    - Test: add comment to report
    - Test: update own comment
    - Test: delete own comment
    - Test: event dispatched on comment creation

62. **Feature tests - Notifications**
    - `tests/Feature/NotificationTest.php`
    - Test: email sent on bug report creation
    - Test: email sent on status change
    - Test: email sent on comment add
    - Test: email queue integration

63. **Feature tests - File Uploads**
    - `tests/Feature/AttachmentTest.php`
    - Test: valid file upload
    - Test: invalid mime type rejection
    - Test: file size limit enforcement
    - Test: max files per report
    - Test: file deleted on attachment deletion

64. **Integration tests**
    - `tests/Integration/BugReportFlowTest.php`
    - Test: complete flow from creation to resolution
    - Test: multiple storage disk configurations
    - Test: mail fallback configuration

---

### 11. Documentation & Release

**Atomic Tasks (Phase 9 - Documentation):**

65. **Create comprehensive README**
    - Installation via Composer
    - Running install command
    - Configuration guide (all env variables)
    - API endpoint documentation
    - Authentication/authorization recommendations
    - Vue component usage (optional)
    - Email setup guide
    - Event system documentation
    - Troubleshooting section

66. **Create CONTRIBUTING.md**
    - Code style (Pint)
    - Running tests
    - Submitting PRs
    - Issue reporting guidelines

67. **Create CHANGELOG.md**
    - Semantic versioning format
    - Document v1.0.0 features

68. **Create LICENSE file**
    - Choose appropriate open-source license (MIT recommended)

69. **Add PHPDoc to all classes**
    - Document all public methods
    - Add @param, @return, @throws tags
    - Explain complex logic in comments

**Atomic Tasks (Phase 10 - Release):**

70. **Security audit**
    - Review all input validation
    - Check XSS prevention
    - Verify CSRF protection
    - Test file upload security
    - Review SQL injection prevention

71. **Performance testing**
    - Test with large datasets
    - Check query optimization (N+1 problems)
    - Test file upload with max size files

72. **Cross-version compatibility testing**
    - Test on Laravel 10.x
    - Test on Laravel 11.x
    - Test on PHP 8.4

73. **Prepare for Packagist**
    - Create GitHub repository
    - Tag v1.0.0 release
    - Register on Packagist
    - Set up auto-update webhooks

---

## Iteration 2: Advanced Features (Future)

### High Priority Future Features

**User Report Viewing**
- [ ] API endpoint for users to view their own bug reports
- [ ] Filter and search user's own reports
- [ ] **TODO**: Revisit notification recipients configuration (callback-based recipient resolution)

**Statistics Dashboard**
- [ ] Dashboard statistics API endpoint
- [ ] Count by status, recent activity, resolution times
- [ ] Exportable reports

**Priority System**
- [ ] Add `priority` field back to bug_report_reports table
- [ ] Priority selection in create form
- [ ] Filter reports by priority

**Internal Comments**
- [ ] Add `is_internal` flag to bug_report_comments table
- [ ] Admin-only internal notes
- [ ] Separate UI for internal vs public comments

### Medium Priority Future Features

**Third-Party Integrations**
- Jira integration via API (adapter pattern)
- Linear integration via API (adapter pattern)
- Abstract integration layer for pluggable ticket systems
- Webhook support for custom integrations
- Zapier integration

**AI Analysis**
- Integration with OpenAI/Claude API
- Automatic categorization of bugs
- Severity assessment based on description
- Duplicate detection using embeddings
- Suggested fixes/solutions

**Spam Filtering**
- Configurable spam detection rules
- Machine learning classification
- Akismet integration (optional)
- Manual spam marking with learning
- CAPTCHA for anonymous submissions (if anonymous support added)

### Long-term Future Features

**User Journey Tracking**
- JavaScript tracking library
- Session recording integration (Hotjar, FullStory)
- Breadcrumb trail of user actions
- Performance metrics capture
- Console error logging

**Advanced Workflow**
- Customizable status workflows
- Assignment system (assign reports to team members)
- SLA tracking and alerts
- Report templates
- Bulk operations

**API Versioning**
- `/api/v1/bug-reports` structure
- Deprecation strategy
- Version negotiation

---

## Development Workflow Summary

### Recommended Development Order

**Phase 1: Foundation (Tasks 1-13)**
- Package structure, CI/CD, ServiceProvider, Config
- Database migrations
- Initial setup complete, ready for models

**Phase 2: Core Backend (Tasks 14-27)**
- Models, enums, factories
- Events and listeners
- Core business logic established

**Phase 3: API Layer (Tasks 28-41)**
- Services, exceptions, middleware
- Form requests
- Controllers and routes
- Complete backend API functional

**Phase 4: Notifications (Tasks 42-45)**
- Email notifications and templates
- Email service provider fallback

**Phase 5: Frontend (Tasks 46-54)** - Optional, can be done last
- Vue components
- API service layer
- i18n translations
- UI polish

**Phase 6: Testing (Tasks 55-64)**
- Test environment setup
- Unit, feature, and integration tests
- Aim for 90%+ coverage

**Phase 7: Documentation & Release (Tasks 65-73)**
- README, CONTRIBUTING, CHANGELOG
- PHPDoc comments
- Security audit
- Performance testing
- Packagist release

---

## File Structure

```
bug-report/
├── .github/
│   └── workflows/
│       └── tests.yml                          # CI/CD pipeline
├── config/
│   └── bug-report.php                         # Package configuration
├── database/
│   ├── factories/
│   │   ├── BugReportFactory.php
│   │   ├── BugReportAttachmentFactory.php
│   │   └── BugReportCommentFactory.php
│   ├── migrations/
│   │   ├── create_bug_report_reports_table.php
│   │   ├── create_bug_report_attachments_table.php
│   │   └── create_bug_report_comments_table.php
│   └── seeders/
│       └── BugReportSeeder.php
├── resources/
│   ├── js/
│   │   ├── components/
│   │   │   ├── ReportBugButton.vue
│   │   │   ├── BugReportCreate.vue
│   │   │   ├── BugReportDashboard.vue
│   │   │   ├── BugReportDetail.vue
│   │   │   ├── BugReportComments.vue
│   │   │   └── shared/
│   │   │       ├── StatusBadge.vue
│   │   │       ├── FileUpload.vue
│   │   │       ├── Pagination.vue
│   │   │       └── LoadingSpinner.vue
│   │   └── services/
│   │       └── BugReportApi.js                # Axios API client
│   ├── views/
│   │   └── emails/
│   │       ├── bug-created.blade.php
│   │       ├── status-changed.blade.php
│   │       └── comment-added.blade.php
│   └── lang/
│       └── en/
│           └── bug-report.php                 # English translations
├── routes/
│   └── api.php                                # API routes (appended to consuming app)
├── src/
│   ├── BugReportServiceProvider.php           # Main service provider
│   ├── BugReportEventServiceProvider.php      # Event service provider
│   ├── Console/
│   │   └── Commands/
│   │       ├── InstallCommand.php
│   │       └── TestEmailCommand.php
│   ├── Enums/
│   │   └── BugReportStatus.php
│   ├── Events/
│   │   ├── BugReportCreated.php
│   │   ├── BugReportStatusChanged.php
│   │   └── CommentAdded.php
│   ├── Exceptions/
│   │   ├── BugReportNotFoundException.php
│   │   ├── UnauthorizedBugReportAccessException.php
│   │   ├── InvalidFileUploadException.php
│   │   └── RateLimitExceededException.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── BugReportController.php        # User-facing
│   │   │   ├── BugReportAdminController.php   # Admin-facing
│   │   │   └── CommentController.php
│   │   ├── Middleware/
│   │   │   └── BugReportRateLimit.php
│   │   └── Requests/
│   │       ├── StoreBugReportRequest.php
│   │       ├── UpdateBugReportRequest.php
│   │       ├── UpdateStatusRequest.php
│   │       ├── StoreCommentRequest.php
│   │       └── UpdateCommentRequest.php
│   ├── Listeners/
│   │   ├── SendBugReportNotification.php
│   │   ├── SendStatusChangeNotification.php
│   │   └── SendCommentNotification.php
│   ├── Models/
│   │   ├── BugReport.php
│   │   ├── BugReportAttachment.php
│   │   └── BugReportComment.php
│   ├── Notifications/
│   │   ├── BugReportCreatedNotification.php
│   │   ├── BugReportStatusChangedNotification.php
│   │   └── CommentAddedNotification.php
│   └── Services/
│       ├── BugReportService.php
│       ├── AttachmentService.php
│       └── EmailService.php
├── tests/
│   ├── Feature/
│   │   ├── BugReportTest.php
│   │   ├── BugReportAdminTest.php
│   │   ├── AttachmentTest.php
│   │   ├── CommentTest.php
│   │   └── NotificationTest.php
│   ├── Integration/
│   │   └── BugReportFlowTest.php
│   ├── Unit/
│   │   ├── Enums/
│   │   │   └── BugReportStatusTest.php
│   │   ├── Models/
│   │   │   ├── BugReportTest.php
│   │   │   ├── BugReportAttachmentTest.php
│   │   │   └── BugReportCommentTest.php
│   │   └── Services/
│   │       ├── AttachmentServiceTest.php
│   │       ├── BugReportServiceTest.php
│   │       └── EmailServiceTest.php
│   └── TestCase.php
├── .gitignore
├── CHANGELOG.md
├── composer.json
├── CONTRIBUTING.md
├── LICENSE
├── pint.json                                  # Laravel Pint config
├── phpunit.xml                                # PHPUnit configuration
└── README.md
```

---

## Key Design Decisions

### 1. API-First, Frontend Optional
- **API is primary deliverable**, fully functional standalone
- Vue 3 components are **optional** and publishable separately
- Allows consuming apps to use any frontend framework
- Enables mobile app integration
- Flexibility for custom UI implementations

### 2. Authentication/Authorization Delegation
- Package **does not** manage roles or permissions
- Install command adds routes to consuming app's `api.php`
- README provides middleware recommendations for admin routes
- Consuming app controls who can access what
- Package focuses on functionality, not access control

### 3. Attachment Upload Strategy
- Files uploaded **WITH** bug report creation (single multipart request)
- Simplifies user experience
- Reduces orphaned file issues
- AttachmentService validates before storage

### 4. Soft Deletes with Foreign Key Integrity
- All deletes are soft deletes
- Foreign key constraints maintained (NO CASCADE)
- Enables data recovery and auditing
- Prevents accidental data loss

### 5. Event-Driven Architecture
- Dispatches events for all major actions
- Consuming apps can hook into package behavior
- Enables custom workflows without modifying package
- Events: BugReportCreated, BugReportStatusChanged, CommentAdded

### 6. Configurable Everything
- Storage driver (local, S3, etc.)
- Email provider with fallback
- Route prefix
- User model (for compatibility with different auth setups)
- Rate limiting thresholds
- Pagination defaults

### 7. Security by Default
- Authenticated users only (consuming app's responsibility)
- Rate limiting out of the box
- File validation and sanitization
- Custom exception classes for error handling
- XSS prevention in all user input
- SQL injection prevention through Eloquent

### 8. Testing with MySQL
- Uses MySQL for tests (not SQLite)
- Catches foreign key constraint issues
- More realistic testing environment
- Requires MySQL service in CI/CD

---

## Success Metrics for Iteration 1

### Core Functionality
- [ ] Package installable via Composer
- [ ] Install command successfully publishes all assets
- [ ] Migrations run without errors on MySQL
- [ ] Users can submit bug reports with attachments (multipart)
- [ ] API returns bug reports with pagination
- [ ] Status updates work and fire events
- [ ] Comments can be added, edited, deleted
- [ ] Soft deletes work correctly with foreign key constraints

### Notifications & Events
- [ ] Email notifications sent on new reports to array of recipients
- [ ] Email notifications sent on status changes
- [ ] Email notifications sent on new comments
- [ ] All events dispatched correctly (testable)

### Quality & Testing
- [ ] 90%+ test coverage
- [ ] All tests pass on Laravel 10 and 11
- [ ] All tests pass on PHP 8.4
- [ ] Pint code style check passes
- [ ] CI/CD pipeline runs successfully

### Security
- [ ] All file upload validation working
- [ ] HTML sanitization on user input
- [ ] Rate limiting prevents abuse
- [ ] Custom exceptions return proper JSON responses

### Documentation
- [ ] README complete with all sections
- [ ] API endpoints documented
- [ ] Configuration options explained
- [ ] Authentication recommendations provided
- [ ] Vue component usage documented
- [ ] Event system explained

---

## Task Summary

**Total Atomic Tasks: 73**

- Phase 1 (Foundation): Tasks 1-13
- Phase 2 (Core Backend): Tasks 14-27
- Phase 3 (API Layer): Tasks 28-41
- Phase 4 (Notifications): Tasks 42-45
- Phase 5 (Frontend): Tasks 46-54
- Phase 6 (Testing): Tasks 55-64
- Phase 7 (Documentation & Release): Tasks 65-73

**Estimated Timeline:** 6-8 weeks for full iteration 1 completion

**Minimum Viable Package (MVP):** Tasks 1-41 (API functional, no frontend)

---

## Dependencies (composer.json)

```json
{
    "name": "your-org/bug-report",
    "description": "Laravel bug reporting package with API-first design",
    "type": "library",
    "license": "MIT",
    "require": {
        "php": "^8.4",
        "illuminate/support": "^10.0|^11.0",
        "illuminate/database": "^10.0|^11.0",
        "illuminate/mail": "^10.0|^11.0",
        "illuminate/http": "^10.0|^11.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0|^11.0",
        "orchestra/testbench": "^8.0|^9.0",
        "mockery/mockery": "^1.6",
        "laravel/pint": "^1.0"
    },
    "autoload": {
        "psr-4": {
            "YourOrg\\BugReport\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "YourOrg\\BugReport\\Tests\\": "tests/"
        }
    },
    "scripts": {
        "test": "vendor/bin/phpunit",
        "format": "vendor/bin/pint"
    },
    "extra": {
        "laravel": {
            "providers": [
                "YourOrg\\BugReport\\BugReportServiceProvider"
            ]
        }
    }
}
```

---

## Risk Management

### Potential Risks & Mitigations

| Risk | Impact | Mitigation |
|------|--------|-----------|
| Large file uploads crash server | High | File size limits (5MB default), validation before storage |
| Spam submissions | Medium | Rate limiting (10/hour default), authenticated users only |
| Storage costs for hosted images | Medium | Configurable retention policies (future), compression (future) |
| Email delivery failures | Medium | Queue emails, provide fallback configuration, test command |
| XSS vulnerabilities | High | Sanitize all user input, use v-text in Vue components |
| Foreign key constraint violations | Medium | Proper migration design, MySQL testing, no cascade deletes |
| Package conflicts with consuming app | Medium | Prefixed tables, namespaced classes, configurable routes |
| Incompatibility with Laravel versions | Medium | Test on both 10 and 11, document requirements clearly |

---

## Next Steps

With this revised plan, we now have:

✅ **73 atomic tasks** broken down into manageable chunks
✅ **Clear scope** for iteration 1 vs future iterations
✅ **Authentication/authorization** delegated to consuming app
✅ **API-first design** with optional frontend components
✅ **Event system** for extensibility
✅ **Complete file structure** defined
✅ **Testing strategy** with MySQL
✅ **CI/CD pipeline** specification
✅ **Security considerations** addressed
✅ **Risk mitigation** strategies

**Ready to begin development!** Start with Phase 1, Task 1: Initialize package structure.

---

This plan provides a complete, actionable roadmap for developing the bug report package. Each atomic task can be tackled independently and tracked for progress.
