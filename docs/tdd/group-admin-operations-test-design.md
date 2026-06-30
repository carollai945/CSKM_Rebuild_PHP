# 系統管理與維運 TDD 設計

## 對應

- BDD：`bdd/group-admin-operations.feature`
- SDD：`sdd/group-admin-operations-sdd.md`

## 測試主題

- F00 區域 CRUD
- F01 部門 / 職稱 CRUD
- F03 備份列表與觸發
- F04 Excel 匯入與去重複
- F05 人員列表與離職標示

## 代表案例

- `F00_post_shouldCreateOrUpdateBranch`
- `F00_delete_shouldRemoveBranch`
- `F01_search_shouldReturnDepartmentTree`
- `F010_new_shouldCreateDepartment`
- `F011_new_shouldCreateTitle`
- `F03_get_shouldListBackupFiles`
- `F04_post_shouldImportExcelAndCountResults`
- `F04_post_shouldSkipDuplicateRows`
- `F05_get_shouldReturnActiveAndRetiredStaff`
- `F05_get_shouldIncludeRegionStats`

## Workflow

- 建立區域 → 建立部門 / 職稱 → 建立人員 → 離職後於 F05 驗證狀態
