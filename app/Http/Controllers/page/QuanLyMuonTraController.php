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

    public function handleAddTransaction(BorrowRequest $request)
    {
        $this->borrowService->handleAddTransaction($request->validated());

        return redirect()->route('qlmt')->with(['type' => 'success', 'message' => 'Ghi mượn thành công!']);
    }

    public function extendBook(ExtendRequest $request)
    {
        $this->borrowService->extendBook($request->all());

        return redirect()->route('qlmt')->with(['type' => 'success', 'message' => 'Gia hạn thành công!']);
    }

    public function returnBook(Request $request)
    {
        $save = $this->borrowService->returnBook($request->book_code_return, $request->return_quantity);

        if (!$save) {
            return redirect()->route('qlmt')->with([
                'type' => 'error',
                'message' => 'Trả sách không thành công!'
            ]);
        }

        return redirect()->route('qlmt')->with([
            'type' => 'success',
            'message' => 'Trả sách thành công!'
        ]);
    }
}
