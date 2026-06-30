# D02 公告審核 TDD 設計

## 1. 對應需求

- Story：`D02-1`
- 對應 BDD：`bdd/d02-announcement-approval.feature`
- 對應 SDD：`sdd/d02-announcement-approval-sdd.md`

## 2. 測試分層

### Unit

- 退回必填意見
- 主管不得執行 CEO 審核

### Integration

- 主管核准/退回成功
- CEO 核准/退回成功

### Workflow

- 主管核准 → CEO 核准

### UI Acceptance

- 依角色顯示不同按鈕

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| AA-UNIT-01 | Unit | rejectShouldRequireComment | 驗證退回意見 |
| AA-INT-01 | Integration | managerApproveShouldUpdateState | 驗證主管核准 |
| AA-INT-02 | Integration | ceoApproveShouldUpdateState | 驗證 CEO 核准 |
| AA-WF-01 | Workflow | announcementApprovalHappyPath | 驗證完整流程 |

## 4. Given / When / Then 範本

- Given：存在一筆待主管審核公告
- When：主管填寫意見並核准
- Then：狀態流向 CEO 待審

## 5. 邊界案例

- 無權限進入
- 已審核再次送出

## 6. 完成標準

- 主管與 CEO 流程皆有測試
