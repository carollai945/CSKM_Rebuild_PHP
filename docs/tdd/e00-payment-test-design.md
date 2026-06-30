# E00 繳費查詢與雙重確認 TDD 設計

## 1. 對應需求

- Story：`E00-1`、`E00-2`
- 對應 BDD：`bdd/e00-payment.feature`
- 對應 SDD：`sdd/e00-payment-sdd.md`

## 2. 測試分層

### Unit

- 金額、幣別、日期必填驗證
- `pay_fin` / `pay_aca` 完成條件判斷
- 已完成資料不可重複確認
- 退回必須填寫原因

### Integration

- `POST /E/E00/AjaxSearch` 可依條件回傳結果
- `POST /E/E00` 可新增一筆 `payment_info`
- `POST /E/E00/Finance` 更新 `pay_fin`
- `POST /E/E00/Academic` 更新 `pay_aca`
- `POST /E/E00/Reject` 保存退回原因

### Workflow

- 建立繳費 → 財務確認 → 教務確認 → 完成
- 建立繳費 → 退回 → 修正後再確認

### UI Acceptance

- 搜尋條件欄位與結果表格可正常互動
- 按鈕依角色顯示正確
- 確認後畫面即時反映狀態

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| PM-UNIT-01 | Unit | requiredFields_shouldBeValidated | 驗證欄位 |
| PM-UNIT-02 | Unit | duplicateConfirmation_shouldBeRejected | 驗證重複確認限制 |
| PM-UNIT-03 | Unit | rejectWithoutReason_shouldFail | 驗證退回必填 |
| PM-INT-01 | Integration | ajaxSearch_shouldReturnMatchedPayments | 驗證查詢 |
| PM-INT-02 | Integration | createPayment_shouldInsertPaymentInfo | 驗證建立 |
| PM-INT-03 | Integration | financeConfirm_shouldUpdatePayFin | 驗證財務確認 |
| PM-INT-04 | Integration | academicConfirm_shouldUpdatePayAca | 驗證教務確認 |
| PM-INT-05 | Integration | rejectPayment_shouldSaveReason | 驗證退回 |
| PM-WF-01 | Workflow | paymentDoubleConfirmationHappyPath | 驗證雙重確認流程 |
| PM-WF-02 | Workflow | paymentRejectedThenReprocessed | 驗證退回重處理 |

## 4. Given / When / Then 範本

### PM-INT-03

- Given：建立一筆未完成確認的 `payment_info`
- And：登入 session 為財務角色
- When：送出 `POST /E/E00/Finance`
- Then：`pay_fin=1`
- And：`pay_aca` 保持原值

### PM-INT-05

- Given：建立一筆待確認 `payment_info`
- When：送出 `POST /E/E00/Reject` 與原因
- Then：保存退回原因
- And：該筆資料不可視為完成

## 5. 邊界案例

- 金額為 0 或負值
- 幣別未設定
- 同一角色重複確認
- 未授權角色呼叫確認 API
- 查詢條件全部空白時的回傳規則

## 6. 完成標準

- 查詢 / 建立 / 財務確認 / 教務確認 / 退回 皆有整合測試
- 至少覆蓋 1 條完成流程與 1 條退回流程
