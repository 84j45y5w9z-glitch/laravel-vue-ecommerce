#!/usr/bin/env bash
# 一鍵初始化腳本
# 用途：在乾淨的 Laravel 骨架上，疊上本專案已寫好的 Model / Controller / Migration / Route / Service 等客製檔案
# 使用方式：於專案根目錄執行 bash setup.sh
set -e

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$ROOT_DIR/backend"

echo "==> 1. 檢查 backend 是否已是完整的 Laravel 專案"
if [ ! -f "$BACKEND_DIR/artisan" ]; then
  echo "==> 尚未安裝 Laravel 骨架，使用 composer 建立全新專案..."
  TMP_DIR="$ROOT_DIR/.laravel_tmp"
  rm -rf "$TMP_DIR"
  composer create-project laravel/laravel "$TMP_DIR" "11.*"

  echo "==> 合併全新 Laravel 骨架與已寫好的客製檔案（不覆蓋既有檔案）"
  rsync -a --ignore-existing "$TMP_DIR/" "$BACKEND_DIR/"
  rm -rf "$TMP_DIR"
else
  echo "==> 偵測到 backend/artisan，略過骨架安裝"
fi

cd "$BACKEND_DIR"

echo "==> 2. 安裝套件（Sanctum）"
composer require laravel/sanctum

echo "==> 3. 準備 .env"
if [ ! -f .env ]; then
  cp .env.example .env
fi

echo "==> 4. 產生 APP_KEY"
php artisan key:generate

echo "==> 5. 發佈 Sanctum 設定檔"
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

echo ""
echo "==> 完成！接下來請執行："
echo "    docker compose up -d --build"
echo "    docker compose exec app php artisan migrate --seed"
echo "    docker compose exec app php artisan storage:link"
echo ""
echo "前端： cd frontend && npm install && npm run dev"
echo "（或直接用 docker compose，frontend 服務已內建 npm install + dev server）"
