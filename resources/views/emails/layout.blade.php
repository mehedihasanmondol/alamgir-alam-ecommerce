<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            color: #f0f0f0;
            margin: 10px 0 0 0;
            font-size: 14px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #333333;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #667eea;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #5568d3;
        }
        .product-grid {
            display: table;
            width: 100%;
            margin: 30px 0;
        }
        .product-item {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
        .product-card {
            background: #f9f9f9;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
        }
        .product-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .product-details {
            padding: 15px;
        }
        .product-name {
            font-size: 16px;
            font-weight: 600;
            color: #333333;
            margin: 0 0 10px 0;
        }
        .product-price {
            font-size: 18px;
            color: #667eea;
            font-weight: 700;
        }
        .product-old-price {
            font-size: 14px;
            color: #999999;
            text-decoration: line-through;
            margin-left: 8px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px 20px;
            text-align: center;
            border-top: 1px solid #eeeeee;
        }
        .footer p {
            margin: 5px 0;
            font-size: 13px;
            color: #666666;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #667eea;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .content {
                padding: 30px 20px;
            }
            .product-item {
                display: block;
                width: 100% !important;
                padding: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Health & Wellness Store</p>
        </div>

        <!-- Content -->
        <div class="content">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>
                <a href="{{ config('app.url') }}">Visit Our Website</a> | 
                <a href="{{ $unsubscribeUrl ?? '#' }}">Email Preferences</a>
            </p>
            <p style="font-size: 12px; color: #999999; margin-top: 15px;">
                You are receiving this email because you subscribed to our mailing list.<br>
                If you no longer wish to receive these emails, you can <a href="{{ $unsubscribeUrl ?? '#' }}">update your preferences</a>.
            </p>
        </div>
    </div>
</body>
</html>
