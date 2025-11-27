<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body style="font-family: Poppins, Arial, sans-serif; background-color: #F8FAFE; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #FFFFFF; border-radius: 10px; padding: 40px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #0D9488; font-size: 24px; font-weight: 700; margin: 0;">Reloved</h1>
        </div>
        
        <h2 style="color: #000000; font-size: 20px; font-weight: 600; margin-bottom: 20px;">Reset Kata Sandi Anda</h2>
        
        <p style="color: #374151; font-size: 16px; line-height: 1.5; margin-bottom: 30px;">
            Anda menerima email ini karena kami menerima permintaan reset kata sandi untuk akun Anda. Klik tombol di bawah untuk mereset kata sandi Anda:
        </p>
        
        <div style="text-align: center; margin-bottom: 30px;">
            <a href="{{ $resetLink }}" style="background-color: #0D9488; color: #FFFFFF; text-decoration: none; padding: 15px 30px; border-radius: 10px; font-size: 16px; font-weight: 600;">
                Reset Kata Sandi
            </a>
        </div>
        
        <p style="color: #6B7280; font-size: 14px; line-height: 1.5; margin-bottom: 10px;">
            Link reset kata sandi ini akan kedaluwarsa dalam 60 menit.
        </p>
        
        <p style="color: #6B7280; font-size: 14px; line-height: 1.5;">
            Jika Anda tidak meminta reset kata sandi, abaikan email ini.
        </p>
        
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #E5E7EB;">
            <p style="color: #9CA3AF; font-size: 12px; text-align: center; margin: 0;">
                &copy; {{ date('Y') }} Reloved. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
