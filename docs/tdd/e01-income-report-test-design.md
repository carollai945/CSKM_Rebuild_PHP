# E01 收入報表 TDD 設計

## 1. 對應需求

- Story：`E01-1`
- 對應 BDD：`bdd/e01-income-report.feature`
- 對應 SDD：`sdd/e01-income-report-sdd.md`

## 2. 測試分層

### Unit

- 查詢期間格式驗證正確
- 金額彙總規則正確

### Integration

- 條件查詢回傳正確統計
- 角色僅能看可見範圍資料

### Workflow

- 設定期間 → 查詢收入 → 顯示總額

### UI Acceptance

- 顯示總額與明細列表

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| IRP-UNIT-01 | Unit | dateRangeValidatorShouldWork | 驗證期間格式 |
| IRP-UNIT-02 | Unit | summaryAmountShouldAggregateCorrectly | 驗證彙總 |
| IRP-INT-01 | Integration | queryIncomeReportShouldReturnSummary | 驗證報表查詢 |
| IRP-WF-01 | Workflow | incomeReportQueryFlow | 驗證查詢流程 |

## 4. Given / When / Then 範本

- Given：查詢期間內存在多筆收入資料
- When：財務送出查詢
- Then：系統顯示彙總金額與明細

## 5. 邊界案例

- 起日大於迄日
- 查無資料

## 6. 完成標準

- 查詢與彙總皆有測試
