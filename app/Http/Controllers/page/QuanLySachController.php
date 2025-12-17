<?php

namespace App\Http\Controllers\page;

use App\Http\Controllers\Controller;
use App\Http\Requests\book\BookRequest;
use App\Models\BookModel;
use App\Services\BookManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $result = $this->bookService->createBook($request);
        if ($result) {
            return redirect()->route('qls')->with(['type' => 'success', 'message' =>  'Thêm sách thành công!']);
        } else {
            return redirect()->route('qls')->with(['type' => 'error', 'message' =>  'Thêm sách thất bại!']);
        }
    }
    public function updateBook(BookRequest $request)
    {
        $result =  $this->bookService->updateBook($request);
        if ($result) {
            return redirect()->route('qls')->with(['type' => 'success', 'message' =>  'Sửa sách thành công!']);
        } else {
            return redirect()->route('qls')->with(['type' => 'error', 'message' =>  'Sửa sách thất bại!']);
        }
    }


    public function deleteBook($id)
    {
        $result = $this->bookService->deleteBook($id);

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
