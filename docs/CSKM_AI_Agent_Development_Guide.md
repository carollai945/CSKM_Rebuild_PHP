# CSKM AI Agent 持續開發指引（PHP 版）

> 目的：讓 CSKM 的重建文件不只適合人工閱讀，也適合 **AI Agent 持續接手、逐步實作、反覆驗證與交接**。  
> 本文件適用於 **PHP 8.3 + Laravel 11 + Vue 3** 技術棧。

---

## 1. 適用目標

本指引適用於：

1. 使用 **PHP 8.3 + Laravel 11 + Vue 3**
2. 以 **文件驅動重建** 而非舊系統原地重構
3. 希望未來能讓多個 AI Agent 或不同輪次的 Agent 持續接手開發

---

## 2. AI Agent 友善的核心原則

1. **規格先行，不讓 Agent 猜**
2. **任務切片，小步交付**
3. **命名一致，可機械追溯**
4. **狀態、權限、錯誤碼都要顯性化**
5. **每個切片都必須可驗證、可回歸**

---

## 3. Agent 必須依賴的文件集合

每個功能切片至少應能追到以下文件：

| 文件 | Agent 使用目的 |
|---|---|
| `CSKM_UserStories.md` | 確認商業目標與驗收結果 |
| `bdd/*.feature` | 確認角色、情境、異常與資料範圍 |
| `page-design/*.md` | 確認畫面區塊、欄位、按鈕、動態 UI |
| `sdd/*.md` | 確認前後端職責、資料流、模組邊界 |
| `tdd/*.md` | 確認測試案例與驗收順序 |
| `api-design/CSKM_API_Design.md` | 確認 endpoint、request/response、錯誤碼格式 |
| `data-model/CSKM_Data_Model_Design.md` | 確認 Model、Migration、欄位、關聯 |
| `flow-design/CSKM_Process_State_Design.md` | 確認狀態機、allowedActions、批次規則 |

---

## 4. 建議的 Agent 開發切片單位

不要一次交給 Agent「完成整個模組」，建議切成：

1. **一個頁面 + 一組 API**
2. **一個流程節點 + 對應狀態轉移**
3. **一個列表頁 + 一個明細頁**
4. **一個批次操作能力**
5. **一個主檔維護功能**

### 合適範例

- `A04 請假申請建立與送審`
- `D00 主管批次審核請假`
- `B00 電訪名單查詢與批次派發`
- `F02 人員權限查詢與儲存`
- `E00 財務確認繳費`

### 不合適範例

- `完成 A 模組`
- `完成所有審核`
- `完成整個財務系統`

---

## 5. 每個 Agent 任務都應具備的輸入

每次派給 Agent 的任務說明至少要有：

| 欄位 | 說明 |
|---|---|
| **Task ID** | 例如 `A04-submit-form` |
| **Story ID** | 例如 `A04-1` |
| **功能範圍** | 只包含哪個頁面 / API / 流程 |
| **相關文件** | BDD、Page Design、SDD、TDD、API、Flow |
| **輸入資料** | 需要的欄位、Request DTO、主檔 |
| **狀態限制** | 哪些狀態可操作 |
| **角色限制** | 哪些角色可用 |
| **完成條件** | UI、API、測試、錯誤處理 |

---

## 6. Agent 任務切片模板

```md
## Task ID
A04-submit-form

## Story ID
A04-1

## Goal
完成請假申請單的草稿儲存與送審

## In Scope
- LeaveRequestDetailView
- POST /api/v1/applications/leave-requests
- POST /api/v1/applications/leave-requests/{id}/submit

## Out of Scope
- 審核頁
- 訊息中心通知

## Source Documents
- bdd/a04-petition.feature
- page-design/a03-leave-request-page-design.md
- sdd/a04-petition-sdd.md
- tdd/a04-petition-test-design.md
- api-design/CSKM_API_Design.md
- flow-design/CSKM_Process_State_Design.md

## Done When
- 可儲存草稿
- 可送審
- 驗證失敗顯示正確訊息
- 狀態由 DRAFT -> SUBMITTED
- 相關測試通過
```

---

## 7. 對 Agent 友善的 PHP 後端文件寫法

### 7.1 Controller

- 每個 Controller 只處理 HTTP 進出（Request → Service → Response）
- 使用 Form Request 做輸入驗證，不在 Controller 中撰寫 `if` 驗證
- 回傳格式統一使用 API Resource

### 7.2 Service

- 業務邏輯集中在 Service 層
- Service 不直接操作 DB，透過 Repository 存取
- Service 拋出自訂 Exception，Controller 統一處理

### 7.3 Model / Migration

