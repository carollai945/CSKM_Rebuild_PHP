# B02 學員服務管理 TDD 設計

## 1. 對應需求

- Story：`B02-1`
- 對應 BDD：`bdd/b02-student-service.feature`
- 對應 SDD：`sdd/b02-student-service-sdd.md`

## 2. 測試分層

### Unit

- 課程切換時重建繳費項目
- 金額與備註驗證正確
- 未選項目不得送出

### Integration

- 服務紀錄保存成功
- 繳費資料建立成功
- 角色範圍限制正確

### Workflow

- 選課 → 重建項目 → 建立繳費資料

### UI Acceptance

- 全選/全不選正確作用於付款項目

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| SS-UNIT-01 | Unit | courseChangeShouldRebuildItems | 驗證動態項目 |
| SS-UNIT-02 | Unit | noPaymentItemShouldFail | 驗證必選項目 |
| SS-INT-01 | Integration | createServiceRecordShouldPersist | 驗證服務紀錄 |
| SS-INT-02 | Integration | createPaymentShouldPersist | 驗證繳費資料 |
| SS-WF-01 | Workflow | paymentItemFlow | 驗證課程到繳費流程 |

## 4. Given / When / Then 範本

- Given：使用者已選擇學員與課程
- When：勾選付款項目並送出
- Then：系統保存對應的繳費資料

## 5. 邊界案例

- 金額格式錯誤
- 未選課程
- 無權限修改他區資料

## 6. 完成標準

- 動態項目與繳費流程皆有測試
