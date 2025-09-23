<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background: #ffffff; margin: 0; }
        .container { max-width: 520px; margin: 40px auto; padding: 0 16px; text-align: center; }
        .avatar { width: 160px; height: 160px; border-radius: 50%; object-fit: cover; border: 4px solid #cfcfcf; display: block; margin: 0 auto 24px; }
        .card { background: #e6e6e6; border-radius: 6px; padding: 14px 16px; margin: 16px 0; font-size: 22px; font-weight: 700; color: #333; }
    </style>
    <link rel="icon" href="/favicon.ico">
    <!-- Drop your profile image at public/images/profile.jpg -->
    <!-- Alternatively, you can place any path you prefer and adjust below -->
</head>
<body>
    <div class="container">
        <img class="avatar" src="{{ asset('images/profile.jpg') }}" alt="Foto Profil Ananda Anhar Subing">
        <div class="card">Ananda Anhar Subing</div>
        <div class="card">A</div>
        <div class="card">2317051082</div>
    </div>
</body>
</html>


