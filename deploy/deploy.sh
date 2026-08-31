#!/usr/bin/env bash
# 在 EC2 上執行的部署腳本，由 GitHub Actions 透過 SSH 觸發，也可手動執行。
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP_DIR"

echo "==> 拉取最新程式碼"
git pull origin main

echo "==> 安裝 PHP 套件（正式環境，不含 dev 依賴）"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> 安裝並 build 前端資源"
npm ci
npm run build

echo "==> 執行資料庫遷移"
php artisan migrate --force

echo "==> 建立 storage 軟連結（已存在則略過）"
php artisan storage:link || true

echo "==> 重建快取"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> 重新載入 PHP-FPM"
sudo systemctl reload php8.2-fpm

echo "==> 部署完成"
