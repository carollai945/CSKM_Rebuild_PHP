# A06 公告管理 TDD 設計

## 1. 對應需求

- Story：`A06-1`、`A06-2`
- 對應 BDD：`bdd/a06-announcement.feature`
- 對應 SDD：`sdd/a06-announcement-sdd.md`

## 2. 測試分層

### Unit

- 引用簽呈僅接受本人已核准資料
- 引用模式下主旨/內容應鎖定
- 主旨與內容為必填

### Integration

- 公告建立/更新成功
- 引用簽呈後帶入內容成功
- 送審資料寫入正確狀態

### Workflow

- 一般公告建立 → 送審
- 引用簽呈 → 內容鎖定 → 確認送出

### UI Acceptance

- A06 清單顯示狀態
- A060 顯示引用簽呈入口與確認動作

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| AN-UNIT-01 | Unit | onlyApprovedOwnPetitionCanBeQuoted | 驗證引用來源 |
| AN-UNIT-02 | Unit | quotedFieldsShouldBeReadonly | 驗證欄位鎖定 |
| AN-INT-01 | Integration | createAnnouncementShouldPersist | 驗證公告建立 |
| AN-INT-02 | Integration | quotePetitionShouldPrefillContent | 驗證引用帶值 |
| AN-WF-01 | Workflow | quotedAnnouncementSubmitFlow | 驗證引用公告送出 |

## 4. Given / When / Then 範本

- Given：使用者有一筆本人已核准簽呈
- When：選擇引用並送出公告
- Then：畫面鎖定引用內容並建立待審公告

## 5. 邊界案例

- 引用他人簽呈
- 內容空白
- 已送審後再次修改

## 6. 完成標準

- 一般模式與引用模式皆有測試
