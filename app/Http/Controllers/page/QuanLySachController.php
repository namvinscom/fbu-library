<?php

namespace App\Http\Controllers\page;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Services\BookManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class QuanLySachController extends Controller
{
    public function __construct(
        private readonly BookManagementService $bookService
    ) {}

    public function index(): View
    {
        $data = [
            'title' => 'Quản Lý Tài Liệu',
            'titleWeb' => 'Quản Lý Thư Viện - Sách',
            'books' => $this->bookService->getAllBooks()
        ];
        return view('page.QuanLySach', $data);
    }

    public function addBook(BookRequest $request)
    {
        // Xử lý upload ảnh
        if ($request->hasFile('book_cover')) {
            $file = $request->file('book_cover');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/image', $filename);
            
            // Gộp tên ảnh vào request
            $request->merge(['book_cover' => $filename]);
        }

        // --- SỬA: Chỉ gọi hàm createBook ĐÚNG 1 LẦN ---
        $result = $this->bookService->createBook($request);

        if ($result) {
            return redirect()->route('qls')->with(['type' => 'success', 'message' => 'Thêm sách thành công!']);
        } else {
            return redirect()->route('qls')->with(['type' => 'error', 'message' => 'Thêm sách thất bại!']);
        }
    }

    public function updateBook(BookRequest $request)
    {
        // Xử lý upload ảnh khi sửa
        if ($request->hasFile('book_cover')) {
            $file = $request->file('book_cover');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/image', $filename);
            $request->merge(['book_cover' => $filename]);
        }

        // --- SỬA: Chỉ gọi hàm updateBook ĐÚNG 1 LẦN ---
        $result = $this->bookService->updateBook($request);

        if ($result) {
            return redirect()->route('qls')->with(['type' => 'success', 'message' => 'Sửa sách thành công!']);
        } else {
            return redirect()->route('qls')->with(['type' => 'error', 'message' => 'Sửa sách thất bại!']);
        }
    }

    public function deleteBook($id)
    {
        $result = $this->bookService->deleteBook($request->id);

        if ($result) {
            return response()->json(['message' => 'Xóa sách thành công!'], 200);
        } else {
            return response()->json(['message' => 'Không tìm thấy sách để xóa!'], 404);
        }
    }

    public function searchBook(Request $request)
    {
        $books = $this->bookService->searchBooks($request->input('query'));
        $data = [
            'title' => 'Quản Lý Tài Liệu',
            'titleWeb' => 'Quản Lý Thư Viện - Sách',
            'books' => $books
        ];

        return view('page.QuanLySach', $data);
    }
}