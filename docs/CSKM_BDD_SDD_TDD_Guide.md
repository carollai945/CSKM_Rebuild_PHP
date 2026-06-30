# CSKM 系統 BDD + SDD + TDD 指引文件

> 參考來源：`session-state/files/CSKM_UserStories.md`  
> 適用系統：CSKM Java / Spring MVC / JSP / MySQL  
> 文件目的：將既有 User Story 系統化轉換為 **BDD（Behavior-Driven Development）**、**頁面設計文件（Page Design）**、**SDD（System / Software Design Description）**、**TDD（Test-Driven Development）** 的可執行產物，並銜接到 **API 設計 / 資料模型設計 / 流程狀態設計** 的重建規格層。

---

## 1. 文件目的與使用方式

本文件的用途不是重寫 User Story，而是建立一套 **從 Story 到設計、再到測試** 的落地方法。

建議使用順序：

1. 先從 `CSKM_UserStories.md` 選定一個模組或一個 Story。
2. 依本文件產出對應的 **BDD Feature / Scenario**。
3. 為頁面級功能建立對應的 **Page Design 頁面設計文件**。
4. 為同一個 `group-*` 或功能編號頁面建立對應的 **SDD 設計文件**。
5. 若目標是前後端分離重建，再補 **API / Data Model / Flow-State** 文件。
6. 最後以同名 BDD / Page Design / SDD 建立對應的 **TDD 測試清單**。

---

## 3.1 頁面設計文件建構指引

每一份頁面設計文件至少應包含：

1. **基本資料**：頁面名稱、JSP、對應 BDD / SDD / TDD、主要使用者
2. **頁面目的**：這頁要解決什麼工作
3. **版面區塊**：需依 JSP 實際可見的查詢列、表格、caption、按鈕列、modal、批次操作區命名，不可只由 BDD / SDD 反推；凡是由 JavaScript / jQuery / Vue 動態插入、切換、重建的區塊，也必須納入
4. **欄位與顯示規則**：必填、唯讀、預設值、顯示條件，並補充哪些欄位或選項是由前端動態產生 / 重建
5. **按鈕與動作**：各按鈕的入口與效果，包含由 Ajax 結果列或 Vue `v-for` 清單動態長出的操作按鈕
6. **互動規則**：Ajax、全選、連動下拉、autocomplete、modal、Vue 狀態切換，以及查詢後如何重建表格 / modal / 欄位內容
7. **訊息設計**：成功、失敗、驗證、空資料、確認訊息
8. **權限與狀態**：不同角色與不同狀態的可見範圍與操作差異

---

## 2. 三種文件的定位

| 類型 | 目的 | 核心問題 | 產出形式 |
|---|---|---|---|
| **BDD** | 定義業務行為 | 使用者怎麼操作？系統應怎麼反應？ | Gherkin feature、情境清單 |
| **Page Design** | 定義頁面結構 | 這個頁面有哪些區塊、欄位、按鈕、互動與訊息？ | 頁面區塊、欄位規則、按鈕、popup、互動設計 |
| **SDD** | 定義系統設計 | 這個行為要由哪些頁面、controller、table、流程狀態實現？ | 模組設計說明、流程圖、資料表與責任分配 |
| **TDD** | 定義驗證方式 | 怎麼證明功能正確且可持續維護？ | 單元測試、整合測試、流程測試清單 |
| **API Design** | 定義前後端契約 | 前端怎麼呼叫後端？ | Endpoint、request/response、錯誤碼 |
| **Data Model** | 定義核心資料實體 | 系統有哪些 Aggregate、Entity、Code Table？ | 資料模型、欄位、關聯 |
| **Flow-State** | 定義流程與狀態機 | 狀態如何流轉？誰能操作？ | 狀態圖、轉移表、批次規則 |

### 命名與粒度一致性

