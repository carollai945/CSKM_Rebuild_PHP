# C00 課程與主資料設定 TDD 設計

## 1. 對應需求

- Story：`C00-1`
- 對應 BDD：`bdd/c00-course-setup.feature`
- 對應 SDD：`sdd/c00-course-setup-sdd.md`

## 2. 測試分層

### Unit

- 區域/機構/課程連動規則正確
- 課程代碼不可重複

### Integration

- 課程新增/修改成功
- 查詢條件可正確過濾資料

### Workflow

- 選區域 → 選機構 → 新增課程 → 返回列表

### UI Acceptance

- 連動下拉刷新正確

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| CS-UNIT-01 | Unit | dropdownChainShouldRefresh | 驗證連動下拉 |
| CS-UNIT-02 | Unit | duplicateCourseCodeShouldFail | 驗證唯一性 |
| CS-INT-01 | Integration | createCourseShouldPersist | 驗證課程建立 |
| CS-WF-01 | Workflow | createCourseFlow | 驗證建立流程 |

## 4. Given / When / Then 範本

- Given：使用者已選定區域與機構
- When：新增課程並儲存
- Then：課程出現在查詢列表

## 5. 邊界案例

- 缺少主資料
- 重複代碼

## 6. 完成標準

- 連動與建立流程皆有測試
