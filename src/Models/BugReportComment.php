<?php

namespace Arriendo\BugReport\Models;

use Arriendo\BugReport\Events\CommentAdded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BugReportComment extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bug_report_comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'bug_report_id',
        'user_id',
        'comment',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (BugReportComment $comment) {
            event(new CommentAdded($comment));
        });
    }

    /**
     * Get the bug report that owns the comment.
     */
    public function bugReport(): BelongsTo
    {
        return $this->belongsTo(BugReport::class, 'bug_report_id');
    }

    /**
     * Get the user who created the comment.
     */
    public function user(): BelongsTo
    {
        $userModel = config('bug-report.user_model', 'App\\Models\\User');
        return $this->belongsTo($userModel, 'user_id');
    }

    /**
     * Scope a query to only include comments for a specific bug report.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $bugReportId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForReport($query, int $bugReportId)
    {
        return $query->where('bug_report_id', $bugReportId);
    }

    /**
     * Scope a query to only include comments from a specific user.
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
     * Get the user's name who created the comment.
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

    /**
     * Get a truncated version of the comment.
     */
    public function getTruncatedCommentAttribute(): string
    {
        return str($this->comment)->limit(100);
    }
}
