# 個人自助功能 TDD 設計

## 對應

- BDD：`bdd/group-personal-self-service.feature`
- SDD：`sdd/group-personal-self-service-sdd.md`

## 測試主題

- A00 載入個人資料
- A00 上傳照片
- A01 修改密碼成功 / 失敗
- A02 AjaxSearch / AjaxSearchDefault

## 代表案例

- `A00_get_shouldLoadOwnProfile`
- `A00_post_photoUpload_shouldUpdateImagePath`
- `A01_post_validOldPassword_shouldUpdatePassword`
- `A01_post_wrongOldPassword_shouldReject`
- `A02_ajaxSearch_shouldReturnReportRows`
- `A02_ajaxSearchDefault_shouldReturnDefaultRange`

## Workflow

- 進入 A00 → 更新照片 → 進入 A01 改密碼 → 重新登入驗證新密碼
