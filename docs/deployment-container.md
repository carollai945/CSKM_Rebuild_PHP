# CSKM 容器佈署文件（含資料庫）

本文件提供 **Docker Compose 容器佈署**流程，包含：

1. Laravel backend 容器
2. Vue frontend build 容器
3. Nginx 容器
4. **MySQL 8 容器（資料庫）**
5. Laravel queue worker 容器

---

## 1. 前置需求

1. Docker Engine 24+
2. Docker Compose Plugin（`docker compose`）
3. Git

---

## 2. 檔案位置

- Compose：`docker/docker-compose.container.yml`
- Backend image：`docker/backend/Dockerfile`
- Nginx 設定：`docker/nginx/default.conf`
- 佈署腳本：`scripts/deploy-container.sh`

---

## 3. 首次佈署（含資料庫）

```bash
cd /var/www
git clone <YOUR_REPO_URL> cskm
cd cskm
chmod +x scripts/deploy-container.sh
```

執行（請替換密碼）：

```bash
REPO_DIR=/var/www/cskm \
BRANCH=main \
APP_PORT=8080 \
APP_URL=http://your-domain.example \
MYSQL_ROOT_PASSWORD='<ROOT_PASSWORD>' \
MYSQL_DATABASE='cskm_prod' \
MYSQL_USER='cskm_user' \
MYSQL_PASSWORD='<DB_PASSWORD>' \
./scripts/deploy-container.sh
```

腳本會自動完成：

1. 更新 repo
2. 建立/更新 `backend/.env`
3. 啟動 MySQL + backend 容器
4. Laravel `composer install` / `migrate --force` / cache
5. 建置前端 `dist`
6. 啟動 Nginx + queue 容器

---

## 4. 資料庫資訊（容器）

- 服務名：`mysql`
- 內部連線（Laravel）：`DB_HOST=mysql`, `DB_PORT=3306`
- 主機對外 Port：`${MYSQL_PORT:-3306}`
- 資料持久化 volume：`mysql_data`

---

## 5. 日常更新

```bash
cd /var/www/cskm
REPO_DIR=/var/www/cskm \
BRANCH=main \
MYSQL_ROOT_PASSWORD='<ROOT_PASSWORD>' \
MYSQL_DATABASE='cskm_prod' \
MYSQL_USER='cskm_user' \
MYSQL_PASSWORD='<DB_PASSWORD>' \
./scripts/deploy-container.sh
```

---

## 6. 常用維運指令

查看容器狀態：

```bash
docker compose -f docker/docker-compose.container.yml ps
```

查看 backend log：

```bash
docker compose -f docker/docker-compose.container.yml logs -f backend
```

查看 queue log：

```bash
docker compose -f docker/docker-compose.container.yml logs -f queue
```

---

## 7. 資料庫備份與還原

備份：

```bash
docker compose -f docker/docker-compose.container.yml exec -T mysql \
  mysqldump -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" "${MYSQL_DATABASE}" \
  > /var/backups/cskm_prod_$(date +%F).sql
```

還原：

```bash
cat /var/backups/cskm_prod_YYYY-MM-DD.sql | docker compose -f docker/docker-compose.container.yml exec -T mysql \
  mysql -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" "${MYSQL_DATABASE}"
```

---

## 8. 關閉與重啟

停止：

```bash
docker compose -f docker/docker-compose.container.yml down
```

停止但保留資料庫 volume（預設即保留）：

```bash
docker compose -f docker/docker-compose.container.yml down
```

刪除資料庫 volume（危險，會刪除資料）：

```bash
docker compose -f docker/docker-compose.container.yml down -v
```
