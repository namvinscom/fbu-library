<?php

namespace App\Services;

use App\Models\BookModel;
use App\Models\BorrowModel;
use App\Models\StudentModel;

class BorrowReBorrowManagementService
{
    // ... (Các hàm getAllTransactions, checkBookQuantity, searchTransactions giữ nguyên) ...
    public function getAllTransactions()
    {
        return BorrowModel::query()
            ->join('books', 'borrows.book_code', '=', 'books.book_code')
            ->join('students', 'borrows.student_code', '=', 'students.student_code')
            ->select(
                'borrows.id',
                'borrows.return_day',
                'borrows.book_code',
                'borrows.borrow_day',
                'borrows.student_code',
                'borrows.quantity_borrow',
                'borrows.overdue',
                'borrows.is_return',
                'borrows.description',
                'books.book_name',
                'books.book_type',
                'students.student_name'
            )
            ->orderBy('borrows.created_at', 'desc') // Sắp xếp mới nhất lên đầu
            ->paginate(10);
    }
    
    public function checkBookQuantity($bookCode)
    {
        return BookModel::where('books.book_code', $bookCode)
            ->leftJoin('borrows', function ($join) {
                $join->on('borrows.book_code', '=', 'books.book_code')
                    ->where('borrows.is_return', '=', 0);
            })
            ->selectRaw('books.quantity, books.broken, COALESCE(SUM(borrows.quantity_borrow), 0) as borrowed_quantity')
            ->groupBy('books.book_code', 'books.quantity', 'books.broken')
            ->first();
    }

    public function searchTransactions($query)
    {
        return BorrowModel::where(function ($q) use ($query) {
            $q->whereRaw('LOWER(borrows.return_day) LIKE ?', ['%' . strtolower($query) . '%'])
                ->orWhereRaw('LOWER(borrows.book_code) LIKE ?', ['%' . strtolower($query) . '%'])
                ->orWhereRaw('LOWER(borrows.student_code) LIKE ?', ['%' . strtolower($query) . '%'])
                ->orWhereRaw('LOWER(borrows.borrow_day) LIKE ?', ['%' . strtolower($query) . '%'])
                ->orWhereRaw('LOWER(borrows.description) LIKE ?', ['%' . strtolower($query) . '%']);
        })
            ->join('books', 'borrows.book_code', '=', 'books.book_code')
            ->join('students', 'borrows.student_code', '=', 'students.student_code')
            ->paginate(10)
            ->appends(['query' => $query]);
    }

    // --- PHẦN SỬA LỖI MƯỢN ---
    public function handleAddTransaction($data)
    {
        // 1. Kiểm tra Sinh viên có tồn tại và có bị khóa không
        $student = StudentModel::where('student_code', $data['student_code'])->first();
        
        if (!$student) {
            return 'student_not_found'; // Không tìm thấy SV
        }

        // Giả sử cột trạng thái là 'status'. 
        // Nếu trong DB bạn lưu "Bị khóa" là chuỗi thì so sánh chuỗi.
        // Nếu lưu là số (0: khóa, 1: hoạt động) thì sửa lại logic dưới đây.
        if ($student->status == 'Bị khóa' || $student->status == 0) { 
            return 'student_banned'; // Bị cấm mượn
        }

        // 2. Logic cộng dồn mượn (Giữ nguyên)
        $existTransaction = BorrowModel::where('student_code', $data['student_code'])
            ->where('book_code', $data['book_code'])
            ->where('is_return', 0)
            ->first();

        if ($existTransaction) {
            $existTransaction->quantity_borrow += $data['quantity'];
            $existTransaction->borrow_day = $data['borrow_date'];
            $existTransaction->return_day = $data['return_date'];
            $existTransaction->description = $data['description'] ?? null;
            $existTransaction->overdue = 0;
            return $existTransaction->save();
        }

        return BorrowModel::create([
            'book_code'        => $data['book_code'],
            'student_code'     => $data['student_code'],
            'quantity_borrow'  => $data['quantity'],
            'borrow_day'       => $data['borrow_date'],
            'return_day'       => $data['return_date'],
            'description'      => $data['description'] ?? null,
            'overdue'          => 0,
            'is_return'        => 0,
        ]);
    }

    // ... (Hàm extendBook giữ nguyên) ...
     public function extendBook($data)
    {
        $transaction = BorrowModel::find($data['transaction_id']);

        if (!$transaction) {
            return false;
        }

        $transaction->return_day = $data['new_due_date'];
        $transaction->description = $data['note'];
        return $transaction->save();
    }

    // --- PHẦN SỬA LỖI TRẢ SÁCH (QUAN TRỌNG) ---
    // Thay vì tìm theo book_code, ta tìm chính xác theo ID phiếu mượn
    public function returnBook($transactionId, $quantityReturn)
    {
        $transaction = BorrowModel::find($transactionId);

        if (!$transaction) {
            return false; 
        }

        // Trừ số lượng
        if ($quantityReturn < $transaction->quantity_borrow) {
            $transaction->quantity_borrow -= $quantityReturn;
        } else {
            // Nếu trả hết hoặc trả dư -> Đánh dấu đã trả xong
            $transaction->is_return = 1;
        }

        return $transaction->save();
    }
}