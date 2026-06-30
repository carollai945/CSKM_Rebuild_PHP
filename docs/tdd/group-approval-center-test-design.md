# 申請 / 批核中心 TDD 設計

## 對應

- BDD：`bdd/group-approval-center.feature`
- SDD：`sdd/group-approval-center-sdd.md`

## 測試主題

- A03 / A05 / A06 建單與搜尋
- D00 / D02 / D03 批核
- D04 學員意見查詢
- E03 財務確認 / 退件

## 代表案例

- `A03_ajaxSearch_shouldReturnOwnForms`
- `A03_post_submit_shouldCreateApplyNo`
- `A05_ajaxDelete_shouldDeleteDraftInvoice`
- `A06_post_shouldCreateAnnouncementWithValidDate`
- `D00_post_approve_shouldPersistDecision`
- `D02_post_reject_shouldPersistReason`
- `D03_search_shouldReturnReportQueue`
- `D04_ajaxSearch_shouldFilterSuggestions`
- `E03_finance_shouldConfirmReimbursement`
- `E03_returnBack_shouldRequireReturnReason`

## Workflow

- 建立請假單 → 主管批核
- 建立公告 → 批核 → 首頁顯示
- 建立請款 → 財務退件 → 申請人重送
