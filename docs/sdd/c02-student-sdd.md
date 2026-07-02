# C02 學生管理 SDD

## 1. 對應需求

- Story：`C02-1`, `C02-2`, `C02-3`
- 對應 BDD：`bdd/c02-student-management.feature`
- 目標：維護學生基本資料，支援查詢、新增、修改及匯出 Excel

## 2. 角色

- 教務人員 / 管理員：查詢、新增、修改學生資料、匯出 Excel
- 一般員工：僅查詢

## 3. 畫面與路由

- 前端路由：`/students`
- Vue 元件：`StudentView.vue`、`StudentDetailView.vue`（C046）
- 對應舊系統：`C02.jsp`、`C020.jsp`

## 4. 資料設計

主檔：`students`

| 欄位 | 型別 | 說明 |
|---|---|---|
| `id` | BIGINT PK | 學生識別碼 |
| `name` | VARCHAR(200) | 姓名 |
| `email` | VARCHAR(200) | 電子信箱 |
| `phone` | VARCHAR(50) | 聯絡電話 |
| `region_id` | BIGINT FK | 所屬區域 |
| `status` | VARCHAR(20) | 狀態：`ACTIVE` / `INACTIVE` |
| `enrolled_at` | DATE | 入學日期 |

## 5. API 設計

| Method | Path | 說明 |
|---|---|---|
| `GET` | `/api/v1/students` | 查詢學生清單（支援 region_id、keyword、status 篩選） |
| `GET` | `/api/v1/students/{id}` | 取得單一學生明細 |
| `POST` | `/api/v1/students` | 新增學生 |
| `PUT` | `/api/v1/students/{id}` | 修改學生 |
| `DELETE` | `/api/v1/students/{id}` | 刪除學生 |
| `GET` | `/api/v1/students/export` | 匯出學生清單為 Excel（.xlsx） |

## 6. 匯出規格（C02-3）

- 觸發：管理員 / 教務點擊「匯出 Excel」按鈕
- 格式：`.xlsx`
- 欄位：學號、姓名、電話、Email、區域、狀態、入學日期
- 篩選：與列表相同（region_id、keyword、status）
