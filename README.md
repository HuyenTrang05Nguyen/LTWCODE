# 🌍 Cẩm Nang Du Lịch Việt

Website chia sẻ kinh nghiệm và cẩm nang du lịch Việt Nam, xây dựng bằng **Laravel 12**.

🔗 **Demo trực tuyến:** [https://du-lich-viet.onrender.com](https://du-lich-viet.onrender.com) *(cập nhật sau khi deploy)*

---

## ✨ Tính năng

### 👤 Người dùng
- Đăng ký / Đăng nhập / Quên mật khẩu
- Xem danh sách và chi tiết bài viết
- Tìm kiếm theo tiêu đề, nội dung, địa điểm
- Lọc theo danh mục, sắp xếp (mới nhất / phổ biến)
- Bình luận bài viết (chờ admin duyệt)
- Lưu bài viết yêu thích
- Đánh giá bài viết (1–5 sao)
- Cập nhật hồ sơ cá nhân & avatar

### 👑 Quản trị viên
- Dashboard thống kê với biểu đồ Chart.js
- Quản lý bài viết: thêm, sửa, xóa, lọc
- Quản lý danh mục: CRUD đầy đủ
- Quản lý người dùng: đổi role, xóa
- Kiểm duyệt bình luận: duyệt, ẩn, xóa

---

## 🛠 Công nghệ

| Thành phần | Công nghệ |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Database | MySQL |
| Frontend | Bootstrap 5, Font Awesome 6, Chart.js |
| Build tool | Vite |
| Deploy | Render.com |

---

## ⚙️ Cài đặt Local (XAMPP)

```bash
# 1. Clone dự án
git clone https://github.com/your-username/du-lich.git
cd du-lich

# 2. Cài PHP dependencies
composer install

# 3. Cấu hình môi trường
cp .env.example .env
# Mở .env, sửa thông tin database:
# DB_DATABASE=du_lich_db
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Tạo APP_KEY
php artisan key:generate

# 5. Tạo database và chạy migration + seed
php artisan migrate:fresh --seed

# 6. Tạo symlink storage (hiển thị ảnh upload)
php artisan storage:link

# 7. Build frontend
npm install && npm run build

# 8. Chạy server
php artisan serve
```

Truy cập: `http://localhost:8000`

---

## 🔐 Tài khoản mặc định (sau khi seed)

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Admin | admin@dulich.com | password |
| User | user1@dulich.com | password |
| User | user2@dulich.com | password |

---

## 🚀 Deploy lên Render.com (Miễn phí)

### Bước 1 — Chuẩn bị database MySQL miễn phí

Dùng **[Aiven.io](https://aiven.io)** hoặc **[Railway.app](https://railway.app)** để tạo MySQL miễn phí:

1. Đăng ký tài khoản tại [Aiven.io](https://aiven.io)
2. Tạo service **MySQL** (free tier)
3. Lưu lại: `Host`, `Port`, `Database`, `Username`, `Password`

### Bước 2 — Push code lên GitHub

```bash
git add .
git commit -m "Ready for deploy"
git push origin main
```

### Bước 3 — Tạo Web Service trên Render

1. Đăng ký tại [render.com](https://render.com)
2. Chọn **New → Web Service**
3. Kết nối GitHub repo của bạn
4. Cấu hình:

| Trường | Giá trị |
|---|---|
| **Runtime** | PHP |
| **Build Command** | `composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache` |
| **Start Command** | `php artisan serve --host=0.0.0.0 --port=$PORT` |
| **Plan** | Free |

### Bước 4 — Cấu hình Environment Variables trên Render

Vào tab **Environment** của service, thêm các biến sau:

```
APP_NAME=Du Lich Viet
APP_ENV=production
APP_DEBUG=false
APP_KEY=                    ← Chạy: php artisan key:generate --show
APP_URL=https://your-app.onrender.com

DB_CONNECTION=mysql
DB_HOST=your-aiven-host
DB_PORT=3306
DB_DATABASE=du_lich_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
LOG_CHANNEL=stderr
LOG_LEVEL=error
```

### Bước 5 — Chạy Migration trên Render

Sau khi deploy xong, vào **Shell** của Render service và chạy:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

### Bước 6 — Truy cập website

URL sẽ có dạng: `https://du-lich-viet.onrender.com`

---

## 📁 Cấu trúc thư mục chính

```
du-lich/
├── app/
│   ├── Http/Controllers/       # Controllers (Admin + Frontend)
│   ├── Models/                 # Eloquent Models
│   └── Http/Middleware/        # AdminMiddleware
├── database/
│   ├── migrations/             # Database schema
│   └── seeders/                # Dữ liệu mẫu
├── resources/views/            # Blade templates
│   ├── layouts/                # app.blade.php, admin.blade.php
│   ├── admin/                  # Giao diện admin
│   ├── posts/                  # Trang bài viết
│   └── auth/                   # Đăng nhập, đăng ký
├── routes/web.php              # Định nghĩa routes
├── render.yaml                 # Cấu hình Render.com
├── deploy.sh                   # Script deploy tự động
└── .env.production.example     # Template biến môi trường production
```

---

## 📝 Ghi chú

- Ảnh upload được lưu trong `storage/app/public/` — cần chạy `php artisan storage:link` sau deploy
- Trên Render free tier, server sẽ ngủ sau 15 phút không có request (spin-up ~30 giây)
- Để reset password hoạt động trên production, cần cấu hình SMTP thật (Mailtrap, Gmail, v.v.)



