# Login / 首頁訊息中心頁面設計

## 1. 對應文件

- SDD：`sdd/group-login-sdd.md`
- BDD：`bdd/group-login.feature`
- 目標頁面：`login.jsp`、`msglist.jsp`

## 2. 頁面目的

- `login.jsp`：提供員工登入入口，驗證帳號密碼後建立 session
- `msglist.jsp`：提供首頁訊息中心，顯示公告與各模組待辦入口

## 3. 版面區塊

### login.jsp

- 登入欄位區：帳號、密碼
- 操作按鈕區：登入、取消
- 錯誤提示區：帳密錯誤、離職、欄位未填

### msglist.jsp

- 頂部導覽與使用者資訊區
- 訊息分類 Tab 區（公告、假單、報表、簽呈、請款等）
- 訊息列表區
- 關鍵字搜尋與已讀操作區

## 4. 欄位與互動規則

- 登入欄位皆為必填，任一缺漏不得送出
- 登入成功後導向 `msglist.jsp`
- `msglist.jsp` 支援 Tab 切換、公告搜尋、訊息已讀 Ajax 動作
- 未登入或權限不足時導向錯誤頁（`/login/error`、`/login/permit_error`）

## 5. 訊息設計

- 登入失敗：顯示帳號或密碼錯誤訊息
- 離職帳號：顯示離職訊息
- Ajax 失敗：顯示可辨識的操作失敗訊息，並保留原列表
