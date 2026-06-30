# C04 學員管理 TDD 設計

## 1. 對應需求

- Story：`C04-1`
- 對應 BDD：`bdd/c04-student-management.feature`
- 對應 SDD：`sdd/c04-student-management-sdd.md`

## 2. 測試分層

### Unit

- 課程切換重建繳費項目
- 日期、匯率、金額驗證正確

### Integration

- 學員基本資料保存成功
- 繳費資料與課程狀態寫入成功
- 學顧調整成功

### Workflow

- 查詢學員 → 編修基本資料 → 建立繳費 → 調整學顧

### UI Acceptance

- 子頁切換與 popup 操作正常

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| SM-UNIT-01 | Unit | courseChangeShouldRebuildPaymentItems | 驗證動態項目 |
| SM-UNIT-02 | Unit | invalidAmountShouldFail | 驗證金額格式 |
| SM-INT-01 | Integration | saveStudentProfileShouldPersist | 驗證基本資料保存 |
| SM-INT-02 | Integration | savePaymentShouldPersist | 驗證繳費保存 |
| SM-INT-03 | Integration | reassignAdvisorShouldPersist | 驗證學顧變更 |
| SM-WF-01 | Workflow | studentMaintenanceFlow | 驗證主要維護流程 |

## 4. Given / When / Then 範本

- Given：使用者已開啟學員子頁
- When：變更課程並建立繳費資料
- Then：系統重建付款項目並保存資料

## 5. 邊界案例

- 未選課程即建立繳費
- 無權限修改他區學員

## 6. 完成標準

- 子頁、popup、付款流程皆有測試
