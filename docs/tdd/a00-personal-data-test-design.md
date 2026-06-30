# A00 個人資料維護 TDD 設計

## 1. 對應需求

- Story：`A00-1`、`A00-2`
- 對應 BDD：`bdd/a00-personal-data.feature`
- 對應 SDD：`sdd/a00-personal-data-sdd.md`

## 2. 測試分層

### Unit

- 手機與體重欄位必須為數字
- 各欄位長度限制正確
- 唯讀模式應隱藏操作按鈕

### Integration

- 個資頁載入既有資料成功
- 儲存個資與照片成功
- 儲存失敗時顯示失敗訊息
- F02 帶入唯讀模式時不可儲存

### Workflow

- 開啟頁面 → 修改資料 → 儲存成功
- 按下同上 → 通訊地址自動帶入

### UI Acceptance

- 血型與性別下拉預設值正確
- 閱讀員工守則按鈕可開啟 PDF

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| PD-UNIT-01 | Unit | phoneAndWeightShouldBeNumeric | 驗證數字欄位 |
| PD-UNIT-02 | Unit | readonlyModeShouldHideButtons | 驗證唯讀模式 |
| PD-INT-01 | Integration | loadPersonalDataShouldRenderSavedValues | 驗證個資載入 |
| PD-INT-02 | Integration | savePersonalDataShouldPersistChanges | 驗證儲存 |
| PD-INT-03 | Integration | readonlyModeShouldRejectSaveAction | 驗證唯讀不得儲存 |
| PD-WF-01 | Workflow | copyAddressAndSaveFlow | 驗證帶入地址後儲存 |

## 4. Given / When / Then 範本

- Given：員工已開啟個人資料頁
- When：修改聯絡資料並按下儲存
- Then：系統保存個資並重新載入頁面

## 5. 邊界案例

- 非數字手機
- 超長文字
- 儲存失敗
- 非本人嘗試更新個資
- `mode=readonly` 仍送出更新請求

## 6. 完成標準

- 載入、驗證、儲存與唯讀模式皆有測試
