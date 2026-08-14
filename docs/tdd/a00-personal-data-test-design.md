# A00 個人資料維護 TDD 設計

## 1. 對應需求

- Story：`A00-1`、`A00-2`
- 對應 BDD：`bdd/a00-personal-data.feature`
- 對應 SDD：`sdd/a00-personal-data-sdd.md`

## 2. 測試分層

### Unit

- 唯讀模式應隱藏操作按鈕並停用欄位
- `同上` 啟用後應同步戶籍地址到通訊地址
- 個資頁應根據路由切換本人 / 他人唯讀載入

### Integration

- 個資頁載入既有資料成功
- 顯示 `join_date`、守則連結與既有照片成功
- 儲存基本資料與巢狀 JSON 區塊成功
- 巢狀欄位驗證失敗時回傳錯誤
- 儲存照片成功
- F02 帶入唯讀模式時不可儲存

### Workflow

- 開啟頁面 → 修改地址 / 證照 / 家庭資料 → 儲存成功
- F02 人員管理 → 開啟個資唯讀頁 → 返回人員管理
- 點擊 `閱讀員工守則` 可開啟 `staff.pdf`

### UI Acceptance

- 血型與性別下拉預設值正確
- 技能欄位可輸入並回填逗號分隔資料
- 唯讀模式不顯示照片、增刪列與儲存操作

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| PD-UNIT-01 | Unit | readonlyModeShouldHideActions | 驗證唯讀模式 |
| PD-UNIT-02 | Unit | sameAddressShouldCopyRegisteredAddress | 驗證地址同步 |
| PD-UNIT-03 | Unit | routeShouldSelectReadonlyApi | 驗證路由切換 |
| PD-INT-01 | Integration | loadPersonalDataShouldRenderSavedValues | 驗證完整個資載入 |
| PD-INT-02 | Integration | saveNestedPersonalDataShouldPersistChanges | 驗證 JSON 區塊儲存 |
| PD-INT-03 | Integration | invalidNestedDataShouldReturnValidationErrors | 驗證巢狀驗證 |
| PD-INT-04 | Integration | readonlyModeShouldRejectSaveAction | 驗證唯讀不得儲存 |
| PD-WF-01 | Workflow | openReadonlyProfileFromStaffManage | 驗證 F02 帶入唯讀頁 |
| PD-WF-02 | Workflow | openEmployeeHandbookFromPersonalData | 驗證守則連結 |

## 4. Given / When / Then 範本

- Given：員工已開啟個人資料頁
- When：修改地址、證照或家庭資料並按下儲存
- Then：系統保存個資並重新載入頁面

## 5. 邊界案例

- 無照片時顯示預設頭像
- 地址同步後再次修改戶籍地址
- 空白明細列不應造成錯誤資料落地
- 儲存失敗
- 非本人嘗試更新個資
- 非管理者嘗試檢視他人個資
- 唯讀模式仍送出更新請求

## 6. 完成標準

- 載入、地址同步、巢狀資料儲存 / 驗證、守則連結與唯讀模式皆有測試
