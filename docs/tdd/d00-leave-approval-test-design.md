# D00 請假審核 TDD 設計

## 1. 對應需求

- Story：`D00-1`
- 對應 BDD：`bdd/d00-leave-approval.feature`
- 對應 SDD：`sdd/d00-leave-approval-sdd.md`

## 2. 測試分層

### Unit

- 主管不得執行 CEO 動作
- 退回必填意見
- 主管與 CEO 勾選集合不可混用

### Integration

- 單筆核准/退回成功
- `saveMultiReply` 可批次更新
- 失敗單號清單回傳正確

### Workflow

- 主管批次回覆多筆資料
- CEO 單筆核准最終完成

### UI Acceptance

- 全選/全不選按鈕正確
- 角色切換時僅顯示可用批次區

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| LA-UNIT-01 | Unit | managerCannotUseCeoBatchReply | 驗證角色限制 |
| LA-UNIT-02 | Unit | rejectShouldRequireComment | 驗證退回意見 |
| LA-INT-01 | Integration | approveSingleLeaveShouldUpdateState | 驗證單筆審核 |
| LA-INT-02 | Integration | batchReplyShouldUpdateSelectedRows | 驗證批次回覆 |
| LA-INT-03 | Integration | batchReplyShouldReturnFailedNumbers | 驗證失敗清單 |
| LA-WF-01 | Workflow | managerBatchReplyFlow | 驗證主管批次流程 |

## 4. Given / When / Then 範本

- Given：主管待審清單中有多筆未回覆請假單
- When：主管勾選多筆資料並送出批次回覆
- Then：系統更新勾選資料並回傳成功或失敗結果

## 5. 邊界案例

- 未勾選資料
- 狀態已被他人更新
- CEO 嘗試處理未經主管核准資料

## 6. 完成標準

- 單筆、批次、失敗回饋皆有測試
