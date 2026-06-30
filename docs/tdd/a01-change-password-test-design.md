# A01 修改密碼 TDD 設計

## 1. 對應需求

- Story：`A01-1`
- 對應 BDD：`bdd/a01-change-password.feature`
- 對應 SDD：`sdd/a01-change-password-sdd.md`

## 2. 測試分層

### Unit

- 舊密碼驗證規則正確
- 兩次新密碼必須一致

### Integration

- 正確舊密碼可成功更新
- 錯誤舊密碼不得更新
- 不一致新密碼不得更新

### Workflow

- 輸入舊密碼與新密碼 → 送出修改

### UI Acceptance

- 錯誤訊息顯示於表單下方

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| CP-UNIT-01 | Unit | oldPasswordShouldBeVerified | 驗證舊密碼 |
| CP-UNIT-02 | Unit | newPasswordTwiceShouldMatch | 驗證兩次新密碼 |
| CP-INT-01 | Integration | correctOldPasswordShouldUpdatePassword | 驗證正常修改 |
| CP-INT-02 | Integration | wrongOldPasswordShouldFail | 驗證舊密碼錯誤 |
| CP-WF-01 | Workflow | passwordChangeFlow | 驗證修改流程 |

## 4. Given / When / Then 範本

- Given：員工已登入並進入修改密碼頁
- When：輸入正確舊密碼與一致的新密碼後送出
- Then：系統更新密碼

## 5. 邊界案例

- 舊密碼錯誤
- 兩次新密碼不同

## 6. 完成標準

- 成功與失敗流程皆有測試
