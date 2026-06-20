<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access — Navigro</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/admin.css'])
    <style>
        .access-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; background: linear-gradient(135deg, #ffffff 0%, #eff6ff 60%, #dbeafe 100%); }
        .access-card { width: 100%; max-width: 400px; }
        .access-logo { text-align: center; margin-bottom: 6px; }
        .access-logo img { width: 100%; max-height: 108px; object-fit: contain; display: block; }
        .access-sub { text-align: center; color: var(--text3); font-size: 13px; margin-bottom: 24px; }
    </style>
</head>
<body>
    <div class="access-wrap">
        <div class="access-card card">
            @yield('content')
        </div>
    </div>
</body>
</html>
