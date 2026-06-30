# F04 電訪名單匯入 TDD 設計

## 1. 對應需求

- Story：`F04-1`
- 對應 BDD：`bdd/f04-import-leads.feature`
- 對應 SDD：`sdd/f04-import-leads-sdd.md`

## 2. 測試分層

### Unit

- Excel 欄位格式驗證正確
- 缺漏欄位時可產出錯誤原因

### Integration

- 檔案上傳成功後寫入名單
- 部分失敗時可回傳逐列錯誤

### Workflow

- 上傳 Excel → 顯示成功/失敗筆數

### UI Acceptance

- 頁面顯示匯入格式說明與結果摘要

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| IM-UNIT-01 | Unit | excelValidatorShouldCheckRequiredColumns | 驗證欄位規則 |
| IM-INT-01 | Integration | importValidFileShouldCreateLeads | 驗證成功匯入 |
| IM-INT-02 | Integration | importInvalidRowsShouldReturnErrors | 驗證失敗回饋 |
| IM-WF-01 | Workflow | excelImportFlow | 驗證完整匯入流程 |

## 4. Given / When / Then 範本

- Given：使用者選擇一份合法 Excel 檔
- When：送出匯入
- Then：系統建立名單並顯示結果摘要

## 5. 邊界案例

- 檔案格式錯誤
- 空檔案
- 無權限上傳

## 6. 完成標準

- 上傳、驗證、錯誤回饋皆有測試
