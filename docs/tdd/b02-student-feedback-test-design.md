# B02 學員服務與意見 TDD 設計

## 1. 對應需求

- Story：`B02-1`
- 對應 BDD：`bdd/b02-student-service.feature`
- 對應 SDD：`sdd/b02-student-service-sdd.md`

## 2. 測試分層

### Integration（StudentFeedbackControllerTest）

- 已登入用戶可查詢學員意見清單
- 依 `status` 篩選學員意見
- 依日期區間篩選
- 業務人員可新增學員意見
- 修改學員意見成功
- 刪除學員意見成功
- 查詢單一學員意見明細

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| B02-INT-01 | Integration | `authenticated_user_can_list_feedbacks` | 查詢學員意見清單 |
| B02-INT-02 | Integration | `filter_feedbacks_by_status` | 依狀態篩選 |
| B02-INT-03 | Integration | `staff_can_create_feedback` | 新增學員意見 |
| B02-INT-04 | Integration | `staff_can_update_feedback` | 修改學員意見 |
| B02-INT-05 | Integration | `get_feedback_detail` | 取得明細 |
