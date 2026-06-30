# D03 報表審核 TDD 設計

## 1. 對應需求

- Story：`D03-1`
- 對應 BDD：`bdd/d03-report-approval.feature`
- 對應 SDD：`sdd/d03-report-approval-sdd.md`

## 2. 測試分層

### Unit

- 報表類型應導向正確明細頁
- 退回必填意見

### Integration

- 報表列表查詢成功
- 審核結果寫入成功

### Workflow

- 主管審核 → CEO 審核

### UI Acceptance

- 列表顯示報表類型與期間

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| RA-UNIT-01 | Unit | reportTypeShouldRouteCorrectDetail | 驗證導頁規則 |
| RA-INT-01 | Integration | approveReportShouldPersistResult | 驗證審核寫入 |
| RA-WF-01 | Workflow | reportApprovalFlow | 驗證完整流程 |

## 4. Given / When / Then 範本

- Given：存在一筆待審日報
- When：主管於對應明細頁送出核准
- Then：狀態更新並返回清單

## 5. 邊界案例

- 報表類型不明
- 已審核再審

## 6. 完成標準

- 導頁與審核流程皆有測試
