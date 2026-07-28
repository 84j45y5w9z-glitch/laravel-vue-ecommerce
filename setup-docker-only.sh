#!/usr/bin/env bash
# 一鍵初始化腳本（純 Docker 版，本機不需要安裝 PHP / Composer）
# 使用方式：於專案根目錄執行 bash setup-docker-only.sh
set -e

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$ROOT_DIR/backend"

echo "==> 1. 檢查 Docker 是否可用"
if ! command -v docker &> /dev/null; then
  echo "找不到 docker 指令，請先安裝 Docker Desktop（https://www.docker.com/products/docker-desktop/）"
  exit 1
fi

echo "==> 2. 檢查 backend 是否已是完整的 Laravel 專案"
if [ ! -f "$BACKEND_DIR/artisan" ]; then
  echo "==> 尚未安裝 Laravel 骨架，改用官方 composer 映像檔在容器內建立（本機不需要裝 PHP/Composer）"
  TMP_DIR="$ROOT_DIR/.laravel_tmp"
  rm -rf "$TMP_DIR"
  mkdir -p "$TMP_DIR"

  docker run --rm \
    -v "$TMP_DIR":/app \
    -w /app \
    composer:2 \
    create-project laravel/laravel . 

  echo "==> 合併全新 Laravel 骨架與已寫好的客製檔案（不覆蓋既有檔案）"
  rsync -a --ignore-existing "$TMP_DIR/" "$BACKEND_DIR/"
  rm -rf "$TMP_DIR"
else
  echo "==> 偵測到 backend/artisan，略過骨架安裝"
fi

echo "==> 3. 在容器內安裝 Sanctum"
docker run --rm \
  -v "$BACKEND_DIR":/app \
  -w /app \
  composer:2 \
  require laravel/sanctum

echo "==> 4. 準備 .env"
if [ ! -f "$BACKEND_DIR/.env" ]; then
  cp "$BACKEND_DIR/.env.example" "$BACKEND_DIR/.env"
fi

echo "==> 5. 建置並啟動所有容器（php-fpm / nginx / mysql / redis / phpmyadmin / frontend）"
cd "$ROOT_DIR"
docker compose up -d --build

echo "==> 6. 等待 MySQL 啟動完成"
sleep 8

echo "==> 7. 產生 APP_KEY、發佈 Sanctum 設定、跑 migration + seed"
docker compose exec app php artisan key:generate
docker compose exec app php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link

echo ""
echo "==> 完成！服務網址："
echo "    前台網站：http://localhost:5173"
echo "    API：     http://localhost:8080/api"
echo "    phpMyAdmin：http://localhost:8081"
echo ""
echo "測試帳號："
echo "    管理員：admin@example.com / password"
echo "    一般會員：customer@example.com / password"
