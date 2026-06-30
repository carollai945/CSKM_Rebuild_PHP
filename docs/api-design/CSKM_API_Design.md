# CSKM 重建 API 設計

> 目標架構：Java 21 + Spring Boot 3 + Vue 3  
> API 風格：REST JSON API  
> Base Path：`/api/v1`

---

## 1. 設計原則

1. **以前端頁面需求為入口，但 API 以資源設計為主**
2. **把舊 JSP 的 Ajax action 整併成穩定的 REST endpoint**
3. **批次操作、狀態轉移、核准 / 退回，使用明確 action endpoint**
4. **所有列表 API 都支援關鍵字、狀態、角色資料範圍與分頁**
5. **API 規格必須足夠穩定，讓 AI Agent 不需閱讀 controller 原始碼也能實作前後端**

---

## 2. 共通規格

### 2.1 Request / Response

- Content-Type：`application/json`
- 時區：`Asia/Taipei`
- 日期格式：`yyyy-MM-dd`
- 日期時間格式：ISO-8601

### 2.2 成功回應

```json
{
  "data": {},
  "meta": {
    "traceId": "uuid",
    "timestamp": "2026-05-29T18:00:00+08:00"
  }
}
```

### 2.3 失敗回應

```json
{
  "error": {
    "code": "APPROVAL_COMMENT_REQUIRED",
    "message": "退回時必須輸入意見",
    "details": []
  },
  "meta": {
    "traceId": "uuid",
    "timestamp": "2026-05-29T18:00:00+08:00"
  }
}
```

### 2.4 分頁

列表查詢參數：

- `page`
- `size`
- `sort`
- `keyword`
- `status`
- `from`
- `to`

### 2.5 AI Agent 友善規格

- 每個 endpoint 都應對應穩定的 `resource name`
- request / response DTO 應維持固定命名
- 錯誤回應必須使用明確 `error.code`
- 狀態型 API 建議回傳 `currentStatus` 與 `allowedActions`
- 列表結果建議補 `filters` 與 `sort` 回顯，方便 Agent 與前端對齊

---

## 3. 認證與登入

對應舊系統：`login.jsp`、`msglist.jsp`、`Login.java`

| Method | Path | 用途 |
|---|---|---|
| `POST` | `/auth/login` | 登入 |
| `POST` | `/auth/logout` | 登出 |
| `GET` | `/auth/me` | 取得當前登入者資訊與權限 |
| `POST` | `/auth/change-password` | 修改自己的密碼 |
| `POST` | `/auth/reset-password` | 管理員重設他人密碼 |
| `GET` | `/messages` | 取得訊息中心資料 |
| `PATCH` | `/messages/{messageId}/read` | 標記已讀 |

---

## 4. 主檔 API

對應舊系統：`F00`、`F01`、`C00`、`C01`、`C02`、`C03`、`C05`

### 4.1 組織結構

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/regions` | 查詢區域 |
| `POST` | `/regions` | 建立區域 |
| `PUT` | `/regions/{regionId}` | 修改區域 |
| `GET` | `/departments` | 查詢部門 |
| `POST` | `/departments` | 建立部門 |
| `PUT` | `/departments/{departmentId}` | 修改部門 |
| `GET` | `/titles` | 查詢職稱 |
| `POST` | `/titles` | 建立職稱 |
| `PUT` | `/titles/{titleId}` | 修改職稱 |

### 4.2 教務主檔

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/institutes` | 查詢機構 |
| `POST` | `/institutes` | 建立機構 |
| `PUT` | `/institutes/{instituteId}` | 修改機構 |
| `GET` | `/courses` | 查詢課程 |
| `POST` | `/courses` | 建立課程 |
| `PUT` | `/courses/{courseId}` | 修改課程 |
| `GET` | `/subjects` | 查詢科目 |
| `POST` | `/subjects` | 建立科目 |
| `PUT` | `/subjects/{subjectId}` | 修改科目 |
| `GET` | `/classrooms` | 查詢教室 |
| `POST` | `/classrooms` | 建立教室 |
| `PUT` | `/classrooms/{classroomId}` | 修改教室 |
| `DELETE` | `/classrooms/{classroomId}` | 刪除教室 |

### 4.3 師資管理（C01）

