# D01 簽呈批核 頁面設計

## 1. 基本資料

- 頁面路徑：`/approvals/petitions`
- 對應 BDD / SDD / TDD：`d01-petition-approval`
- 使用者：RegMgr、CEO

## 2. 頁面目的

查看所有待批核的簽呈申請，並執行核准或退回操作。

## 3. 版面區塊

- 頁面標題：「簽呈批核」
- 載入提示：「載入中...」（API 回應前）
- 待批清單表格（見下）
- 空清單提示：「目前無待批核簽呈」

## 4. 表格欄位

| 欄位 | 說明 |
|---|---|
| ID | 簽呈編號 |
| 申請人 | 員工姓名（從 staff 關聯取得） |
| 標題 | 簽呈標題 |
| 狀態 | PENDING / APPROVED / REJECTED |
| 操作 | 核准按鈕、退回按鈕 |

## 5. 按鈕與動作

- `核准`：呼叫 `POST /approvals/petitions/{id}/approve`，成功後刷新清單
- `退回`：呼叫 `POST /approvals/petitions/{id}/reject`，成功後刷新清單

## 6. 互動規則

- 頁面載入時自動取得待批清單
- 操作成功後清單即時刷新

## 7. 權限

- RegMgr：僅顯示管轄區域內的簽呈
- CEO：顯示所有簽呈
- Staff：無法存取此頁面
