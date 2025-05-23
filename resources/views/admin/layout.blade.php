<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản lý sản phẩm</title>


</style>
    <!-- Bootstrap -->
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> --}}

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Dark Mode Script -->
    <script src="{{ asset('js/layout.js') }}" defer></script>
</head>

<body class="bg-white text-black   transition-colors duration-300">

    <!-- Nội dung của bạn ở đây -->
    <div class="flex min-h-screen bg-white ">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-1/5 bg-white   border-r border-gray-300 ">
            @include('components.admin.sidebar')
        </aside>

        <!-- Main Content -->
        <main  id="mainContent" class="w-4/5 bg-white  border-b border-gray-300 ">
            @include('components.admin.header')
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap Script -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
    @stack('scripts')
</body>
</html>
