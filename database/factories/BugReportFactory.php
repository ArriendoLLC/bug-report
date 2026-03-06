<?php

namespace Arriendo\BugReport\Database\Factories;

use Arriendo\BugReport\Enums\BugReportStatus;
use Arriendo\BugReport\Models\BugReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Arriendo\BugReport\Models\BugReport>
 */
class BugReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BugReport::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userModel = config('bug-report.user_model', 'App\\Models\\User');

        return [
            'user_id' => $userModel::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraphs(3, true),
            'url' => fake()->url(),
            'status' => fake()->randomElement(BugReportStatus::values()),
        ];
    }

    /**
     * Indicate that the bug report is new.
     */
    public function new(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BugReportStatus::New->value,
        ]);
    }

    /**
     * Indicate that the bug report is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BugReportStatus::InProgress->value,
        ]);
    }

    /**
     * Indicate that the bug report is resolved.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BugReportStatus::Resolved->value,
        ]);
    }

    /**
     * Indicate that the bug report is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BugReportStatus::Closed->value,
        ]);
    }

    /**
     * Indicate that the bug report has no URL.
     */
    public function withoutUrl(): static
    {
        return $this->state(fn (array $attributes) => [
            'url' => null,
        ]);
    }
}
