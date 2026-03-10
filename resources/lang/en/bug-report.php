<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bug Report Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used throughout the Bug Report package.
    | You are free to modify these language lines according to your
    | application's requirements.
    |
    */

    // General
    'title' => 'Bug Report',
    'bug_reports' => 'Bug Reports',
    'report_bug' => 'Report Bug',
    'submit' => 'Submit',
    'cancel' => 'Cancel',
    'save' => 'Save',
    'delete' => 'Delete',
    'edit' => 'Edit',
    'close' => 'Close',
    'loading' => 'Loading...',
    'no_results' => 'No results found',
    'search' => 'Search',
    'filter' => 'Filter',
    'reset' => 'Reset',

    // Report Form
    'form' => [
        'title' => 'Report a Bug',
        'title_label' => 'Title',
        'title_placeholder' => 'Brief description of the issue',
        'description_label' => 'Description',
        'description_placeholder' => 'Provide detailed information about the bug',
        'url_label' => 'URL (where the bug occurred)',
        'url_placeholder' => 'https://example.com/page',
        'attachments_label' => 'Attachments',
        'attachments_help' => 'You can upload up to :max files (max :size KB each)',
        'drop_files' => 'Drop files here or click to upload',
        'remove_file' => 'Remove file',
    ],

    // Messages
    'messages' => [
        'submit_success' => 'Bug report submitted successfully!',
        'submit_error' => 'Failed to submit bug report. Please try again.',
        'update_success' => 'Bug report updated successfully!',
        'update_error' => 'Failed to update bug report.',
        'delete_success' => 'Bug report deleted successfully!',
        'delete_error' => 'Failed to delete bug report.',
        'comment_added' => 'Comment added successfully!',
        'comment_updated' => 'Comment updated successfully!',
        'comment_deleted' => 'Comment deleted successfully!',
        'status_updated' => 'Status updated successfully!',
    ],

    // Validation
    'validation' => [
        'title_required' => 'Title is required',
        'title_max' => 'Title must not exceed :max characters',
        'description_required' => 'Description is required',
        'invalid_url' => 'Please enter a valid URL',
        'file_too_large' => 'File :filename is too large (max :max KB)',
        'file_type_not_allowed' => 'File type not allowed for :filename',
        'too_many_files' => 'Maximum :max files allowed',
    ],

    // Dashboard
    'dashboard' => [
        'title' => 'Bug Reports Dashboard',
        'all_reports' => 'All Reports',
        'search_placeholder' => 'Search by title or description...',
        'filter_by_status' => 'Filter by status',
        'all_statuses' => 'All Statuses',
        'total_reports' => 'Total Reports',
        'no_reports' => 'No bug reports found',
    ],

    // Report Detail
    'detail' => [
        'title' => 'Bug Report Details',
        'reported_by' => 'Reported by',
        'reported_at' => 'Reported at',
        'updated_at' => 'Updated at',
        'status' => 'Status',
        'description' => 'Description',
        'url' => 'URL',
        'attachments' => 'Attachments',
        'no_attachments' => 'No attachments',
        'download' => 'Download',
        'update_status' => 'Update Status',
        'delete_report' => 'Delete Report',
        'confirm_delete' => 'Are you sure you want to delete this bug report?',
    ],

    // Comments
    'comments' => [
        'title' => 'Comments',
        'add_comment' => 'Add Comment',
        'no_comments' => 'No comments yet',
        'comment_placeholder' => 'Write a comment...',
        'post_comment' => 'Post Comment',
        'edit_comment' => 'Edit Comment',
        'delete_comment' => 'Delete Comment',
        'confirm_delete' => 'Are you sure you want to delete this comment?',
        'posted_by' => 'Posted by',
        'edited' => '(edited)',
    ],

    // Status Options
    'status' => [
        'new' => 'New',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ],

    // Pagination
    'pagination' => [
        'previous' => 'Previous',
        'next' => 'Next',
        'showing' => 'Showing',
        'to' => 'to',
        'of' => 'of',
        'results' => 'results',
    ],

    // Errors
    'errors' => [
        'generic' => 'An error occurred. Please try again.',
        'not_found' => 'Bug report not found',
        'unauthorized' => 'You are not authorized to perform this action',
        'rate_limit' => 'Too many requests. Please try again later.',
        'validation' => 'Please check your input and try again',
        'network' => 'Network error. Please check your connection.',
    ],
];
