# C02 學生管理 TDD 設計

## 1. 對應需求

- Story：`C02-1`, `C02-2`, `C02-3`
- 對應 BDD：`bdd/c02-student-management.feature`
- 對應 SDD：`sdd/c02-student-sdd.md`

## 2. 測試分層

### Integration（StudentControllerTest）

- 已登入用戶可查詢學生清單
- 依 `keyword` 篩選學生
- 依 `region_id` 篩選學生
- `admin/ceo/regmgr` 可新增學生
- 非 `admin/ceo/regmgr` 不可新增（403）
- 修改學生成功
- 刪除學生成功
- 匯出 Excel 回傳 `.xlsx`（C02-3）
- 未登入存取回傳 401

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| C02-INT-01 | Integration | `authenticated_user_can_list_students` | 查詢學生清單 |
| C02-INT-02 | Integration | `filter_students_by_keyword` | 關鍵字篩選 |
| C02-INT-03 | Integration | `filter_students_by_region` | 區域篩選 |
| C02-INT-04 | Integration | `admin_can_create_student` | 管理員新增學生 |
| C02-INT-05 | Integration | `non_management_cannot_create_student` | 非管理員不可新增 |
| C02-INT-06 | Integration | `admin_can_export_students_to_excel` | 匯出 Excel |
| C02-INT-07 | Integration | `unauthenticated_request_returns_401` | 未登入存取 |
