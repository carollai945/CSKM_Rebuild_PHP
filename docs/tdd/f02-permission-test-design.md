# F02 人員帳號與權限管理 TDD 設計

## 1. 對應需求

- Story：`F02-2`、`F02-3`
- 對應 BDD：`bdd/f02-permission.feature`
- 對應 SDD：`sdd/f02-permission-sdd.md`

## 2. 測試分層

### Unit

- 員工編號不得重複
- 區域/部門/職稱為必填
- 非管理員不得修改權限
- 離職帳號不可再視為正常啟用

### Integration

- `POST /F/F020/save` 可新增員工
- `POST /F/F02/search` 可載入既有人員資料
- `POST /F/F02` 可更新角色與頁面權限
- `POST /F/F02/getdep` 可回傳部門清單
- `POST /F/F02/gettitle` 可回傳職稱清單
- `POST /F/F02/delete` 可刪除或停用帳號
- `POST /F/F02/reset` 可重設密碼

### Workflow

- 新增員工 → 設定權限 → 該員工登入驗證新權限
- 搜尋既有人員 → 修改頁面權限 → 下次登入生效
- 停用員工 → 該員工登入失敗

### UI Acceptance

- F020 頁面可依區域與部門動態帶出下拉選單
- F02 搜尋後權限 checkbox 正確回填
- 重設密碼與刪除按鈕顯示正確

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| HR-UNIT-01 | Unit | duplicateStaffNo_shouldFail | 驗證唯一鍵 |
| HR-UNIT-02 | Unit | nonAdmin_shouldNotUpdatePermission | 驗證權限限制 |
| HR-INT-01 | Integration | saveNewStaff_shouldInsertHrRecord | 驗證新增 |
| HR-INT-02 | Integration | searchStaff_shouldReturnPermissionState | 驗證搜尋 |
| HR-INT-03 | Integration | updatePermission_shouldPersistMenuSettings | 驗證更新權限 |
| HR-INT-04 | Integration | getdep_shouldReturnDepartmentsByRegion | 驗證部門 Ajax |
| HR-INT-05 | Integration | gettitle_shouldReturnTitlesByDepartment | 驗證職稱 Ajax |
| HR-INT-06 | Integration | deleteStaff_shouldDisableAccount | 驗證停用 |
| HR-INT-07 | Integration | resetPassword_shouldReplaceOldPassword | 驗證重設密碼 |
| HR-WF-01 | Workflow | createStaffThenLoginWithAssignedPermission | 驗證新帳號與權限 |
| HR-WF-02 | Workflow | disableStaffThenRejectLogin | 驗證停用後登入失敗 |

## 4. Given / When / Then 範本

### HR-INT-01

- Given：管理員已登入
- When：送出 `POST /F/F020/save` 並帶完整員工資料
- Then：`hr_management` 新增對應員工
- And：建立頁面與角色權限資料

### HR-INT-03

- Given：既有員工存在
- When：管理員送出 `POST /F/F02` 更新權限勾選
- Then：menu 或對應授權資料更新成功
- And：下次登入權限立即生效

## 5. 邊界案例

- 重複員工編號
- 區域切換後部門下拉未更新
- 部門與職稱不匹配
- 目標員工已離職仍被授權
- 重設密碼後舊密碼仍可登入

## 6. 完成標準

- CRUD / Ajax / reset / delete 全面覆蓋
- 至少 1 條新增人員流程與 1 條停用人員流程
- 權限更新需以登入驗證結果收斂
