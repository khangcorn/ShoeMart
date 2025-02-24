<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Dark Mode Script -->
    <script src="{{ asset('js/darkmode.js') }}" defer></script>
</head>
<body class="bg-gray-100 text-black dark:bg-black dark:text-white transition-colors duration-300">

    <!-- Nội dung của bạn ở đây -->
    <div class="flex min-h-screen bg-gray-100 dark:bg-gray-800">
        <!-- Sidebar -->
        <aside class="w-1/5 bg-gray-100 dark:bg-gray-800 dark:text-white border-r border-gray-300 dark:border-gray-700">
            @include('components.admin.sidebar')
        </aside>

        <!-- Main Content -->
        <main class="w-4/5 bg-gray-100 dark:bg-gray-800 dark:text-white">
            @include('components.admin.header')
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
