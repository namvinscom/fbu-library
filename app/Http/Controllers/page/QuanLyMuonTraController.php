<?php

namespace App\Http\Controllers\page;

use App\Http\Controllers\Controller;
use App\Http\Requests\borrow\BorrowRequest;
use App\Http\Requests\borrow\ExtendRequest;
use App\Services\BorrowReBorrowManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class QuanLyMuonTraController extends Controller
{
    public function __construct(
        private readonly BorrowReBorrowManagementService $borrowService
    ) {}

    public function index(): View
    {
        return view('page.QuanLyMuonTra', [
            'title' => 'Quản Lý Mượn Trả',
            'titleWeb' => 'Quản Lý Thư Viện - Mượn Trả',
            'transactions' => $this->borrowService->getAllTransactions(),
        ]);
    }

    public function handleBorrow(Request $request): View
    {
        return view('page.QuanLyMuonTra', [
            'title' => 'Quản Lý Mượn Trả',
            'titleWeb' => 'Quản Lý Thư Viện - Mượn Trả',
            'transactions' => $this->borrowService->getAllTransactions(),
            'student_code' => $request->student_code,
        ]);
    }

    public function checkBookQuantity(Request $request)
    {
        $book = $this->borrowService->checkBookQuantity($request->book_code);

        if (!$book) {
            return response()->json(['error' => 'Không tìm thấy sách']);
        }

        $availableBooks = max(0, ($book->quantity ?? 0) - ($book->broken ?? 0) - ($book->borrowed_quantity ?? 0));
        return response()->json(['quantity' => $availableBooks]);
    }


    public function searchTransaction(Request $request)
    {
        return view('page.QuanLyMuonTra', [
            'title' => 'Quản Lý Mượn Trả',
            'titleWeb' => 'Quản Lý Thư Viện - Mượn Trả',
            'transactions' => $this->borrowService->searchTransactions($request->input('query')),
        ]);
    }

    // --- CẬP NHẬT 1: Sửa hàm Ghi Mượn để bắt lỗi Sinh viên bị khóa ---
    public function handleAddTransaction(BorrowRequest $request)
    {
        // Gọi Service và hứng kết quả trả về
        $result = $this->borrowService->handleAddTransaction($request->validated());

        // Kiểm tra các mã lỗi cụ thể từ Service
        if ($result === 'student_not_found') {
            return redirect()->route('qlmt')->with([
                'type' => 'error', 
                'message' => 'Lỗi: Mã sinh viên không tồn tại trong hệ thống!'
            ]);
        }

        if ($result === 'student_banned') {
            return redirect()->route('qlmt')->with([
                'type' => 'error', 
                'message' => 'CẢNH BÁO: Sinh viên này đang bị KHÓA (Vi phạm), không được phép mượn sách!'
            ]);
        }

        // Nếu thành công (trả về Object hoặc true)
        if ($result) {
            return redirect()->route('qlmt')->with(['type' => 'success', 'message' => 'Ghi mượn thành công!']);
        }

        // Trường hợp lỗi không xác định
        return redirect()->route('qlmt')->with(['type' => 'error', 'message' => 'Ghi mượn thất bại!']);
    }

    public function extendBook(ExtendRequest $request)
    {
        $this->borrowService->extendBook($request->all());

        return redirect()->route('qlmt')->with(['type' => 'success', 'message' => 'Gia hạn thành công!']);
    }

    // --- CẬP NHẬT 2: Sửa hàm Trả Sách để dùng Transaction ID ---
    public function returnBook(Request $request)
    {
        // Lấy ID phiếu mượn từ input ẩn (transaction_id) thay vì book_code
        // Vì book_code có thể trùng nhau giữa nhiều người mượn
        $transactionId = $request->input('transaction_id');
        $quantity = $request->input('return_quantity');

        $save = $this->borrowService->returnBook($transactionId, $quantity);

        if (!$save) {
            return redirect()->route('qlmt')->with([
                'type' => 'error',
                'message' => 'Lỗi: Không tìm thấy phiếu mượn hoặc có lỗi xảy ra!'
            ]);
        }

        return redirect()->route('qlmt')->with([
            'type' => 'success',
            'message' => 'Trả sách thành công!'
        ]);
    }
}