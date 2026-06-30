# Login / 首頁訊息中心 TDD 設計

## 1. 對應需求

- Story：`Login-1`、`Login-2`、`Login-3`
- 對應 BDD：`bdd/group-login.feature`
- 對應 SDD：`sdd/group-login-sdd.md`

## 2. 測試分層

### Unit

- 密碼加鹽後比對成功
- 空白帳號或密碼回傳錯誤
- 離職員工被判定不可登入
- menu 權限判斷為 false 時應拒絕存取

### Integration

- `POST /login/verify` 成功建立 session
- `POST /login/verify` 錯誤帳密不建立 session
- `GET /login/out` 清除 session
- `POST /login/msgread` 可更新訊息已讀
- `POST /login/anncounce/search` 可回傳過濾後公告

### Workflow

- 正確登入 → 進入 msglist → 標記已讀 → 登出
- 正確登入 → 嘗試進入未授權頁面 → 被導向 permit_error

### UI Acceptance

- `login.jsp` 顯示帳號與密碼欄位
- 錯誤訊息文字正確
- `msglist.jsp` 的 badge、Tab、Ajax 更新正常

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| LG-UNIT-01 | Unit | validPassword_shouldPassHashValidation | 驗證密碼規則 |
| LG-UNIT-02 | Unit | blankCredentials_shouldFailValidation | 驗證必填 |
| LG-UNIT-03 | Unit | retiredUser_shouldBeRejected | 驗證離職限制 |
| LG-INT-01 | Integration | verifyLogin_success_shouldCreateSession | 驗證成功登入 |
| LG-INT-02 | Integration | verifyLogin_wrongPassword_shouldReject | 驗證錯誤密碼 |
| LG-INT-03 | Integration | logout_shouldClearSession | 驗證登出 |
| LG-INT-04 | Integration | msgread_shouldUpdateReadFlag | 驗證已讀 Ajax |
| LG-INT-05 | Integration | announceSearch_shouldFilterResults | 驗證公告搜尋 Ajax |
| LG-WF-01 | Workflow | loginToMsgCenterAndLogout | 驗證主流程 |
| LG-WF-02 | Workflow | unauthorizedPage_shouldRedirect | 驗證權限攔截 |

## 4. Given / When / Then 範本

### LG-INT-01

- Given：建立在職員工、設定 menu 權限
- When：送出 `POST /login/verify`
- Then：response 導向 `msglist`
- And：session 包含 `SESS_IS_LOGIN`、`SESS_USER_ID`

### LG-INT-02

- Given：建立在職員工
- When：以錯誤密碼送出登入
- Then：返回登入頁或錯誤頁
- And：session 不應有登入資訊

## 5. 邊界案例

- 帳號大小寫差異
- 密碼含特殊字元
- 員工存在但無任何 menu 權限
- Ajax 請求在 session 逾時後發送

## 6. 完成標準

- 成功 / 失敗 / 離職 / 未授權場景皆有覆蓋
- session 建立與清除可驗證
- 兩支 Ajax 端點至少各有 1 個整合測試