- 每個 Eloquent Model 都要有對應 Migration
- 狀態欄位使用 PHP Enum（BackedEnum）而非魔術字串
- 關聯方法（hasMany / belongsTo）要明確宣告

### 7.4 API Design

應補清楚：

- endpoint
- Form Request（輸入驗證規則）
- API Resource（回傳格式）
- error code
- 分頁 / 篩選 / 排序欄位
- idempotent 要求

### 7.5 Flow-State

應補清楚：

- currentStatus
- allowedActions
- nextStatus
- actor role
- batch rule
- failure reason

### 7.6 TDD

至少分成：

- 單元測試（PHPUnit / Pest Unit）
- API 整合測試（Feature Test with `actingAs`）
- 前端互動測試（Vitest / Vue Test Utils）
- E2E / workflow 測試

---

## 8. Definition of Ready for Agent Task

只有符合以下條件的功能，才適合交給 Agent 實作：

1. 有明確 **Story ID**
2. 有對應 **BDD**
3. 有對應 **Page Design**
4. 有對應 **SDD**
5. API / Data Model / Flow-State 中可找到所需規格
6. 完成條件明確
7. 範圍夠小，可在單次交付中完成

---

## 9. Definition of Done for Agent Task

一個切片要算完成，至少要滿足：

1. **功能可執行**
2. **文件可追溯**
3. **狀態流轉正確**
4. **權限限制正確**（Policy / Gate 驗證）
5. **錯誤碼與錯誤訊息一致**
6. **測試覆蓋已更新**（PHPUnit / Pest Feature Test）
7. **必要文件同步更新**

---

## 10. 建議的重建專案結構

### 後端（PHP / Laravel）

```text
backend/
  app/
    Http/
      Controllers/
      Requests/
      Resources/
    Models/
    Services/
    Repositories/
    DTO/
    Enums/
    Policies/
    Jobs/
  config/
  database/
    migrations/
    seeders/
    factories/
  routes/
    api.php
  tests/
    Unit/
    Feature/
  composer.json
```

### 前端

```text
frontend/
  src/
    views/
    components/
    api/
    stores/
    router/
    types/
    tests/
```

### 規格

```text
docs/
  user story
  bdd
  page-design
  sdd
  tdd
  api-design
  data-model
  flow-design
```

這種固定結構對 AI Agent 最友善，因為同類責任總是在同一位置。

---

## 11. 變更規則

當 Agent 修改某一個功能時，至少要同步檢查：

1. BDD 是否需要新增 / 調整 Scenario
2. Page Design 是否需要新增欄位、按鈕或互動
3. SDD 是否需要調整責任分層
4. API Design 是否需要補 endpoint / DTO / error code
5. Flow-State 是否需要補狀態轉移或 allowedActions
6. TDD 是否需要補測試案例

---

## 12. 與 GitHub 結合方式

若要讓 Agent 可以持續接手，建議把文件鏈接到 GitHub 工作流：

1. 用 **GitHub Issue** 承接單一任務切片
2. 用 **branch** 隔離單一切片修改
3. 用 **Pull Request** 記錄交付內容與交接資訊
4. 用 **GitHub Actions** 檢查 PR 格式與必要欄位

請搭配：

- `CSKM_AI_Agent_PHP_Development_Guide.md`
- `CSKM_GitHub_AI_Agent_Workflow.md`
- `.github/ISSUE_TEMPLATE/ai-agent-task.yml`
- `.github/pull_request_template.md`
- `.github/workflows/agent-pr-check.yml`

目前 workflow 也會檢查 `Source Documents` 路徑是否存在，並在偵測到 `backend/composer.json` 時自動執行 `composer install && php artisan test`。

---

## 13. 建議的 Agent 實作順序

1. **Login / Auth / Permission**（Sanctum Token + Policy）
2. **Master Data**（主檔 API）
3. **Application + Approval**（申請與批核流程）
4. **Report + Approval**（報表與批核）
5. **Student Lifecycle**（學員生命周期）
6. **Finance**（財務確認）
7. **Import / Export / Message Center**（匯入匯出 / 訊息中心）

---

## 14. 最重要的實務原則

若要讓 AI Agent 能長期持續開發，請始終維持：

1. **一個功能只有一套名稱**
2. **一個狀態只有一種 Enum**（PHP BackedEnum）
3. **一個錯誤只有一組 code**
4. **一個任務只處理一個切片**
5. **每次交付都能靠測試驗證**（Pest Feature Test）

---

## 15. 一句話結論

**AI Agent 最怕的不是程式碼多，而是規格模糊、命名漂移、狀態隱藏、任務過大；這份文件的目的，就是把 CSKM 重建規格整理成適合 Agent 以 PHP Laravel 持續接手的工程格式。**
