# CSKM Backend（PHP 8.3 + Laravel 11）

後端服務實作區，使用 PHP 8.3 + Laravel 11。

## 技術棧

- PHP 8.3+
- Laravel 11
- Laravel Sanctum（API 認證）
- Eloquent ORM
- PHPUnit / Pest（測試）

## 專案結構

```text
app/
  Http/
    Controllers/    # API Controller（只處理 HTTP 進出）
    Requests/       # Form Request 輸入驗證
    Resources/      # API Resource（回傳格式）
  Models/           # Eloquent Model
  Services/         # 業務邏輯層
  Repositories/     # 資料存取抽象
  DTO/              # 資料傳輸物件
  Enums/            # 狀態 Enum（BackedEnum）
  Policies/         # 授權 Policy
  Jobs/             # 非同步工作
database/
  migrations/       # 資料庫版本管理
  seeders/
  factories/
routes/
  api.php           # API 路由
tests/
  Unit/
  Feature/          # API 整合測試
```

## 啟動方式

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## 測試

```bash
php artisan test
# 或
vendor/bin/pest
```
