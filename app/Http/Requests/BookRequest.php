<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép ai cũng được gửi dữ liệu
    }

    public function rules(): array
    {
        // Lấy ID sách nếu đang ở chức năng Cập nhật (để tránh báo lỗi trùng với chính nó)
        // Lưu ý: Kiểm tra route của bạn dùng tham số là 'id' hay 'book'
        // Nếu route là /update/{id} thì dùng $this->route('id')
        $id = $this->route('id'); 

        return [
            // QUAN TRỌNG: Kiểm tra trùng mã sách trong bảng books
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
            'book_code.unique'   => 'Mã sách này ĐÃ TỒN TẠI! Vui lòng nhập mã khác.',
            'book_name.required' => 'Tên sách không được để trống.',
            'book_type.required' => 'Vui lòng chọn thể loại sách.',
            'author.required'    => 'Tên tác giả không được để trống.',
            'quantity.required'  => 'Số lượng phải nhập và không được để trống.',
            'quantity.integer'   => 'Số lượng phải là số nguyên.',
            'quantity.min'       => 'Số lượng sách phải lớn hơn 0.',
        ];
    }
}