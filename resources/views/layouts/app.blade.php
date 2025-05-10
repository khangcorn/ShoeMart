<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Trang quản trị')</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>
<body>

    <!-- Header -->
    <nav class="navbar navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Admin Panel</a>
    </nav>

    <div class="container mt-4">
        @yield('content') <!-- Đây là nơi sẽ chèn nội dung từ các file con -->
    </div>

    <!-- Footer -->
    <footer class="text-center mt-5">
        <p>&copy; {{ date('Y') }} - Quản lý sliders</p>
    </footer>

</body>
</html>
