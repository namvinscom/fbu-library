@extends('layout.main')
@section('title')
    {{ $title }}
@endsection
@section('titleWeb')
    {{ $titleWeb }}
@endsection
@section('content')

    <div class="mb-6">
        <form action="{{ route('book.search') }}" method="GET" class="flex items-center gap-2">
            <input type="text"
                class="flex-grow max-w-md px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary shadow-sm"
                placeholder="Tìm kiếm sách, mã sách, tác giả..." name="query" />

            <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-300 flex items-center gap-2">
                <i class="fas fa-search"></i>
                <span>Tìm kiếm</span>
            </button>

            <button type="button" id="addBookBtn"
                class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-300 flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Thêm sách</span>
            </button>

            <button type="button" onclick="window.location.href='{{ route('qls') }}';"
                class="bg-amber-500 text-white px-4 py-2 rounded-lg hover:bg-amber-600 transition duration-300 flex items-center gap-2">
                <i class="fas fa-sync-alt"></i>
                <span>Làm mới</span>
            </button>
        </form>
    </div>

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
                        <tr class="border-b hover:bg-gray-50 transition duration-200" 
                            data-book="{{ htmlspecialchars(json_encode($book), ENT_QUOTES, 'UTF-8') }}">
                            <td class="p-4">
                                <img class="w-16 h-16 object-cover rounded-lg"
                                    src="{{ $book->book_cover ? asset('storage/image/' . $book->book_cover) : 'https://via.placeholder.com/150?text=No+Image' }}"
                                    alt="Ảnh bìa sách">
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
                                    <button type="button" onclick="editBook(this)" 
                                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-300 editBookBtn">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Sửa
                                    </button>
                                    <button class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300 deleteBookBtn">Xóa</button>
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

    <div id="addBookModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white p-3 rounded-lg shadow-lg w-full max-w-2xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-primary">Thêm sách mới</h2>
                <button type="button" class="text-gray-600 text-2xl leading-none p-4" onclick="document.getElementById('addBookModal').classList.add('hidden')">&times;</button>
            </div>
            
            <form id="addBookForm" enctype="multipart/form-data" method="POST" action="{{ route('book.add') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-gray-700">Mã sách</label>
                        <input type="text" name="book_code" class="w-full border rounded-lg p-2" value="{{ old('book_code') }}" required placeholder="Nhập mã sách">
                        @error('book_code')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Tên sách</label>
                        <input type="text" name="book_name" class="w-full border rounded-lg p-2" value="{{ old('book_name') }}" required placeholder="Nhập tên sách">
                        @error('book_name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Kiểu tài liệu</label>
                        <select name="book_type" class="w-full border rounded-lg p-2" required>
                            <option value="" disabled selected>Chọn kiểu tài liệu</option>
                            <option value="Bài tập lớn">Bài tập lớn</option>
                            <option value="Giáo trình">Giáo trình</option>
                            <option value="Luận văn">Luận văn</option>
                            <option value="Tiểu luận">Tiểu luận</option>
                            <option value="Luận án">Luận án</option>
                        </select>
                        @error('book_type')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Tác giả</label>
                        <input type="text" name="author" class="w-full border rounded-lg p-2" value="{{ old('author') }}" required placeholder="Tên tác giả">
                        @error('author')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Số lượng</label>
                        <input type="number" name="quantity" class="w-full border rounded-lg p-2" value="{{ old('quantity') }}" required placeholder="Số lượng">
                        @error('quantity')
                             <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4 w-full">
                        <label class="block text-gray-700">Ảnh bìa sách</label>
                        <input type="file" name="book_cover" class="w-full border rounded-lg p-2" accept="image/*">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Mô tả</label>
                    <textarea name="description" class="w-full border rounded-lg p-2" rows="3" placeholder="Mô tả sách">{{ old('description') }}</textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-lg" onclick="document.getElementById('addBookModal').classList.add('hidden')">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editBookModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white p-3 rounded-lg shadow-lg w-full max-w-2xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-primary" id="formTitle">Cập nhật sách</h2>
                <button id="closeModal" class="text-gray-600 text-2xl leading-none p-4" onclick="document.getElementById('editBookModal').classList.add('hidden')">&times;</button>
            </div>
            
            <form id="updateBookForm" enctype="multipart/form-data" method="POST" action="{{ route('book.update') }}">
                @csrf
                <input type="hidden" name="id" id="update_book_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-gray-700">Mã sách</label>
                        <input type="text" name="book_code" class="w-full border rounded-lg p-2" value="{{ old('book_code') }}" required>
                        @error('book_code')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Tên sách</label>
                        <input type="text" name="book_name" class="w-full border rounded-lg p-2" value="{{ old('book_name') }}" required>
                        @error('book_name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Kiểu tài liệu</label>
                        <select name="book_type" class="w-full border rounded-lg p-2" required>
                            <option value="" disabled {{ old('book_type') ? '' : 'selected' }}>Chọn kiểu tài liệu</option>
                            <option value="Bài tập lớn" {{ old('book_type') == 'Bài tập lớn' ? 'selected' : '' }}>Bài tập lớn</option>
                            <option value="Giáo trình" {{ old('book_type') == 'Giáo trình' ? 'selected' : '' }}>Giáo trình</option>
                            <option value="Luận văn" {{ old('book_type') == 'Luận văn' ? 'selected' : '' }}>Luận văn</option>
                            <option value="Tiểu luận" {{ old('book_type') == 'Tiểu luận' ? 'selected' : '' }}>Tiểu luận</option>
                            <option value="Luận án" {{ old('book_type') == 'Luận án' ? 'selected' : '' }}>Luận án</option>
                        </select>
                        @error('book_type')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Tác giả</label>
                        <input type="text" name="author" class="w-full border rounded-lg p-2" value="{{ old('author') }}" required>
                        @error('author')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Số lượng</label>
                        <input type="number" name="quantity" class="w-full border rounded-lg p-2" value="{{ old('quantity') }}" required>
                        @error('quantity')
                             <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4 w-full">
                        <label class="block text-gray-700">Ảnh bìa sách</label>
                        <input type="file" name="book_cover" id="bookImageInput" class="w-full border rounded-lg p-2" accept="image/*">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Mô tả</label>
                    <textarea name="description" class="w-full border rounded-lg p-2" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-lg" onclick="document.getElementById('editBookModal').classList.add('hidden')">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Lưu</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteBookModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-primary">Xóa sách</h2>
                <button id="closeDeleteModal" class="text-gray-600 text-2xl leading-none p-4">&times;</button>
            </div>
            <p class="mb-4">Bạn có chắc chắn muốn xóa sách này không?</p>
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelDeleteModal" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Hủy</button>
                <button type="button" id="confirmDeleteModal" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-300">Xóa</button>
            </div>
        </div>
    </div>

    <div id="notificationModal" class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50 z-50 hidden">
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
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- Xử lý Nút Thêm Sách ---
        const addBookBtn = document.getElementById('addBookBtn');
        const addBookModal = document.getElementById('addBookModal');
        
        if(addBookBtn && addBookModal) {
            addBookBtn.addEventListener('click', function() {
                addBookModal.classList.remove('hidden');
            });
        }
    });

    // --- Hàm Javascript Sửa sách (Điền dữ liệu vào form update) ---
    function editBook(button) {
        // 1. Tìm dòng (tr) chứa nút bấm
        let row = button.closest('tr');

        // 2. Lấy cục dữ liệu từ data-book
        let bookData = row.getAttribute('data-book');
        
        if (!bookData) {
            console.error("Không tìm thấy dữ liệu sách!");
            return;
        }

        // Giải mã JSON
        let book = JSON.parse(bookData);
        console.log("Đang sửa sách:", book);

        // 3. Điền ID vào ô ẩn
        let idInput = document.getElementById('update_book_id');
        if(idInput) idInput.value = book.id;

        // 4. Tìm Form sửa (Tìm theo ID updateBookForm)
        let form = document.getElementById('updateBookForm');
        
        if (!form) {
            console.error("LỖI: Không tìm thấy form updateBookForm");
            return;
        }

        // 5. Điền dữ liệu vào các ô input
        if (form.querySelector('input[name="book_code"]')) 
            form.querySelector('input[name="book_code"]').value = book.book_code;

        if (form.querySelector('input[name="book_name"]')) 
            form.querySelector('input[name="book_name"]').value = book.book_name;

        if (form.querySelector('input[name="author"]')) 
            form.querySelector('input[name="author"]').value = book.author;

        if (form.querySelector('input[name="quantity"]')) 
            form.querySelector('input[name="quantity"]').value = book.quantity;

        // Điền mô tả
        if (form.querySelector('textarea[name="description"]')) 
            form.querySelector('textarea[name="description"]').value = book.description || '';

        // Chọn kiểu tài liệu
        let selectType = form.querySelector('select[name="book_type"]');
        if (selectType) {
            selectType.value = book.book_type;
        }

        // 6. Mở Modal Sửa
        let modal = document.getElementById('editBookModal');
        if(modal) modal.classList.remove('hidden');
    }
    </script>
@endsection

@if($errors->any())
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                var btn = document.getElementById('addBookBtn');
                if (btn) {
                    btn.click(); // Tự động mở lại modal Thêm nếu có lỗi
                }
            }, 500); 
        });
    </script>
@endif