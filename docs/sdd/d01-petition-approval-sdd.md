# D01 簽呈批核 — 系統設計文件 (SDD)

## 1. 功能概述

提供主管與 CEO 查看、核准或退回員工簽呈申請的功能。

## 2. API 設計

| Method | Path | 說明 |
|---|---|---|
| `GET` | `/api/v1/approvals/petitions/pending` | 取得待批核簽呈清單 |
| `POST` | `/api/v1/approvals/petitions/{id}/approve` | 核准簽呈 |
| `POST` | `/api/v1/approvals/petitions/{id}/reject` | 退回簽呈 |
| `POST` | `/api/v1/approvals/petitions/batch-approve` | 批次核准（計劃中） |
| `POST` | `/api/v1/approvals/petitions/batch-reject` | 批次退回（計劃中） |

### 回應格式

```json
{
  "data": [
    {
      "id": 1,
      "title": "採購申請",
      "content": "...",
      "status": "PENDING",
      "staff": { "id": 1, "name": "王小明" },
      "created_at": "2026-07-01T09:00:00+08:00"
    }
  ]
}
```

## 3. 狀態流

```
DRAFT → PENDING → APPROVED
                → REJECTED
```

## 4. 權限規則

- `GET pending`：RegMgr、CEO 可存取（Staff 403）
- `POST approve/reject`：RegMgr（管轄範圍內）、CEO（全部）
- RegMgr 資料範圍：僅自己區域的簽呈
- CEO 資料範圍：所有區域

## 5. Controller

- 類別：`PetitionApprovalController`
- 方法：`pending()`、`approve()`、`reject()`

## 6. 前端頁面

- 路徑：`/approvals/petitions`
- 元件：`PetitionApprovalView.vue`
- 功能：顯示待批清單、核准/退回單筆、空清單提示
