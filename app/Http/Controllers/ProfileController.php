<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang thông tin cá nhân (Profile) của người dùng hiện tại
     */
    public function show()
    {
        // Lấy thông tin của người dùng đã đăng nhập thành công vào hệ thống
        $user = auth()->user();
        
        // Lấy danh sách các bài viết do chính user này đăng, sắp xếp mới nhất và phân trang (6 bài/trang)
        $posts = $user->posts()->latest()->paginate(6);
        
        // Trả về view và truyền dữ liệu user cùng danh sách bài viết qua hàm compact
        return view('profile.show', compact('user', 'posts'));
    }

    /**
     * Cập nhật thông tin cá nhân, ảnh đại diện và mật khẩu của người dùng
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        // Xác thực dữ liệu đầu vào (Validation) để đảm bảo an toàn hệ thống
        $request->validate([
            'name' => 'required|string|max:255',
            // Kiểm tra email là duy nhất trong bảng users nhưng loại trừ ID của chính user hiện tại để tránh lỗi trùng lặp khi không sửa email
            'email' => 'required|email|unique:users,email,' . $user->id,
            // Định dạng ảnh đại diện hợp lệ và giới hạn dung lượng tối đa 2048 KB (2MB)
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            // Mật khẩu không bắt buộc nhập, nhưng nếu nhập thì phải từ 8 ký tự trở lên và phải khớp với ô xác nhận (password_confirmation)
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Gán các giá trị cơ bản từ form gửi lên vào model user
        $user->name = $request->name;
        $user->email = $request->email;

        // Xử lý upload ảnh đại diện mới (nếu có file đính kèm gửi lên)
        if ($request->hasFile('avatar')) {
            // Nếu người dùng đã có ảnh đại diện cũ trước đó, tiến hành xóa file cũ trong ổ đĩa 'public' để tránh rác server
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Lưu file ảnh mới vào thư mục 'avatars' thuộc ổ đĩa 'public' và gán lại đường dẫn vào database
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        // Xử lý thay đổi mật khẩu (chỉ thực hiện nếu người dùng có nhập vào ô mật khẩu)
        if ($request->filled('password')) {
            // Sử dụng Facade Hash::make để mã hóa một chiều mật khẩu trước khi lưu vào database nhằm bảo mật thông tin
            $user->password = Hash::make($request->password);
        }

        // Lưu toàn bộ các thay đổi vào cơ sở dữ liệu
        $user->save();

        // Quay trở lại trang trước đó kèm theo một thông báo thành công (Flash Session)
        return back()->with('success', 'Cập nhật thông tin thành công!');
    }
}