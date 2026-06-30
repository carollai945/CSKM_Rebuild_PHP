# CSKM User Story 覆蓋矩陣

> 用途：核對 `CSKM_UserStories.md` 與目前已建立的 BDD / SDD / TDD 子文件，標示已覆蓋情況，以及目前仍需持續細化的實作對齊項目。

---

## 1. 核對結論

### 覆蓋統計

- `CSKM_UserStories.md` 共 **46** 條 Story
- 目前已建立 **33 份 BDD / 26 份 Page Design / 33 份 SDD / 33 份 TDD / 5 份系統級重建與 AI Agent 規格文件** 文件
- 文件結構已統一為 **7 份 `group-*` 群組總覽 + 26 份功能編號頁面文件**
- 以「單一文件或群組文件對應 Story」方式計算，**46 / 46 條 Story 已有對應設計文件**
- 目前主要工作已從「補文件」轉為 **持續細化頁面子功能、子頁差異與更深的前端驗收細節**

### 先前已覆蓋

- Login-1 ~ Login-3
- A04-1 ~ A04-2（請假申請）
- E00-1 ~ E00-2（繳費記錄）
- F02-2 ~ F02-3（新增員工 / 修改權限）

### 本次補齊

- A00、A01、A02 個人自助流程
- A03、A05、A06 與其後續批核流程
- B 模組完整業務 / 招生 / 學員追蹤流程
- C 模組主資料、師資、學生、教室、費用設定
- E01、E02、E03 財務報表與請款簽核
- F00、F01、F02-1、F02-4、F03、F04、F05 系統管理流程

### 目前仍建議持續細化的項目

1. **A02 / D03 報表流程已改正名稱，但仍可再細拆 `A020~A027` 與 `D030~D033` 的頁面差異。**
2. **B02 與 C04 相關子頁雖已開始頁面級細切，但服務記錄、繳費記錄、課程狀態、popup 變更學顧仍可再獨立成檔。**
3. **E00 的財務 / 教務雙重確認細節目前已在前端補充說明，若要做更完整驗收可再升級為獨立故事。**

### 本次 BDD 細切

- `bdd/a00-personal-data.feature`
- `bdd/a01-change-password.feature`
- `bdd/a02-personal-report.feature`
- `bdd/a03-leave-request.feature`
- `bdd/a05-invoice-request.feature`
- `bdd/a06-announcement.feature`
- `bdd/d00-leave-approval.feature`
- `bdd/d02-announcement-approval.feature`
- `bdd/d03-report-approval.feature`
- `bdd/b00-lead-management.feature`
- `bdd/b01-interview-record.feature`
- `bdd/b02-student-service.feature`
- `bdd/c00-course-setup.feature`
- `bdd/c04-student-management.feature`
- `bdd/c046-student-detail.feature`
- `bdd/e01-income-report.feature`
- `bdd/e02-invoice-list.feature`
- `bdd/e03-finance-confirm.feature`
- `bdd/f00-region-management.feature`
- `bdd/f01-department-title.feature`
- `bdd/f03-backup.feature`
- `bdd/f04-import-leads.feature`
- `bdd/f05-staff-overview.feature`

### 本次 BDD 重構方向

- 由「單頁單流程」補強為 **單頁多角色情境**
- 每個重要頁面補入 **按鈕可見性、可操作動作、資料範圍、無權限情境**
- 群組型總覽 BDD 一律改用 `group-*` 命名，與功能編號型 `.feature` 明確區分
- 優先重構檔案：
  - `bdd/b00-lead-management.feature`
  - `bdd/b02-student-service.feature`
  - `bdd/d00-leave-approval.feature`
  - `bdd/d02-announcement-approval.feature`
  - `bdd/d03-report-approval.feature`
  - `bdd/e03-finance-confirm.feature`
  - `bdd/f02-permission.feature`

### 本次 SDD / TDD 對齊結果

- 群組型 SDD / TDD 已改為 `group-*` 命名，與群組型 BDD 一致
- 頁面級 BDD 均已建立同名頁面級 SDD / TDD
- A00、A01、A02 已由群組型 BDD 補拆為頁面級文件，結構與其他 A 模組頁面一致
- 關鍵高互動頁面如 `b00`、`d00`、`e03`、`f02` 已在 SDD / TDD 中補入 Vue、批次、modal 與動態欄位行為

### 本次新增頁面設計文件

- 已為所有頁面級功能新增 `page-design/*.md`
- 命名規則為 `<功能編號>-<功能名稱>-page-design.md`
- 內容固定包含：版面區塊、欄位規則、按鈕動作、互動規則、訊息設計、權限與狀態
- 已重新校正高互動頁面的版面區塊，使其對齊實際 JSP 的表格、caption、按鈕列與 modal 結構
- 頁面設計文件已補充 JavaScript / Vue 動態渲染內容，包含 Ajax 結果列、Vue `v-for` 清單、autocomplete 回填與條件顯示區塊

### 本次新增重建規格文件

