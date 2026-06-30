Feature: Login 與首頁訊息中心
  As an 員工
  I want to 使用帳號密碼登入系統並透過訊息中心處理待辦
  So that 我能依畫面上的 tab、badge、Ajax 清單快速進入後續作業

  Background:
    Given 登入頁提供帳號與密碼欄位
    And 登入頁提供登入按鈕可送出帳號與密碼
    And 訊息中心以前端 tab 呈現公告、假單、報表、簽呈、請款單與員工守則
    And 訊息中心會依各分類清單的筆數計算 badge
    And 訊息中心提供公告搜尋欄、已讀勾選框與各類待辦清單

  Scenario: 使用正確帳號與密碼登入成功
    Given 員工帳號在 hr_management 中為在職狀態
    And 密碼經 MD5 加上 salt 後可比對成功
    When 使用者在登入頁輸入帳號與密碼並送出登入
    Then 系統建立登入 session
    And session 內含 SESS_IS_LOGIN、SESS_USER_ID、SESS_MENU_INFO、SESS_MENU_TITLE
    And 系統導向訊息中心首頁
    And 首頁預設載入公告 tab 與對應 badge 數量

  Scenario: 帳號或密碼錯誤時拒絕登入
    Given 員工帳號存在但密碼比對失敗
    When 使用者送出登入表單
    Then 顯示「帳號或密碼錯誤！」
    And 系統不得建立登入 session

  Scenario: 離職員工不可登入
    Given hr_management 中該員工已有離職日期 l_date
    When 使用者送出登入表單
    Then 顯示「人員已離職！」
    And 系統不得建立登入 session

  Scenario: 帳號或密碼空白時提示必填
    When 使用者未完整輸入帳號或密碼
    Then 顯示「請輸入帳號或密碼！」
    And 停留在登入頁

  Scenario: 首頁自動切換到第一個有資料的 tab
    Given 使用者已成功登入並進入訊息中心首頁
    And 公告 tab 沒有任何資料列
    And 假單 tab 或其他後續 tab 至少有一筆資料
    When 訊息中心完成各分類列數統計
    Then 前端會依 countA、countF、countR、countP、countI 的順序自動觸發對應 tab click
    And 使用者看到第一個非空清單而不是空白公告頁

  Scenario: 在訊息中心將訊息標記已讀
    Given 使用者已成功登入並進入訊息中心首頁
    And 某一筆公告或待辦列上提供已讀勾選框
    When 使用者將該筆資料標記為已讀
    Then 對應資料列會從目前清單移除
    And 該分類 badge 數量立即減 1
    And 若清單被清空則只剩表頭

  Scenario: 以關鍵字搜尋公告並重建公告表格
    Given 使用者已成功登入並停留在公告 tab
    When 使用者在公告搜尋欄輸入關鍵字並按下搜尋
    Then 前端送出公告搜尋請求
    And 只重建公告清單內容
    And 回傳列仍包含公告內容、主管意見、CEO 意見與已讀 checkbox
    And 其他假單、報表、簽呈與請款 tab 的內容不應被清空

  Scenario: 從訊息中心直接開啟公告或單據明細
    Given 使用者已成功登入並看到訊息中心清單
    When 使用者點擊公告、請假或簽呈的明細連結
    Then 前端會帶入該筆單號並立即送出導頁表單
    And 系統導向公告、請假或簽呈明細頁

  Scenario: 點擊員工守則 tab 觸發閱讀動作
    Given 使用者已成功登入並看到「員工守則」tab
    When 使用者點擊該 tab
    Then 前端會觸發員工守則閱讀動作
    And 系統開啟員工守則內容