1. **群組總覽文件** 一律使用 `group-*` 命名，例如 `bdd/group-approval-center.feature`、`sdd/group-approval-center-sdd.md`、`tdd/group-approval-center-test-design.md`
2. **頁面 / 功能文件** 一律使用功能編號命名，例如 `bdd/d00-leave-approval.feature`、`sdd/d00-leave-approval-sdd.md`、`tdd/d00-leave-approval-test-design.md`
3. **頁面設計文件** 只建立在頁面 / 功能層級，命名為 `page-design/<code>-<name>-page-design.md`
4. BDD、Page Design、SDD、TDD 四者應維持 **同一顆粒度對應**：同一頁面至少要能互相追到對應設計與測試文件
5. API Design、Data Model、Flow-State 屬於 **系統級 / 子域級補充文件**，不要求與單頁一對一，但必須能覆蓋頁面級文件所需的契約、資料與狀態規格
6. 若目標是 AI Agent 持續開發，請再對應 `CSKM_AI_Agent_Development_Guide.md`，確保功能能切成小任務並具備完整追溯鏈

---

## 3. 建議總流程

### Step 1：選定 Story 單位

優先以「一個主流程」作為單位，而不是一次處理整個模組。

建議切分單位：

- Login-1 員工登入系統
- A04-1 請假申請
- A06-1 公告發布
- B01-1 電訪進度追蹤
- C04-1 學員分配給學顧
- E00-1 / E00-2 繳費查詢與新增
- F02-2 / F02-3 新增員工與調整權限

### Step 2：先整理 Story Traceability

每個 Story 都建立一筆對應表：

| Story ID | Story 標題 | Actor | 主要畫面 | 主要資料表 | 狀態流程 | 風險 |
|---|---|---|---|---|---|---|
| Login-1 | 員工登入系統 | 員工 | login.jsp, msglist.jsp | hr_management, menu | 登入成功 / 失敗 / 離職 | 權限、密碼驗證 |
| A04-1 | 填寫請假申請單 | 系統使用者 | A04.jsp, A040.jsp, D00.jsp, D000.jsp | petition | 草稿→送審→主管/CEO審核 | 狀態跳轉錯誤 |
| E00-2 | 新增繳費記錄 | 財務人員 | E00.jsp | payment_info | 建立→財務確認→教務確認 | 雙重確認一致性 |

這張表是 BDD、SDD、TDD 的共同索引。

---

## 4. BDD 建構指引

## 4.1 BDD 的寫法原則

每個 Story 至少拆出 4 類情境：

1. **Happy Path**：標準成功流程
2. **Validation**：欄位缺漏、格式錯誤、非法值
3. **Authorization**：角色是否有權限
4. **State Transition / Exception**：狀態流轉、退回、重送、重複操作

如果同一頁會因身份不同而顯示不同功能，BDD 應改成 **頁面級檔案 + 角色級 Scenario**：

1. **先固定頁面**：一個 `.feature` 對應一個頁面或一組高度相連的頁面
2. **再切角色**：每個角色各自擁有獨立 Scenario，不混寫
3. **每個角色都驗證 4 件事**：
   - 看得到哪些欄位、按鈕、清單
   - 能執行哪些動作
   - 能看到哪些資料範圍（本人 / 本區 / 全部）
   - 送出後狀態會流向哪一個下一關

如果有前端互動，額外補：

5. **UI / Ajax Scenario**：查詢、分頁、Tab、popup、動態下拉、已讀標記
6. **Batch Action Scenario**：全選 / 全不選、批次勾選、批次回覆、批次核准、批次失敗回報

---

## 4.2 BDD Scenario Template

