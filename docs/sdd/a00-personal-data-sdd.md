# A00 個人資料維護 SDD

## 1. 對應需求

- Story：`A00-1`、`A00-2`
- 對應 BDD：`bdd/a00-personal-data.feature`
- 目標：讓員工維護完整個人資料與照片，並支援 F02 唯讀檢視

## 2. 角色

- 員工：編修自己的基本個資、地址、語言能力、技能、證照、家庭狀況、工作經歷與照片
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
- 基本欄位：姓名、電話、性別、血型、生日、照片、員工編號、到職日期、區域、部門、職稱、狀態
- JSON 欄位：`registered_address`、`mailing_address`、`language_abilities`、`skills`、`certifications`、`family_information`、`work_experiences`
- API 回傳狀態欄位：`currentStatus`、`allowedActions`、`handbook_url`

## 5. 流程設計

1. 員工進入頁面時載入既有個資與照片
2. 員工可更新基本資料、地址、語言能力、技能、證照、家庭狀況與工作經歷
3. `同上` 啟用後，通訊地址會同步戶籍地址內容
4. 儲存時呼叫 `PUT /api/v1/me/personal-data`，成功後以前後端一致格式回填畫面
5. 若有新照片則呼叫 `POST /api/v1/me/personal-data/photo`
6. 由 F02 帶入時改呼叫 `GET /api/v1/staff/{staffId}/personal-data`，狀態固定為唯讀
7. `閱讀員工守則` 會依 `handbook_url` 開啟 `staff.pdf`
8. 唯讀頁面不載入系統側邊導覽，也不提供儲存、照片或明細列增刪操作

## 6. 權限與前端互動

- 血型與性別下拉於載入時自動帶入目前值
- 生日、證照日期、工作經歷日期欄位使用日期輸入
- 到職日期 `join_date` 為唯讀顯示欄位
- 技能欄位以逗號分隔輸入並轉為陣列儲存
- 唯讀模式需隱藏導覽列與操作按鈕，並停用編修欄位

## 7. 驗證與錯誤處理

- 基本資料沿用既有姓名 / 電話 / 性別 / 血型 / 生日驗證
- 地址欄位驗證字串長度與布林旗標
- 語言、證照、家庭與工作經歷明細列需驗證必要欄位與日期格式
- 儲存失敗時顯示錯誤訊息
- 照片上傳失敗時優先顯示欄位驗證錯誤
- 非管理權限使用者查詢他人個資時回傳 `403`

## 8. 可測試點

- 個資載入與 `join_date` 顯示
- 地址同步與 JSON 資料持久化
- 語言 / 技能 / 證照 / 家庭 / 工作經歷儲存成功
- 巢狀欄位驗證失敗
- 唯讀模式與 API 權限：本人可寫、管理者代查唯讀
