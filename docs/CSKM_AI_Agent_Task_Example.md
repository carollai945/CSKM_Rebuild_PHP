# CSKM AI Agent 任務範例文件

> 用途：提供一份可以直接複製修改的 AI Agent 任務文件範例，讓後續功能能用固定格式持續交付。

---

## 1. 文件基本資料

| 欄位 | 內容 |
|---|---|
| Task ID | `A04-submit-form` |
| Story ID | `A04-1` |
| 功能名稱 | 請假申請：草稿儲存與送出審核 |
| 優先順序 | High |
| 建議切片大小 | 一個頁面 + 一組 API + 一個狀態轉移 |

---

## 2. Goal

完成請假申請單的：

1. 草稿儲存
2. 送出審核

讓員工可先建立草稿，確認資料後再送出，並將狀態由 `DRAFT` 轉為 `SUBMITTED`。

---

## 3. Business Context

舊系統中的請假申請功能同時包含表單輸入、欄位驗證、草稿暫存、送審與後續主管批核流程。  
本任務只處理「申請人建立與送出」這一段，不包含主管審核與批次處理。

---

## 4. In Scope

- 請假申請表單頁
- 欄位輸入與前端驗證
- 建立草稿 API
- 送出審核 API
- 後端狀態轉移邏輯
- 成功 / 失敗訊息顯示

---

## 5. Out of Scope

- 主管審核頁
- 批次審核
- 訊息中心通知
- 附件上傳
- 審核意見回寫

---

## 6. Source Documents

| 類型 | 文件 |
|---|---|
| BDD | `bdd/a04-petition.feature` |
| Page Design | `page-design/a03-leave-request-page-design.md` |
| SDD | `sdd/a04-petition-sdd.md` |
| TDD | `tdd/a04-petition-test-design.md` |
| API Design | `api-design/CSKM_API_Design.md` |
| Flow-State | `flow-design/CSKM_Process_State_Design.md` |
| Agent Guide | `CSKM_AI_Agent_Development_Guide.md` |

---

## 7. UI Requirements

### 7.1 欄位

- 請假類型
- 開始日期
- 結束日期
- 請假原因

### 7.2 按鈕

- `儲存草稿`
- `送出審核`
- `返回`

### 7.3 畫面行為

1. 使用者可輸入請假資料
2. 點擊 `儲存草稿` 後可保存目前內容
3. 點擊 `送出審核` 前需先通過欄位驗證
4. 送出成功後畫面需顯示目前狀態為 `SUBMITTED`
5. 驗證失敗時需顯示明確錯誤訊息

---

## 8. API Requirements

### 8.1 建立 / 儲存草稿

`POST /api/v1/applications/leave-requests`

#### Request

```json
{
  "leaveType": "SICK",
  "startDate": "2026-06-01",
  "endDate": "2026-06-02",
  "reason": "Fever"
}
```

#### Response

```json
{
  "id": 1001,
  "currentStatus": "DRAFT",
  "allowedActions": ["SAVE_DRAFT", "SUBMIT"]
}
```

### 8.2 送出審核

`POST /api/v1/applications/leave-requests/{id}:submit`

#### Response

```json
{
  "id": 1001,
  "currentStatus": "SUBMITTED",
  "allowedActions": []
}
```

### 8.3 Error Code

| 錯誤碼 | 說明 |
|---|---|
| `LEAVE_REQUEST_REQUIRED_FIELD_MISSING` | 必填欄位未填 |
| `LEAVE_REQUEST_INVALID_DATE_RANGE` | 起訖日期錯誤 |
| `LEAVE_REQUEST_STATUS_NOT_SUBMITTABLE` | 目前狀態不可送出 |

---

## 9. Flow Requirements

| 項目 | 規則 |
|---|---|
| 初始狀態 | `DRAFT` |
| 可執行動作 | `SAVE_DRAFT`, `SUBMIT` |
| 送出後狀態 | `SUBMITTED` |
| 角色 | 申請人 |
| 限制 | 必填欄位未完成不可送出 |

### 狀態轉移

```text
DRAFT --SUBMIT--> SUBMITTED
```

---

## 10. Validation Rules

1. 請假類型必填
2. 開始日期必填
3. 結束日期必填
4. 開始日期不可晚於結束日期
5. 請假原因必填

---

## 11. Backend Implementation Hint

### 建議後端切分

- `LeaveRequestController`
- `LeaveRequestService`
- `LeaveRequestRepository`
- `LeaveRequest`
- `LeaveRequestCreateRequest`
- `LeaveRequestResponse`

### 建議方法

- `saveDraft()`
- `submit()`
- `validateBeforeSubmit()`

---

## 12. Frontend Implementation Hint

### 建議前端切分

- `LeaveRequestDetailView.vue`
- `useLeaveRequestForm.ts`
- `leaveRequestApi.ts`
- `leaveRequest.types.ts`

### 建議互動

1. 載入表單資料
2. 編輯欄位
3. 點擊儲存草稿
4. 點擊送出審核
5. 顯示成功 / 失敗訊息

---

## 13. Testing Requirements

### 單元測試

- 驗證日期區間檢查
- 驗證必填欄位檢查
- 驗證 `DRAFT -> SUBMITTED` 狀態轉移

### API 整合測試

- 可建立草稿
- 可送出審核
- 缺少必填欄位時回傳正確錯誤碼
- 錯誤狀態不可重複送出

### 前端互動測試

- 未填欄位點送出時顯示錯誤
- 儲存草稿成功後顯示成功訊息
- 送出成功後狀態顯示為 `SUBMITTED`

---

## 14. Done When

- 使用者可成功建立請假草稿
- 使用者可成功送出審核
- 前端欄位驗證正確
- 狀態流轉正確
- API 回應含 `currentStatus` 與 `allowedActions`
- 對應測試案例已完成
- 若規格有變動，相關文件同步更新

---

## 15. 可直接交給 Agent 的簡化版本

```md
請依據以下文件完成請假申請的草稿儲存與送出審核功能。

Task ID: A04-submit-form
Story ID: A04-1

In Scope:
- 請假申請表單頁
- POST /api/v1/applications/leave-requests
- POST /api/v1/applications/leave-requests/{id}:submit
- 狀態 DRAFT -> SUBMITTED

Source Documents:
- bdd/a04-petition.feature
- page-design/a03-leave-request-page-design.md
- sdd/a04-petition-sdd.md
- tdd/a04-petition-test-design.md
- api-design/CSKM_API_Design.md
- flow-design/CSKM_Process_State_Design.md
- CSKM_AI_Agent_Development_Guide.md

Done When:
- 可儲存草稿
- 可送出審核
- 驗證錯誤訊息正確
- 狀態流轉正確
- 測試完成
```

---

## 16. 使用方式

後續你只要把這份文件複製一份，改掉：

- `Task ID`
- `Story ID`
- `Goal`
- `In Scope / Out of Scope`
- `Source Documents`
- `Done When`

就能快速產生下一個可交給 AI Agent 的任務文件。