```gherkin
Feature: [模組名稱] [功能名稱]
  As a [主要角色]
  I want to [主要動作]
  So that [主要目的]

  Background:
    Given [頁面] 提供 [查詢欄位 / 清單 / 按鈕 / 視窗]
    And 系統存在合法測試資料

  Scenario: [角色 A] 可完成主要流程
    Given 使用者身份為 [角色 A]
    And 使用者已登入且具備對應權限
    When [角色 A 的具體操作]
    Then [角色 A 看見的按鈕 / 清單 / 結果]
    And [資料狀態]

  Scenario: [角色 A] 驗證失敗
    Given 使用者身份為 [角色 A]
    When [輸入錯誤資料或缺漏必要意見]
    Then [顯示錯誤訊息]
    And [資料不得寫入]

  Scenario: [角色 B] 權限不足
    Given 使用者身份為 [角色 B]
    When [嘗試進入頁面或按下受限制按鈕]
    Then [禁止存取、看不到按鈕或不能送出]

  Scenario: [角色 C] 資料範圍不同
    Given 使用者身份為 [角色 C]
    When [進入頁面或查詢]
    Then [只看到本人 / 本區 / 全部資料]

  Scenario: [角色 A] 批次處理多筆資料
    Given 使用者身份為 [角色 A]
    And 畫面提供勾選框、全選按鈕、全不選按鈕與批次回覆區
    When [角色 A] 勾選多筆資料並輸入共同意見後送出
    Then 所有勾選資料應套用同一批次處理結果
    And 成功或失敗訊息應明確列出結果
```

---

## 4.3 BDD 範例一：Login-1 員工登入系統

```gherkin
Feature: 員工登入系統
  As a 員工
  I want to 以員工編號與密碼登入
  So that 我能進入被授權的功能頁面

  Scenario: 使用正確帳號密碼登入成功
    Given hr_management 中存在在職員工帳號
    And 該帳號擁有至少一個 menu 權限
    When 使用者在 login.jsp 輸入正確帳號與密碼並送出
    Then 系統建立登入 session
    And 系統載入 SESS_MENU_INFO 與 SESS_MENU_TITLE
    And 畫面導向 msglist.jsp

  Scenario: 輸入錯誤密碼
    Given hr_management 中存在在職員工帳號
    When 使用者輸入錯誤密碼並送出
    Then 顯示「帳號或密碼錯誤！」
    And 不建立登入 session

  Scenario: 離職員工登入
    Given hr_management 中該員工已有 l_date
    When 使用者送出登入
    Then 顯示「人員已離職！」
    And 不建立登入 session

  Scenario: 帳號或密碼空白
    When 使用者未輸入帳號或密碼
    Then 顯示「請輸入帳號或密碼！」
```

---

## 4.4 BDD 範例二：A04 / D00 請假申請與審核

```gherkin
Feature: 請假申請送審與主管審核
  As a 申請人或主管
  I want to 依角色完成請假建立、送審與審核
  So that 請假單能流向正確的下一關

  Background:
    Given 請假明細頁提供日期欄位、假別欄位、事由欄位、儲存按鈕與送審按鈕
    And 請假審核頁提供待審清單、主管意見欄位、核准按鈕、退回按鈕與批次回覆區

  Scenario: 申請人送出請假單
    Given 使用者身份為申請人
    And 使用者已開啟自己的請假明細頁
    When 使用者填寫請假日期、假別與事由後按下送審
    Then 請假單會產生申請單號
    And 這筆請假單會出現在主管待審清單

  Scenario: 主管核准請假單
    Given 使用者身份為主管
    And 主管已開啟待審請假單
    When 主管填入主管意見並按下核准
    Then 該筆請假單會保存主管意見與核准結果

  Scenario: 主管批次回覆多筆待審請假單
    Given 使用者身份為主管
    And 請假審核清單中有多筆主管未回覆的請假單
    When 主管勾選多筆待審資料、輸入主管意見並按下批次回覆
    Then 所有勾選的請假單都會更新為主管已回覆狀態
    And 畫面重新整理待審清單並顯示批次結果

  Scenario: 非主管不得執行請假審核
    Given 使用者身份為一般員工
    When 使用者嘗試進入請假審核頁
    Then 畫面不得顯示核准按鈕與退回按鈕
```

---

## 4.5 BDD 範例三：E00 繳費建立與雙重確認

```gherkin
Feature: 學生繳費建立與確認
  As a 財務或教務人員
  I want to 建立並確認學生繳費資料
  So that 系統能正確追蹤收款完成狀態

  Scenario: 財務建立繳費記錄
    Given 學員資料存在
    When 財務人員於 E00.jsp 建立一筆 payment_info
    Then payment_info 寫入成功
    And 初始確認欄位為未完成

  Scenario: 財務確認成功
    Given payment_info 已建立
    When 財務人員按下 Finance 確認
    Then pay_fin 更新為已確認

  Scenario: 教務確認成功
    Given payment_info 已建立
    When 教務人員按下 Academic 確認
    Then pay_aca 更新為已確認

  Scenario: 任一方退回
    Given payment_info 尚未完成最終確認
    When 財務或教務執行退回
    Then 系統保留退回原因
    And 該筆資料不可視為完成
```

