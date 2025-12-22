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
        // 1. Lấy ID sách từ trên thanh địa chỉ (URL) nếu đang sửa
        // Ví dụ: route là /update-book/{id} thì lấy cái {id} đó
        $id = $this->route('id'); 

        // 2. Tạo bộ luật
        return [
            // Luật: Bắt buộc nhập | Phải là duy nhất trong bảng books, cột book_code, trừ cái ID hiện tại ra
            'book_code' => 'required|unique:books,book_code,' . $id,
            
            'book_name' => 'required',
            'book_type' => 'required',
            'author'    => 'required',
            'quantity'  => 'required|integer|min:1',
            // Nếu bạn có input ảnh là 'book_cover', có thể thêm rule validate ảnh nếu muốn
            // 'book_cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ];
    }

    public function messages(): array
    {
        return [
            'book_code.required' => 'Mã sách không được để trống.',
            'book_code.unique'   => 'Mã sách này ĐÃ TỒN TẠI! Vui lòng chọn mã khác.', // Đây là dòng bạn đang cần
            'book_name.required' => 'Tên sách không được để trống.',
            'book_type.required' => 'Vui lòng chọn thể loại sách.',
            'author.required'    => 'Tên tác giả không được để trống.',
            'quantity.required'  => 'Số lượng không được để trống.',
            'quantity.integer'   => 'Số lượng phải là số nguyên.',
            'quantity.min'       => 'Số lượng phải lớn hơn 0.',
        ];
    }
}