<?php

namespace Arriendo\BugReport\Database\Factories;

use Arriendo\BugReport\Models\BugReport;
use Arriendo\BugReport\Models\BugReportComment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Arriendo\BugReport\Models\BugReportComment>
 */
class BugReportCommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BugReportComment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userModel = config('bug-report.user_model', 'App\\Models\\User');

        return [
            'bug_report_id' => BugReport::factory(),
            'user_id' => $userModel::factory(),
            'comment' => fake()->paragraphs(2, true),
        ];
    }

    /**
     * Indicate that the comment is short.
     */
    public function short(): static
    {
        return $this->state(fn (array $attributes) => [
            'comment' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the comment is long.
     */
    public function long(): static
    {
        return $this->state(fn (array $attributes) => [
            'comment' => fake()->paragraphs(5, true),
        ]);
    }
}
