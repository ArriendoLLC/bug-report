<?php

namespace Arriendo\BugReport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BugReportAttachment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bug_report_attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'bug_report_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    /**
     * The attributes that should be guarded.
     *
     * @var array<string>
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Delete the file from storage when the model is deleted
        static::deleting(function (BugReportAttachment $attachment) {
            $disk = config('bug-report.storage.disk', 'local');
            if (Storage::disk($disk)->exists($attachment->file_path)) {
                Storage::disk($disk)->delete($attachment->file_path);
            }
        });
    }

    /**
     * Get the bug report that owns the attachment.
     */
    public function bugReport(): BelongsTo
    {
        return $this->belongsTo(BugReport::class, 'bug_report_id');
    }

    /**
     * Get the full URL to the file.
     */
    public function getFileUrlAttribute(): string
    {
        $disk = config('bug-report.storage.disk', 'local');
        return Storage::disk($disk)->url($this->file_path);
    }

    /**
     * Get the human-readable file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Check if the attachment is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->file_type, 'image/');
    }

    /**
     * Check if the attachment is a video.
     */
    public function isVideo(): bool
    {
        return str_starts_with($this->file_type, 'video/');
    }

    /**
     * Check if the attachment is a PDF.
     */
    public function isPdf(): bool
    {
        return $this->file_type === 'application/pdf';
    }
}
