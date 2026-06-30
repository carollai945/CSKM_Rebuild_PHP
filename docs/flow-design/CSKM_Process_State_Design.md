# CSKM 重建流程 / 狀態設計

> 目的：把舊系統分散在按鈕、hidden Type、Ajax action、審核頁中的流程邏輯，整理成可重建的狀態機規格。

---

## 1. 設計原則

1. **狀態機獨立於畫面存在**
2. **單筆與批次動作使用同一組狀態規則**
3. **狀態改變一定留下 action log**
4. **角色權限、資料範圍、狀態轉移三者分開定義**

---

## 2. 流程清單

| 流程 | 對應功能 |
|---|---|
| 帳號登入與停用 | Login、F02 |
| 申請單送審 / 審核 | A03、A04、A05、A06、D00、D02、E03 |
| 報表送審 / 審核 | A02、D03 |
| 繳費雙重確認 | E00 |
| 電訪名單派發與轉學員 | B00、B01、B02、C04 |
| 批次匯入 / 通知 | F04、msglist |

---

## 3. 帳號生命周期

### 狀態

- `ACTIVE`
- `LOCKED`
- `INACTIVE`
- `LEFT`

### 轉移

| 目前狀態 | 事件 | 下一狀態 | 角色 |
|---|---|---|---|
| `ACTIVE` | 管理員停用 | `INACTIVE` | 系統管理員 |
| `ACTIVE` | 填入離職日 | `LEFT` | 系統管理員 |
| `LOCKED` | 管理員解鎖 | `ACTIVE` | 系統管理員 |
| `INACTIVE` | 重新啟用 | `ACTIVE` | 系統管理員 |

### 規則

- `LEFT` 狀態不得登入
- `INACTIVE` 不得出現在可派發 / 可審核名單
- 重設密碼不改變帳號生命周期狀態

---

## 4. 申請單流程

適用：

- 請假
- 簽呈
- 公告
- 請款 / 發票相關申請

### 4.1 主狀態

- `DRAFT`
- `SUBMITTED`
- `MANAGER_APPROVED`
- `MANAGER_REJECTED`
- `CEO_APPROVED`
- `CEO_REJECTED`
- `CANCELLED`
- `COMPLETED`

### 4.2 狀態轉移

| 目前狀態 | 事件 | 下一狀態 | 角色 |
|---|---|---|---|
| `DRAFT` | 儲存草稿 | `DRAFT` | 申請人 |
| `DRAFT` | 送審 | `SUBMITTED` | 申請人 |
| `SUBMITTED` | 主管核准 | `MANAGER_APPROVED` 或 `COMPLETED` | 主管 |
| `SUBMITTED` | 主管退回 | `MANAGER_REJECTED` | 主管 |
| `MANAGER_APPROVED` | CEO 核准 | `CEO_APPROVED` / `COMPLETED` | CEO |
| `MANAGER_APPROVED` | CEO 退回 | `CEO_REJECTED` | CEO |
| `MANAGER_REJECTED` | 申請人重送 | `SUBMITTED` | 申請人 |
| `CEO_REJECTED` | 申請人重送 | `SUBMITTED` | 申請人 |
| `DRAFT` / `SUBMITTED` | 自行取消 | `CANCELLED` | 申請人 |

### 4.3 規則

1. 退回必填意見
2. 已 `COMPLETED` 不得再次編輯
3. 批次核准 / 退回仍逐筆留下 action log
4. 公告類型需額外檢查生效日期

---

## 5. 批次審核流程

對應：

- `D00`
- `D01`
- `D02`
- `E03`

### 5.1 批次動作

- `BATCH_APPROVE`
- `BATCH_REJECT`

### 5.2 批次處理規則

1. 前端可全選 / 全不選，但後端逐筆檢查權限與狀態
2. 同一批次可共用 comment
3. 回傳結果必須區分：
   - 成功單號
   - 失敗單號
   - 失敗原因

### 5.3 失敗條件

- 任務已被他人處理
- 任務狀態已不是待審
- 使用者對該筆資料沒有資料範圍權限

---

## 6. 報表流程

適用：

- `A02`
- `D03`

### 6.1 主狀態

- `DRAFT`
- `SUBMITTED`
- `MANAGER_APPROVED`
- `MANAGER_REJECTED`
- `CEO_APPROVED`
- `CEO_REJECTED`
- `ARCHIVED`

### 6.2 類型

- `DAILY`
- `WEEKLY`

### 6.3 轉移規則

| 目前狀態 | 事件 | 下一狀態 |
|---|---|---|
| `DRAFT` | 儲存 | `DRAFT` |
| `DRAFT` | 送審 | `SUBMITTED` |
| `SUBMITTED` | 主管核准 | `MANAGER_APPROVED` 或 `ARCHIVED` |
| `SUBMITTED` | 主管退回 | `MANAGER_REJECTED` |
| `MANAGER_APPROVED` | CEO 核准 | `CEO_APPROVED` / `ARCHIVED` |
| `MANAGER_APPROVED` | CEO 退回 | `CEO_REJECTED` |
| `MANAGER_REJECTED` / `CEO_REJECTED` | 重新編修送審 | `SUBMITTED` |

### 6.4 額外規則