---

## 4.6 BDD 輸出建議結構

建議於 session-state 後續建立：

```text
files/
  bdd/
    group-login.feature
    a04-petition.feature
    e00-payment.feature
    f02-permission.feature
```

---

## 5. SDD 建構指引

## 5.1 SDD 的核心內容

每個 Story 對應的 SDD 應至少包含：

1. **需求摘要**
2. **參與角色**
3. **頁面與路由**
4. **後端組件責任**
5. **資料表與欄位**
6. **狀態機**
7. **例外與錯誤處理**
8. **權限規則**
9. **可測試點**

---

## 5.2 SDD Template

```markdown
# [模組/功能] SDD

## 1. 功能目標
- 對應 Story：
- 主要使用者：

## 2. 畫面與流程
- 入口頁：
- 編輯頁：
- 檢視/審核頁：
- Ajax/Popup：

## 3. 路由與 Controller
- GET /...
- POST /...
- Ajax /...

## 4. 資料設計
- 主要資料表：
- 關鍵欄位：
- 狀態欄位：

## 5. 狀態機
- Draft -> Submitted -> Approved / Rejected ...

## 6. 權限與角色
- 哪些角色可讀 / 可寫 / 可審核

## 7. 錯誤處理
- 驗證錯誤
- 權限錯誤
- 狀態衝突

## 8. 測試切點
- 單元測試：
- 整合測試：
- UI/流程測試：
```

---

## 5.3 SDD 實作重點：CSKM 專案特別需要補強的地方

### A. 畫面流程設計

CSKM 很多功能不是單頁完成，而是：

- 入口頁（查詢 / 列表）
- 編輯頁（新增 / 修改）
- 檢視頁（唯讀）
- 審核頁（主管 / CEO）

因此 SDD 不可只寫 controller，要把 **頁面流向** 寫清楚。

例如：

| 模組 | 入口頁 | 明細頁 | 審核頁 |
|---|---|---|---|
| A04 請假 | A04.jsp | A040.jsp | D000.jsp |
| A06 公告 | A06.jsp | A060.jsp / A061.jsp | D020.jsp |
| 報表 | A02.jsp | A020~A027.jsp | D030~D033.jsp |
| 請款 | A05.jsp / A050.jsp | E02.jsp / E020.jsp | E03.jsp |

### B. 狀態機設計

對於 A / D / E 模組，必須明確寫出狀態轉移：

| 狀態碼 | 名稱 | 說明 |
|---|---|---|
| 1 | Draft | 申請人可編輯 |
| 2 | Submitted | 待主管審核 |
| 3 | Supervisor Approved | 待下一階段 |
| 4 | Supervisor Rejected | 退回修改 |
| 5 | CEO Approved / Finance Confirmed | 流程完成 |
| 6 | CEO Rejected | 最終拒絕 |
| 7 | Self Cancelled | 申請人取消 |

### C. 權限設計

F02 的權限設計會影響整個系統，所以 SDD 需要把權限規則當成橫切需求：

- `priv_ceo`：跨區域與最終審核
- `priv_regmgr`：區域主管審核與查詢
- `priv_finmgr`：財務確認
- `priv_clsmgr`：教務確認
- `priv_staff`：一般員工 / 學顧

### D. Ajax / 前端互動設計

若 Story 涉及以下情境，SDD 必須補上：

- 動態下拉選單：Region → Institution → Course → Subject
- 已讀 / 搜尋：`msglist.jsp`
- 日期搜尋：A02、E00
- popup 視窗：C045
- 匯入匯出：F04、C02、PDF 檢視頁

---

## 5.4 SDD 範例：Login 功能

