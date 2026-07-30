#!/usr/bin/env bash
set -euo pipefail

REPO_DIR="${REPO_DIR:-/var/www/cskm}"
BRANCH="${BRANCH:-main}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"

BACKEND_DIR="${REPO_DIR}/backend"
FRONTEND_DIR="${REPO_DIR}/frontend"

log() {
  printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$1"
}

require_dir() {
  if [[ ! -d "$1" ]]; then
    echo "Directory not found: $1" >&2
    exit 1
  fi
}

require_file() {
  if [[ ! -f "$1" ]]; then
    echo "File not found: $1" >&2
    exit 1
  fi
}

log "Start deploy: repo=${REPO_DIR}, branch=${BRANCH}"
require_dir "$REPO_DIR"
require_dir "$BACKEND_DIR"
require_dir "$FRONTEND_DIR"
require_file "${BACKEND_DIR}/artisan"

log "Update source code"
git -C "$REPO_DIR" fetch --all --prune
git -C "$REPO_DIR" checkout "$BRANCH"
git -C "$REPO_DIR" pull --ff-only origin "$BRANCH"

log "Enable maintenance mode"
if ! "$PHP_BIN" "$BACKEND_DIR/artisan" down; then
  log "Maintenance mode command failed, continue deploy with caution"
fi

log "Install backend dependencies"
"$COMPOSER_BIN" --working-dir="$BACKEND_DIR" install --no-dev --optimize-autoloader --no-interaction

log "Run backend migrations/cache"
"$PHP_BIN" "$BACKEND_DIR/artisan" migrate --force
"$PHP_BIN" "$BACKEND_DIR/artisan" config:cache
"$PHP_BIN" "$BACKEND_DIR/artisan" route:cache
"$PHP_BIN" "$BACKEND_DIR/artisan" view:cache

log "Install and build frontend"
"$NPM_BIN" --prefix "$FRONTEND_DIR" ci
"$NPM_BIN" --prefix "$FRONTEND_DIR" run build

log "Restart queue workers"
"$PHP_BIN" "$BACKEND_DIR/artisan" queue:restart

log "Disable maintenance mode"
"$PHP_BIN" "$BACKEND_DIR/artisan" up

log "Deploy completed successfully"
