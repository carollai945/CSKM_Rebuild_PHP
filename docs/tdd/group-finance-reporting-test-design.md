# 財務報表與請款流程 TDD 設計

## 對應

- BDD：`bdd/group-finance-reporting.feature`
- SDD：`sdd/group-finance-reporting-sdd.md`

## 測試主題

- E01 年度收入報表
- E02 個人 / 請款查詢
- E03 財務確認 / 退件

## 代表案例

- `E01_ajaxSearch_shouldReturnYearlyRevenue`
- `E01_ajaxSearch_shouldApplyDecimalFormat`
- `E02_ajaxSearch_shouldFilterByDateAndDept`
- `E03_ajaxSearch_shouldReturnPendingItems`
- `E03_finance_shouldMarkConfirmed`
- `E03_returnBack_shouldPersistReturnReason`

## Workflow

- 查詢請款 → 財務確認
- 查詢請款 → 財務退件 → 重新處理
