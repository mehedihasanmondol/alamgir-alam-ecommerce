<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 30px 20px;
        }
        .message-info {
            background: #f9fafb;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #4b5563;
            min-width: 100px;
        }
        .info-value {
            color: #1f2937;
        }
        .message-content {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 20px;
            margin-top: 20px;
        }
        .message-content h3 {
            margin-top: 0;
            color: #1f2937;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .message-text {
            color: #4b5563;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .email-footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }
        .action-button {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            font-weight: 600;
        }
        .action-button:hover {
            background: #1e40af;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-new {
            background: #dbeafe;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>📧 New Contact Message</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">You have received a new message from your website</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Message Info -->
            <div class="message-info">
                <div class="info-row">
                    <span class="info-label">From:</span>
                    <span class="info-value"><strong>{{ $contactMessage->name }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><a href="mailto:{{ $contactMessage->email }}" style="color: #2563eb; text-decoration: none;">{{ $contactMessage->email }}</a></span>
                </div>
                @if($contactMessage->phone)
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value"><a href="tel:{{ $contactMessage->phone }}" style="color: #2563eb; text-decoration: none;">{{ $contactMessage->phone }}</a></span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Subject:</span>
                    <span class="info-value"><strong>{{ $contactMessage->subject }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Received:</span>
                    <span class="info-value">{{ $contactMessage->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><span class="badge badge-new">New</span></span>
                </div>
            </div>

            <!-- Message Content -->
            <div class="message-content">
                <h3>Message:</h3>
                <div class="message-text">{{ $contactMessage->message }}</div>
            </div>

            <!-- Action Button -->
            <div style="text-align: center;">
                <a href="{{ route('admin.contact.messages.index') }}" class="action-button">
                    View in Admin Panel
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p style="margin: 0 0 10px 0;">This is an automated notification from {{ config('app.name') }}</p>
            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                IP Address: {{ $contactMessage->ip_address ?? 'N/A' }}
            </p>
        </div>
    </div>
</body>
</html>
