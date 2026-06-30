# 業務招生與學員服務 TDD 設計

## 對應

- BDD：`bdd/group-sales-student-lifecycle.feature`
- SDD：`sdd/group-sales-student-lifecycle-sdd.md`

## 測試主題

- B00 AjaxSearch / New / Assign_Forward
- B01 狀態篩選
- B02 課程業績與服務狀態
- C04 搜尋、重新指派學顧

## 代表案例

- `B00_ajaxSearch_shouldFilterLeadList`
- `B00_new_shouldCreateLeadRecord`
- `B00_assignForward_shouldChangeOwner`
- `B01_ajaxSearch_shouldReturnOwnProgress`
- `B02_getCrs_shouldReturnCoursesByInstitute`
- `B02_ajaxSearch_shouldReturnPerformanceStats`
- `C04_search_shouldLimitByRegion`
- `C04_changeSS_shouldUpdateSupervisor`

## Workflow

- 建立名單 → 指派學顧 → 追蹤狀態 → 更新課程 / 服務狀態
