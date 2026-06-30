# A02 個人報表入口 TDD 設計

## 1. 對應需求

- Story：`A02-1`
- 對應 BDD：`bdd/a02-personal-report.feature`
- 對應 SDD：`sdd/a02-personal-report-sdd.md`

## 2. 測試分層

### Unit

- 日期未完整輸入時不得查詢
- 報表類型與角色切換規則正確
- 狀態決定修改或檢視按鈕

### Integration

- `AjaxSearchDefault` 回傳最近五筆資料
- `AjaxSearch` 依日期與角色回傳正確資料
- 送出列表按鈕可帶報表編號進入明細

### Workflow

- 首次進入 → 預設查詢 → 顯示列表
- 選日期查詢 → 由列表進入修改或檢視

### UI Acceptance

- 日報區與週報區同步重建
- 行政與學顧資料顏色標示正確

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| PR-UNIT-01 | Unit | dateRangeShouldBeRequiredForSearch | 驗證查詢條件 |
| PR-UNIT-02 | Unit | actionButtonShouldMatchStatus | 驗證按鈕切換 |
| PR-INT-01 | Integration | defaultSearchShouldReturnRecentFiveRows | 驗證預設查詢 |
| PR-INT-02 | Integration | dateSearchShouldFilterRows | 驗證日期查詢 |
| PR-WF-01 | Workflow | searchAndOpenReportFlow | 驗證查詢到明細流程 |

## 4. Given / When / Then 範本

- Given：員工剛進入個人報表入口頁
- When：頁面完成載入
- Then：系統查詢最近五筆資料並重建日報與週報列表

## 5. 邊界案例

- 只填一個日期
- Ajax 查詢失敗
- 查無資料

## 6. 完成標準

- 預設查詢、日期查詢、列表導頁皆有測試
