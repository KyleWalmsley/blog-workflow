<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Blog Workflow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/admin.css'])
</head>
<body>
    <div class="shell">
        @include('admin.partials.sidebar')
        <div class="content">
            @include('admin.partials.topbar')
            <main class="page">
                @include('admin.partials.flash')
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
