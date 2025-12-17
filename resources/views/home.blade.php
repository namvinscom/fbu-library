@extends('layout.main')
@section('title')
    {{ $title }}
@endsection
@section('titleWeb')
    {{ $titleWeb }}
@endsection
@section('content')
    <!-- Thống kê quản lý sách -->
    <section class="mb-8">
        <h2 class="text-2xl font-bold text-primary mb-4">Thống kê quản lý sách</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Tổng số sách -->
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700">Tổng số sách</h3>
                <p class="text-3xl font-bold text-primary">{{ $totalBooks }}</p>
            </div>
            <!-- Sách sẵn sàng cho mượn -->
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700">Sách sẵn sàng cho mượn</h3>
                <p class="text-3xl font-bold text-primary">{{ $availableBooks }}</p>
            </div>
            <!-- Sách đang mượn -->
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700">Sách đang mượn</h3>
                <p class="text-3xl font-bold text-primary">{{ $borrowedBooks }}</p>
            </div>
            <!-- Sách bị mất, hỏng lý do khác-->
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700">Sách bị mất, hỏng lý do khác</h3>
                <p class="text-3xl font-bold text-primary">{{ $brokenBooks }}</p>
            </div>
        </div>
        <!-- Biểu đồ cho thống kê sách -->
        <div class="bg-gray-50 p-6 rounded-lg shadow-sm mt-6 text-center">
            <canvas id="chartBooks"></canvas>
            <h3 class="text-2xl font-bold text-primary mt-4">Biểu đồ</h3>
        </div>
    </section>

    <!-- Thống kê quản lý bạn đọc -->
    <section class="mb-8">
        <h2 class="text-2xl font-bold text-primary mb-4">Thống kê quản lý bạn đọc</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Tổng số bạn đọc -->
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700">Tổng số bạn đọc</h3>
                <p class="text-3xl font-bold text-primary">{{ $totalReaders }}</p>
            </div>
            <!-- Bạn đọc đang mượn -->
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700">Bạn đọc đang mượn</h3>
                <p class="text-3xl font-bold text-primary">{{ $borrowedReaders }}</p>
            </div>
            <!-- Bạn đọc mới (tháng) -->
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700">Bạn đọc mới (tháng)</h3>
                <p class="text-3xl font-bold text-primary">{{ $newReaders }}</p>
            </div>
            <!-- Bạn đọc vi phạm -->
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700">Bạn đọc vi phạm</h3>
                <p class="text-3xl font-bold text-primary">{{ $banedReaders }}</p>
            </div>
        </div>
        <!-- Biểu đồ cho thống kê bạn đọc -->
        <div class="bg-gray-50 p-6 rounded-lg shadow-sm mt-6 text-center">
            <canvas id="chartReaders"></canvas>
            <h3 class="text-2xl font-bold text-primary mt-4">Biểu đồ</h3>
        </div>
    </section>

    <!-- Thống kê quản lý mượn/trả -->
    <section class="mb-8">
        <h2 class="text-2xl font-bold text-primary mb-4">Thống kê quản lý mượn/trả</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Sách đã mượn -->
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700">Sách đã mượn</h3>
                <p class="text-3xl font-bold text-primary">{{ $borrowedBooks }}</p>
            </div>
            <!-- Sách đã trả -->
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700">Sách đã trả</h3>
                <p class="text-3xl font-bold text-primary">{{ $returnedBooks }}</p>
            </div>
            <!-- Sách quá hạn chưa trả -->
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700">Sách quá hạn chưa trả</h3>
                <p class="text-3xl font-bold text-primary">{{ $overdueBooks }}</p>
            </div>
        </div>
        <!-- Biểu đồ cho thống kê mượn/trả -->
        <div class="bg-gray-50 p-6 rounded-lg shadow-sm mt-6 text-center">
            <canvas id="chartLoan"></canvas>
            <h3 class="text-2xl font-bold text-primary mt-4">Biểu đồ</h3>
        </div>
    </section>
@endsection

@section('scripts')
    <script script src="{{ asset('asset/js/home.js') }}"></script>
    <script>
        initDashboard({
            totalBooks: {{ $totalBooks }},
            brokenBooks: {{ $brokenBooks }},
            borrowedBooks: {{ $borrowedBooks }},
            returnedBooks: {{ $returnedBooks }},
            overdueBooks: {{ $overdueBooks }},
            availableBooks: {{ $availableBooks }},
            totalReaders: {{ $totalReaders }},
            borrowedReaders: {{ $borrowedReaders }},
            newReaders: {{ $newReaders }},
            banedReaders: {{ $banedReaders }}
        });
    </script>
@endsection
