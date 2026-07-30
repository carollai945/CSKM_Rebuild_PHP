# CSKM 手動佈署文件

本文件適用於 **CSKM_Rebuild_PHP**（`backend` Laravel API + `frontend` Vue 3），目標為 Linux 伺服器（建議 Ubuntu 22.04+）。

## 1. 佈署前需求

1. 安裝 PHP 8.3（含 `mbstring`, `xml`, `curl`, `bcmath`, `zip`, `intl`, `pdo`, `pdo_mysql`）
2. 安裝 Composer 2
3. 安裝 Node.js 20+ 與 npm
4. 安裝並設定 Nginx
5. 安裝 Git
6. 安裝 MySQL 8.0+（或可連線的既有 MySQL）
7. 可寫入 `backend/storage` 與 `backend/bootstrap/cache`

## 2. 目錄規劃（範例）

- 專案路徑：`/var/www/cskm`
- 後端路徑：`/var/www/cskm/backend`
- 前端路徑：`/var/www/cskm/frontend`

## 3. 首次手動佈署步驟

```bash
cd /var/www
git clone <YOUR_REPO_URL> cskm
cd cskm
```

### 3.1 資料庫佈署（MySQL）

#### 3.1.1 安裝與啟用 MySQL（若尚未安裝）

```bash
sudo apt update
sudo apt install -y mysql-server
sudo systemctl enable mysql
sudo systemctl start mysql
```

#### 3.1.2 建立資料庫與應用帳號

```sql
CREATE DATABASE cskm_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cskm_user'@'%' IDENTIFIED BY 'REPLACE_WITH_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON cskm_prod.* TO 'cskm_user'@'%';
FLUSH PRIVILEGES;
```

可用以下方式進入 MySQL：

```bash
sudo mysql
```

#### 3.1.3 設定 Laravel `.env`

```bash
cd /var/www/cskm/backend
cp .env.example .env
```

編輯 `backend/.env`（至少以下欄位）：

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cskm_prod
DB_USERNAME=cskm_user
DB_PASSWORD=REPLACE_WITH_STRONG_PASSWORD
```

#### 3.1.4 驗證 PHP / MySQL 驅動

```bash
php -m | grep -Ei "pdo|mysql"
```

### 3.2 後端（Laravel）

```bash
cd /var/www/cskm/backend
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

> 請先完成 `.env` 內 DB / APP_URL / MAIL 等生產環境設定後再上線。

### 3.3 權限設定

```bash
cd /var/www/cskm/backend
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
```

### 3.4 前端（Vue）

```bash
cd /var/www/cskm/frontend
npm ci
npm run build
```

建置後檔案位於：`/var/www/cskm/frontend/dist`

## 4. Nginx 設定範例

```nginx
server {
    listen 80;
    server_name your-domain.example;

    # 前端 SPA
    root /var/www/cskm/frontend/dist;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    # API 反向代理到 Laravel public
    location /api {
        try_files $uri $uri/ @laravel;
    }

    location @laravel {
        root /var/www/cskm/backend/public;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME /var/www/cskm/backend/public/index.php;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
}
```

> 若你採「API 子網域」架構，建議另開 `api.your-domain.example` 指向 `backend/public`。

## 5. Queue Worker（必要）

本專案預設 `QUEUE_CONNECTION=database`，需常駐 queue worker。

### Supervisor 範例

```ini
[program:cskm-queue]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /var/www/cskm/backend/artisan queue:work --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/supervisor/cskm-queue.log
```

套用：

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start cskm-queue:*
```

## 6. 日常手動更新流程

```bash
cd /var/www/cskm
git pull origin main

cd backend
composer install --no-dev --optimize-autoloader
php artisan down
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
php artisan up

cd ../frontend
npm ci
npm run build
```

### 6.1 資料庫備份（建議）

```bash
mysqldump -u cskm_user -p cskm_prod > /var/backups/cskm_prod_$(date +%F).sql
```

## 7. 自動佈署

請使用 `scripts/deploy.sh`（本次已提供），可將上述流程自動化。  
執行前請先確認：

1. `backend/.env` 已存在且 DB 參數可連線
2. PHP 已啟用 `pdo_mysql`
3. 目標 MySQL 帳號有 `CREATE/ALTER/INDEX` 等 migration 權限
