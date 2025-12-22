@extends('layout.main')
@section('title') {{ $title }} @endsection
@section('titleWeb') {{ $titleWeb }} @endsection

@section('content')
<div class="mb-6">
    <form action="{{ route('book.search') }}" method="GET" class="flex items-center gap-2">
        <input type="text" name="query" class="flex-grow max-w-md px-4 py-2 border border-gray-300 rounded-lg" placeholder="Tìm kiếm sách...">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Tìm kiếm</button>
        
        <button type="button" onclick="document.getElementById('addBookModal').classList.remove('hidden')" 
            class="bg-green-500 text-white px-4 py-2 rounded-lg">Thêm sách</button>
        
        <button type="button" onclick="window.location.href='{{ route('qls') }}';" class="bg-amber-500 text-white px-4 py-2 rounded-lg">Làm mới</button>
    </form>
</div>

<div class="overflow-x-auto bg-white rounded-lg shadow-md mb-4">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-primary text-white">
                <th class="p-4 text-left">Ảnh</th>
                <th class="p-4 text-left">Mã sách</th>
                <th class="p-4 text-left">Tên sách</th>
                <th class="p-4 text-left">Kiểu</th>
                <th class="p-4 text-left">Tác giả</th>
                <th class="p-4 text-left">Số lượng</th>
                <th class="p-4 text-left">Mô tả</th>
                <th class="p-4 text-left">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($books) && $books->isNotEmpty())
                @foreach ($books as $book)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4">
                            <img class="w-16 h-16 object-cover rounded-lg" src="{{ $book->book_cover ? asset('storage/image/' . $book->book_cover) : 'https://via.placeholder.com/150' }}">
                        </td>
                        <td class="p-4">{{ $book->book_code }}</td>
                        <td class="p-4">{{ $book->book_name }}</td>
                        <td class="p-4">{{ $book->book_type }}</td>
                        <td class="p-4">{{ $book->author }}</td>
                        <td class="p-4">{{ $book->quantity }}</td>
                        <td class="p-4">{{ Str::limit($book->description, 50) }}</td>
                        <td class="p-4">
                            <button type="button" 
                                onclick="editBook(this)"
                                data-id="{{ $book->id }}"
                                data-code="{{ $book->book_code }}"
                                data-name="{{ $book->book_name }}"
                                data-type="{{ $book->book_type }}"
                                data-author="{{ $book->author }}"
                                data-qty="{{ $book->quantity }}"
                                data-desc="{{ $book->description }}"
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg">Sửa</button>
                            
                            <button class="bg-red-500 text-white px-4 py-2 rounded-lg">Xóa</button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr><td colspan="8" class="p-8 text-center">Không có dữ liệu</td></tr>
            @endif
        </tbody>
    </table>
</div>

<div id="addBookModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl">
        <h2 class="text-xl font-bold mb-4">Thêm sách mới</h2>
        <form method="POST" action="{{ route('book.add') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="book_code" placeholder="Mã sách" class="border p-2 rounded" required>
                <input type="text" name="book_name" placeholder="Tên sách" class="border p-2 rounded" required>
                <select name="book_type" class="border p-2 rounded" required>
                    <option value="Giáo trình">Giáo trình</option>
                    <option value="Bài tập lớn">Bài tập lớn</option>
                    <option value="Luận văn">Luận văn</option>
                </select>
                <input type="text" name="author" placeholder="Tác giả" class="border p-2 rounded" required>
                <input type="number" name="quantity" placeholder="Số lượng" class="border p-2 rounded" required>
                <input type="file" name="book_cover" class="border p-2 rounded">
            </div>
            <textarea name="description" placeholder="Mô tả" class="w-full border p-2 rounded mt-4"></textarea>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="document.getElementById('addBookModal').classList.add('hidden')" class="bg-gray-500 text-white px-4 py-2 rounded">Hủy</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Thêm mới</button>
            </div>
        </form>
    </div>
</div>

<div id="editBookModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl">
        <h2 class="text-xl font-bold mb-4">Cập nhật sách</h2>
        <form id="updateBookForm" method="POST" action="{{ route('book.update') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="edit_id">
            
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <label>Mã sách</label>
                    <input type="text" name="book_code" id="edit_code" class="border p-2 rounded" required>
                </div>
                <div class="flex flex-col">
                    <label>Tên sách</label>
                    <input type="text" name="book_name" id="edit_name" class="border p-2 rounded" required>
                </div>
                <div class="flex flex-col">
                    <label>Kiểu tài liệu</label>
                    <select name="book_type" id="edit_type" class="border p-2 rounded" required>
                        <option value="Giáo trình">Giáo trình</option>
                        <option value="Bài tập lớn">Bài tập lớn</option>
                        <option value="Luận văn">Luận văn</option>
                        <option value="Tiểu luận">Tiểu luận</option>
                        <option value="Luận án">Luận án</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label>Tác giả</label>
                    <input type="text" name="author" id="edit_author" class="border p-2 rounded" required>
                </div>
                <div class="flex flex-col">
                    <label>Số lượng</label>
                    <input type="number" name="quantity" id="edit_qty" class="border p-2 rounded" required>
                </div>
                <div class="flex flex-col">
                    <label>Ảnh mới (Nếu muốn thay)</label>
                    <input type="file" name="book_cover" class="border p-2 rounded">
                </div>
            </div>
            <div class="mt-4">
                <label>Mô tả</label>
                <textarea name="description" id="edit_desc" class="w-full border p-2 rounded"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="document.getElementById('editBookModal').classList.add('hidden')" class="bg-gray-500 text-white px-4 py-2 rounded">Hủy</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>
<div id="deleteBookModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-primary">Xóa sách</h2>
            <button onclick="document.getElementById('deleteBookModal').classList.add('hidden')" class="text-gray-600 text-2xl leading-none p-4">&times;</button>
        </div>
        <p class="mb-4">Bạn có chắc chắn muốn xóa sách này không?</p>
        
        <form id="deleteBookForm" method="POST" action="{{ route('book.delete') }}">
            @csrf
            <input type="hidden" name="id" id="delete_book_id">

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('deleteBookModal').classList.add('hidden')"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg">Hủy</button>
                
                <button type="submit" 
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-300">
                    Đồng ý Xóa
                </button>
            </div>
        </form>
        </div>
</div>
@endsection

@section('scripts')
<script>
    // Hàm Javascript sửa sách (Không dùng JSON.parse nữa, cực kỳ an toàn)
    function editBook(btn) {
        // 1. Lấy dữ liệu trực tiếp từ nút bấm
        let id = btn.getAttribute('data-id');
        let code = btn.getAttribute('data-code');
        let name = btn.getAttribute('data-name');
        let type = btn.getAttribute('data-type');
        let author = btn.getAttribute('data-author');
        let qty = btn.getAttribute('data-qty');
        let desc = btn.getAttribute('data-desc');

        // 2. Điền vào Form Sửa
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_code').value = code;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_author').value = author;
        document.getElementById('edit_qty').value = qty;
        document.getElementById('edit_desc').value = desc;

        // Xử lý Select box
        let select = document.getElementById('edit_type');
        select.value = type;

        // 3. Mở Modal
        document.getElementById('editBookModal').classList.remove('hidden');
    }
    // Hàm mở modal xóa và điền ID
function confirmDelete(id) {
    // 1. Điền ID vào ô input ẩn trong form xóa
    document.getElementById('delete_book_id').value = id;
    
    // 2. Mở Modal Xóa
    document.getElementById('deleteBookModal').classList.remove('hidden');
}
</script>

@endsection