<?php

namespace Arriendo\BugReport\Services;

use Arriendo\BugReport\Exceptions\InvalidFileUploadException;
use Arriendo\BugReport\Models\BugReport;
use Arriendo\BugReport\Models\BugReportAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class AttachmentService
{
    /**
     * Validate uploaded files against configuration rules.
     *
     * @param  array  $files
     *
     * @throws InvalidFileUploadException
     */
    public function validateFiles(array $files): void
    {
        $maxFiles = config('bug-report.storage.max_files_per_report', 5);
        $maxFileSize = config('bug-report.storage.max_file_size', 5120); // KB
        $allowedMimeTypes = config('bug-report.storage.allowed_mime_types', []);

        // Check number of files
        if (count($files) > $maxFiles) {
            throw new InvalidFileUploadException("Maximum of {$maxFiles} files allowed per report");
        }

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                throw new InvalidFileUploadException('Invalid file upload');
            }

            // Check file size (convert to KB)
            $fileSizeKB = $file->getSize() / 1024;
            if ($fileSizeKB > $maxFileSize) {
                throw new InvalidFileUploadException(
                    "File '{$file->getClientOriginalName()}' exceeds maximum size of {$maxFileSize}KB"
                );
            }

            // Check mime type
            $mimeType = $file->getMimeType();
            if (! in_array($mimeType, $allowedMimeTypes)) {
                throw new InvalidFileUploadException(
                    "File type '{$mimeType}' is not allowed for file '{$file->getClientOriginalName()}'"
                );
            }
        }
    }

    /**
     * Store files and create attachment records.
     *
     * @param  array  $files
     * @param  BugReport  $report
     * @return Collection
     *
     * @throws InvalidFileUploadException
     */
    public function storeFiles(array $files, BugReport $report): Collection
    {
        $this->validateFiles($files);

        $attachments = collect();
        $disk = config('bug-report.storage.disk', 'local');
        $path = config('bug-report.storage.path', 'bug-reports');

        foreach ($files as $file) {
            // Generate unique filename
            $filename = time().'_'.uniqid().'_'.$file->getClientOriginalName();

            // Store file
            $filePath = $file->storeAs($path, $filename, $disk);

            // Create attachment record
            $attachment = BugReportAttachment::create([
                'bug_report_id' => $report->id,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $attachments->push($attachment);
        }

        return $attachments;
    }

    /**
     * Delete file from storage and remove attachment record.
     *
     * @param  BugReportAttachment  $attachment
     */
    public function deleteFile(BugReportAttachment $attachment): void
    {
        $disk = config('bug-report.storage.disk', 'local');

        // Delete file from storage if it exists
        if (Storage::disk($disk)->exists($attachment->file_path)) {
            Storage::disk($disk)->delete($attachment->file_path);
        }

        // Delete attachment record
        $attachment->delete();
    }
}
