# F05 人員總覽 TDD 設計

## 1. 對應需求

- Story：`F05-1`
- 對應 BDD：`bdd/f05-staff-overview.feature`
- 對應 SDD：`sdd/f05-staff-overview-sdd.md`

## 2. 測試分層

### Unit

- 主管僅可看本區資料
- 在職狀態顯示規則正確

### Integration

- 人員清單查詢成功
- 權限摘要可正確載入

### Workflow

- 輸入條件 → 查詢人員 → 查看摘要

### UI Acceptance

- 顯示員工編號、姓名、區域、部門、狀態

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| SO-UNIT-01 | Unit | managerShouldSeeOwnRegionOnly | 驗證資料範圍 |
| SO-UNIT-02 | Unit | activeStatusShouldMapCorrectly | 驗證狀態文字 |
| SO-INT-01 | Integration | searchStaffOverviewShouldReturnRows | 驗證查詢 |
| SO-WF-01 | Workflow | staffOverviewQueryFlow | 驗證查詢流程 |

## 4. Given / When / Then 範本

- Given：主管登入系統
- When：進入人員總覽查詢
- Then：只顯示其轄區人員資料

## 5. 邊界案例

- 查無資料
- 無權限看全部人員

## 6. 完成標準

- 查詢、範圍、狀態顯示皆有測試
