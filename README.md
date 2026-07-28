# Shop — PHP 8.4 + Laravel API + Vue3 電商網站

前後端分離的電商作品集專案，Docker 一鍵起環境。

## 技術棧
- 後端：PHP 8.4、Laravel 11（API only）、Sanctum（SPA 驗證）、MySQL 8.4、Redis
- 前端：Vue3（Composition API + `<script setup>`）、Vite、Pinia、Vue Router、Axios
- 金流：綠界 ECPay、藍新 NewebPay（模擬串接，含簽章驗證邏輯，正式上線只需替換商店代號/金鑰）
- 容器：Docker Compose（nginx + php-fpm + mysql + redis + phpMyAdmin + node）

## 功能範圍
- 前台：商品瀏覽/搜尋/排序、購物車、會員註冊登入、結帳、金流導向付款、訂單查詢
- 後台：儀表板統計、商品 CRUD、訂單狀態管理、庫存管理（含低庫存篩選）
- 庫存機制：下單時「預留庫存」，金流付款成功 webhook 通知後才正式扣庫存，避免超賣

## 專案結構
```
ecommerce-project/
├── docker-compose.yml       # 一鍵啟動所有服務
├── docker/                  # Dockerfile、nginx、mysql 設定
├── setup.sh                 # 首次安裝腳本：建立 Laravel 骨架並套用客製檔案
├── backend/                 # Laravel 客製檔案（Model / Controller / Migration / Route / Service）
└── frontend/                # Vue3 SPA
```

> 因為完整的 Laravel 框架（vendor 套件）需要透過 composer 下載，這個專案只包含**客製化的業務邏輯檔案**。
> 執行 `setup.sh` 會自動用 `composer create-project` 建立乾淨的 Laravel 骨架，
> 再把這裡準備好的 Model／Controller／Migration／Route 等檔案疊上去，不會覆蓋你本機的骨架檔案。

## 快速開始

### 1. 安裝後端（需要本機已安裝 PHP + Composer，用來執行 composer create-project）
```bash
bash setup.sh
```
這一步會：
1. 用 `composer create-project laravel/laravel backend "11.*"`（若 backend 尚未安裝）
2. 合併本專案已寫好的客製檔案
3. 安裝 Sanctum、產生 APP_KEY、發佈 Sanctum 設定

### 2. 啟動 Docker 環境
```bash
docker compose up -d --build
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

### 3. 啟動前端
`docker-compose.yml` 已內建 `frontend` 服務（自動 npm install + vite dev server），
或者你也可以在本機直接：
```bash
cd frontend
cp .env.example .env
npm install
npm run dev
```

### 存取網址
| 服務 | 網址 |
|---|---|
| 前台網站（Vite dev server） | http://localhost:5173 |
| API（Nginx） | http://localhost:8080/api |
| phpMyAdmin | http://localhost:8081 |

### 測試帳號（由 Seeder 建立）
| 角色 | Email | 密碼 |
|---|---|---|
| 管理員 | admin@example.com | password |
| 一般會員 | customer@example.com | password |

## 金流設定
`.env` 中的 `ECPAY_*` / `NEWEBPAY_*` 目前使用官方公開的**測試商店代號**，可直接在 stage 環境測試付款流程。
正式上線前請至綠界/藍新後台申請正式商店代號，並替換對應的 HashKey / HashIV，
同時把 `ECPAY_ENV` / `NEWEBPAY_ENV` 改為 `production`。

## 面試展示重點（可以主動提到的設計）
- **服務層抽象**：`PaymentGatewayInterface` + `PaymentGatewayFactory`，Controller 不需要知道目前用哪家金流，方便未來新增第三家金流或寫單元測試（可 mock interface）。
- **庫存防超賣**：下單時只「預留」庫存（`reserved` 欄位），收到金流 webhook 驗簽成功後才正式扣庫存，避免用戶下單後未付款卻卡住庫存，也避免付款前被搶購一空。
- **金額用整數儲存**：`price` / `total_amount` 皆用整數（元），避免浮點數金額誤差。
- **API Resource**：統一輸出格式，前後端資料結構解耦。
- **前後端分離 + Sanctum SPA 驗證**：可延伸說明 CSRF/CORS/Stateful Domain 的設定考量。

## 待你自行擴充（面試可以聊的「還可以怎麼做」）
- 商品圖片上傳（可用 Laravel 內建 Storage + S3）
- Redis 快取商品列表、佇列處理訂單通知信
- 前端上傳圖片、商品規格（顏色/尺寸）多選項
- 測試：Pest/PHPUnit 針對訂單建立、庫存扣減、金流簽章驗證撰寫單元測試
