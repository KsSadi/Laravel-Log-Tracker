<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Tracker Alert</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #dc2626; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 600; }
        .header p  { margin: 4px 0 0; font-size: 14px; opacity: .85; }
        .body { padding: 32px; }
        table.details { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.details td { padding: 8px 4px; vertical-align: top; font-size: 14px; }
        table.details td.label { font-size: 12px; font-weight: 600; text-transform: uppercase; color: #6b7280; width: 140px; padding-right: 16px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .badge-error     { background: #fee2e2; color: #dc2626; }
        .badge-warning   { background: #fef3c7; color: #d97706; }
        .badge-critical,
        .badge-emergency,
        .badge-alert     { background: #fce7f3; color: #be185d; }
        .badge-default   { background: #e5e7eb; color: #374151; }
        .message-box { background: #f9fafb; border-left: 4px solid #dc2626; padding: 16px 20px; border-radius: 4px; font-family: monospace; font-size: 13px; color: #374151; white-space: pre-wrap; word-break: break-all; }
        .footer { padding: 20px 32px; background: #f9fafb; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; }
        .footer code { background: #e5e7eb; padding: 1px 5px; border-radius: 3px; font-size: 11px; }
    </style>
</head>
<body>
    <div class="container">

        <div class="header">
            <h1>&#9888; Log Tracker Alert</h1>
            <p>A log threshold has been exceeded in your application.</p>
        </div>

        <div class="body">
            @php $level = strtolower($payload['level']); @endphp
            @php $badgeClass = in_array($level, ['error','warning','critical','emergency','alert']) ? 'badge-'.$level : 'badge-default'; @endphp

            <table class="details">
                <tr>
                    <td class="label">Level</td>
                    <td><span class="badge {{ $badgeClass }}">{{ strtoupper($payload['level']) }}</span></td>
                </tr>
                <tr>
                    <td class="label">File</td>
                    <td>{{ $payload['file'] }}</td>
                </tr>
                <tr>
                    <td class="label">Count</td>
                    <td>{{ number_format($payload['count']) }} occurrences</td>
                </tr>
                <tr>
                    <td class="label">Threshold</td>
                    <td>{{ number_format($payload['threshold']) }}</td>
                </tr>
                <tr>
                    <td class="label">Window</td>
                    <td>Last {{ $payload['window_minutes'] }} minute(s)</td>
                </tr>
                <tr>
                    <td class="label">Detected at</td>
                    <td>{{ now()->format('D, d M Y H:i:s T') }}</td>
                </tr>
            </table>

            <div class="message-box">{{ $body }}</div>
        </div>

        <div class="footer">
            This alert was sent by <strong>Laravel Log Tracker</strong>.
            Adjust thresholds or disable alerts in <code>config/log-tracker.php</code>.
        </div>

    </div>
</body>
</html>