1. 日報 / 週報可共用一套狀態機
2. UI 以 `草稿 -> 修改`、`送審後 -> 檢視` 呈現
3. 查詢頁需支援依 `reportType` 與 `staffType` 分流

---

## 7. 繳費雙重確認流程

對應：

- `E00`

### 7.1 狀態模型

繳費不建議只靠單一 status，而是兩個軸：

- `finance_status`
- `academic_status`

### 7.2 狀態值

- `PENDING`
- `CONFIRMED`
- `REJECTED`

### 7.3 狀態組合

| finance_status | academic_status | 業務意義 |
|---|---|---|
| `PENDING` | `PENDING` | 新建待確認 |
| `CONFIRMED` | `PENDING` | 財務已確認 |
| `PENDING` | `CONFIRMED` | 教務已確認 |
| `CONFIRMED` | `CONFIRMED` | 完成 |
| `REJECTED` | 任意 | 財務退回 |
| 任意 | `REJECTED` | 教務退回 |

### 7.4 規則

1. 任一方退回都必須有 `reason`
2. 已確認者不得重複確認
3. 若一方退回，前端畫面應標示為需重處理

---

## 8. 電訪名單與學員轉換流程

對應：

- `B00`
- `B01`
- `B02`
- `C04`

### 8.1 Lead 狀態

- `NEW`
- `ASSIGNED`
- `CONTACTING`
- `FOLLOW_UP`
- `CONVERTED`
- `CLOSED`

### 8.2 轉移

| 目前狀態 | 事件 | 下一狀態 |
|---|---|---|
| `NEW` | 派發 | `ASSIGNED` |
| `ASSIGNED` | 建立電訪紀錄 | `CONTACTING` |
| `CONTACTING` | 需追蹤 | `FOLLOW_UP` |
| `FOLLOW_UP` | 成功成單 | `CONVERTED` |
| 任意 | 明確結案 | `CLOSED` |

### 8.3 學員側規則

- `CONVERTED` 後建立 `student`
- 建立學員後可進入服務、修課、繳費流程
- `學顧變更` 必須留下異動紀錄

---

## 9. 匯入與背景工作流程

對應：

- `F04`

### Job 狀態

- `PENDING`
- `RUNNING`
- `PARTIAL_SUCCESS`
- `FAILED`
- `COMPLETED`

### 規則

1. Excel 匯入需保存批次摘要
2. 每列失敗原因需可下載或查詢
3. 匯入後的 lead 建立應可追到來源批次

---

## 10. 狀態與權限分工

狀態是否可轉移，應同時受三種條件限制：

1. **角色權限**：是否為主管、CEO、財務、教務、系統管理員
2. **資料範圍**：本人 / 本區 / 本部門 / 全部
3. **目前狀態**：是否仍可操作

### 範例

- 一般員工不可核准任何申請單
- 區域主管只可處理本區資料
- 已 `CANCELLED` 的單據不可再送審
- 已 `ARCHIVED` 的報表不可再修改

### A00 個人資料維護（補充）

#### 狀態

- `EDITABLE`
- `READONLY`

#### 轉移與限制

| 目前狀態 | 事件 | 下一狀態 | 規則 |
|---|---|---|---|
| `EDITABLE` | 載入個人資料頁 | `EDITABLE` | 一般員工僅能載入自己的資料 |
| `EDITABLE` | 儲存合法資料 | `EDITABLE` | 儲存成功後停留可編修狀態並重新載入資料 |
| `EDITABLE` | 欄位驗證失敗 | `EDITABLE` | 不可送出儲存，停留第一個錯誤欄位 |
| `READONLY` | 由 F02 帶入檢視 | `READONLY` | 隱藏導覽與操作按鈕，不可觸發儲存與照片更新 |

#### A00 權限規則

1. 員工編修僅限本人資料
2. 管理者代查僅限唯讀檢視
3. `allowedActions` 在 `READONLY` 必須為空集合

---

## 11. 前端顯示規則

前端按鈕顯示不應直接寫死，而應依：

- `allowedActions`
- `currentStatus`
- `currentUserRoles`

由後端或前端權限層統一決定。

### 例

- `DRAFT`：顯示 `儲存`、`送審`、`刪除`
- `SUBMITTED`：顯示 `檢視`
- `待審任務`：顯示 `核准`、`退回`
- `批次頁`：顯示 `全選`、`批次核准`、`批次退回`

---

## 12. AI Agent 友善流程輸出

若希望前後端與 AI Agent 都能穩定理解流程，每個流程節點至少應對外提供：

- `currentStatus`
- `allowedActions`
- `availableActors`
- `failureReasons`
- `nextStatuses`

### 補充規則

1. 不使用只有前端知道的 hidden type 作為唯一流程依據
2. 批次流程仍應逐筆回傳成功 / 失敗結果
3. 前端按鈕顯示應由 `allowedActions` 驅動，而不是散落在頁面邏輯中
4. 每次新增狀態，都要同步更新 API、BDD、TDD

---

## 13. 一句話結論

**重建時最重要的不是把舊頁面按鈕搬過去，而是把「送審、批次、核准、退回、雙重確認、匯入」這些隱含在 JSP + JS 裡的流程，提升成明確的狀態機規格。**
