# D04 學員意見管理 SDD

## 1. 對應需求

- Story：`D04-1`
- 對應 BDD：`bdd/d04-student-feedback.feature`
- 目標：管理人員 / 服務人員查詢學員意見、閱讀明細並追蹤處理狀態

## 2. 角色

- 管理人員 / 服務人員：依授權區域查詢與檢視意見
- 主管 / CEO：可跨區域查詢所有意見

## 3. 畫面與路由

- `D04.jsp`：學員意見列表與查詢入口，對應前端路由 `/student-feedback`
- `D040.jsp`：學員意見明細檢視頁（前端以覆蓋層呈現）

### API 路由

| Method | Path | 用途 |
|---|---|---|
| `GET` | `/api/v1/student-feedbacks` | 依條件查詢意見列表 |
| `GET` | `/api/v1/student-feedbacks/{id}` | 查詢單筆意見明細 |

## 4. 資料設計

- 主表：`student_feedback`
- 核心欄位：
  - `feedback_no`：系統產生的意見單號（唯一）
  - `student_name`：學員姓名
  - `region`：區域
  - `department`：部門
  - `handler`：負責人員姓名
  - `status`：處理狀態（`PENDING` / `IN_PROGRESS` / `CLOSED`）
  - `content`：意見內容（TEXT）
  - `reply`：回覆紀錄（TEXT，支援多筆以換行分隔）
  - `submitted_at`：提交時間
  - `updated_at`：最後更新時間

## 5. 流程設計

1. 使用者於 D04 以條件查詢意見列表
2. 後端依使用者角色套用區域過濾（一般使用者限自身授權區域）
3. 點擊「檢視」按鈕進入 D040 明細頁，顯示意見全文與回覆紀錄

## 6. 查詢條件

- `from` / `to`：提交日期區間（可選）
- `region`：區域（可選；一般使用者後端強制過濾）
- `department`：部門（可選）
- `status`：處理狀態（可選）
- `handler`：負責人員姓名關鍵字（可選）

## 7. 狀態說明

| 狀態 | 說明 |
|---|---|
| `PENDING` | 待處理 |
| `IN_PROGRESS` | 處理中 |
| `CLOSED` | 已結案 |

## 8. 權限規則

- `SUPERVISOR` / `CEO`：可查詢所有區域資料
- 其他角色：後端依 `staff.region` 限制可見範圍
