Feature: A04 簽呈申請與 D01 審核流程
  As an 系統使用者或審核主管
  I want to 在入口頁查詢、編輯、送審、取消與批核簽呈
  So that 簽呈流程能依前端操作完整追蹤

  Background:
    Given 簽呈入口頁提供起訖日期查詢與刪除功能
    And 簽呈入口頁在載入時會先顯示最近五筆資料，按下查詢可依日期區間重整列表，勾選草稿可刪除
    And 簽呈明細頁提供儲存、送審與取消等按鈕，審核頁提供核准與退回按鈕
    And 審核頁會先檢查主管意見或 CEO 批示是否填寫完成
    And 簽呈審核列表提供日期查詢欄位、主旨關鍵字欄位與待審單據清單
    And 簽呈審核頁提供主管待回覆全選、執行長待回覆全選、全不選與批次回覆區

  Scenario: 首次進入簽呈入口頁時載入預設清單
    Given 使用者已登入
    And A04 的 start_date 與 end_date 皆為空白
    When 簽呈入口頁完成載入
    Then 前端會送出預設簽呈查詢
    And 預設載入最近 5 筆簽呈
    And end_date 會自動補成 client_yyyy_mm_dd

  Scenario: 使用者以日期區間查詢歷史簽呈
    Given 使用者已建立多筆簽呈資料
    When 使用者在簽呈入口頁以日期選擇器指定區間後按下查詢
    Then 前端送出簽呈查詢請求
    And 畫面顯示符合條件的歷史列表
    And 列表保留狀態、快捷編號與可勾選項目

  Scenario: 審核人用主旨關鍵字查詢超過三個月時會自動縮短區間
    Given 使用者身份為主管或 CEO
    And 使用者已進入簽呈審核頁
    When 使用者輸入主旨關鍵字，且查詢日期區間超過三個月後按下查詢
    Then 起始日期會被自動改成迄日前推三個月內的第一天
    And 畫面提示主旨關鍵字查詢最多只能查三個月

  Scenario: 使用者建立簽呈草稿
    Given 使用者已登入
    When 使用者從簽呈入口頁進入簽呈明細頁並填寫主旨、說明與相關欄位
    And 簽呈明細頁會將申請日期預設為當天
    And 使用者按下儲存
    Then 系統會帶入儲存狀態並送出表單
    And petition 建立一筆新資料
    And 系統自動產生 signed_no
    And 單據狀態為 1 Draft

  Scenario: 說明欄位空白時不得送審
    Given 使用者停留在簽呈明細頁
    When 使用者按下送審但說明欄位為空
    Then 前端顯示「請輸入說明」
    And 表單不得送出

  Scenario: 使用者送審簽呈
    Given petition 狀態為 1 Draft
    When 使用者在簽呈明細頁按下送審
    Then 系統會帶入送審狀態並送出表單
    And 單據狀態更新為 2 Submitted
    And 簽呈待審列表可查到此單據

  Scenario: 使用者刪除仍在草稿狀態的簽呈
    Given petition 狀態為 1 Draft
    When 使用者在簽呈入口頁選取一筆草稿並按下刪除
    Then 前端只允許刪除單筆勾選資料
    And 系統送出刪除請求
    And 列表立即更新

  Scenario: 使用者自行取消已建立簽呈
    Given 使用者正在檢視自己的簽呈明細
    When 使用者輸入取消原因並確認取消單據
    Then 前端顯示「簽呈已取消!」
    And 取消視窗關閉
    And 畫面導回 A04 入口頁

  Scenario: 主管核准待審簽呈
    Given petition 狀態為 2 Submitted
    And 登入者具備主管審核權限
    When 主管在審核頁按下核准但未填主管意見
    Then 前端顯示「請輸入主管意見」
    And 表單不得送出
    When 主管補上主管意見後再次核准
    Then 系統會帶入主管核准狀態並送出表單
    And 單據狀態更新為 3 Supervisor Approved

  Scenario: 主管退回簽呈
    Given petition 狀態為 2 Submitted
    And 登入者具備主管審核權限
    When 主管補上主管意見後按下退回
    Then 系統會帶入主管退回狀態並送出表單
    And 單據狀態更新為 4 Supervisor Rejected
    And 申請人可看到退回原因

  Scenario: CEO 核准或退回主管已核准的簽呈
    Given petition 狀態為 3 Supervisor Approved
    And 登入者具備 CEO 權限
    When CEO 在審核頁按下核准但未填執行長批示
    Then 前端顯示「請輸入執行長批示」
    And 表單不得送出
    When CEO 補上批示後核准
    Then 系統會帶入 CEO 核准狀態並送出表單
    And 單據狀態更新為 5 CEO Approved
    When CEO 補上批示後退回
    Then 系統會帶入 CEO 退回狀態並送出表單
    And 單據狀態更新為 6 CEO Rejected

  Scenario: 主管可批次回覆多筆尚未回覆的簽呈
    Given 使用者身份為主管
    And 簽呈待審清單中有多筆主管未回覆資料
    When 主管勾選多筆主管待回覆資料
    Then 畫面顯示主管批次意見區並隱藏執行長批示區
    When 主管輸入同一段主管意見後按下批次回覆
    Then 所有勾選的簽呈都會更新為主管已回覆
    And 畫面顯示批次回覆結果並重新整理清單

  Scenario: 執行長可批次回覆多筆主管已回覆的簽呈
    Given 使用者身份為 CEO
    And 簽呈待審清單中有多筆執行長未回覆資料
    When 執行長勾選多筆執行長待回覆資料
    Then 畫面顯示執行長批次批示區並隱藏主管意見區
    When 執行長輸入同一段批示後按下批次回覆
    Then 所有勾選的簽呈都會更新為執行長已回覆
    And 畫面顯示批次回覆結果並重新整理清單

  Scenario: 批次回覆失敗時會列出失敗的簽呈編號
    Given 使用者身份為主管或 CEO
    And 使用者已勾選多筆待回覆簽呈並送出批次回覆
    When 其中部分簽呈更新失敗
    Then 畫面提示批次回覆錯誤的簽呈編號清單
