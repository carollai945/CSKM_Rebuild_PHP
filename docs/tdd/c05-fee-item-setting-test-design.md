# C05 費用項目設定 TDD 設計

## 對應

- BDD：`bdd/group-academic-management.feature`
- SDD：`sdd/group-academic-management-sdd.md`
- Story：C05-1

## 測試主題

- C05 費用項目 CRUD（新增、查詢、修改、刪除）
- C05 費用類型彙總預覽

## 代表案例

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| C05-UNIT-01 | Unit | `C05_new_shouldInsertItemSetting` | 驗證新增費用項目並儲存 |
| C05-UNIT-02 | Unit | `createShouldFailWhenCourseNotFound` | 驗證課程不存在時新增失敗 |
| C05-UNIT-03 | Unit | `createShouldFailWhenNotAcademic` | 驗證無教務權限時新增失敗 |
| C05-UNIT-04 | Unit | `updateShouldModifyItemSetting` | 驗證修改費用項目 |
| C05-UNIT-05 | Unit | `updateShouldFailWhenNotFound` | 驗證修改不存在項目時失敗 |
| C05-UNIT-06 | Unit | `deleteShouldRemoveItemSetting` | 驗證刪除費用項目 |
| C05-UNIT-07 | Unit | `deleteShouldFailWhenNotFound` | 驗證刪除不存在項目時失敗 |
| C05-UNIT-08 | Unit | `C05_showPreview_shouldReturnCourseFeeSummary` | 驗證費用預覽依類型彙總 |
| C05-UNIT-09 | Unit | `listByCourseShouldReturnItems` | 驗證依課程查詢費用項目清單 |
| C05-API-01 | API | `fetchFeeItems` | 驗證 GET /api/v1/fee-items?courseId 呼叫 |
| C05-API-02 | API | `createFeeItem` | 驗證 POST /api/v1/fee-items 呼叫 |
| C05-API-03 | API | `updateFeeItem` | 驗證 PUT /api/v1/fee-items/:id 呼叫 |
| C05-API-04 | API | `deleteFeeItem` | 驗證 DELETE /api/v1/fee-items/:id 呼叫 |
| C05-API-05 | API | `fetchFeeItemPreview` | 驗證 GET /api/v1/fee-items/preview?courseId 呼叫 |

## Workflow

- 選擇區域 → 載入機構 → 選擇機構 → 載入課程 → 選擇課程 → 載入費用項目 → 新增/修改/刪除費用項目 → 顯示費用彙總預覽
