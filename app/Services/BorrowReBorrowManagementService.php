<?php

namespace App\Services;

use App\Models\BookModel;
use App\Models\BorrowModel;
use App\Models\StudentModel;

class BorrowReBorrowManagementService
{
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
            ->orderBy('borrows.created_at', 'desc')
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

    // --- PHẦN QUAN TRỌNG: SỬA LỖI LOGIC MƯỢN ---
    public function handleAddTransaction($data)
    {
        // 1. Kiểm tra Sinh viên
        $student = StudentModel::where('student_code', $data['student_code'])->first();
        
        if (!$student) {
            return 'student_not_found'; // Không tìm thấy SV
        }

        // 2. Kiểm tra trạng thái "Bị khóa"
        // Sử dụng === để so sánh chính xác tuyệt đối.
        // Tránh lỗi: "Hoạt động" == 0 (PHP cũ sẽ hiểu là True)
        
        // Nếu trạng thái là chuỗi "Bị khóa"
        if ($student->status === 'Bị khóa') { 
            return 'student_banned'; 
        }

        // Nếu trạng thái là số 0 hoặc chuỗi "0"
        if ($student->status === 0 || $student->status === '0') {
            return 'student_banned';
        }

        // 3. Logic cộng dồn phiếu mượn (nếu đã mượn sách này rồi)
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

        // 4. Tạo phiếu mượn mới
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

    // --- SỬA LỖI TRẢ SÁCH (Dùng ID phiếu mượn) ---
    public function returnBook($transactionId, $quantityReturn)
    {
        // Tìm đúng phiếu mượn dựa trên ID duy nhất
        $transaction = BorrowModel::find($transactionId);

        if (!$transaction) {
            return false; 
        }

        // Trừ số lượng
        if ($quantityReturn < $transaction->quantity_borrow) {
            $transaction->quantity_borrow -= $quantityReturn;
        } else {
            // Nếu trả hết -> Đánh dấu hoàn thành
            $transaction->is_return = 1;
        }

        return $transaction->save();
    }
}