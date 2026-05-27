<?php
// Mở thẻ PHP

namespace App\Http\Controllers;
// Namespace của controller

use Illuminate\Http\Request;
// Dùng để lấy dữ liệu từ form/request

use Illuminate\Support\Facades\Auth;
// Facade xử lý đăng nhập, đăng xuất

use Illuminate\Support\Facades\Hash;
// Dùng mã hóa password

use Illuminate\Support\Facades\Password;
// Dùng chức năng reset password của Laravel

use App\Models\User;
// Import model User

class AuthController extends Controller
// Controller xử lý xác thực tài khoản
{
    public function showLogin()
    // Hàm hiển thị giao diện login
    {
        return view('auth.login');
        // Mở file:
        // resources/views/auth/login.blade.php
    }

    public function login(Request $request)
    // Hàm xử lý đăng nhập
    {
        $credentials = $request->validate([
        // Validate dữ liệu form login

            'email' => 'required|email',
            // Email:
            // - bắt buộc nhập
            // - đúng định dạng email

            'password' => 'required',
            // Password bắt buộc nhập

        ], [
        // Custom message lỗi tiếng Việt

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
        // Auth::attempt()
        // -> kiểm tra email + password

        // Nếu đúng:
        // Laravel tự tạo session đăng nhập

        // boolean('remember')
        // -> lấy giá trị checkbox "remember me"

            $request->session()->regenerate();
            // Tạo session ID mới để bảo mật

            // Chống:
            // Session Fixation Attack

            if (auth()->user()->isAdmin()) {
            // Kiểm tra user có phải admin không

                // Admin không cần chatbot pop-up

                return redirect()->route('admin.dashboard');
                // Chuyển admin vào dashboard
            }

            // Flash session để chatbot tự mở

            return redirect()->intended('/')
            // intended()
            // -> quay lại URL trước login

            ->with('chatbot_open', true);

            // chatbot_open = true
            // frontend có thể tự bật chatbot
        }

        return back()->withErrors([
        // Nếu login sai

            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])

        ->onlyInput('email');
        // Giữ lại email cũ
        // KHÔNG giữ password
    }

    public function showRegister()
    // Hiển thị form register
    {
        return view('auth.register');
        // Mở file register.blade.php
    }

    public function register(Request $request)
    // Xử lý đăng ký tài khoản
    {
        $request->validate([
        // Validate dữ liệu

            'name' => 'required|string|max:255',
            // Tên bắt buộc
            // kiểu string
            // tối đa 255 ký tự

            'email' => 'required|string|email|max:255|unique:users',
            // Email:
            // - đúng format
            // - không được trùng database

            'password' => 'required|string|min:8|confirmed',
            // Password:
            // - ít nhất 8 ký tự
            // - phải khớp password_confirmation

        ], [

            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = User::create([
        // Tạo user mới trong database

            'name' => $request->name,
            // Lưu tên

            'email' => $request->email,
            // Lưu email

            'password' => Hash::make($request->password),
            // Hash::make()
            // -> mã hóa password trước khi lưu

            'role' => 'user',
            // Role mặc định là user
        ]);

        Auth::login($user);
        // Đăng nhập luôn sau khi register

        return redirect('/')
        // Chuyển về trang chủ

        ->with('success', 'Đăng ký thành công! Chào mừng bạn.');
        // Flash message thành công
    }

    public function logout(Request $request)
    // Hàm logout
    {
        Auth::logout();
        // Xóa trạng thái đăng nhập

        $request->session()->invalidate();
        // Xóa session hiện tại

        $request->session()->regenerateToken();
        // Tạo CSRF token mới để bảo mật

        return redirect('/')
        // Quay về trang chủ

        ->with('success', 'Đã đăng xuất thành công!');
    }

    // -------------------------------------------------------
    // Quên mật khẩu
    // -------------------------------------------------------

    public function showForgotPassword()
    // Hiển thị form quên mật khẩu
    {
        return view('auth.forgot-password');
        // Mở forgot-password.blade.php
    }

    public function sendResetLink(Request $request)
    // Gửi email reset password
    {
        $request->validate([
        // Validate email

            'email' => 'required|email|exists:users,email',
            // exists:users,email
            // -> email phải tồn tại database

        ], [

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.exists' => 'Email này chưa được đăng ký trong hệ thống.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Laravel tự:
        // - tạo token reset
        // - gửi mail
        // - lưu token database

        if ($status === Password::RESET_LINK_SENT) {
        // Nếu gửi mail thành công

            return back()->with(
                'success',
                'Đã gửi link đặt lại mật khẩu vào email của bạn!'
            );
        }

        return back()->withErrors([
            'email' => 'Không thể gửi email. Vui lòng thử lại.'
        ]);
        // Nếu gửi mail lỗi
    }

    public function showResetPassword(string $token)
    // Hiển thị form nhập password mới
    {
        return view('auth.reset-password', [
            'token' => $token
        ]);

        // Truyền token sang view
    }

    public function resetPassword(Request $request)
    // Xử lý reset password
    {
        $request->validate([
        // Validate dữ liệu reset password

            'token' => 'required',
            // Token bắt buộc

            'email' => 'required|email',
            // Email hợp lệ

            'password' => 'required|min:8|confirmed',
            // Password >= 8 ký tự
            // phải khớp confirmation

        ], [

            'email.required' => 'Vui lòng nhập email.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $status = Password::reset(
        // Laravel xử lý reset password

            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),

            function (User $user, string $password) {
            // Callback khi token hợp lệ

                $user->forceFill([
                // forceFill()
                // -> gán dữ liệu trực tiếp

                    'password' => Hash::make($password)
                    // Hash password mới
                ])->save();

                // Lưu password mới vào database
            }
        );

        if ($status === Password::PASSWORD_RESET) {
        // Nếu reset thành công

            return redirect()->route('login')

            ->with(
                'success',
                'Đặt lại mật khẩu thành công! Vui lòng đăng nhập.'
            );
        }

        return back()->withErrors([
            'email' => 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.'
        ]);
        // Nếu token lỗi/hết hạn
    }
}
