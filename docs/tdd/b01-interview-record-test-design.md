# B01 電訪紀錄 TDD 設計

## 1. 對應需求

- Story：`B01-1`
- 對應 BDD：`bdd/b01-interview-record.feature`
- 對應 SDD：`sdd/b01-interview-record-sdd.md`

## 2. 測試分層

### Unit

- 聯絡結果為必填
- 下次追蹤時間規則正確

### Integration

- 電訪紀錄建立/更新成功
- 查詢只回傳可見範圍資料

### Workflow

- 建立電訪紀錄 → 設定下次追蹤 → 清單回顯

### UI Acceptance

- B01 列表顯示結果與下次追蹤時間

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| IV-UNIT-01 | Unit | contactResultShouldBeRequired | 驗證必填欄位 |
| IV-INT-01 | Integration | saveInterviewRecordShouldPersist | 驗證紀錄寫入 |
| IV-INT-02 | Integration | searchShouldRespectRoleScope | 驗證範圍限制 |
| IV-WF-01 | Workflow | createFollowupFlow | 驗證追蹤流程 |

## 4. Given / When / Then 範本

- Given：存在一筆待追蹤名單
- When：業務填入聯絡結果與下次時間
- Then：系統保存電訪紀錄並更新列表

## 5. 邊界案例

- 下次追蹤時間早於目前時間
- 非負責人修改紀錄

## 6. 完成標準

- 查詢、寫入、權限皆有測試
