# 部署指南（AWS EC2 免費方案）

對應企劃書第 7 節。這份文件假設你還沒申請 AWS 帳號、EC2、網域——先照這個順序做，跑到哪卡住了都可以回來問。

## 0. 前置準備

- [ ] AWS 帳號（免費方案 12 個月，EC2 選 `t2.micro` / `t3.micro`）
- [ ] 網域（企劃書建議先申請 eu.org，備案 ClouDNS / DuckDNS / 付費網域，見主企劃書第 7 節）
- [ ] GitHub repo（已由你自己建立）

## 1. 建立 EC2 執行個體

- OS 選 Ubuntu 22.04 LTS（或你熟悉的其他 LTS 版本）
- 規格：`t2.micro` 或 `t3.micro`（免費方案）
- 安全群組開放：22（SSH）、80（HTTP）、443（HTTPS）
- 建立並下載 SSH 金鑰對，之後 GitHub Actions 會用到

## 2. EC2 上安裝基礎環境

```bash
sudo apt update && sudo apt upgrade -y

# PHP 8.2 + 擴充套件（需先加 ondrej/php PPA）
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml \
    php8.2-curl php8.2-gd php8.2-zip php8.2-intl php8.2-opcache php8.2-bcmath

# MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Nginx
sudo apt install -y nginx

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js（LTS）
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Git
sudo apt install -y git
```

## 3. 加 Swap（1GB RAM 免費方案必做，否則 composer install / npm build 容易 OOM）

```bash
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```

## 4. MySQL 記憶體調校

1GB RAM 機器上 MySQL 預設的 `innodb_buffer_pool_size` 太大，編輯 `/etc/mysql/mysql.conf.d/mysqld.cnf`：

```ini
[mysqld]
innodb_buffer_pool_size = 128M
```

`sudo systemctl restart mysql`

建立資料庫與帳號：

```sql
CREATE DATABASE blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'blog'@'localhost' IDENTIFIED BY '換成一組強密碼';
GRANT ALL PRIVILEGES ON blog.* TO 'blog'@'localhost';
FLUSH PRIVILEGES;
```

## 5. 第一次手動部署

```bash
sudo mkdir -p /var/www/blog
sudo chown $USER:$USER /var/www/blog
git clone <你的 GitHub repo URL> /var/www/blog
cd /var/www/blog

cp .env.example .env
# 編輯 .env：APP_ENV=production、APP_DEBUG=false、APP_URL=https://你的網域、
# DB_* 填第 4 步建立的帳密

composer install --no-dev --optimize-autoloader
npm ci && npm run build

php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan filament:install --panels --no-interaction
php artisan make:filament-user   # 建立正式環境的管理員帳號
```

複製 [`nginx.conf.example`](nginx.conf.example) 到 `/etc/nginx/sites-available/blog`，
軟連結到 `sites-enabled`，`sudo nginx -t && sudo systemctl reload nginx`。

## 6. Nginx Proxy Manager（對外 SSL／網域轉發）

企劃書指定用 NPM 統一管理對外網域與 Let's Encrypt 憑證。EC2 上需要 Docker 來跑 NPM
（僅這裡用 Docker，跟本機開發環境無關）：

```bash
curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker $USER
```

參考 [Nginx Proxy Manager 官方文件](https://nginxproxymanager.com/setup/) 建立
`docker-compose.yml` 並啟動，登入後台後：

1. 新增 Proxy Host，指向 `127.0.0.1:8080`（本專案的 Nginx server block）
2. Domain 填你申請好的網域
3. SSL 分頁申請 Let's Encrypt 憑證，開啟 Force SSL

## 7. Scheduler 與備份排程

```bash
crontab -e
```

貼上 [`crontab.example`](crontab.example) 的內容（記得把路徑改成實際部署路徑）。

## 8. 設定 GitHub Actions 自動部署

到 GitHub repo → Settings → Secrets and variables → Actions，新增：

| Secret | 說明 |
|---|---|
| `EC2_HOST` | EC2 的公開 IP 或網域 |
| `EC2_USER` | SSH 登入帳號（通常是 `ubuntu`） |
| `EC2_SSH_KEY` | 步驟 1 下載的 SSH 私鑰內容（整份貼上） |
| `EC2_APP_DIR` | 部署路徑，例如 `/var/www/blog` |

設定完成後，push 到 `main` 分支就會自動跑 [`.github/workflows/deploy.yml`](../.github/workflows/deploy.yml)：
先跑測試，測試過了才 SSH 進 EC2 執行 [`deploy.sh`](deploy.sh)。

## 9. 之後的部署

正常情況下不用手動操作，push 到 `main` 就會自動部署。要手動重跑的話：

```bash
ssh <user>@<EC2_HOST>
cd /var/www/blog && bash deploy/deploy.sh
```

## 檔案對照表

| 檔案 | 用途 |
|---|---|
| [`../.github/workflows/deploy.yml`](../.github/workflows/deploy.yml) | CI：跑測試 → SSH 部署到 EC2 |
| [`deploy.sh`](deploy.sh) | 在 EC2 上實際執行的部署腳本 |
| [`nginx.conf.example`](nginx.conf.example) | Laravel 應用的 Nginx server block |
| [`backup.sh`](backup.sh) | 每週備份 MySQL + 圖片目錄，只留最近 5 份 |
| [`crontab.example`](crontab.example) | Scheduler 與備份的 cron 設定 |
