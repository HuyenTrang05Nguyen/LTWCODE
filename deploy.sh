#!/bin/bash
# =============================================================
# Script deploy cho Render.com / VPS / Hosting Linux
# Chạy: bash deploy.sh
# =============================================================

set -e  # Dừng nếu có lỗi

echo "🚀 Bắt đầu deploy Du Lịch Việt..."

# 1. Cài đặt PHP dependencies (không có dev packages)
echo "📦 Cài đặt Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# 2. Build frontend assets
echo "🎨 Build frontend assets..."
npm ci
npm run build

# 3. Tạo APP_KEY nếu chưa có
if [ -z "$APP_KEY" ]; then
    echo "🔑 Tạo APP_KEY..."
    php artisan key:generate --force
fi

# 4. Chạy migrations
echo "🗄️  Chạy database migrations..."
php artisan migrate --force

# 5. Tạo symlink storage
echo "🔗 Tạo storage symlink..."
php artisan storage:link || true

# 6. Cache config, routes, views để tăng performance
echo "⚡ Cache config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Optimize autoloader
echo "🔧 Optimize autoloader..."
php artisan optimize

echo "✅ Deploy hoàn tất!"
