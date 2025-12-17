<?php

namespace App\Http\Controllers\page;

use App\Http\Controllers\Controller;
use App\Models\StudentModel;
use App\Services\ReaderManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class QuanLyBanDocController extends Controller
{
    public function __construct(
        private readonly ReaderManagementService $readerService
    ) {}
    public function index(): View
    {
        $data = [
            'title' => 'Quản Lý Bạn Đọc',
            'titleWeb' => 'Quản Lý Thư Viện- Bạn đọc',
            'readers' => $this->readerService->getReaders()
        ];
        return view('page.QuanLyBanDoc', $data); //
    }

    public function searchReader(Request $request)
    {
        $query = $request->input('query'); // Lấy từ khóa tìm kiếm

        // Nếu không có từ khóa, trả về danh sách tất cả sách
        $readers = StudentModel::whereRaw('LOWER(student_name) LIKE ?', ['%' . strtolower($query) . '%'])
            ->orWhereRaw('LOWER(student_code) LIKE ?', ['%' . strtolower($query) . '%'])
            ->orWhereRaw('LOWER(institute) LIKE ?', ['%' . strtolower($query) . '%'])
            ->orWhereRaw('LOWER(class) LIKE ?', ['%' . strtolower($query) . '%'])
            ->orWhereRaw('LOWER(school_year) LIKE ?', ['%' . strtolower($query) . '%'])
            ->orWhereRaw('LOWER(ban) LIKE ?', ['%' . strtolower($query) . '%'])
            ->paginate(10)
            ->appends(['query' => $query]);

        $data = [
            'title' => 'Quản Lý Bạn Đọc',
            'titleWeb' => 'Quản Lý Thư Viện - Bạn đọc',
            'readers' => $readers
        ];
        return view('page.QuanLyBanDoc', $data);
    }
    public function banReader(Request $request)
    {
        return response()->json($this->readerService->banReader($request->all()));
    }


    public function checkBanReader(Request $request)
    {
        return response()->json($this->readerService->checkBanStatus($request->student_code));
    }
}
