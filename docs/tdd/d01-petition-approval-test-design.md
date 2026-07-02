# D01 簽呈批核 — 測試設計文件 (TDD)

## 1. 測試範圍

`PetitionApprovalController` 的 API 與前端 `PetitionApprovalView.vue`。

## 2. 測試案例

### 2.1 GET /approvals/petitions/pending

| ID | 說明 | 前置條件 | 期望結果 |
|---|---|---|---|
| TC-D01-001 | RegMgr 取得待批清單 | 有 PENDING 簽呈 | 200，回傳清單 |
| TC-D01-002 | CEO 取得全區待批清單 | 有多區域 PENDING 簽呈 | 200，回傳所有區域清單 |
| TC-D01-003 | Staff 無存取權 | Staff 角色 | 403 Forbidden |
| TC-D01-004 | 無待批簽呈 | 無 PENDING 簽呈 | 200，空陣列 |

### 2.2 POST /approvals/petitions/{id}/approve

| ID | 說明 | 前置條件 | 期望結果 |
|---|---|---|---|
| TC-D01-010 | RegMgr 核准轄區內簽呈 | PENDING 簽呈，屬管轄區 | 200，status=APPROVED |
| TC-D01-011 | 核准非轄區簽呈 | PENDING 簽呈，非管轄區 | 403 Forbidden |
| TC-D01-012 | 核准已核准的簽呈 | APPROVED 簽呈 | 422，狀態錯誤 |

### 2.3 POST /approvals/petitions/{id}/reject

| ID | 說明 | 前置條件 | 期望結果 |
|---|---|---|---|
| TC-D01-020 | RegMgr 退回轄區內簽呈 | PENDING 簽呈 | 200，status=REJECTED |
| TC-D01-021 | 退回已退回的簽呈 | REJECTED 簽呈 | 422，狀態錯誤 |

## 3. 前端測試要點

- 載入時呼叫 `pending API`
- 核准/退回按鈕點擊後觸發對應 API
- API 回應後清單重新載入
- 空清單時顯示提示文字
