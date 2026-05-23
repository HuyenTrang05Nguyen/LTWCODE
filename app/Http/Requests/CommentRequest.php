<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    /**
     * Xác định người dùng có quyền thực hiện hành động gửi bình luận này hay không
     */
    public function authorize(): bool
    {
        // Trả về true nếu người dùng đã đăng nhập (auth()->check()), bắt buộc phải có tài khoản mới được bình luận
        return auth()->check();
    }

    /**
     * Khai báo các quy tắc xác thực (Validation Rules) áp dụng cho dữ liệu ô bình luận
     */
    public function rules(): array
    {
        return [
            // Trường 'content': Bắt buộc nhập, phải là chuỗi văn bản, tối thiểu 2 ký tự và tối đa 1000 ký tự
            'content' => 'required|string|min:2|max:1000',
        ];
    }

    /**
     * Tùy biến các câu thông báo lỗi (Error Messages) bằng tiếng Việt khi vi phạm quy tắc
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Nội dung bình luận không được để trống.',
            'content.min'      => 'Bình luận phải có ít nhất 2 ký tự.',
            'content.max'      => 'Bình luận tối đa 1000 ký tự.',
        ];
    }
}