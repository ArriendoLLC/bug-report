<?php

namespace Arriendo\BugReport\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bug-report:install
                            {--force : Overwrite existing files}
                            {--with-components : Publish Vue components}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Bug Report package';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Installing Bug Report Package...');
        $this->newLine();

        // Publish configuration
        $this->publishConfig();

        // Publish migrations
        $this->publishMigrations();

        // Publish translations
        $this->publishTranslations();

        // Publish email views
        $this->publishViews();

        // Optionally publish Vue components
        if ($this->option('with-components')) {
            $this->publishComponents();
        }

        // Register routes
        $this->registerRoutes();

        $this->newLine();
        $this->info('✅ Bug Report package installed successfully!');
        $this->newLine();

        $this->displayNextSteps();

        return self::SUCCESS;
    }

    /**
     * Publish the configuration file.
     */
    protected function publishConfig(): void
    {
        $this->info('Publishing configuration...');

        $params = [
            '--tag' => 'bug-report-config',
        ];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }

    /**
     * Publish the migration files.
     */
    protected function publishMigrations(): void
    {
        $this->info('Publishing migrations...');

        $params = [
            '--tag' => 'bug-report-migrations',
        ];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }

    /**
     * Publish translation files.
     */
    protected function publishTranslations(): void
    {
        $this->info('Publishing translations...');

        $params = [
            '--tag' => 'bug-report-translations',
        ];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }

    /**
     * Publish email view files.
     */
    protected function publishViews(): void
    {
        $this->info('Publishing email views...');

        $params = [
            '--tag' => 'bug-report-views',
        ];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }

    /**
     * Publish Vue component files.
     */
    protected function publishComponents(): void
    {
        $this->info('Publishing Vue components...');

        $params = [
            '--tag' => 'bug-report-components',
        ];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }

    /**
     * Register API routes in the application's routes/api.php file.
     */
    protected function registerRoutes(): void
    {
        $this->info('Registering API routes...');

        $apiRoutesPath = base_path('routes/api.php');

        if (!File::exists($apiRoutesPath)) {
            $this->error('routes/api.php not found. Please create it first.');
            return;
        }

        $routeContent = File::get($apiRoutesPath);

        // Check if routes are already registered
        if (str_contains($routeContent, 'Bug Report Package Routes')) {
            $this->warn('Routes already registered in routes/api.php');
            return;
        }

        $routesToAppend = $this->getRouteStub();

        File::append($apiRoutesPath, $routesToAppend);

        $this->info('✅ Routes registered in routes/api.php');
    }

    /**
     * Get the route stub content.
     */
    protected function getRouteStub(): string
    {
        return <<<'PHP'


// =============================================================================
// Bug Report Package Routes
// =============================================================================
// These routes are automatically registered by the Bug Report package.
// You can customize middleware as needed for your application.
//
// IMPORTANT: Admin routes should be protected with appropriate middleware
// (e.g., 'auth:sanctum', 'role:admin', etc.)
// =============================================================================

use Arriendo\BugReport\Http\Controllers\BugReportController;
use Arriendo\BugReport\Http\Controllers\BugReportAdminController;
use Arriendo\BugReport\Http\Controllers\CommentController;

// User Routes (authenticated users can submit bug reports)
Route::middleware(['auth:sanctum'])->prefix('bug-reports')->group(function () {
    Route::post('/', [BugReportController::class, 'store'])->name('bug-reports.store');
});

// Admin Routes (require admin middleware - customize as needed)
// Example: Route::middleware(['auth:sanctum', 'role:admin'])->prefix('bug-reports')->group(function () {
Route::middleware(['auth:sanctum'])->prefix('bug-reports')->group(function () {
    // List and view reports
    Route::get('/', [BugReportAdminController::class, 'index'])->name('bug-reports.index');
    Route::get('/{id}', [BugReportAdminController::class, 'show'])->name('bug-reports.show');

    // Update and delete reports
    Route::put('/{id}/status', [BugReportAdminController::class, 'updateStatus'])->name('bug-reports.update-status');
    Route::delete('/{id}', [BugReportAdminController::class, 'destroy'])->name('bug-reports.destroy');

    // Comments
    Route::post('/{id}/comments', [CommentController::class, 'store'])->name('bug-reports.comments.store');
    Route::put('/{reportId}/comments/{commentId}', [CommentController::class, 'update'])->name('bug-reports.comments.update');
    Route::delete('/{reportId}/comments/{commentId}', [CommentController::class, 'destroy'])->name('bug-reports.comments.destroy');
});

PHP;
    }

    /**
     * Display next steps for the user.
     */
    protected function displayNextSteps(): void
    {
        $this->comment('Next Steps:');
        $this->line('1. Run migrations: php artisan migrate');
        $this->line('2. Configure your .env file with bug report settings:');
        $this->line('   - BUG_REPORT_NOTIFICATION_EMAILS=admin@example.com,team@example.com');
        $this->line('   - BUG_REPORT_STORAGE_DISK=s3 (optional)');
        $this->line('   - BUG_REPORT_RATE_LIMIT=10 (optional)');
        $this->newLine();
        $this->line('3. Update routes/api.php to add admin middleware to admin routes');
        $this->line('   Example: Route::middleware([\'auth:sanctum\', \'role:admin\'])');
        $this->newLine();
        $this->line('4. (Optional) Test email configuration:');
        $this->line('   php artisan bug-report:test-email your@email.com');
        $this->newLine();

        if ($this->option('with-components')) {
            $this->line('5. Vue components published to resources/js/vendor/bug-report');
            $this->line('   - Add to your vite.config.js if needed');
            $this->line('   - Import and register components in your app');
            $this->newLine();
        }

        $this->info('📚 Documentation: https://github.com/arriendo/bug-report');
    }
}
