# C01 師資管理 SDD

## 1. 對應需求

- Story：`C01-1`, `C01-2`
- 對應 BDD：`bdd/c01-professor.feature`
- 目標：維護師資基本資料、上傳照片與文件附件

## 2. 角色

- 教務人員 / 管理員：查詢、新增、修改師資資料、上傳照片與文件

## 3. 畫面與路由

- 前端路由：`/professors`
- Vue 元件：`ProfessorView.vue`
- 對應舊系統：`C01.jsp`（師資清單）、`C010.jsp`（新增 / 修改）

## 4. 資料設計

主檔：`professors`

| 欄位 | 型別 | 說明 |
|---|---|---|
| `id` | BIGINT PK | 師資識別碼 |
| `name` | VARCHAR(200) | 姓名 |
| `email` | VARCHAR(200) | 電子信箱 |
| `phone` | VARCHAR(50) | 聯絡電話 |
| `specialty` | TEXT | 專長領域 |
| `photo_path` | VARCHAR(500) | 照片路徑（`C:\CSKM\Img\C01\`） |
| `status` | VARCHAR(20) | 狀態：`ACTIVE` / `INACTIVE` |

附件表：`prof_attachments`

| 欄位 | 型別 | 說明 |
|---|---|---|
| `id` | BIGINT PK | 附件識別碼 |
| `professor_id` | BIGINT FK | 關聯師資 |
| `file_path` | VARCHAR(500) | 路徑前綴 `C:\CSKM\Other\C01\` |
| `file_name` | VARCHAR(200) | 原始檔名 |

## 5. API 設計

| Method | Path | 說明 |
|---|---|---|
| `GET` | `/api/v1/professors` | 查詢師資清單（支援 keyword、status 篩選） |
| `GET` | `/api/v1/professors/{id}` | 取得單一師資明細 |
| `POST` | `/api/v1/professors` | 新增師資 |
| `PUT` | `/api/v1/professors/{id}` | 修改師資 |
| `DELETE` | `/api/v1/professors/{id}` | 刪除師資 |
| `POST` | `/api/v1/professors/{id}/photo` | 上傳師資照片 |
| `POST` | `/api/v1/professors/{id}/attachments` | 上傳附件 |

## 6. 權限

- 列表查詢：所有已登入用戶
- 新增 / 修改 / 刪除：`admin`、`ceo`
