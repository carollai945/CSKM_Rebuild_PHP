# E02 請款清單 TDD 設計

## 1. 對應需求

- Story：`E02-1`
- 對應 BDD：`bdd/e02-invoice-list.feature`
- 對應 SDD：`sdd/e02-invoice-list-sdd.md`

## 2. 測試分層

### Unit

- 狀態欄位對應顯示文字正確

### Integration

- 請款列表查詢成功
- 明細頁載入附件與關聯資料成功

### Workflow

- 清單查詢 → 進入明細

### UI Acceptance

- 顯示單號、申請人、金額、狀態

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| IL-UNIT-01 | Unit | statusTextShouldMapCorrectly | 驗證狀態顯示 |
| IL-INT-01 | Integration | searchInvoiceListShouldReturnRows | 驗證清單查詢 |
| IL-INT-02 | Integration | detailPageShouldLoadRequestData | 驗證明細載入 |
| IL-WF-01 | Workflow | invoiceListToDetailFlow | 驗證導頁流程 |

## 4. Given / When / Then 範本

- Given：存在一筆請款資料
- When：財務在清單中點選明細
- Then：系統開啟對應請款明細

## 5. 邊界案例

- 無權限查看
- 查無資料

## 6. 完成標準

- 清單與明細導頁皆有測試
