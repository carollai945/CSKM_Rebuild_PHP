# CSKM 重建規格文件指引（PHP 版）

> 目的：讓目前已完成的 **BDD + 頁面設計 + SDD + TDD**，可以進一步升級為適合 **前後端分離 / PHP Laravel + Vue 3** 重建的規格集合。

---

## 1. 適用情境

本文件適用於以下目標：

1. **不是維護舊 JSP，而是依文件重建新系統**
2. **前端與後端分離**
3. **希望從既有業務流程抽出穩定規格，而不是照搬舊程式結構**
4. **後端以 PHP 8.3 + Laravel 11 實作**

---

## 2. 重建時的文件分工

| 文件 | 目的 | 回答的問題 |
|---|---|---|
| **BDD** | 定義業務行為與驗收情境 | 使用者怎麼操作？系統應怎麼回應？ |
| **頁面設計** | 定義前端畫面、互動、狀態顯示 | 使用者會看到哪些區塊、按鈕、訊息、動態內容？ |
| **SDD** | 定義模組責任與前後端設計拆分 | 前端頁面、後端服務、資料責任怎麼分？ |
| **API 設計** | 定義前後端契約 | 前端要呼叫哪些 API？參數與回傳長什麼樣子？ |
| **資料模型設計** | 定義重建後的核心實體與關聯 | 系統有哪些 Model / Migration / Code Table？ |
| **流程 / 狀態設計** | 定義送審、批次、核准、退回等狀態機 | 狀態怎麼轉？哪些角色能做哪些轉移？ |
| **TDD** | 定義驗證方式 | 如何驗證 API、前端互動、流程與權限都正確？ |

---

## 3. 建議重建技術棧

### 後端

- **PHP 8.3+**
- **Laravel 11**
- Laravel Sanctum（API 認證 / Token）
- Eloquent ORM（依查詢複雜度可搭配 Query Builder）
- PHPUnit / Pest（單元與整合測試）

### 前端

- **Vue 3**
- **TypeScript**
- Vite
- Pinia
- Vue Router

### 共同基礎

- MySQL（可先沿用）
- OpenAPI（使用 L5-Swagger 或 Scramble 產生文件）
- 檔案儲存服務（附件、圖片、PDF；使用 Laravel Storage）
- Job / Queue 機制（匯入、報表彙整、通知；使用 Laravel Queue）

---

## 4. 文件使用順序

建議順序不是先寫程式，而是先固定規格：

1. 先以 **BDD** 固定行為與角色
2. 用 **頁面設計** 固定前端畫面與動態互動
3. 用 **流程 / 狀態設計** 固定狀態機與批次規則
4. 用 **資料模型設計** 固定核心 Model、欄位與關聯
5. 用 **API 設計** 固定前後端契約
6. 回頭把 **SDD** 拆成前端實作與後端服務責任
7. 最後用 **TDD** 固定單元、整合與 E2E 驗收

---

## 5. 本次新增的重建規格文件

### 系統級文件

- `CSKM_Rebuild_PHP_Specification_Guide.md`
- `api-design/CSKM_API_Design.md`
- `data-model/CSKM_Data_Model_Design.md`
- `flow-design/CSKM_Process_State_Design.md`

### 與既有文件的關係

- **BDD / 頁面設計 / SDD / TDD**：維持以頁面或群組為單位
- **API / Data Model / Flow-State**：以系統級或子域級為單位，不需一頁一檔

---

## 6. 重建交付物最小集合

若要讓團隊可以直接依文件重建，至少要有：

1. 已完成的 BDD
2. 已完成的頁面設計
3. 已完成的 SDD
4. 已完成的 TDD
5. **API 設計**
6. **資料模型設計**
7. **流程 / 狀態設計**

其中第 5 ~ 7 項就是舊 JSP 專案通常最缺的重建規格層。

---

## 7. 重建階段建議

### Phase 1：基礎平台

- 登入 / Token（Laravel Sanctum）
- 權限模型（Gate / Policy）
- 主檔 API（區域、部門、職稱、機構、課程）

### Phase 2：主流程

- 電訪名單與學員生命周期
- 表單建立 / 送審 / 批核
- 報表建立 / 查詢 / 批核
- 繳費與請款確認

### Phase 3：支援能力

- 訊息中心
- 附件 / PDF / Excel 匯入（使用 Laravel Storage + PhpSpreadsheet）
- 備份、批次與稽核紀錄（使用 Laravel Queue / Scheduler）

---

## 8. 建議的 PHP 後端專案結構

```text
backend/
  app/
    Http/
      Controllers/       # API Controller，只處理 HTTP 進出
      Requests/          # Form Request 驗證
      Resources/         # API Resource（DTO 轉換）
    Models/              # Eloquent Model
    Services/            # 業務邏輯層
    Repositories/        # 資料存取抽象
    DTO/                 # 資料傳輸物件
    Enums/               # 狀態 Enum（PHP 8.1+）
    Policies/            # 授權 Policy
    Jobs/                # 非同步工作
    Events/              # 事件
    Listeners/           # 事件監聽
  config/
  database/
    migrations/          # 資料庫版本管理
    seeders/
    factories/
  routes/
    api.php              # API 路由
  tests/
    Unit/
    Feature/             # API 整合測試
  composer.json
```

---

## 9. 重建原則

1. **沿用業務規則，不沿用舊 JSP 結構**
2. **沿用功能碼與業務名稱，但 API 與資料模型重新正規化**
3. **把角色、狀態與資料範圍變成明確規格，不依畫面 if/else 判斷**
4. **舊系統的 Ajax / Vue / jQuery 行為應轉成前端狀態與 API 契約**
5. **批次審核、全選、退回、引用來源等特殊流程都要落在 Flow-State 與 API 設計中**
6. **PHP Enum（BackedEnum）取代魔術字串，所有狀態碼都要顯性化**
7. **使用 Form Request 進行輸入驗證，不在 Controller 中撰寫驗證邏輯**

---

## 10. 一句話結論

**目前的 BDD + 頁面設計 + SDD + TDD 可以作為重建骨架；本次新增的 API / Data Model / Flow-State 文件，則補齊了真正可執行的 PHP 前後端分離重建規格。**
