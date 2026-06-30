# A05 請款申請 TDD 設計

## 1. 對應需求

- Story：`A05-1`
- 對應 BDD：`bdd/a05-invoice-request.feature`
- 對應 SDD：`sdd/a05-invoice-request-sdd.md`

## 2. 測試分層

### Unit

- 金額格式需合法
- 必填附件規則正確
- 僅草稿或退回單可再編修

### Integration

- 請款單建立/更新成功
- 請款列表查詢成功
- 退回後可重送

### Workflow

- 建立草稿 → 送審 → 財務退回 → 修正重送

### UI Acceptance

- A05 顯示歷史清單與狀態
- A050 顯示金額、附件與送審按鈕

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| IR-UNIT-01 | Unit | invalidAmountShouldFail | 驗證金額格式 |
| IR-UNIT-02 | Unit | returnedFormShouldBeEditable | 驗證退回重編 |
| IR-INT-01 | Integration | createInvoiceRequestShouldPersist | 驗證請款建立 |
| IR-INT-02 | Integration | resubmitReturnedRequestShouldSucceed | 驗證退回重送 |
| IR-WF-01 | Workflow | returnedAndResubmittedFlow | 驗證完整重送流程 |

## 4. Given / When / Then 範本

- Given：存在一筆被退回的請款單
- When：申請人補件後重新送審
- Then：狀態回到待財務處理

## 5. 邊界案例

- 金額為 0
- 缺少附件
- 非本人修改

## 6. 完成標準

- 建立、退回、重送都有測試
