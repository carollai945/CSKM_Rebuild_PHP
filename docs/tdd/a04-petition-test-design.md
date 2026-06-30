# A04 簽呈申請 / D01 審核流程 TDD 設計

## 1. 對應需求

- Story：`A04-1`、`A04-2`
- 對應 BDD：`bdd/a04-petition.feature`
- 對應 SDD：`sdd/a04-petition-sdd.md`

## 2. 測試分層

### Unit

- 簽呈草稿可建立
- Draft 才能送審
- Submitted 狀態不可再次送審
- 主管不可執行 CEO 專屬動作
- CEO 不可審核未經主管核准的單據

### Integration

- `POST /A/A04` 可建立或更新請假單
- `POST /A/A04/AjaxSearch` 可查詢歷史資料
- `POST /A/A04/AjaxDelete` 只能刪除草稿
- `POST /D/D010` 可依 Type 更新正確狀態
- `POST /D/D010/saveMultiReply` 可保存批次審核意見

### Workflow

- 建立草稿 → 送審 → 主管核准 → CEO 核准
- 建立草稿 → 送審 → 主管退回 → 申請人修改後重送
- 建立草稿 → 申請人刪除

### UI Acceptance

- A04 列表頁可查詢歷史簽呈
- A040 表單頁顯示單號與送審按鈕
- D010 依角色顯示不同審核按鈕

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| LV-UNIT-01 | Unit | draft_shouldBeCreatable | 驗證草稿建立 |
| LV-UNIT-02 | Unit | onlyDraft_shouldBeSubmittable | 驗證送審前置狀態 |
| LV-UNIT-03 | Unit | managerCannotUseCeoAction | 驗證角色限制 |
| LV-INT-01 | Integration | createPetition_shouldInsertPetition | 驗證資料寫入 |
| LV-INT-02 | Integration | searchPetition_shouldReturnMatchedRows | 驗證 Ajax 查詢 |
| LV-INT-03 | Integration | deleteDraft_shouldRemoveOnlyDraft | 驗證刪除限制 |
| LV-INT-04 | Integration | approveByManager_shouldMoveToState3 | 驗證主管核准 |
| LV-INT-05 | Integration | rejectByManager_shouldMoveToState4 | 驗證主管退回 |
| LV-INT-06 | Integration | approveByCeo_shouldMoveToState5 | 驗證 CEO 核准 |
| LV-WF-01 | Workflow | petitionApprovalHappyPath | 驗證完整流程 |
| LV-WF-02 | Workflow | petitionRejectedAndResubmitted | 驗證退回重送 |

## 4. Given / When / Then 範本

### LV-INT-04

- Given：建立一筆狀態為 2 的 petition
- And：建立具主管權限的登入 session
- When：送出 `POST /D/D010` 並帶 `Type=3`
- Then：petition 狀態更新為 3
- And：保存主管意見

### LV-INT-06

- Given：建立一筆狀態為 3 的 petition
- And：建立具 CEO 權限的登入 session
- When：送出 `POST /D/D010` 並帶 `Type=5`
- Then：petition 狀態更新為 5

## 5. 邊界案例

- 簽呈主旨或內容為空
- 附件缺失但被要求為必填
- 非本人嘗試編輯已送審單據
- 無意見即退回
- 同一單據被重複審核

## 6. 完成標準

- 所有狀態碼轉換都有測試
- 至少 1 條 happy path 與 1 條 rejected path workflow
- 列表查詢與刪除 Ajax 均有整合測試
