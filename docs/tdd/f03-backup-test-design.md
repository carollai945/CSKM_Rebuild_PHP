# F03 資料庫備份 TDD 設計

## 1. 對應需求

- Story：`F03-1`
- 對應 BDD：`bdd/f03-backup.feature`
- 對應 SDD：`sdd/f03-backup-sdd.md`

## 2. 測試分層

### Unit

- 僅管理者可執行備份

### Integration

- 備份成功時寫入 log
- 備份失敗時回傳錯誤訊息

### Workflow

- 點擊備份 → 顯示結果 → 更新紀錄列表

### UI Acceptance

- 顯示最近備份結果

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| BK-UNIT-01 | Unit | onlyAdminCanBackup | 驗證權限 |
| BK-INT-01 | Integration | backupSuccessShouldCreateLog | 驗證成功紀錄 |
| BK-INT-02 | Integration | backupFailureShouldReturnMessage | 驗證失敗訊息 |
| BK-WF-01 | Workflow | backupExecutionFlow | 驗證備份流程 |

## 4. Given / When / Then 範本

- Given：管理者位於備份頁
- When：按下備份按鈕
- Then：系統顯示成功或失敗結果並記錄 log

## 5. 邊界案例

- 備份指令失敗
- 非管理者操作

## 6. 完成標準

- 權限與結果回饋皆有測試
