# C046 學員明細 TDD 設計

## 1. 對應需求

- Story：`C04-2`
- 對應 BDD：`bdd/c046-student-detail.feature`
- 對應 SDD：`sdd/c046-student-detail-sdd.md`

## 2. 測試分層

### Unit

- 明細頁資料整併規則正確
- 無權限時不得顯示個資

### Integration

- 明細頁載入學員摘要成功
- 子頁跳轉參數正確

### Workflow

- 由列表進入明細 → 再進入子頁

### UI Acceptance

- 顯示基本資料、課程與繳費摘要

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| SD-UNIT-01 | Unit | detailAggregatorShouldMergeData | 驗證資料整併 |
| SD-INT-01 | Integration | detailPageShouldLoadSummary | 驗證頁面載入 |
| SD-WF-01 | Workflow | detailToChildPageFlow | 驗證跳轉流程 |

## 4. Given / When / Then 範本

- Given：存在一筆可見範圍內的學員
- When：開啟明細頁
- Then：顯示學員摘要與子頁入口

## 5. 邊界案例

- 查無學員
- 超出權限範圍

## 6. 完成標準

- 整併顯示與跳轉流程皆有測試
