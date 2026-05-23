<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class AuthController extends Controller
{
    // Hiển thị giao diện form đăng nhập
    public function showLogin()
    {
        return view('auth.login');
    }

    // Xử lý logic khi người dùng nhấn nút Đăng nhập
    public function login(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào (Validation) kèm thông báo lỗi tiếng Việt
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        // Thực hiện xác thực tài khoản và mật khẩu, kèm tính năng "Ghi nhớ đăng nhập" nếu tích chọn
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Đăng nhập đúng: Làm mới ID Session để chống tấn công cố định tài khoản (Session Fixation)
            $request->session()->regenerate();

            // Nếu tài khoản là Admin, chuyển thẳng hướng vào trang quản trị (Dashboard Admin)
            if (auth()->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            // Nếu là User thường, chuyển về trang chủ và gửi kèm tín hiệu (Flash Session) để tự bật Chatbot lên chào
            return redirect()->intended('/')->with('chatbot_open', true);
        }

        // Đăng nhập thất bại: Quay lại trang trước, báo lỗi và giữ lại email người dùng vừa nhập trong ô input
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    // Hiển thị giao diện form đăng ký tài khoản mới
    public function showRegister()
    {
        return view('auth.register');
    }

    // Xử lý logic khi người dùng nhấn nút Đăng ký tài khoản
    public function register(Request $request)
    {
        // Ràng buộc dữ liệu: Email không được trùng (unique:users), mật khẩu tối thiểu 8 ký tự và phải khớp với ô nhập lại
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        // Tạo người dùng mới trong database, mật khẩu bắt buộc phải mã hóa bằng Hash::make()
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Mặc định tài khoản đăng ký mới là thành viên thường (user)
        ]);

        // Sau khi đăng ký thành công, tự động đăng nhập luôn cho người dùng vừa tạo
        Auth::login($user);

        // Chuyển hướng về trang chủ kèm thông báo thành công màu xanh
        return redirect('/')->with('success', 'Đăng ký thành công! Chào mừng bạn.');
    }

    // Xử lý đăng xuất tài khoản khỏi hệ thống
    public function logout(Request $request)
    {
        Auth::logout(); // Xóa trạng thái đăng nhập của người dùng

        $request->session()->invalidate(); // Hủy bỏ toàn bộ dữ liệu phiên làm việc (Session) cũ

        $request->session()->regenerateToken(); // Làm mới mã bảo mật CSRF Token để tránh bị khai thác bảo mật

        return redirect('/')->with('success', 'Đã đăng xuất thành công!');
    }

    // -------------------------------------------------------
    // QUÊN MẬT KHẨU & ĐẶT LẠI MẬT KHẨU
    // -------------------------------------------------------

    // Hiển thị form nhập email để yêu cầu cấp lại mật khẩu
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    // Xử lý gửi đường dẫn đặt lại mật khẩu (Reset Link) vào hòm thư điện tử
    public function sendResetLink(Request $request)
    {
        // Kiểm tra tính hợp lệ và đảm bảo email này phải tồn tại trong bảng users (exists:users,email)
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.exists' => 'Email này chưa được đăng ký trong hệ thống.',
        ]);

        // Sử dụng chức năng có sẵn của Laravel gửi link chứa mã Token bảo mật qua email
        $status = Password::sendResetLink($request->only('email'));

        // Kiểm tra trạng thái gửi, nếu thành công thì thông báo cho người dùng check mail
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Đã gửi link đặt lại mật khẩu vào email của bạn!');
        }

        return back()->withErrors(['email' => 'Không thể gửi email. Vui lòng thử lại.']);
    }

    // Hiển thị form điền mật khẩu mới khi người dùng click vào link gửi kèm Token từ Mailbox
    public function showResetPassword(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    // Xử lý lưu mật khẩu mới sau khi người dùng điền form đặt lại mật khẩu
    public function resetPassword(Request $request)
    {
        // Kiểm tra tính hợp lệ của mật khẩu mới (tối thiểu 8 ký tự và phải khớp với ô xác nhận)
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        // Thực hiện cập nhật mật khẩu mới thông qua Token xác thực
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                // forceFill(): Ép buộc cập nhật trực tiếp trường password mà bỏ qua bộ lọc $fillable trong Model
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        // Nếu mã Token hợp lệ và đổi thành công, chuyển hướng người dùng ra trang Đăng nhập để đăng nhập lại
        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập.');
        }

        // Nếu link hết hạn hoặc mã Token sai lệch, báo lỗi ra màn hình
        return back()->withErrors(['email' => 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.']);
    }
}