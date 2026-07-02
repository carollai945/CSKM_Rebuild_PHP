# C01 師資管理 TDD 設計

## 1. 對應需求

- Story：`C01-1`, `C01-2`
- 對應 BDD：`bdd/c01-professor.feature`
- 對應 SDD：`sdd/c01-professor-sdd.md`

## 2. 測試分層

### Integration（ProfessorControllerTest）

- 已登入用戶可查詢師資清單
- `admin` 可新增師資
- 非 `admin/ceo` 不可新增師資（回傳 403）
- 新增師資成功回傳 201
- 修改師資成功回傳 200
- 修改不存在師資回傳 404
- 刪除師資成功回傳 204
- 上傳師資照片成功更新 `photo_path`
- 未登入查詢回傳 401

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| C01-INT-01 | Integration | `authenticated_user_can_list_professors` | 查詢師資清單 |
| C01-INT-02 | Integration | `admin_can_create_professor` | 管理員新增師資 |
| C01-INT-03 | Integration | `non_admin_cannot_create_professor` | 非管理員不可新增 |
| C01-INT-04 | Integration | `admin_can_update_professor` | 管理員修改師資 |
| C01-INT-05 | Integration | `update_nonexistent_professor_returns_404` | 修改不存在師資 |
| C01-INT-06 | Integration | `admin_can_delete_professor` | 管理員刪除師資 |
| C01-INT-07 | Integration | `unauthenticated_request_returns_401` | 未登入存取 |
