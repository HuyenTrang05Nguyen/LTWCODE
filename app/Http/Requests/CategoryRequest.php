<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Xác định người dùng có quyền thực hiện yêu cầu (Request) này hay không
     */
    public function authorize(): bool
    {
        // Trả về true để cho phép tất cả người dùng (đã qua bộ lọc Middleware) có thể gửi dữ liệu lên form này
        return true;
    }

    /**
     * Khai báo các quy tắc xác thực (Validation Rules) áp dụng cho các trường dữ liệu trong form
     */
    public function rules(): array
    {
        return [
            // Trường 'name' (Tên danh mục): Bắt buộc phải nhập, định dạng chuỗi văn bản, tối đa 255 ký tự
            'name' => 'required|string|max:255',
            
            // Trường 'description' (Mô tả): Không bắt buộc nhập, nếu nhập phải là chuỗi văn bản, tối đa 1000 ký tự
            'description' => 'nullable|string|max:1000',
            
            // Trường 'image' (Ảnh danh mục): Không bắt buộc nhập, nếu có thì phải là file ảnh (jpeg, png, jpg, gif, webp), dung lượng tối đa 2048 KB (2MB)
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    /**
     * Tùy biến các câu thông báo lỗi (Error Messages) bằng tiếng Việt khi dữ liệu vi phạm quy tắc quy định ở trên
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.max'      => 'Tên danh mục tối đa 255 ký tự.',
            'image.image'   => 'File phải là hình ảnh.',
            'image.max'     => 'Hình ảnh tối đa 2MB.',
        ];
    }
}