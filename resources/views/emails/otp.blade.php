<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Verifikasi Email</title>
</head>
<body style="font-family: Poppins, Arial, sans-serif; background-color: #F8FAFE; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #FFFFFF; border-radius: 10px; padding: 40px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #0D9488; font-size: 24px; font-weight: 700; margin: 0;">Reloved</h1>
        </div>
        
        <h2 style="color: #000000; font-size: 20px; font-weight: 600; margin-bottom: 20px;">Verifikasi Email Anda</h2>
        
        <p style="color: #374151; font-size: 16px; line-height: 1.5; margin-bottom: 30px;">
            Terima kasih telah mendaftar di Reloved! Gunakan kode OTP berikut untuk memverifikasi email Anda:
        </p>
        
        <div style="background-color: #F8FAFE; border: 2px solid #0D9488; border-radius: 10px; padding: 20px; text-align: center; margin-bottom: 30px;">
            <div style="font-size: 32px; font-weight: 700; color: #0D9488; letter-spacing: 8px; font-family: monospace;">
                {{ $otp }}
            </div>
        </div>
        
        <p style="color: #6B7280; font-size: 14px; line-height: 1.5; margin-bottom: 10px;">
            Kode ini akan kedaluwarsa dalam <strong>15 menit</strong>.
        </p>
        
        <p style="color: #6B7280; font-size: 14px; line-height: 1.5;">
            Jika Anda tidak meminta kode ini, abaikan email ini.
        </p>
        
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #E5E7EB;">
            <p style="color: #9CA3AF; font-size: 12px; text-align: center; margin: 0;">
                &copy; {{ date('Y') }} Reloved. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>

