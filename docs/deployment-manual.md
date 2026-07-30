# CSKM 手動佈署文件

本文件適用於 **CSKM_Rebuild_PHP**（`backend` Laravel API + `frontend` Vue 3），目標為 Linux 伺服器（建議 Ubuntu 22.04+）。

## 1. 佈署前需求

1. 安裝 PHP 8.3（含 `mbstring`, `xml`, `curl`, `sqlite3`, `bcmath`, `zip`, `intl`）
2. 安裝 Composer 2
3. 安裝 Node.js 20+ 與 npm
4. 安裝並設定 Nginx
5. 安裝 Git
6. 可寫入 `backend/storage` 與 `backend/bootstrap/cache`

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

### 3.1 後端（Laravel）

```bash
cd /var/www/cskm/backend
cp .env.example .env
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

> 請先完成 `.env` 內 DB / APP_URL / MAIL 等生產環境設定後再上線。

### 3.2 權限設定

```bash
cd /var/www/cskm/backend
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
```

### 3.3 前端（Vue）

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

## 7. 自動佈署

請使用 `scripts/deploy.sh`（本次已提供），可將上述流程自動化。
