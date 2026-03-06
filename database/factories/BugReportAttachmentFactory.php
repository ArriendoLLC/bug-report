<?php

namespace Arriendo\BugReport\Database\Factories;

use Arriendo\BugReport\Models\BugReport;
use Arriendo\BugReport\Models\BugReportAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Arriendo\BugReport\Models\BugReportAttachment>
 */
class BugReportAttachmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BugReportAttachment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'video/mp4',
            'application/pdf',
        ];

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'video/mp4' => 'mp4',
            'application/pdf' => 'pdf',
        ];

        $mimeType = fake()->randomElement($mimeTypes);
        $extension = $extensions[$mimeType];
        $fileName = fake()->word() . '.' . $extension;
        $filePath = config('bug-report.storage.path', 'bug-reports') . '/' . fake()->uuid() . '.' . $extension;

        return [
            'bug_report_id' => BugReport::factory(),
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $mimeType,
            'file_size' => fake()->numberBetween(1024, 5242880), // 1KB to 5MB
        ];
    }

    /**
     * Indicate that the attachment is an image.
     */
    public function image(): static
    {
        return $this->state(function (array $attributes) {
            $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $mimeType = fake()->randomElement($imageTypes);
            $extension = match ($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            };

            return [
                'file_type' => $mimeType,
                'file_path' => config('bug-report.storage.path', 'bug-reports') . '/' . fake()->uuid() . '.' . $extension,
                'file_name' => fake()->word() . '.' . $extension,
            ];
        });
    }

    /**
     * Indicate that the attachment is a video.
     */
    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_type' => 'video/mp4',
            'file_path' => config('bug-report.storage.path', 'bug-reports') . '/' . fake()->uuid() . '.mp4',
            'file_name' => fake()->word() . '.mp4',
            'file_size' => fake()->numberBetween(1048576, 10485760), // 1MB to 10MB
        ]);
    }

    /**
     * Indicate that the attachment is a PDF.
     */
    public function pdf(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_type' => 'application/pdf',
            'file_path' => config('bug-report.storage.path', 'bug-reports') . '/' . fake()->uuid() . '.pdf',
            'file_name' => fake()->word() . '.pdf',
            'file_size' => fake()->numberBetween(102400, 2097152), // 100KB to 2MB
        ]);
    }
}
