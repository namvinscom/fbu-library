<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('titleWeb')</title>
    <link rel="shortcut icon" href="https://sinhvien.fbu.edu.vn/Content/AConfig/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('asset/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/style.css') }}">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- JavaScript Dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('asset/js/toastr.min.js') }}"></script>
</head>

<body>

    <main class="bg-gray-100 h-full">
        @if (!isset($layout))
            @include('block.header')
        @endif
        <!-- Toastr Message -->
        @if (session('message'))
            <script>
                $(document).ready(function() {
                    toastr.{{ session('type') }}("{{ session('message') }}");
                });
            </script>
        @endif

        <!-- Main Container -->
        <div class="flex h-[calc(100%-64px)]"> <!-- Trừ chiều cao header -->
            @if (!isset($layout))
                @include('layout.sideBar')
            @endif

           <div class="{{ !isset($layout) ? 'flex-1 bg-white shadow-md border border-gray-200 p-6 overflow-y-auto rounded-xl m-4' : 'flex-1 p-6 overflow-y-hidden' }}">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="{{ asset('asset/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @yield('scripts')
</body>

</html>