對應舊系統：`C01`、`C010`

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/professors` | 查詢師資列表（支援 `keyword` 篩選） |
| `POST` | `/professors` | 新增師資（含文件檔名） |
| `GET` | `/professors/{professorId}` | 取得師資明細（含附件清單） |
| `PUT` | `/professors/{professorId}` | 修改師資資料（含文件檔名） |
| `DELETE` | `/professors/{professorId}` | 刪除師資及其所有附件 |
| `DELETE` | `/professors/{professorId}/files/{fileId}` | 刪除單一附件 |

#### C01 API 規則

- `GET /professors` 支援 `keyword` 查詢參數，以姓名模糊比對篩選
- `POST /professors` / `PUT /professors/{id}` 的 `documentFileNames` 欄位為文件檔名清單；若 `PUT` 時提供此欄位，系統會先刪除舊附件再重新建立
- 照片路徑儲存於 `prof_data.photo_path`；文件附件儲存於 `prof_attachment` 表，路徑前綴為 `C:\CSKM\Other\C01\`
- `DELETE /professors/{id}/files/{fileId}` 需確認附件屬於該師資，否則回傳 404

---

## 5. 人員與權限 API

對應舊系統：`F02`、`F05`

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/staff` | 查詢人員 |
| `POST` | `/staff` | 建立人員 |
| `GET` | `/staff/{staffId}` | 查詢單一人員 |
| `PUT` | `/staff/{staffId}` | 修改人員基本資料 |
| `PATCH` | `/staff/{staffId}/status` | 啟用 / 停用 / 離職 |
| `GET` | `/staff/{staffId}/permissions` | 查詢角色與 menu 權限 |
| `PUT` | `/staff/{staffId}/permissions` | 更新角色與 menu 權限 |
| `GET` | `/permission-groups` | 取得模組權限矩陣 |
| `GET` | `/staff/autocomplete` | 姓名自動完成 |

### 5.1 A00 個人資料維護（Personal Data）

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/me/personal-data` | 取得本人個人資料頁所需資料（含血型/性別目前值與照片） |
| `PUT` | `/me/personal-data` | 更新本人個人資料（不含照片檔案本體） |
| `POST` | `/me/personal-data/photo` | 更新本人照片 |
| `GET` | `/staff/{staffId}/personal-data?mode=readonly` | 管理者由 F02 帶入時，以唯讀模式檢視指定員工資料 |

#### A00 API 規則

- `/staff/{staffId}/personal-data?mode=readonly` 必須檢查 `STAFF_ADMIN` 或等效權限，且 `allowedActions` 應為空集合
- `PUT /me/personal-data` 僅可更新登入者自己的個資
- `POST /me/personal-data/photo` 需檢查檔案格式與大小，並回傳最新照片資源位置
- 回傳建議包含：`currentStatus`（`EDITABLE` / `READONLY`）與 `allowedActions`（如 `SAVE`、`UPLOAD_PHOTO`）

### 權限模型建議

- 角色旗標：`CEO`、`REGION_MANAGER`、`DEPARTMENT_MANAGER`、`FINANCE_MANAGER`、`CLASS_MANAGER`、`STAFF_ADMIN`
- Menu 權限：以功能碼 `A00` ~ `F05` 或 route key 儲存
- 資料範圍：本人 / 本區 / 本部門 / 全部

---

## 6. 業務 / 學員生命周期 API

對應舊系統：`B00`、`B01`、`B02`、`C04`、`C046`

### 6.1 電訪名單

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/leads` | 查詢電訪名單 |
| `POST` | `/leads` | 建立名單 |
| `PUT` | `/leads/{leadId}` | 修改名單 |
| `DELETE` | `/leads/{leadId}` | 刪除名單 |
| `POST` | `/leads:assign` | 批次派發 |
| `POST` | `/leads:import` | Excel 匯入 |

### 6.2 電訪與服務

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/interviews` | 查詢電訪紀錄 |
| `POST` | `/interviews` | 新增電訪紀錄 |
| `PUT` | `/interviews/{interviewId}` | 修改電訪紀錄 |
| `GET` | `/student-services` | 查詢學員服務紀錄 |
| `POST` | `/student-services` | 建立服務紀錄 |

### 6.3 學員

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/students` | 查詢學員 |
| `POST` | `/students` | 建立學員 |
| `GET` | `/students/{studentId}` | 取得學員詳細資料 |
| `PUT` | `/students/{studentId}` | 修改學員基本資料 |
| `PATCH` | `/students/{studentId}/advisor` | 變更學顧 |
| `GET` | `/students/{studentId}/courses` | 查詢學員修課狀態 |
| `PUT` | `/students/{studentId}/courses` | 更新學員修課狀態 |
| `GET` | `/students/{studentId}/payments` | 查詢學員繳費 |

---

## 7. 申請單 / 批核 API

對應舊系統：`A03`、`A04`、`A05`、`A06`、`D00`、`D02`、`D04`

### 7.1 申請單主資源

建議統一為：

