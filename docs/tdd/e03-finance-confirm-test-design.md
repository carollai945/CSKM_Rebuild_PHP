# E03 財務確認 TDD 設計

## 1. 對應需求

- Story：`E03-1`
- 對應 BDD：`bdd/e03-finance-confirm.feature`
- 對應 SDD：`sdd/e03-finance-confirm-sdd.md`

## 2. 測試分層

### Unit

- 退回必填原因
- 退回 modal 開啟時需重置原因並帶入單號

### Integration

- 財務核准成功
- 財務退回成功
- 關聯簽呈狀態提示載入成功

### Workflow

- 清單查看 → 開啟退回視窗 → 填原因退回

### UI Acceptance

- 顯示核准/退回按鈕與關聯狀態提示

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| FC-UNIT-01 | Unit | returnReasonShouldBeRequired | 驗證退回原因 |
| FC-UNIT-02 | Unit | returnModalShouldResetFields | 驗證 modal 預設值 |
| FC-INT-01 | Integration | approveInvoiceShouldPersistResult | 驗證核准流程 |
| FC-INT-02 | Integration | returnInvoiceShouldPersistReason | 驗證退回流程 |
| FC-WF-01 | Workflow | returnWithModalFlow | 驗證退回視窗流程 |

## 4. Given / When / Then 範本

- Given：財務開啟某筆請款的退回視窗
- When：填入退回原因並送出
- Then：系統保存原因並更新清單狀態

## 5. 邊界案例

- 原因空白
- 非待處理狀態再次操作

## 6. 完成標準

- 核准、退回、modal 行為皆有測試