```markdown
# Login 功能 SDD

## 1. 功能目標
- 對應 Story：Login-1, Login-2, Login-3
- 目的：完成員工身分驗證、選單載入、首頁導向與未授權阻擋

## 2. 畫面與流程
- 入口頁：login.jsp
- 成功頁：msglist.jsp
- 錯誤頁：error.jsp

## 3. 路由與 Controller
- POST /app/login/verify
- GET /app/login/out
- POST /app/login/msgread
- POST /app/login/anncounce/search

## 4. 資料設計
- hr_management：帳號、密碼、在職狀態、權限
- menu：畫面選單定義

## 5. Session 設計
- SESS_IS_LOGIN
- SESS_USER_ID
- SESS_MENU_INFO
- SESS_MENU_TITLE
- SESS_REG_DEP_ID

## 6. 權限與攔截
- RequestInitializeInterceptor 負責登入檢查與畫面初始化

## 7. 可測試點
- 密碼比對
- 離職不可登入
- 權限頁面不可存取
- 首頁 badge / msgread Ajax
```

---

## 6. TDD 建構指引

## 6.1 TDD 的落地原則

此專案是傳統 Spring MVC + JSP + JDBC 風格，建議採分層測試：

1. **規則層單元測試**：驗證欄位規則、狀態轉移、權限判斷
2. **控制器整合測試**：驗證 request / response / session / DB 互動
3. **流程測試**：驗證一整段業務流程
4. **UI 驗收測試**：可先用手動腳本，後續再工具化

---

## 6.2 TDD Test Slice

| 測試層級 | 適合測什麼 | 範例 |
|---|---|---|
| Unit | 密碼驗證、欄位檢查、狀態轉換、權限判斷 | Login password hash、A04 status transition |
| Integration | Controller + DB + Session | `/login/verify`、`/E/E00/AjaxSearch` |
| Workflow | 多步驟流程 | A040 建單 → D000 主管審核 → CEO 核准 |
| UI Acceptance | 頁面可操作與訊息顯示 | login.jsp、msglist.jsp、C045 popup |

---

## 6.3 TDD 命名規則建議

```text
[Module]_[Function]_[Condition]_[ExpectedResult]
```

範例：

- `Login_verify_validCredentials_shouldCreateSession`
- `Login_verify_retiredUser_shouldRejectLogin`
- `Petition_submit_draftRecord_shouldMoveToSubmitted`
- `Payment_confirm_financeApproved_shouldSetPayFin`
- `Permission_check_nonCEO_shouldBlockCrossRegionAccess`

---

## 6.4 TDD Template

```markdown
### 測試主題
- Story：
- Scenario：
- 測試層級：Unit / Integration / Workflow / UI

### Given
- 初始資料：
- Session：

### When
- 呼叫 API / Controller / Method

### Then
- Response：
- DB：
- Session：
- 畫面訊息：
```

---

## 6.5 TDD 範例一：Login

### Unit

- 輸入正確密碼時，MD5 + salt 比對成功
- 密碼空白時回傳錯誤
- 使用者為離職狀態時回傳拒絕登入

### Integration

- POST `/app/login/verify` 成功時建立 session 與 menu
- POST `/app/login/verify` 失敗時導向 errorPath
- GET `/app/login/out` 清除 session

### UI / Workflow

- login.jsp 欄位空白顯示錯誤
- 成功登入後進入 msglist.jsp
- msglist.jsp 已讀操作後 badge 數量遞減

---

## 6.6 TDD 範例二：A04 / D00 請假流程

### Unit

- 狀態 1 可送審，狀態 2 不可再次送審
- 主管不可直接執行 CEO 動作
- 非本人不可編輯已送審單據

### Integration

- POST 建立請假單成功寫入 `petition`
- POST 送審後狀態變為 2
- D000 審核後正確更新狀態與簽核意見

### Workflow

- 建立草稿 → 送審 → 主管核准 → CEO 核准 → 完成
- 建立草稿 → 送審 → 主管退回 → 申請人修改後重送

---

## 6.7 TDD 範例三：E00 / E02 / E03 財務流程

### Unit