- `/applications/leave-requests`
- `/applications/petitions`
- `/applications/invoice-requests`
- `/applications/announcements`

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/applications/{type}` | 查詢申請單 |
| `POST` | `/applications/{type}` | 建立草稿 |
| `GET` | `/applications/{type}/{applicationId}` | 查詢明細 |
| `PUT` | `/applications/{type}/{applicationId}` | 修改草稿 |
| `POST` | `/applications/{type}/{applicationId}:submit` | 送審 |
| `POST` | `/applications/{type}/{applicationId}:cancel` | 取消 |
| `POST` | `/applications/{type}/{applicationId}:delete` | 刪除草稿 |

### 7.2 批核任務

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/approval-tasks` | 查詢待審任務 |
| `POST` | `/approval-tasks/{taskId}:approve` | 單筆核准 |
| `POST` | `/approval-tasks/{taskId}:reject` | 單筆退回 |
| `POST` | `/approval-tasks:batch-approve` | 批次核准 |
| `POST` | `/approval-tasks:batch-reject` | 批次退回 |

### 7.3 設計重點

- 批次 API 必須回傳：
  - 成功筆數
  - 失敗筆數
  - 失敗單號清單
- 退回一定要有 `comment`
- Approval task 與 Application 分離，避免每種單據各寫一套審核邏輯

---

## 8. 報表 API

對應舊系統：`A02`、`A020~A027`、`D03`

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/reports` | 查詢我的日報 / 週報 |
| `POST` | `/reports` | 建立報表 |
| `GET` | `/reports/{reportId}` | 查詢報表明細 |
| `PUT` | `/reports/{reportId}` | 修改草稿 |
| `POST` | `/reports/{reportId}:submit` | 送審 |
| `GET` | `/report-approval-tasks` | 查詢待審報表 |
| `POST` | `/report-approval-tasks/{taskId}:approve` | 核准報表 |
| `POST` | `/report-approval-tasks/{taskId}:reject` | 退回報表 |

### 重要欄位

- `reportType`：`DAILY` / `WEEKLY`
- `staffType`：`SALES` / `ADMIN`
- `periodStart` / `periodEnd`
- `content`
- `status`

---

## 9. 財務 API

對應舊系統：`E00`、`E01`、`E02`、`E03`

### 9.1 繳費

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/payments` | 查詢繳費 |
| `POST` | `/payments` | 建立繳費 |
| `GET` | `/payments/{paymentId}` | 查詢繳費明細 |
| `POST` | `/payments/{paymentId}:finance-confirm` | 財務確認 |
| `POST` | `/payments/{paymentId}:academic-confirm` | 教務確認 |
| `POST` | `/payments/{paymentId}:reject` | 退回繳費 |

### 9.2 請款

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/reimbursements` | 查詢請款單 |
| `GET` | `/reimbursements/{reimbursementId}` | 查詢請款明細 |
| `POST` | `/reimbursements/{reimbursementId}:finance-confirm` | 財務確認 |
| `POST` | `/reimbursements/{reimbursementId}:return` | 財務退回 |

### 9.3 財務報表

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/finance-reports/income` | 收入報表 |
| `GET` | `/finance-reports/invoices` | 發票 / 請款彙整 |
| `GET` | `/finance-reports/payments` | 繳費明細彙整 |

---

## 10. 檔案與附件 API

| Method | Path | 用途 |
|---|---|---|
| `POST` | `/files` | 上傳附件 |
| `GET` | `/files/{fileId}` | 下載附件 |
| `DELETE` | `/files/{fileId}` | 刪除附件 |
| `GET` | `/manuals/{manualCode}` | 下載操作手冊 |
| `GET` | `/exports/{exportId}` | 下載匯出結果 |

---

## 11. 前端頁面與 API 對照原則

### 例 1：A03 請假入口 + D00 審核

- 前端頁面：
  - `LeaveRequestListView`
  - `LeaveRequestDetailView`
  - `LeaveApprovalView`
- 後端 API：
  - `GET /applications/leave-requests`
  - `POST /applications/leave-requests`
  - `POST /applications/leave-requests/{id}:submit`
  - `GET /approval-tasks?type=LEAVE_REQUEST`
  - `POST /approval-tasks/{id}:approve`

### 例 2：B00 電訪名單管理

- 前端頁面：
  - `LeadListView`
  - `LeadEditDialog`
- 後端 API：
  - `GET /leads`
  - `POST /leads`
  - `PUT /leads/{id}`
  - `POST /leads:assign`

---

## 12. AI Agent 實作補充規則

1. 新功能優先沿用既有 resource 命名，不新增同義 endpoint
2. 每個 action endpoint 都應明寫狀態前提與失敗錯誤碼
3. 每個列表 endpoint 都應明寫分頁、排序、篩選與資料範圍
4. 每個 mutation endpoint 都應能從 TDD 反推驗證案例

---

## 13. 一句話結論

**重建 API 不應照舊系統的 JSP / Ajax action 一比一搬移，而應以登入、主檔、學員生命周期、申請批核、報表、財務六大子域重新整理成穩定的 REST 資源。**
