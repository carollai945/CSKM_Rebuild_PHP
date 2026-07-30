#!/usr/bin/env bash
set -euo pipefail

REPO_DIR="${REPO_DIR:-/var/www/cskm}"
BRANCH="${BRANCH:-main}"
COMPOSE_FILE="${COMPOSE_FILE:-${REPO_DIR}/docker/docker-compose.container.yml}"

APP_URL="${APP_URL:-http://localhost:${APP_PORT:-8080}}"
MYSQL_DATABASE="${MYSQL_DATABASE:-cskm_prod}"
MYSQL_USER="${MYSQL_USER:-cskm_user}"
MYSQL_PASSWORD="${MYSQL_PASSWORD:-cskm_password_change_me}"
MYSQL_HOST="${MYSQL_HOST:-mysql}"
MYSQL_PORT="${MYSQL_PORT:-3306}"

BACKEND_DIR="${REPO_DIR}/backend"
ENV_FILE="${BACKEND_DIR}/.env"

log() {
  printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$1"
}

require_cmd() {
  if ! command -v "$1" >/dev/null 2>&1; then
    echo "Command not found: $1" >&2
    exit 1
  fi
}

set_env() {
  local key="$1"
  local value="$2"
  if grep -qE "^${key}=" "$ENV_FILE"; then
    sed -i "s#^${key}=.*#${key}=${value}#g" "$ENV_FILE"
  else
    printf '%s=%s\n' "$key" "$value" >> "$ENV_FILE"
  fi
}

require_cmd git
require_cmd docker

if ! docker compose version >/dev/null 2>&1; then
  echo "docker compose is required." >&2
  exit 1
fi

if [[ ! -d "$REPO_DIR" ]]; then
  echo "Repository not found: $REPO_DIR" >&2
  exit 1
fi

if [[ ! -f "${BACKEND_DIR}/.env.example" ]]; then
  echo ".env.example not found: ${BACKEND_DIR}/.env.example" >&2
  exit 1
fi

log "Update source code"
git -C "$REPO_DIR" fetch --all --prune
git -C "$REPO_DIR" checkout "$BRANCH"
git -C "$REPO_DIR" pull --ff-only origin "$BRANCH"

if [[ ! -f "$ENV_FILE" ]]; then
  log "Create backend .env"
  cp "${BACKEND_DIR}/.env.example" "$ENV_FILE"
fi

log "Apply production database settings to backend .env"
set_env APP_ENV production
set_env APP_DEBUG false
set_env APP_URL "$APP_URL"
set_env DB_CONNECTION mysql
set_env DB_HOST "$MYSQL_HOST"
set_env DB_PORT "$MYSQL_PORT"
set_env DB_DATABASE "$MYSQL_DATABASE"
set_env DB_USERNAME "$MYSQL_USER"
set_env DB_PASSWORD "$MYSQL_PASSWORD"
set_env QUEUE_CONNECTION database

log "Start MySQL and backend containers"
docker compose -f "$COMPOSE_FILE" up -d --build mysql backend

log "Wait for MySQL readiness"
for i in $(seq 1 30); do
  if docker compose -f "$COMPOSE_FILE" exec -T mysql mysqladmin ping -h 127.0.0.1 -uroot -p"${MYSQL_ROOT_PASSWORD:-root_password_change_me}" --silent; then
    break
  fi
  if [[ "$i" -eq 30 ]]; then
    echo "MySQL is not ready after timeout." >&2
    exit 1
  fi
  sleep 2
done

log "Install backend dependencies and run migrations"
docker compose -f "$COMPOSE_FILE" exec -T backend composer install --no-dev --optimize-autoloader --no-interaction

if ! grep -qE '^APP_KEY=base64:' "$ENV_FILE"; then
  docker compose -f "$COMPOSE_FILE" exec -T backend php artisan key:generate --force
fi

docker compose -f "$COMPOSE_FILE" exec -T backend php artisan migrate --force
docker compose -f "$COMPOSE_FILE" exec -T backend php artisan config:cache
docker compose -f "$COMPOSE_FILE" exec -T backend php artisan route:cache
docker compose -f "$COMPOSE_FILE" exec -T backend php artisan view:cache

log "Build frontend assets"
docker compose -f "$COMPOSE_FILE" run --rm frontend_builder

log "Start nginx and queue"
docker compose -f "$COMPOSE_FILE" up -d nginx queue

log "Container deployment completed"
