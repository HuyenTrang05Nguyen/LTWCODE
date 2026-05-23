<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Xác định người dùng có quyền thực hiện yêu cầu tạo/sửa bài viết này hay không
     */
    public function authorize(): bool
    {
        // Trả về true để cho phép tiếp tục xử lý (việc phân quyền chi tiết đã được đảm nhận bởi Middleware)
        return true;
    }

    /**
     * Khai báo các quy tắc xác thực (Validation Rules) cho các trường thông tin của bài viết
     */
    public function rules(): array
    {
        return [
            // Tiêu đề bài viết: Bắt buộc nhập, phải là chuỗi văn bản, không vượt quá 255 ký tự
            'title' => 'required|string|max:255',
            
            // Danh mục bài viết: Bắt buộc chọn, và giá trị gửi lên phải tồn tại (exists) trong cột 'id' của bảng 'categories'
            'category_id' => 'required|exists:categories,id',
            
            // Mô tả ngắn: Không bắt buộc nhập, nếu có nhập thì tối đa là 500 ký tự
            'excerpt' => 'nullable|string|max:500',
            
            // Nội dung bài viết: Bắt buộc nhập, định dạng chuỗi và phải có độ dài tối thiểu từ 10 ký tự trở lên
            'content' => 'required|string|min:10',
            
            // Ảnh đại diện bài viết: Không bắt buộc, nếu có phải là file ảnh định dạng chuẩn, dung lượng tối đa 2MB (2048 KB)
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            
            // Địa điểm/Điểm đến: Không bắt buộc nhập, tối đa là 255 ký tự
            'location' => 'nullable|string|max:255',
            
            // Trạng thái bài viết: Bắt buộc phải chọn và giá trị chỉ được phép nằm trong danh sách (in) quy định: 'draft' (nháp) hoặc 'published' (xuất bản)
            'status' => 'required|in:draft,published',
        ];
    }

    /**
     * Tùy biến các thông báo lỗi hiển thị bằng tiếng Việt khi dữ liệu đầu vào vi phạm các quy tắc trên
     */
    public function messages(): array
    {
        return [
            'title.required'        => 'Tiêu đề bài viết không được để trống.',
            'title.max'             => 'Tiêu đề tối đa 255 ký tự.',
            'category_id.required'  => 'Vui lòng chọn danh mục.',
            'category_id.exists'    => 'Danh mục không hợp lệ.',
            'content.required'      => 'Nội dung bài viết không được để trống.',
            'content.min'           => 'Nội dung phải có ít nhất 10 ký tự.',
            'image.image'           => 'File phải là hình ảnh.',
            'image.max'             => 'Hình ảnh tối đa 2MB.',
            'status.required'       => 'Vui lòng chọn trạng thái.',
        ];
    }
}