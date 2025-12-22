<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lấy ID sách hiện tại (nếu đang sửa). Nếu thêm mới thì $id sẽ là null.
        // Lưu ý: Kiểm tra xem trong file web.php route sửa của bạn là {id} hay {book}
        // Nếu là {book} thì sửa dòng dưới thành: $id = $this->route('book') ? $this->route('book')->id : null;
        $id = $this->route('id'); 

        return [
            // Rule: Bắt buộc nhập | Kiểm tra trùng trong bảng books, cột book_code, ngoại trừ id hiện tại
            'book_code' => 'required|unique:books,book_code,' . $id,
            'book_name' => 'required',
            'book_type' => 'required',
            'author'    => 'required',
            'quantity'  => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'book_code.required' => 'Mã sách không được để trống.',
            'book_code.unique'   => 'Mã sách này ĐÃ TỒN TẠI! Vui lòng chọn mã khác.',
            'book_name.required' => 'Tên sách không được để trống.',
            // Các thông báo khác...
        ];
    }
}