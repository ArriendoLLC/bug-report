<?php

namespace Arriendo\BugReport\Services;

use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Check if mail configuration is valid.
     *
     * @return bool
     */
    public function checkMailConfiguration(): bool
    {
        // Check if Laravel mail is configured
        $defaultDriver = config('mail.default');

        if ($defaultDriver && $defaultDriver !== 'array' && $defaultDriver !== 'log') {
            return true;
        }

        // Check if package-specific mail config is available
        $packageDriver = config('bug-report.mail_provider.driver');

        if ($packageDriver && $packageDriver !== 'log' && $packageDriver !== 'array') {
            return true;
        }

        return false;
    }

    /**
     * Get the mailer instance (Laravel default or package-specific).
     *
     * @return Mailer
     */
    public function getMailer(): Mailer
    {
        // If Laravel mail is configured, use it
        if (config('mail.default') && config('mail.default') !== 'array') {
            return Mail::mailer();
        }

        // Otherwise, use package-specific mail configuration
        $driver = config('bug-report.mail_provider.driver', 'log');

        // Create temporary mail config for package
        $packageConfig = [
            'transport' => $driver,
            'host' => config('bug-report.mail_provider.host'),
            'port' => config('bug-report.mail_provider.port'),
            'username' => config('bug-report.mail_provider.username'),
            'password' => config('bug-report.mail_provider.password'),
            'encryption' => config('bug-report.mail_provider.encryption'),
        ];

        // Set temporary config and return mailer
        Config::set('mail.mailers.bug_report_mailer', $packageConfig);

        return Mail::mailer('bug_report_mailer');
    }

    /**
     * Send a test email to verify configuration.
     *
     * @param  string  $email
     * @return bool
     */
    public function sendTest(string $email): bool
    {
        try {
            $mailer = $this->getMailer();

            $mailer->raw('This is a test email from the Bug Report package.', function ($message) use ($email) {
                $message->to($email)
                    ->subject('Bug Report Package - Test Email')
                    ->from(
                        config('bug-report.notifications.from_address', config('mail.from.address')),
                        config('bug-report.notifications.from_name', 'Bug Report System')
                    );
            });

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
