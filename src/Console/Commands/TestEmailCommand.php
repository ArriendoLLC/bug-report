<?php

namespace Arriendo\BugReport\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bug-report:test-email {email : The email address to send the test to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify bug report notification configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address provided.');
            return self::FAILURE;
        }

        $this->info('Checking mail configuration...');

        try {
            // Check if mail is configured
            $mailDriver = config('mail.default');
            $this->line("Mail driver: {$mailDriver}");

            // Send test email
            $this->info("Sending test email to {$email}...");

            Mail::raw(
                $this->getTestEmailBody(),
                function ($message) use ($email) {
                    $message->to($email)
                        ->subject('Bug Report Package - Test Email')
                        ->from(
                            config('bug-report.notifications.from_address', config('mail.from.address')),
                            config('bug-report.notifications.from_name', config('mail.from.name'))
                        );
                }
            );

            $this->newLine();
            $this->info('✅ Test email sent successfully!');
            $this->line("Check your inbox at: {$email}");
            $this->newLine();
            $this->comment('If you don\'t receive the email, check:');
            $this->line('- Your spam/junk folder');
            $this->line('- Mail configuration in .env file');
            $this->line('- Mail logs: storage/logs/laravel.log');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Failed to send test email.');
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            $this->comment('Please check:');
            $this->line('1. Your mail configuration in .env:');
            $this->line('   - MAIL_MAILER');
            $this->line('   - MAIL_HOST');
            $this->line('   - MAIL_PORT');
            $this->line('   - MAIL_USERNAME');
            $this->line('   - MAIL_PASSWORD');
            $this->newLine();
            $this->line('2. Or configure Bug Report specific mail settings:');
            $this->line('   - BUG_REPORT_MAIL_DRIVER');
            $this->line('   - BUG_REPORT_MAIL_HOST');
            $this->line('   - BUG_REPORT_MAIL_PORT');
            $this->line('   - BUG_REPORT_MAIL_USERNAME');
            $this->line('   - BUG_REPORT_MAIL_PASSWORD');

            return self::FAILURE;
        }
    }

    /**
     * Get the test email body content.
     */
    protected function getTestEmailBody(): string
    {
        return <<<'TEXT'

Bug Report Package - Test Email
================================

This is a test email from the Bug Report package.

If you're receiving this email, your mail configuration is working correctly!

Configuration Details:
- Package: Arriendo Bug Report
- Environment: {{ app_env }}
- Timestamp: {{ timestamp }}

Next Steps:
1. Configure notification recipients in your .env file:
   BUG_REPORT_NOTIFICATION_EMAILS=admin@example.com,team@example.com

2. Ensure your application routes are properly configured with authentication middleware

3. Start receiving bug reports from your users!

For more information, visit: https://github.com/arriendo/bug-report

---
This email was sent as a test. No action is required.

TEXT;
    }
}
