<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->route()->getName() === 'book.update';

        return [
            'book_code' => [
                'required',
                'string',
                'max:255',
                'regex:/^BK\d{4}$/',
                $isUpdate ? 'exists:books,book_code' : 'unique:books,book_code'
            ],
            'book_name'   => ['required', 'string', 'max:255'],
            'book_type'   => ['required', 'string', 'max:255'],
            'author'      => ['required', 'string', 'max:255'],
            'quantity'    => 'required|integer|min:1',
            'description' => 'nullable|string',
            'book_cover'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'book_code.required' => 'Mã sách không được để trống.',
            'book_code.string'   => 'Mã sách phải là chữ.',
            'book_code.max'      => 'Mã sách không quá 255 ký tự.',
            'book_code.regex' => 'Mã sách phải có định dạng BK**** (VD: BK1234).',
            'book_code.exists'      => 'Mã sách không tồn tại.',
            'book_code.unique'      => 'Mã sách đã tồn tại.',

            'book_name.required' => 'Tên sách không được để trống.',
            'book_name.string'   => 'Tên sách phải là chữ.',
            'book_name.max'      => 'Tên sách không quá 255 ký tự.',

            'book_type.required' => 'Loại sách không được để trống.',
            'book_type.string'   => 'Loại sách phải là chữ.',
            'book_type.max'      => 'Loại sách không quá 255 ký tự.',

            'author.required'    => 'Tác giả không được để trống.',
            'author.string'      => 'Tác giả phải là chữ.',
            'author.max'         => 'Tác giả không quá 255 ký tự.',

            'quantity.required'  => 'Số lượng sách không được để trống.',
            'quantity.integer'   => 'Số lượng sách phải là số.',
            'quantity.min'       => 'Số lượng sách phải lớn hơn 0.',
        ];
    }


    public function failedValidation(Validator $validator)
    {
        $isUpdate = $this->route()->getName() === 'book.update';
        throw new HttpResponseException(
            back()
                ->withErrors($validator)
                ->withInput()
                ->with($isUpdate ? 'validateUpdate' : 'validateAdd', true)
        );
    }
}