- `CSKM_Rebuild_Specification_Guide.md`
- `CSKM_AI_Agent_Development_Guide.md`
- `api-design/CSKM_API_Design.md`
- `data-model/CSKM_Data_Model_Design.md`
- `flow-design/CSKM_Process_State_Design.md`
- 補齊前後端分離重建所需的 API 契約、資料模型、狀態機與 AI Agent 持續開發規格

### 本次額外補齊的 Javascript 行為

- `D01`：批次勾選、主管/CEO 批次回覆、全選/全不選、主旨關鍵字三個月查詢限制
- `A06`：引用簽呈後主旨/內容唯讀、一般送審按鈕隱藏、引用送公告前再次確認
- `E03`：退回視窗會先帶入請款單編號並清空前次原因、清單列顯示關聯簽呈狀態提示
- `F02`：姓名自動完成搜尋、區域主管/部門主管互斥、部門主管至少勾選一個管理部門、管理部門全選
- `B022` / `C043`：勾選課程後動態重建繳費項目、繳費項目全選/全不選、備註/匯率/日期/金額驗證

---

## 2. Story 對照表

| Story / 群組 | 目前覆蓋文件 | 狀態 |
|---|---|---|
| A00-1, A00-2, A01-1, A02-1 | `bdd/group-personal-self-service.feature` + `bdd/a00-personal-data.feature` + `bdd/a01-change-password.feature` + `bdd/a02-personal-report.feature` / `sdd/group-personal-self-service-sdd.md` + `sdd/a00-personal-data-sdd.md` + `sdd/a01-change-password-sdd.md` + `sdd/a02-personal-report-sdd.md` / `tdd/group-personal-self-service-test-design.md` + `tdd/a00-personal-data-test-design.md` + `tdd/a01-change-password-test-design.md` + `tdd/a02-personal-report-test-design.md` | 已補齊 |
| A03-1, A03-2, A05-1, A06-1, A06-2 | `bdd/group-approval-center.feature` / `sdd/group-approval-center-sdd.md` / `tdd/group-approval-center-test-design.md` | 已補齊 |
| A04-1, A04-2 | `bdd/a04-petition.feature` / `sdd/a04-petition-sdd.md` / `tdd/a04-petition-test-design.md` | 已覆蓋 |
| B00-1, B01-1, B02-1, C04-1 | `bdd/group-sales-student-lifecycle.feature` / `sdd/group-sales-student-lifecycle-sdd.md` / `tdd/group-sales-student-lifecycle-test-design.md` | 已補齊 |
| C00-1, C01-1, C01-2, C02-1, C02-2, C02-3, C03-1, C05-1 | `bdd/group-academic-management.feature` / `sdd/group-academic-management-sdd.md` / `tdd/group-academic-management-test-design.md` | 已補齊 |
| D00-1 ~ D03-1 | `bdd/group-approval-center.feature` / `sdd/group-approval-center-sdd.md` / `tdd/group-approval-center-test-design.md` | 已校正並補齊 |
| D04-1 | `bdd/d04-student-feedback.feature` / `sdd/d04-student-feedback-sdd.md` / `tdd/d04-student-feedback-test-design.md` | 已實作完成 |
| E00-1, E00-2 | `bdd/e00-payment.feature` / `sdd/e00-payment-sdd.md` / `tdd/e00-payment-test-design.md` | 已覆蓋 |
| E01-1, E02-1, E03-1 | `bdd/group-finance-reporting.feature` / `sdd/group-finance-reporting-sdd.md` / `tdd/group-finance-reporting-test-design.md` | 已補齊 |
| F00-1, F01-1, F02-1, F02-4, F03-1, F04-1, F05-1 | `bdd/group-admin-operations.feature` / `sdd/group-admin-operations-sdd.md` / `tdd/group-admin-operations-test-design.md` | 已補齊 |
| F02-2, F02-3 | `bdd/f02-permission.feature` / `sdd/f02-permission-sdd.md` / `tdd/f02-permission-test-design.md` | 已覆蓋 |
| Login-1 ~ Login-3 | `bdd/group-login.feature` / `sdd/group-login-sdd.md` / `tdd/group-login-test-design.md` | 已覆蓋 |

---

## 3. 建議後續細化 Story 的項目

目前主要命名落差已修正；若要進一步貼近頁面，可再補寫以下細部故事：

1. **A02 子頁：日報 / 週報各自的建立、編修、送審**
2. **D03 子頁：不同報表明細頁的差異化批核規則**
3. **C04 / B02 子頁流程：服務記錄、繳費記錄、課程狀態、變更學顧 popup**
4. **E00 財務 / 教務雙重確認的分工規則**

---

## 4. 一句話結論

**目前 46 / 46 條 Story 都已有對應設計文件，且 BDD / SDD / TDD 已完成同層級對齊；若要直接依文件重建並交由 AI Agent 持續開發，API / Data Model / Flow-State / AI Agent Guide 也已補上基礎規格。**
