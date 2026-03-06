<?php

namespace Arriendo\BugReport\Database\Seeders;

use Arriendo\BugReport\Models\BugReport;
use Arriendo\BugReport\Models\BugReportAttachment;
use Arriendo\BugReport\Models\BugReportComment;
use Illuminate\Database\Seeder;

class BugReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding bug reports...');

        // Create 20 bug reports with varying statuses
        BugReport::factory()
            ->count(5)
            ->new()
            ->has(BugReportAttachment::factory()->count(rand(1, 3)), 'attachments')
            ->has(BugReportComment::factory()->count(rand(0, 2)), 'comments')
            ->create();

        BugReport::factory()
            ->count(8)
            ->inProgress()
            ->has(BugReportAttachment::factory()->count(rand(1, 5)), 'attachments')
            ->has(BugReportComment::factory()->count(rand(1, 5)), 'comments')
            ->create();

        BugReport::factory()
            ->count(5)
            ->resolved()
            ->has(BugReportAttachment::factory()->count(rand(1, 3)), 'attachments')
            ->has(BugReportComment::factory()->count(rand(2, 8)), 'comments')
            ->create();

        BugReport::factory()
            ->count(2)
            ->closed()
            ->has(BugReportAttachment::factory()->count(rand(1, 2)), 'attachments')
            ->has(BugReportComment::factory()->count(rand(3, 10)), 'comments')
            ->create();

        $this->command->info('✅ Bug reports seeded successfully!');
        $this->command->newLine();
        $this->command->info('Total bug reports created: 20');
        $this->command->info('- New: 5');
        $this->command->info('- In Progress: 8');
        $this->command->info('- Resolved: 5');
        $this->command->info('- Closed: 2');
    }
}
