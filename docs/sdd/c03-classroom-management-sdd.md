# C03 教室管理 SDD

## 1. 對應需求

- Story：`C03-1`
- 對應 BDD：`bdd/c03-classroom-management.feature`
- 目標：維護教室基本資料，供排課時選用正確教室並掌握容量

## 2. 角色

- 教務人員 / 管理員：新增、修改、刪除教室資料
- 系統：依區域分類顯示教室清單，並驗證容量合法性

## 3. 畫面與路由

- 前端路由：`/classroom-management`
- Vue 元件：`C03ClassroomView.vue`
- 對應舊系統：`C03.jsp`（教室清單）、`C030.jsp`（教室新增 / 修改）

## 4. 資料設計

- 主檔：`classroom`
- 核心欄位：

| 欄位 | 型別 | 說明 |
|---|---|---|
| `id` | BIGINT PK | 教室識別碼（自動產生） |
| `region_id` | BIGINT FK | 所屬區域 |
| `classroom_name` | VARCHAR(200) | 教室名稱 |
| `capacity` | INT | 容量（≥ 1） |
| `status` | VARCHAR(20) | 狀態：`ACTIVE` / `INACTIVE` |

## 5. API 設計

| Method | Path | 說明 |
|---|---|---|
| `GET` | `/api/v1/classrooms` | 查詢教室清單（可選 `regionId` 篩選） |
| `POST` | `/api/v1/classrooms` | 新增教室 |
| `PUT` | `/api/v1/classrooms/{classroomId}` | 修改教室 |
| `DELETE` | `/api/v1/classrooms/{classroomId}` | 刪除教室 |

### 請求格式（POST / PUT）

```json
{
  "regionId": 1,
  "classroomName": "101教室",
  "capacity": 30,
  "status": "ACTIVE"
}
```

### 回應格式（GET / POST / PUT）

```json
{
  "data": {
    "id": 1,
    "regionId": 1,
    "classroomName": "101教室",
    "capacity": 30,
    "status": "ACTIVE"
  }
}
```

## 6. 流程設計

1. 使用者進入教室管理頁，系統自動載入區域清單與全部教室清單
2. 使用者選擇區域後按下查詢，系統清空原清單並顯示該區域教室
3. 使用者填入教室資料後按下新增，系統驗證並儲存，然後刷新清單
4. 使用者按下編修，欄位進入可編輯狀態，修改後按儲存或取消
5. 使用者按下刪除，系統刪除紀錄並刷新清單

## 7. 權限

- 僅具 `ROLE_ACADEMIC` 或 `ROLE_ADMIN` 角色者可執行新增、修改、刪除
- 查詢（GET）對所有已驗證使用者開放

## 8. 錯誤處理

| 錯誤碼 | HTTP 狀態 | 說明 |
|---|---|---|
| `CLASSROOM_MANAGEMENT_FORBIDDEN` | 403 | 無教務設定權限 |
| `CLASSROOM_NOT_FOUND` | 404 | 教室不存在 |
| `REGION_NOT_FOUND` | 404 | 區域不存在 |
| `VALIDATION_ERROR` | 400 | 欄位驗證失敗（如容量 < 1） |

## 9. 可測試點

- 依區域篩選教室清單
- 新增教室並預設 `status = ACTIVE`
- 修改教室名稱、容量與狀態
- 刪除教室後清單不再顯示
- 無權限者無法新增或修改
