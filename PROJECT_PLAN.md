# Bug Report Package - Development Plan

## Project Overview

An open-source Laravel Composer package that provides out-of-the-box bug reporting functionality for Laravel applications. Built with PHP 8.4, fully tested, and designed with security as a priority.

## Core Requirements

### Technical Stack
- **PHP Version**: 8.4
- **Target Framework**: Laravel 10+
- **Frontend**: API-first with Vue 3 default views
- **Storage**: Configurable (filesystem default, S3/bucket support)
- **Authentication**: Laravel's built-in auth system
- **Database**: Prefixed tables (`bug_report_*`)

### Security Considerations
- No direct user input to database (use Eloquent ORM with proper mass assignment protection)
- File upload validation (type, size, mime-type checking)
- CSRF protection on all forms
- XSS prevention in user-submitted content
- Rate limiting on bug submission endpoints
- Authenticated users only (prevents automated bot spam)
- SQL injection prevention through parameter binding
- Sanitization of user-generated content before display

---

## Iteration 1: Core Functionality

### 1. Package Structure & Installation

**Tasks:**
- Initialize Composer package structure with proper PSR-4 autoloading
- Create ServiceProvider for package registration
- Implement artisan install command (`php artisan bug-report:install`)
- Create configuration file structure (`config/bug-report.php`)
- Design migration files with `bug_report_` prefix
- Create publishable assets (migrations, config, views, Vue components)

**Deliverables:**
- `/src` directory with ServiceProvider
- `/config/bug-report.php` with all configuration options
- `/database/migrations` for package tables
- Artisan command to publish migrations and assets
- `composer.json` with proper dependencies and autoload configuration

---

### 2. Database Schema Design

**Tables to Create:**

#### `bug_report_reports`
- `id` (primary key)
- `user_id` (foreign key to users table)
- `title` (string, required)
- `description` (text, required)
- `status` (enum: new, in_progress, resolved, closed)
- `url` (string, captured automatically from referrer)
- `priority` (enum: low, medium, high - nullable, for future use)
- `timestamps`
- `deleted_at` (soft deletes)

#### `bug_report_attachments`
- `id` (primary key)
- `bug_report_id` (foreign key to bug_report_reports)
- `file_path` (string)
- `file_name` (string)
- `file_type` (string)
- `file_size` (integer, bytes)
- `timestamps`

#### `bug_report_comments`
- `id` (primary key)
- `bug_report_id` (foreign key to bug_report_reports)
- `user_id` (foreign key to users table)
- `comment` (text, required)
- `is_internal` (boolean, for admin-only notes)
- `timestamps`
- `deleted_at` (soft deletes)

**Indexes:**
- Index on `bug_report_reports.status`
- Index on `bug_report_reports.user_id`
- Index on `bug_report_reports.created_at`
- Composite index on `bug_report_comments.bug_report_id`

---

### 3. Configuration System

**`config/bug-report.php` Structure:**

```php
return [
    // Route configuration
    'route_prefix' => env('BUG_REPORT_ROUTE_PREFIX', 'bug-reports'),
    'middleware' => ['web', 'auth'],

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
            'video/mp4',
            'video/webm',
        ],
        'max_files_per_report' => env('BUG_REPORT_MAX_FILES', 5),
    ],

    // Email notification configuration
    'notifications' => [
        'enabled' => env('BUG_REPORT_NOTIFICATIONS_ENABLED', true),
        'recipients' => env('BUG_REPORT_NOTIFICATION_EMAIL', ''),
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

    // Status options (configurable for future expansion)
    'statuses' => [
        'new' => 'New',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ],

    // Permissions (for future role-based access)
    'permissions' => [
        'view_reports' => 'bug-report.view',
        'create_reports' => 'bug-report.create',
        'update_reports' => 'bug-report.update',
        'delete_reports' => 'bug-report.delete',
        'comment_reports' => 'bug-report.comment',
    ],
];
```

---

### 4. Models & Eloquent Relationships

**Models to Create:**

#### `BugReport` Model
```php
- Relationships:
  - belongsTo(User::class, 'user_id')
  - hasMany(BugReportAttachment::class)
  - hasMany(BugReportComment::class)
- Fillable: title, description, url, status
- Casts: status as enum
- Appends: formatted_created_at, user_name
- Soft deletes enabled
```

