# A00 個人資料維護 SDD

## 1. 對應需求

- Story：`A00-1`、`A00-2`
- 對應 BDD：`bdd/a00-personal-data.feature`
- 目標：讓員工維護個人基本資料與照片，並支援 F02 唯讀檢視

## 2. 角色

- 員工：編修自己的基本個資與照片
- 管理者：從 F02 人員管理唯讀檢視員工個資
- 系統：載入既有資料、執行欄位驗證並保存更新結果

## 3. 畫面與路由

- `A00.jsp`：個人資料維護頁
- `GET /api/v1/me/personal-data`：載入本人個資與照片
- `PUT /api/v1/me/personal-data`：更新本人個資
- `POST /api/v1/me/personal-data/photo`：更新本人照片
- `GET /api/v1/staff/{staffId}/personal-data`：由 F02 帶入唯讀檢視
- 前端路由：`/me/personal-data`（本人可編修）、`/staff/{staffId}/personal-data`（唯讀）

## 4. 資料設計

- 主檔：`staff`
- 關聯：`region`、`department`、`title`
- 核心欄位：姓名、電話、性別、血型、生日、照片、員工編號、到職日期、區域、部門、職稱
- API 回傳狀態欄位：`currentStatus`、`allowedActions`

## 5. 流程設計

1. 員工進入頁面時載入既有個資與照片
2. 員工可更新姓名、電話、性別、血型、生日，並可選擇照片檔案
3. 儲存時呼叫 `PUT /api/v1/me/personal-data`，成功後以前後端一致格式回填畫面
4. 若有新照片則呼叫 `POST /api/v1/me/personal-data/photo`
5. 由 F02 帶入時改呼叫 `GET /api/v1/staff/{staffId}/personal-data`，狀態固定為唯讀
6. 唯讀頁面不載入系統側邊導覽，也不提供儲存或照片操作

## 6. 權限與前端互動

- 血型與性別下拉於載入時自動帶入目前值
- 生日欄位使用日期輸入
- 到職日期 `join_date` 為唯讀顯示欄位
- 唯讀模式需隱藏導覽列與操作按鈕，並停用編修欄位

## 7. 錯誤處理

- 儲存失敗時顯示錯誤訊息
- 照片上傳失敗時優先顯示欄位驗證錯誤
- 非管理權限使用者查詢他人個資時回傳 `403`

## 8. 可測試點

- 個資載入
- `join_date` 顯示
- 唯讀模式
- 儲存成功/失敗
- API 權限：本人可寫、管理者代查唯讀
