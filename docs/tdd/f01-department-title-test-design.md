# F01 部門與職稱管理 TDD 設計

## 1. 對應需求

- Story：`F01-1`
- 對應 BDD：`bdd/f01-department-title.feature`
- 對應 SDD：`sdd/f01-department-title-sdd.md`

## 2. 測試分層

### Unit

- 部門/職稱代碼唯一性驗證正確
- 必填欄位驗證正確
- 已被引用資料不得停用

### Integration

- 部門新增/修改成功
- 職稱新增/修改成功
- 非管理員不可操作
- 停用已被引用資料應被拒絕

### Workflow

- 查詢 → 編修 → 儲存
- F01 異動 → F02 可見最新部門與職稱

### UI Acceptance

- 顯示部門/職稱清單與狀態
- 權限不足時隱藏維護操作

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| DT-UNIT-01 | Unit | duplicateDepartmentCodeShouldFail | 驗證部門唯一性 |
| DT-UNIT-02 | Unit | duplicateTitleCodeShouldFail | 驗證職稱唯一性 |
| DT-UNIT-03 | Unit | missingRequiredFieldShouldFail | 驗證必填欄位 |
| DT-UNIT-04 | Unit | referencedRecordCannotBeDisabled | 驗證引用中資料不得停用 |
| DT-INT-01 | Integration | saveDepartmentShouldPersist | 驗證部門儲存 |
| DT-INT-02 | Integration | saveTitleShouldPersist | 驗證職稱儲存 |
| DT-INT-03 | Integration | nonAdminShouldBeRejected | 驗證權限限制 |
| DT-INT-04 | Integration | disableReferencedRecordShouldFail | 驗證引用檢查 |
| DT-WF-01 | Workflow | departmentTitleMaintainFlow | 驗證主檔維護流程 |
| DT-WF-02 | Workflow | maintainThenF02ShouldLoadLatestOptions | 驗證 F02 選單一致性 |

## 4. Given / When / Then 範本

### DT-INT-01

- Given：管理者進入部門編修頁
- When：輸入新部門並儲存
- Then：部門資料出現在列表

### DT-INT-04

- Given：目標部門或職稱已被現有人員資料引用
- When：管理者將其設為停用並儲存
- Then：系統拒絕儲存並回傳已被引用訊息

## 5. 邊界案例

- 重複代碼
- 缺少部門或職稱名稱
- 已被引用時停用
- 無權限操作

## 6. 完成標準

- 唯一性、必填、引用檢查皆有測試
- 部門與職稱兩類流程皆有測試
- 至少 1 條權限限制與 1 條 F02 一致性流程測試