#### `BugReportAttachment` Model
```php
- Relationships:
  - belongsTo(BugReport::class)
- Fillable: file_path, file_name, file_type, file_size
- Appends: file_url (uses Storage facade)
- Deletes file on model deletion (model event)
```

#### `BugReportComment` Model
```php
- Relationships:
  - belongsTo(BugReport::class)
  - belongsTo(User::class)
- Fillable: comment, is_internal
- Soft deletes enabled
- Appends: user_name, formatted_created_at
```

---

### 5. API Endpoints (RESTful)

**Route Group:** `/api/{route_prefix}`

#### Public Routes (authenticated users)
- `POST /api/bug-reports` - Create new bug report
- `POST /api/bug-reports/{id}/attachments` - Upload attachment
- `GET /api/bug-reports` - List user's own reports
- `GET /api/bug-reports/{id}` - View single report
- `POST /api/bug-reports/{id}/comments` - Add comment
- `PUT /api/bug-reports/{id}/comments/{commentId}` - Update own comment
- `DELETE /api/bug-reports/{id}/comments/{commentId}` - Delete own comment

#### Admin Routes (configurable permission middleware)
- `GET /api/bug-reports/admin` - List all reports with filtering
- `PUT /api/bug-reports/{id}/status` - Update report status
- `DELETE /api/bug-reports/{id}` - Delete report
- `GET /api/bug-reports/stats` - Dashboard statistics

**Request Validation:**
- All endpoints use Form Request classes
- File upload validation (mime types, size, count)
- XSS sanitization on text inputs
- Rate limiting middleware on create endpoints

---

### 6. Frontend Components (Vue 3)

**Components to Build:**

#### Admin Dashboard View
- **File**: `resources/js/components/BugReportDashboard.vue`
- **Features**:
  - Filterable/sortable table of all bug reports
  - Status badges with color coding
  - Search functionality
  - Pagination
  - Quick status update dropdown
  - Click to view detail

#### Bug Report Detail View
- **File**: `resources/js/components/BugReportDetail.vue`
- **Features**:
  - Display all report information
  - Show attachments (images inline, others as download links)
  - Status update UI (admin only)
  - Comment thread display
  - Add new comment form

#### Bug Report Create Form
- **File**: `resources/js/components/BugReportCreate.vue`
- **Features**:
  - Title and description fields
  - File upload with preview
  - Auto-capture of current URL
  - Client-side validation
  - Loading states

#### Comment Component
- **File**: `resources/js/components/BugReportComments.vue`
- **Features**:
  - Display comment thread
  - Add new comment
  - Edit own comments
  - Delete own comments
  - Admin badge for internal comments

#### Shared UI Components
- Status badge component
- File upload component with preview
- Pagination component
- Loading spinner component

---

### 7. Notification System

**Email Notifications:**

#### `BugReportCreatedNotification`
- Sent to configured admin email(s)
- Contains: Title, Description, Reporter name, URL, Link to view report
- Uses configurable mail settings or falls back to package mail config

#### `BugReportStatusChangedNotification`
- Sent to bug reporter
- Contains: Status change details, Admin comment (if any), Link to view report

#### `BugReportCommentedNotification`
- Sent to bug reporter when admin adds comment
- Contains: Comment text, Link to view report

**Email Provider Fallback:**
- Check if Laravel's default mail is configured
- If not, use package-specific mail configuration
- Default to 'log' driver if nothing configured (for development)

---

### 8. Artisan Commands

