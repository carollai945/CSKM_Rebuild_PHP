# CSKM GitHub x AI Agent 實作指引

> 目的：把 CSKM 的文件驅動重建方式，真正接到 GitHub 的 Issue、Branch、Pull Request 與自動檢查流程，讓 AI Agent 可以持續接手。

---

## 1. 這份文件解決什麼問題

若只有 BDD / SDD / TDD / API / Flow-State 文件，而沒有 GitHub 任務流程，AI Agent 很容易出現：

1. 任務範圍過大
2. 同一功能被不同 branch 重複實作
3. PR 沒寫清楚來源文件與完成條件
4. 下一個 Agent 不知道要從哪裡接手

因此 GitHub 必須作為 **任務中樞**，而文件則作為 **規格來源**。

---

## 2. 建議的 GitHub 工作流

```text
Story / BDD / Page Design / SDD / API / Flow-State / TDD
            ↓
        GitHub Issue
            ↓
          Branch
            ↓
       Pull Request
            ↓
     GitHub Actions 檢查
            ↓
          Merge
```

---

## 3. 本次已實作的 GitHub 骨架

以下檔案已加到 repo：

| 類型 | 路徑 | 用途 |
|---|---|---|
| Issue Form | `.github/ISSUE_TEMPLATE/ai-agent-task.yml` | 建立 Agent 任務單，固定收集 Story ID、Task ID、Scope、Source Documents、Done When |
| Issue Config | `.github/ISSUE_TEMPLATE/config.yml` | Issue Template 設定 |
| PR Template | `.github/pull_request_template.md` | 要求每個 PR 都寫清楚 Issue、Story、Task、文件來源、測試與交接資訊 |
| Workflow | `.github/workflows/agent-pr-check.yml` | 檢查 PR body 是否包含必要欄位 |

---

## 4. 建議的操作方式

### Step 1：先有規格

先確認功能切片已能追到：

- Story
- BDD
- Page Design
- SDD
- TDD
- API Design
- Flow-State

### Step 2：開 GitHub Issue

使用 `AI Agent Task` Issue Form 建立任務，填入：

- Story ID
- Task ID
- Goal
- In Scope
- Out of Scope
- Source Documents
- Done When

### Step 3：建立 branch

建議命名：

- `feature/A04-submit-form`
- `feature/D00-batch-approve`
- `feature/F02-save-permission`

一個 branch 只做一個任務切片。

### Step 4：交給 AI Agent 實作

將 Issue 內容直接作為 Agent prompt 主體，並補上：

- 目前 branch 名稱
- 必須更新的模組
- 是否需要同步更新文件

### Step 5：開 Pull Request

PR 必須依照 `.github/pull_request_template.md` 填寫，至少包含：

- Linked Issue
- Story ID
- Task ID
- Source Documents
- Scope / Out of Scope
- Done When
- Tests
- Notes for Next Agent

### Step 6：由 GitHub Actions 檢查

目前已實作的 workflow 會自動檢查：

1. PR 不可為空（0 changed files）
2. 不可將 `[WIP]`（非草稿）直接合併到 `main`
3. 先執行自動安全掃描（Trivy，阻擋 HIGH / CRITICAL 漏洞）
4. 非 `[WIP]` PR 在安全掃描通過後，是否至少有 1 個 approved code review，且不可有 `CHANGES_REQUESTED`（若倉庫無其他可用 reviewer，或尚無 reviewer 的核准/退回決策，則暫時放行並待 reviewer 決策後再檢查）
5. 非 `[WIP]` PR 的 PR body 是否含有必要章節
6. 非 `[WIP]` PR 的 `Source Documents` 內列出的路徑是否存在
7. 非 `docs:` 且非 `[WIP]` PR 是否有實際 `backend/` 或 `frontend/` 程式碼變更
8. 核心重建文件鏈是否齊備
9. 若已有 backend / frontend 實作骨架，則條件式執行對應測試或 build
10. Action 結束後自動更新 PR body 的 `CI Checklist（自動更新）`，同步每個 job 的結果

---

## 5. Issue 與 PR 的角色分工

| 項目 | 主要用途 |
|---|---|
| Issue | 定義任務範圍與完成條件 |
| Branch | 隔離單一切片的程式修改 |
| Pull Request | 提交交付成果與交接資訊 |
| Workflow | 自動檢查交付格式是否完整 |

---

## 6. 建議的 Label

建議在 GitHub 手動建立以下 label：

- `agent-ready`
- `frontend`
- `backend`
- `api`
- `flow`
- `docs`
- `blocked`
- `needs-spec`

其中：

- `agent-ready`：文件齊全，可直接交給 Agent
- `needs-spec`：還缺 BDD / API / Flow-State 等規格，不應直接開發
- `blocked`：等待決策或依賴

---

## 7. 最小可執行做法

若你要從零開始，先做這 5 步就夠：

1. 選一個最小功能切片
2. 用 Issue Form 建立一張 `AI Agent Task`
3. 建立對應 branch
4. 用 PR Template 開一張 PR
5. 讓 GitHub Actions 檢查 PR 是否寫齊

---

## 8. 推薦的第一個實戰任務

推薦先從以下任務之一開始：

1. `Login-1 使用者登入`
2. `A04-1 請假申請草稿儲存與送審`
3. `B00-1 電訪名單查詢`

這些功能範圍小、流程單純，適合先練 GitHub + AI Agent 協作方式。

---

## 9. 實務提醒

1. 不要一張 Issue 做整個模組
2. 不要讓 PR 同時混進多個 Story
3. Issue 與 PR 內一定要放 `Source Documents`
4. 若規格有變，就先改文件再開發
5. 下一位 Agent 是否能接手，取決於 PR 是否把上下文寫完整

---

## 10. 一句話結論

**文件是規格來源，GitHub 是任務中樞；把 Issue、Branch、PR、Workflow 固定化後，AI Agent 才能穩定地一棒接一棒持續開發。**
