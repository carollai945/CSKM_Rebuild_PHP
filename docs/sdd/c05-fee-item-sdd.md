# C05 費用項目設定 SDD

## 1. 對應需求

- Story：`C05-1`
- 對應 BDD：`bdd/c05-fee-item-setting.feature`（如已存在）
- 目標：設定課程費用項目，供請款與繳費流程使用

## 2. 角色

- 管理員 / CEO：新增、修改、刪除費用項目
- 一般員工：僅查詢

## 3. 畫面與路由

- 前端路由：`/fee-items`
- Vue 元件：`FeeItemView.vue`
- 對應舊系統：`C05.jsp`

## 4. 資料設計

主檔：`fee_items`

| 欄位 | 型別 | 說明 |
|---|---|---|
| `id` | BIGINT PK | 費用項目識別碼 |
| `name` | VARCHAR(200) | 費用名稱 |
| `amount` | DECIMAL(12,2) | 金額 |
| `description` | TEXT | 說明 |
| `status` | VARCHAR(20) | 狀態：`ACTIVE` / `INACTIVE` |

## 5. API 設計

| Method | Path | 說明 |
|---|---|---|
| `GET` | `/api/v1/fee-items` | 查詢費用項目清單 |
| `POST` | `/api/v1/fee-items` | 新增費用項目 |
| `PUT` | `/api/v1/fee-items/{id}` | 修改費用項目 |
| `DELETE` | `/api/v1/fee-items/{id}` | 刪除費用項目 |

## 6. 權限

- 查詢：所有已登入用戶
- 新增 / 修改 / 刪除：`admin`、`ceo`
