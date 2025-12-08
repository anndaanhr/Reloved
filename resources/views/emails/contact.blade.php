<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Baru dari Form Kontak</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .info-row {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
        }
        .value {
            color: #6b7280;
        }
        .message-box {
            background-color: white;
            padding: 15px;
            border-left: 4px solid #4F46E5;
            margin-top: 15px;
            border-radius: 4px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">Pesan Baru dari Form Kontak</h1>
    </div>
    <div class="content">
        <div class="info-row">
            <div class="label">Nama:</div>
            <div class="value">{{ $name }}</div>
        </div>
        <div class="info-row">
            <div class="label">Email:</div>
            <div class="value">{{ $email }}</div>
        </div>
        <div class="info-row">
            <div class="label">Subjek:</div>
            <div class="value">{{ $contactSubject }}</div>
        </div>
        <div class="message-box">
            <div class="label">Pesan:</div>
            <div class="value" style="white-space: pre-wrap;">{{ $messageContent }}</div>
        </div>
    </div>
    <div class="footer">
        <p>Email ini dikirim dari form kontak di website Reloved.</p>
        <p>Anda dapat membalas email ini langsung ke {{ $email }}</p>
    </div>
</body>
</html>

