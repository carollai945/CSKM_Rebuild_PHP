# B02 學員服務意見 SDD

## 1. 對應需求

- Story：`B02-1`
- 對應 BDD：`bdd/b02-student-service.feature`
- 目標：記錄並追蹤學員服務意見與回覆狀況

## 2. 角色

- 業務人員 / 服務人員：新增、修改學員意見記錄
- 管理員：查詢所有意見、修改狀態

## 3. 畫面與路由

- 前端路由：`/student-feedbacks`
- Vue 元件：`StudentFeedbackView.vue`
- 對應舊系統：`D04.jsp`（D04 學員意見管理）

## 4. 資料設計

主檔：`student_feedbacks`

| 欄位 | 型別 | 說明 |
|---|---|---|
| `id` | BIGINT PK | 意見識別碼 |
| `student_id` | BIGINT FK | 關聯學生 |
| `staff_id` | BIGINT FK | 記錄人員 |
| `content` | TEXT | 意見內容 |
| `status` | VARCHAR(20) | `PENDING` / `IN_PROGRESS` / `CLOSED` |
| `created_at` | TIMESTAMP | 建立時間 |

## 5. API 設計

| Method | Path | 說明 |
|---|---|---|
| `GET` | `/api/v1/student-feedbacks` | 查詢清單（支援 status、keyword、from、to 篩選） |
| `GET` | `/api/v1/student-feedbacks/{id}` | 取得明細 |
| `POST` | `/api/v1/student-feedbacks` | 新增意見 |
| `PUT` | `/api/v1/student-feedbacks/{id}` | 修改意見 |
| `DELETE` | `/api/v1/student-feedbacks/{id}` | 刪除意見 |

## 6. 權限

- 查詢：所有已登入用戶
- 新增 / 修改：已登入用戶（限自己建立的記錄）
- 刪除：`admin`、`ceo`
