# Login / 首頁訊息中心 SDD

## 1. 對應需求

- Story：`Login-1`、`Login-2`、`Login-3`
- 前端補充：`login.jsp`、`msglist.jsp`
- 目標：完成員工登入、登出、權限驗證與訊息中心首頁載入

## 2. 角色

- 員工：登入、登出、查看個人首頁
- 系統：驗證帳號、建立 session、攔截未授權請求
- 管理者：透過 F02 決定可用 menu 與權限

## 3. 畫面與路由

### 畫面

- `login.jsp`：帳號、密碼、登入/取消按鈕
- `msglist.jsp`：公告、假單、報表、簽呈、請款單、員工守則等 Tab
- `error.jsp`：未登入或權限不足時的錯誤頁

### 路由

- `GET /login`：登入頁入口
- `POST /login/verify`：驗證登入
- `GET /login/out`：登出
- `POST /login/msgread`：將訊息標記為已讀
- `GET /login/msgread`：已讀後回導訊息中心
- `POST /login/anncounce/search`：公告關鍵字搜尋
- `GET /login/error`：未登入錯誤
- `GET /login/permit_error`：無權限錯誤

## 4. 後端責任切分

### Login controller

- 驗證帳號與密碼
- 檢查員工是否為在職狀態
- 初始化首頁資料與 menu
- 處理訊息已讀與公告搜尋
- 登出時清理 session

### RequestInitializeInterceptor

- 驗證 `/app/*` 是否已登入
- 檢查當前頁面是否在使用者授權 menu 內
- 每次請求時刷新選單與必要 session 資訊

## 5. 資料與 session 設計

### 主要資料

- `hr_management`：帳號、密碼、在職狀態、部門、職稱、角色權限
- `menu`：可用頁面與導覽資訊
- 公告、假單、簽呈、請款等首頁訊息來源資料表

### Session 關鍵值

- `SESS_IS_LOGIN`
- `SESS_USER_ID`
- `SESS_MENU_INFO`
- `SESS_MENU_TITLE`
- `SESS_REG_DEP_ID`

## 6. 流程設計

### 成功登入

1. 使用者送出帳號密碼
2. controller 驗證欄位非空
3. 以 MD5 + salt 比對密碼
4. 驗證員工仍在職
5. 載入 menu 權限與首頁訊息資料
6. 建立 session
7. 導向 `msglist.jsp`

### 失敗登入

1. 欄位空白 → 留在登入頁顯示提示
2. 密碼錯誤 → 留在登入頁顯示錯誤
3. 員工已離職 → 留在登入頁顯示離職訊息

### 首頁互動

1. 使用者在 `msglist.jsp` 切換 Tab
2. 透過 Ajax 執行已讀或公告搜尋
3. 依訊息類型連至 A03/A04/A06 等明細頁

## 7. 權限規則

- 未登入使用者不得存取 `/app/*`
- 已登入但未擁有某頁面 menu 權限者不得進入該頁
- 首頁內容依使用者角色與權限載入不同待辦項目

## 8. 錯誤處理

- 空白帳密：顯示必填錯誤
- 密碼錯誤：顯示帳密錯誤
- 已離職：顯示離職訊息
- 未登入：導向 `/login/error`
- 權限不足：導向 `/login/permit_error`
- Ajax 查詢失敗：回傳前端可呈現的空結果或錯誤訊息

## 9. 風險與設計注意事項

- 密碼規則採舊系統 MD5 + salt，需避免與後續新制混用
- 首頁資料來源多，查詢過慢時需注意查詢成本
- 權限錯誤容易出現在 menu 初始化與 interceptor 判斷不一致

## 10. 可測試點

- 正確/錯誤/離職登入
- session 初始化完整性
- 未登入與未授權頁面攔截
- 已讀 Ajax 與公告搜尋 Ajax