- `pay_fin` 與 `pay_aca` 的最終完成條件
- 退回時必須帶原因
- 已完成資料不可再次退回

### Integration

- E00 建立繳費成功寫入 `payment_info`
- 財務確認更新 `pay_fin`
- 教務確認更新 `pay_aca`
- E03 財務確認請款成功封存資料

### Workflow

- 新增繳費 → 財務確認 → 教務確認 → 完成
- A050 建立請款 → E02 查看 → E03 退回 → 申請人重送

---

## 7. 建議的 BDD / SDD / TDD 對照矩陣

| Story 類型 | BDD 重點 | SDD 重點 | TDD 重點 |
|---|---|---|---|
| 登入 / 權限 | 成功、失敗、離職、未授權 | session、interceptor、menu 載入 | session 建立、權限阻擋 |
| 申請 / 審核 | 建立、送審、核准、退回、取消 | 狀態機、審核角色、頁面流向 | 狀態轉移、審核權限、流程整合 |
| 查詢 / Ajax | 篩選條件、空結果、錯誤輸入 | endpoint、查詢條件、回傳 JSON | controller response、查詢結果 |
| 資料維護 | 新增、修改、刪除 | CRUD 責任分層、欄位規則 | validation、DB 寫入 |
| 匯入匯出 | 上傳、格式錯誤、匯入結果 | 檔案格式、重複資料規則 | 檔案解析、重複檢查 |

---

## 8. 建議優先順序

若要實際開始做，建議依風險與覆蓋面排序：

1. **Login / 權限**
2. **A / D 審核流程**
3. **E 財務確認流程**
4. **F02 人員權限管理**
5. **B / C 業務與教務主流程**
6. **匯入匯出、PDF、訊息中心 Ajax**

原因：

- Login 與 F02 是全系統入口與權限核心
- A / D / E 涉及狀態機與簽核，業務風險最高
- B / C 流程跨多頁，適合在權限與狀態機穩定後再展開

---

## 9. Definition of Ready

在開始撰寫某個功能的 BDD / SDD / TDD 前，至少要具備：

- 已有對應 User Story
- 確認主角色與次角色
- 確認主畫面、子頁面、Ajax 頁面
- 確認主要資料表
- 確認狀態碼與權限碼
- 確認成功與失敗條件
- 若預計交由 AI Agent 實作，需先確認對應的 API、Flow-State 與測試完成條件

---

## 10. Definition of Done

完成某個功能的 BDD + SDD + TDD，至少要有：

### BDD
- 1 個 Feature
- 至少 4 個 Scenario（成功 / 驗證 / 權限 / 狀態）

### SDD
- 1 份功能設計說明
- 含畫面流向、controller、table、狀態機、權限規則

### TDD
- 單元測試清單
- 整合測試清單
- 至少 1 條完整 workflow 測試

### AI Agent Ready
- 能對應唯一的 Story ID
- 能對應唯一的頁面 / API / 狀態切片
- 能找到 API、Flow-State、Page Design 三類規格
- 完成條件可明確驗收，且不依賴口頭補充

---

## 11. 建議的 session-state 產物結構

```text
session-state/files/
  CSKM_UserStories.md
  CSKM_BDD_SDD_TDD_Guide.md
  bdd/
    group-login.feature
    a04-petition.feature
    e00-payment.feature
  sdd/
    login-sdd.md
    a04-petition-sdd.md
    e00-payment-sdd.md
  tdd/
    login-test-design.md
    a04-petition-test-design.md
    e00-test-design.md
```

---

## 12. 建議下一步

若要繼續往下做，建議直接從以下 3 條主流程開始產出正式文件：

1. **Login-1 / Login-3**：登入與權限攔截
2. **A04-1 + D00**：請假申請與審核流程
3. **E00-2 + E03**：繳費 / 請款確認流程

這 3 條能覆蓋：

- 權限控制
- 狀態機
- 多頁面流轉
- 資料寫入
- Ajax / UI 互動
- 角色差異

---

## 13. 一句話總結

**User Story 定義要做什麼，BDD 定義怎麼表現，SDD 定義怎麼設計，TDD 定義怎麼證明它正確。**
