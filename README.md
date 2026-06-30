# CSKM_Rebuild_PHP

CSKM 重建專案，目標技術為 **PHP 8.3 + Laravel 11 + Vue 3**，並採用文件驅動與 AI Agent 持續開發模式。

## Repository Structure

- `docs/`：重建規格、BDD、Page Design、SDD、TDD、API、Data Model、Flow-State、GitHub / AI Agent 指引
- `backend/`：後端服務實作區（PHP 8.3 + Laravel 11）
- `frontend/`：前端應用實作區（Vue 3 + TypeScript）
- `.github/`：Issue Form、PR Template、Workflow

## Working Model

1. 先以 `docs/` 中的規格定義功能
2. 每個功能切片使用一張 GitHub Issue
3. 每個切片透過單一 branch 與 PR 交付
4. 持續依照 `docs/CSKM_AI_Agent_PHP_Development_Guide.md` 與 `docs/CSKM_GitHub_AI_Agent_Workflow.md` 開發

## GitHub Actions

目前 PR 驗證流程會自動執行：

1. 檢查 PR 不可為空（0 changed files）且不可將 `[WIP]`（非草稿）直接合併到 `main`
2. 先執行自動安全掃描（Trivy，針對 HIGH / CRITICAL 漏洞）
3. 非 `[WIP]` PR 在安全掃描通過後，檢查至少有 1 個 approved code review（且無 `CHANGES_REQUESTED`）；若無其他可用 reviewer（單人維護），則自動放行
4. 非 `[WIP]` PR 會檢查 PR 必填章節
5. 非 `[WIP]` PR 會檢查核心文件鏈與 `Source Documents` 路徑是否存在
6. 非 `docs:` 且非 `[WIP]` PR，必須包含 `backend/` 或 `frontend/` 程式碼變更
7. 若 `backend/` 已加入 `composer.json`，則自動執行 `composer install && php artisan test`
8. 若 `frontend/` 已加入 `package.json`，則自動執行前端 test / build
9. Action 結束後會自動回寫 PR 內的 `CI Checklist（自動更新）` 區塊
