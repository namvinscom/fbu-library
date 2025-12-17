@extends('layout.main')
@section('title')
    {{ $title }}
@endsection
@section('titleWeb')
    {{ $titleWeb }}
@endsection
@section('content')

    <!-- Search Box -->
    <div class="mb-6">
        <form action="{{ route('book.search') }}" method="GET" class="flex items-center gap-2">
            <!-- Ô tìm kiếm -->
            <input type="text"
                class="flex-grow max-w-md px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary shadow-sm"
                placeholder="Tìm kiếm sách, mã sách, tác giả..." name="query" />

            <!-- Nút Tìm kiếm -->
            <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-300 flex items-center gap-2">
                <i class="fas fa-search"></i> <!-- Icon tìm kiếm -->
                <span>Tìm kiếm</span>
            </button>

            <!-- Nút Thêm sách -->
            <button type="button" id="addBookBtn"
                class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-300 flex items-center gap-2"
                onclick="event.preventDefault();">
                <i class="fas fa-plus"></i> <!-- Icon thêm -->
                <span>Thêm sách</span>
            </button>

            <!-- Nút Làm mới -->
            <button type="button" onclick="window.location.href='{{ route('qls') }}';"
                class="bg-amber-500 text-white px-4 py-2 rounded-lg hover:bg-amber-600 transition duration-300 flex items-center gap-2">
                <i class="fas fa-sync-alt"></i> <!-- Icon làm mới -->
                <span>Làm mới</span>
            </button>

        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow-md mb-4">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-primary text-white">
                    <th class="p-4 text-left">Ảnh bìa sách</th>
                    <th class="p-4 text-left">Mã sách</th>
                    <th class="p-4 text-left">Tên sách</th>
                    <th class="p-4 text-left">Kiểu tài liệu</th>
                    <th class="p-4 text-left">Tác giả</th>
                    <th class="p-4 text-left">Số lượng</th>
                    <th class="p-4 text-left">Sẵn sàng mượn</th>
                    <th class="p-4 text-left">Sách mất hỏng</th>
                    <th class="p-4 text-left">Mô tả</th>
                    <th class="p-4 text-left">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($books) && $books->isNotEmpty())
                    @foreach ($books as $book)
                        <tr class="border-b hover:bg-gray-50 transition duration-200" data-book="{{ json_encode($book) }}">
                            <td class="p-4">
                                <img class="w-16 h-16 object-cover rounded-lg"
                                    src="{{ asset('storage/image/' . $book->book_cover) }}" alt="Ảnh bìa sách">
                            </td>
                            <td class="p-4">{{ $book->book_code }}</td>
                            <td class="p-4 text-nowrap">{{ $book->book_name }}</td>
                            <td class="p-4">{{ $book->book_type }}</td>
                            <td class="p-4 ">{{ $book->author }}</td>
                            <td class="p-4">{{ $book->quantity }}</td>
                            <td class="p-4">{{ $book->availableBooks }}</td>
                            <td class="p-4">{{ $book->broken }}</td>
                            <td class="p-4">{{ $book->description }}</td>
                            <td class="p-4">
                                <div class="flex space-x-2">
                                    <button
                                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-300 editBookBtn">Sửa</button>
                                    <button
                                        class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300 deleteBookBtn">Xóa</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="10" class="p-8 text-center">
                            <h3 class="text-gray-500 font-medium text-lg">Không có dữ liệu</h3>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    @if (isset($books) && $books->isNotEmpty())
        <div class="py-8">
            {{ $books->links() }}
        </div>
    @endif
    <!-- Modal Sửa thông tin sách -->
    <div id="editBookModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-3 rounded-lg shadow-lg w-full max-w-2xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-primary" id="formTitle">Thêm sách mới</h2>
                <button id="closeModal" class="text-gray-600 text-2xl leading-none p-4">&times;</button>
            </div>
            <form id="editBookForm" data-validate-update="{{ session('validateUpdate') }}"
                data-validate-add="{{ session('validateAdd') }}" enctype="multipart/form-data" method="POST"
                action="{{ route('book.update') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-gray-700">Mã sách</label>
                        <input type="text" name="book_code" class="w-full border rounded-lg p-2"
                            value="{{ old('book_code') }}" required>
                        @error('book_code')
                            <div class="error-message"
                                style="color: #DB3030; font-size: 12.25px; margin-top: 4px; width: 100%;">
                                {{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Tên sách</label>
                        <input type="text" name="book_name" class="w-full border rounded-lg p-2"
                            value="{{ old('book_name') }}" required>
                        @error('book_name')
                            <div class="error-message"
                                style="color: #DB3030; font-size: 12.25px; margin-top: 4px; width: 100%;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Kiểu tài liệu</label>
                        <input type="text" name="book_type" class="w-full border rounded-lg p-2"
                            value="{{ old('book_type') }}" required>
                        @error('book_type')
                            <div class="error-message"
                                style="color: #DB3030; font-size: 12.25px; margin-top: 4px; width: 100%;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Tác giả</label>
                        <input type="text" name="author" class="w-full border rounded-lg p-2"
                            value="{{ old('author') }}" required>
                        @error('author')
                            <div class="error-message"
                                style="color: #DB3030; font-size: 12.25px; margin-top: 4px; width: 100%;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Số lượng</label>
                        <input type="number" name="quantity" class="w-full border rounded-lg p-2"
                            value="{{ old('quantity') }}" required>
                        @error('username')
                            <div class="error-message"
                                style="color: #DB3030; font-size: 12.25px; margin-top: 4px; width: 100%;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4 book-lost hidden">
                        <label class="block text-gray-700">Sách mất hỏng</label>
                        <input type="number" name="broken" class="w-full border rounded-lg p-2"
                            value="{{ old('broken') }}">
                    </div>
                    <div class="mb-4 w-full">
                        <label class="block text-gray-700">Ảnh bìa sách</label>
                        <input type="file" name="book_cover" id="bookImageInput" class="w-full border rounded-lg p-2"
                            accept="image/*">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Mô tả</label>
                    <textarea name="description" class="w-full border rounded-lg p-2" rows="3">{{ old('description') }}</textarea>

                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelModal"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Lưu</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Xóa sách -->
    <div id="deleteBookModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-primary">Xóa sách</h2>
                <button id="closeDeleteModal" class="text-gray-600 text-2xl leading-none p-4">&times;</button>
            </div>
            <p class="mb-4">Bạn có chắc chắn muốn xóa sách này không?</p>
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelDeleteModal"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg">Hủy</button>
                <button type="button" id="confirmDeleteModal"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-300">Xóa</button>
            </div>
        </div>
    </div>

    <!-- Modal thông báo -->
    <div id="notificationModal"
        class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50 z-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
            <h3 id="modalTitle" class="text-xl font-semibold"></h3>
            <p id="modalMessage" class="text-sm mt-2"></p>
            <div class="mt-4 flex justify-end space-x-2">
                <button id="modalOkBtn" class="bg-blue-500 text-white px-4 py-2 rounded-lg">OK</button>
            </div>
        </div>
    </div>


@endsection


@section('scripts')
    <script src="{{ asset('asset/js/bookManagement.js') }}"></script>
@endsection