#### `bug-report:install`
```bash
php artisan bug-report:install
```
- Publishes configuration file
- Publishes migrations (doesn't run them)
- Publishes Vue components
- Publishes views (optional Blade views)
- Displays next steps (run migrations, configure email, etc.)

#### `bug-report:test-email`
```bash
php artisan bug-report:test-email {email}
```
- Sends test email to verify configuration
- Useful for debugging email setup

---

### 9. Security Implementation Checklist

- [ ] Validate all file uploads (mime type, size, extension)
- [ ] Sanitize HTML in descriptions and comments (use HTMLPurifier or similar)
- [ ] Implement CSRF protection on all forms
- [ ] Use Eloquent query builder to prevent SQL injection
- [ ] Mass assignment protection on all models
- [ ] Rate limiting on bug submission endpoints
- [ ] Validate and sanitize URL captured from referrer
- [ ] Implement file storage access controls
- [ ] Add authentication middleware to all routes
- [ ] XSS prevention in Vue components (use v-text for user content)
- [ ] Implement file deletion on attachment/report removal
- [ ] Validate user owns resource before allowing updates/deletes

---

### 10. Testing Strategy

**Test Coverage Required:**

#### Unit Tests
- Model relationships and methods
- Configuration loading
- File validation logic
- Email notification content
- Status enum validation

#### Feature Tests
- Bug report creation flow
- File upload and storage
- Comment CRUD operations
- Status updates
- Email sending
- Authorization checks (user can only see/edit own reports)
- Admin access to all reports

#### Integration Tests
- Full bug submission flow with attachments
- Email notification delivery
- File storage across different disk configurations
- Rate limiting functionality

#### Browser Tests (Laravel Dusk - optional for Iteration 1)
- Complete user workflow
- Admin dashboard interaction
- File upload UI

**Testing Tools:**
- PHPUnit for unit and feature tests
- Pest PHP (alternative, more expressive syntax)
- Laravel's built-in testing helpers
- Mockery for mocking email sends

---

## Iteration 2: Advanced Features (Future)

These features will be planned in detail later but should inform architectural decisions:

### Third-Party Integrations
- Jira integration via API
- Linear integration via API
- Abstract integration layer (adapter pattern)
- Webhook support for custom integrations

### AI Analysis
- Integration with OpenAI/Claude API
- Automatic categorization of bugs
- Severity assessment
- Duplicate detection

### Spam Filtering
- Configurable spam detection rules
- Machine learning classification
- Akismet integration (optional)
- Manual spam marking with learning

### User Journey Tracking
- JavaScript tracking library
- Session recording integration
- Breadcrumb trail of user actions
- Performance metrics capture

---

## Development Workflow

### Phase 1: Foundation (Week 1-2)
1. Initialize package structure
2. Create ServiceProvider and configuration
3. Design and create migrations
4. Build Eloquent models with relationships
5. Create install command

### Phase 2: Backend API (Week 2-3)
6. Build API controllers and routes
7. Implement Form Request validation
8. Add rate limiting middleware
9. Implement file upload handling
10. Write unit and feature tests for API

### Phase 3: Frontend (Week 3-4)
11. Build Vue 3 components
12. Create API service layer in JavaScript
13. Implement routing (if SPA) or integrate with Blade
14. Add client-side validation
15. Styling and UX polish

### Phase 4: Notifications (Week 4)
16. Build email notification classes
17. Implement mail configuration logic
18. Create email templates
19. Test email sending and fallback

### Phase 5: Testing & Documentation (Week 5)
20. Complete test coverage
21. Write README with installation and usage
22. Create CONTRIBUTING.md
23. Add inline code documentation
24. Create example implementation guide

### Phase 6: Release Preparation (Week 6)
25. Security audit
26. Performance testing
27. Cross-Laravel version testing
28. Prepare Packagist submission
29. Tag v1.0.0 release

---

## File Structure

```
bug-report/
├── config/
│   └── bug-report.php
├── database/
│   └── migrations/
│       ├── create_bug_report_reports_table.php
│       ├── create_bug_report_attachments_table.php
│       └── create_bug_report_comments_table.php
├── resources/
│   ├── js/
│   │   └── components/
│   │       ├── BugReportDashboard.vue
│   │       ├── BugReportDetail.vue
│   │       ├── BugReportCreate.vue
│   │       └── BugReportComments.vue
│   ├── views/
│   │   └── emails/
│   │       ├── bug-created.blade.php
│   │       ├── status-changed.blade.php
│   │       └── comment-added.blade.php
│   └── lang/
│       └── en/
│           └── bug-report.php
├── routes/
│   ├── api.php
│   └── web.php
├── src/
│   ├── BugReportServiceProvider.php
│   ├── Console/
│   │   └── Commands/
│   │       ├── InstallCommand.php
│   │       └── TestEmailCommand.php
│   ├── Models/
│   │   ├── BugReport.php
│   │   ├── BugReportAttachment.php
│   │   └── BugReportComment.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── BugReportController.php
│   │   │   ├── BugReportAdminController.php
│   │   │   ├── AttachmentController.php
│   │   │   └── CommentController.php
│   │   ├── Requests/
│   │   │   ├── StoreBugReportRequest.php
│   │   │   ├── UpdateBugReportRequest.php
│   │   │   ├── StoreCommentRequest.php
│   │   │   └── UpdateStatusRequest.php
│   │   └── Middleware/
│   │       └── BugReportRateLimit.php
│   ├── Notifications/
│   │   ├── BugReportCreatedNotification.php
│   │   ├── BugReportStatusChangedNotification.php
│   │   └── BugReportCommentedNotification.php
│   ├── Services/
│   │   ├── BugReportService.php
│   │   ├── AttachmentService.php
│   │   └── EmailService.php
│   └── Enums/
│       └── BugReportStatus.php
├── tests/
│   ├── Unit/
│   │   ├── Models/
│   │   └── Services/
│   ├── Feature/
│   │   ├── BugReportTest.php
│   │   ├── AttachmentTest.php
│   │   ├── CommentTest.php
│   │   └── NotificationTest.php
│   └── TestCase.php
├── composer.json
├── README.md
├── LICENSE
└── CHANGELOG.md
```

---

## Key Design Decisions

### 1. API-First Approach
- Allows flexibility for different frontend frameworks
- Enables mobile app integration in future
- Provides default Vue 3 implementation for immediate use

### 2. Configurable Everything
- Storage driver (local, S3, etc.)
- Email provider with fallback
- Route prefix and middleware
- User model (for compatibility with different auth setups)
- Statuses (extensible for future needs)

### 3. Security by Default
- Authenticated users only
- Rate limiting out of the box
- File validation and sanitization
- CSRF and XSS protection
- No direct database queries from user input

### 4. Extensibility Points
- Service classes for business logic (easily extended)
- Event dispatching for custom hooks
- Interface-based design for swappable components
- Config-driven behavior

### 5. Email Flexibility
- Works with or without Laravel mail configuration
- Fallback to package-specific settings
- Log driver default for development
- Easy testing command

---

## Success Metrics for Iteration 1

- [ ] Package installable via Composer
- [ ] Install command successfully publishes all assets
- [ ] Migrations run without errors
- [ ] Users can submit bug reports with attachments
- [ ] Admins can view all reports in dashboard
- [ ] Admins can update report statuses
- [ ] Comments can be added, edited, and deleted
- [ ] Email notifications sent on new reports
- [ ] 90%+ test coverage
- [ ] All security considerations addressed
- [ ] Documentation complete and clear
- [ ] Works on Laravel 10 and 11

---

## Documentation Requirements

### README.md Sections
1. Installation instructions
2. Configuration guide
3. Usage examples
4. API documentation
5. Frontend component usage
6. Email setup guide
7. Troubleshooting
8. Contributing guidelines
9. License information

### Inline Documentation
- PHPDoc blocks for all public methods
- Comments for complex logic
- Type hints throughout
- Explanation of security measures

---

## Dependencies (composer.json)

```json
{
    "require": {
        "php": "^8.4",
        "illuminate/support": "^10.0|^11.0",
        "illuminate/database": "^10.0|^11.0",
        "illuminate/mail": "^10.0|^11.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "orchestra/testbench": "^8.0|^9.0",
        "mockery/mockery": "^1.6"
    }
}
```

---

## Open Questions for Development

1. Should we include a pre-built admin UI or only provide API + components?
   - **Recommendation**: Provide both API and default Vue dashboard

2. How should we handle file cleanup for deleted reports?
   - **Recommendation**: Model event listeners on deletion

3. Should we support anonymous bug reports in future?
   - **Recommendation**: Add to iteration 2 with captcha requirement

4. Versioning strategy for breaking changes?
   - **Recommendation**: Semantic versioning, document upgrade paths

---

## Risk Management

### Potential Risks & Mitigations

| Risk | Impact | Mitigation |
|------|--------|-----------|
| Large file uploads crash server | High | Implement file size limits, chunked uploads |
| Spam submissions | Medium | Rate limiting, authenticated users only |
| Storage costs for hosted images | Medium | Configurable retention policies, compression |
| Email delivery failures | Medium | Queue emails, provide fallback configuration |
| XSS vulnerabilities | High | Sanitize all user input, use v-text in Vue |
| Incompatibility with some Laravel versions | Medium | Test on multiple versions, document requirements |

---

This plan provides a complete roadmap for developing the bug report package. Each section can be expanded into detailed tickets/issues for development tracking.
