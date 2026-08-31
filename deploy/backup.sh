#!/usr/bin/env bash
# 每週備份 MySQL 與圖片目錄，只保留最近 KEEP 份。透過 crontab 排程執行。
set -euo pipefail

APP_DIR="/var/www/blog"
BACKUP_DIR="/var/backups/blog"
KEEP=5
DATE="$(date +%Y%m%d)"

mkdir -p "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR"

# 從 .env 讀取資料庫連線資訊，不寫死在腳本裡
DB_DATABASE="$(grep -E '^DB_DATABASE=' "$APP_DIR/.env" | cut -d '=' -f2-)"
DB_USERNAME="$(grep -E '^DB_USERNAME=' "$APP_DIR/.env" | cut -d '=' -f2-)"
DB_PASSWORD="$(grep -E '^DB_PASSWORD=' "$APP_DIR/.env" | cut -d '=' -f2-)"

echo "==> 備份資料庫"
MYSQL_PWD="$DB_PASSWORD" mysqldump -u"$DB_USERNAME" "$DB_DATABASE" | gzip > "$BACKUP_DIR/db-$DATE.sql.gz"

echo "==> 備份圖片目錄"
tar -czf "$BACKUP_DIR/storage-$DATE.tar.gz" -C "$APP_DIR/storage/app" public

echo "==> 清除超過 $KEEP 份的舊備份"
ls -1t "$BACKUP_DIR"/db-*.sql.gz 2>/dev/null | tail -n +$((KEEP + 1)) | xargs -r rm --
ls -1t "$BACKUP_DIR"/storage-*.tar.gz 2>/dev/null | tail -n +$((KEEP + 1)) | xargs -r rm --

echo "==> 備份完成：$BACKUP_DIR"
