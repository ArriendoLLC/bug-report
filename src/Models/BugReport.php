<?php

namespace Arriendo\BugReport\Models;

use Arriendo\BugReport\Enums\BugReportStatus;
use Arriendo\BugReport\Events\BugReportCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BugReport extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bug_report_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'url',
        'status',
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
        'status' => BugReportStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (BugReport $bugReport) {
            event(new BugReportCreated($bugReport));
        });
    }

    /**
     * Get the user who created the bug report.
     */
    public function user(): BelongsTo
    {
        $userModel = config('bug-report.user_model', 'App\\Models\\User');
        return $this->belongsTo($userModel, 'user_id');
    }

    /**
     * Get all attachments for the bug report.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(BugReportAttachment::class, 'bug_report_id');
    }

    /**
     * Get all comments for the bug report.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(BugReportComment::class, 'bug_report_id');
    }

    /**
     * Scope a query to only include reports with a specific status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param BugReportStatus $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, BugReportStatus $status)
    {
        return $query->where('status', $status->value);
    }

    /**
     * Scope a query to only include reports from a specific user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the user's name who created the report.
     */
    public function getUserNameAttribute(): string
    {
        return $this->user?->name ?? 'Unknown User';
    }

    /**
     * Get the formatted created at date.
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at?->format('M d, Y H:i') ?? '';
    }
}
