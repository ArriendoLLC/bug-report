<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bug Report Status Updated</title>
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
            border-bottom: 3px solid #38c172;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        h1 {
            color: #38c172;
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
            border-left: 3px solid #38c172;
        }
        .status-change {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
        }
        .status-new { background-color: #e3f2fd; color: #1976d2; }
        .status-in_progress { background-color: #fff3e0; color: #f57c00; }
        .status-resolved { background-color: #e8f5e9; color: #388e3c; }
        .status-closed { background-color: #f3e5f5; color: #7b1fa2; }
        .arrow {
            color: #999;
            font-size: 20px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #38c172;
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
            <h1>📋 Bug Report Status Updated</h1>
            <div class="meta">
                Updated on {{ $bugReport->updated_at->format('M d, Y \a\t g:i A') }}
            </div>
        </div>

        <div class="section">
            <span class="label">Report Title:</span>
            <div class="content">
                {{ $bugReport->title }}
            </div>
        </div>

        <div class="section">
            <span class="label">Status Change:</span>
            <div class="status-change">
                <span class="status-badge status-{{ $oldStatus?->value ?? 'new' }}">
                    {{ $oldStatus?->label() ?? 'New' }}
                </span>
                <span class="arrow">→</span>
                <span class="status-badge status-{{ $newStatus->value }}">
                    {{ $newStatus->label() }}
                </span>
            </div>
        </div>

        @if($bugReport->description)
        <div class="section">
            <span class="label">Description:</span>
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
