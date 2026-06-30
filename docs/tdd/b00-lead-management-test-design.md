# B00 電訪名單管理 TDD 設計

## 1. 對應需求

- Story：`B00-1`
- 對應 BDD：`bdd/b00-lead-management.feature`
- 對應 SDD：`sdd/b00-lead-management-sdd.md`

## 2. 測試分層

### Unit

- modal 開啟時應重置或帶入正確值
- 教育程度為其他時顯示自填欄位
- 只有主管或建立者可刪除

### Integration

- 初始查詢載入預設條件
- 建立/修改名單成功
- 批次派發與批次刪除成功

### Workflow

- 初始化查詢 → 新增名單 → 編修 → 派發
- 主管批次勾選 → 批次刪除

### UI Acceptance

- 每頁筆數切換觸發重新查詢
- 全選/全不選正確更新選取陣列

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| LM-UNIT-01 | Unit | modalShouldResetOnCreate | 驗證新增 modal 重置 |
| LM-UNIT-02 | Unit | otherEducationShouldShowCustomField | 驗證其他教育程度欄位 |
| LM-UNIT-03 | Unit | onlyOwnerOrManagerCanDelete | 驗證刪除權限 |
| LM-INT-01 | Integration | initShouldLoadDefaultFilters | 驗證預設查詢 |
| LM-INT-02 | Integration | batchDispatchShouldUpdateSelectedRows | 驗證批次派發 |
| LM-INT-03 | Integration | batchDeleteShouldRemoveSelectedRows | 驗證批次刪除 |
| LM-WF-01 | Workflow | createEditDispatchFlow | 驗證主要流程 |

## 4. Given / When / Then 範本

- Given：主管已勾選多筆名單
- When：按下批次派發並確認
- Then：所有選取資料更新負責人

## 5. 邊界案例

- 未勾選即批次派發
- 非主管刪除他人資料
- 必填欄位缺漏

## 6. 完成標準

- Vue 初始化、modal、批次動作皆有測試
