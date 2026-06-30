# 教務主資料管理 TDD 設計

## 對應

- BDD：`bdd/group-academic-management.feature`
- SDD：`sdd/group-academic-management-sdd.md`

## 測試主題

- C00 區域 / 機構 / 課程連動
- C01 師資 CRUD 與檔案刪除
- C02 學生查詢 / 維護 / 匯出
- C03 教室 CRUD
- C05 費用設定 CRUD 與預覽

## 代表案例

- `C00_srchOrg_shouldReturnInstitutesByRegion`
- `C00_new_shouldCreateCourseSetting`
- `C01_post_shouldSaveProfessorAndFiles`
- `C01_delFile_shouldRemoveAttachment`
- `C02_search_shouldFilterStudents`
- `C02_export_shouldGenerateExcel`
- `C03_search_shouldReturnClassroomsByRegion`
- `C05_new_shouldInsertItemSetting`
- `C05_showPreview_shouldReturnCourseFeeSummary`

## Workflow

- 建立課程 → 建立師資 → 建立學生 → 匯出學生清單
