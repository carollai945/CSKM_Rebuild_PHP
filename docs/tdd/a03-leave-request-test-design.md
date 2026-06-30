# A03 請假申請 TDD 設計

## 1. 對應需求

- Story：`A03-1`、`A03-2`
- 對應 BDD：`bdd/a03-leave-request.feature`
- 對應 SDD：`sdd/a03-leave-request-sdd.md`

## 2. 測試分層

### Unit

- Draft 狀態才可送審
- 日期區間需合法
- 僅本人可刪除草稿

### Integration

- 請假單建立/更新成功
- `AjaxSearch` 回傳本人歷史資料
- `AjaxDelete` 僅刪除可刪單據

### Workflow

- 建立草稿 → 送審成功
- 建立草稿 → 刪除

### UI Acceptance

- A03 列表顯示狀態與單號
- A030 顯示日期、假別、事由與送審按鈕

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| LR-UNIT-01 | Unit | onlyDraftShouldSubmit | 驗證送審前置狀態 |
| LR-UNIT-02 | Unit | invalidDateRangeShouldFail | 驗證日期區間 |
| LR-INT-01 | Integration | createLeaveShouldPersist | 驗證請假建立 |
| LR-INT-02 | Integration | searchShouldReturnOwnRows | 驗證本人資料隔離 |
| LR-INT-03 | Integration | deleteShouldRemoveDraftOnly | 驗證刪除限制 |
| LR-WF-01 | Workflow | submitLeaveHappyPath | 驗證標準送審流程 |

## 4. Given / When / Then 範本

- Given：建立一筆本人 Draft 請假單
- When：送出送審請求
- Then：狀態更新為 Submitted

## 5. 邊界案例

- 事由空白
- 日期起迄顛倒
- 非本人刪除

## 6. 完成標準

- 草稿、送審、刪除三類流程皆有測試
