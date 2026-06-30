# F00 區域管理 TDD 設計

## 1. 對應需求

- Story：`F00-1`
- 對應 BDD：`bdd/f00-area-management.feature`
- 對應 SDD：`sdd/f00-area-management-sdd.md`

## 2. 測試分層

### Unit

- 區域代碼唯一性驗證正確
- 必填欄位驗證正確

### Integration

- 區域新增成功
- 區域修改成功
- 非管理員不可操作

### Workflow

- 區域列表 → 編修 → 儲存

### UI Acceptance

- 顯示狀態與編修入口
- 權限不足時隱藏維護操作

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| RG-UNIT-01 | Unit | duplicateRegionCodeShouldFail | 驗證區域代碼唯一性 |
| RG-UNIT-02 | Unit | missingRequiredFieldShouldFail | 驗證必填欄位 |
| RG-INT-01 | Integration | createRegionShouldPersist | 驗證新增儲存 |
| RG-INT-02 | Integration | updateRegionShouldPersist | 驗證修改儲存 |
| RG-INT-03 | Integration | nonAdminShouldBeRejected | 驗證權限限制 |
| RG-WF-01 | Workflow | regionMaintainFlow | 驗證操作流程 |

## 4. Given / When / Then 範本

### RG-INT-01

- Given：管理員進入區域編修頁
- When：輸入新區域資料並儲存
- Then：區域出現在列表中

### RG-INT-03

- Given：一般員工嘗試進入區域管理頁
- When：使用者送出新增或修改
- Then：系統拒絕操作並回傳權限不足

## 5. 邊界案例

- 重複代碼
- 缺少區域名稱
- 停用區域後仍被選入維護範圍
- 無權限操作

## 6. 完成標準

- 唯一性與必填驗證皆有測試
- 新增、修改與權限限制流程皆有測試
