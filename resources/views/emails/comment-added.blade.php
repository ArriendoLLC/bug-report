<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Comment on Bug Report</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            border-bottom: 3px solid #9561e2;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        h1 {
            color: #9561e2;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 25px;
        }
        .label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }
        .content {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            border-left: 3px solid #9561e2;
        }
        .comment-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            border-left: 4px solid #9561e2;
            margin-top: 10px;
        }
        .commenter {
            font-weight: bold;
            color: #9561e2;
            margin-bottom: 10px;
        }
        .comment-text {
            color: #333;
            line-height: 1.8;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #9561e2;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💬 New Comment on Your Bug Report</h1>
            <div class="meta">
                Posted on {{ $comment->created_at->format('M d, Y \a\t g:i A') }}
            </div>
        </div>

        <div class="section">
            <span class="label">Report Title:</span>
            <div class="content">
                {{ $bugReport->title }}
            </div>
        </div>

        <div class="section">
            <span class="label">Comment:</span>
            <div class="comment-box">
                <div class="commenter">
                    {{ $commenter->name ?? $commenter->email }}
                </div>
                <div class="comment-text">
                    {!! nl2br(e($comment->comment)) !!}
                </div>
            </div>
        </div>

        @if($bugReport->description)
        <div class="section">
            <span class="label">Original Report Description:</span>
            <div class="content">
                {!! nl2br(e(Str::limit($bugReport->description, 200))) !!}
            </div>
        </div>
        @endif

        <div class="footer">
            <p>This is an automated notification from the Bug Report System.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
