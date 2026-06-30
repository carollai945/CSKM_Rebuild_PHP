Feature: E00 學生繳費查詢與雙重確認
  As a 財務人員或教務人員
  I want to 用前端連動條件、確認按鈕與退回面板處理繳費紀錄
  So that 收款狀態可被正確追蹤與簽核

  Background:
    Given E00 提供區域、機構、課程、學顧、學生姓名與日期區間等篩選欄位
    And 切換區域會重建機構與學顧選單
    And 切換機構會重建課程選單
    And 日期欄位會使用日期選擇器並將結束日預設為當天
    And 結果清單中的每筆資料都會顯示財務確認、教務確認或退回等可操作項目

  Scenario: 財務人員使用連動條件查詢學生繳費紀錄
    Given 系統中已有多筆 payment_info
    When 財務人員先選擇區域
    Then 前端會依該區域重新產生機構與學顧下拉選單
    When 財務人員再選擇機構
    Then 前端會依該機構重新產生課程下拉選單
    When 財務人員按下查詢
    Then 前端送出繳費查詢請求
    And 會先清空舊查詢結果再重建清單

  Scenario: 搜尋結果依角色顯示財務與教務確認按鈕
    Given E00 查詢已回傳資料
    When 前端逐列重建結果清單
    Then pay_fin=0 且 priv_finmgr=1 的列顯示「確認」按鈕
    And pay_aca=0 且 priv_clsmgr=1 的列顯示「確認」按鈕
    And 已完成確認的欄位改顯示「已確認」
    And 若財務與教務都已確認則該列不再顯示退回 checkbox

  Scenario: 財務確認繳費後自動重新查詢
    Given payment_info 已存在且登入者具備財務權限
    When 使用者點擊財務確認
    Then 前端送出財務確認請求
    And 成功後顯示「財務核示已確認」
    And 自動重新查詢表格

  Scenario: 教務確認繳費後自動重新查詢
    Given payment_info 已存在且登入者具備教務權限
    When 使用者點擊教務確認
    Then 前端送出教務確認請求
    And 成功後顯示「教務核示已確認」
    And 自動重新查詢表格

  Scenario: 使用者勾選退回項目後展開退回理由區塊
    Given E00 搜尋結果至少有一筆尚未完成雙重確認的資料
    When 使用者選擇某一列作為退回對象
    Then 前端會取消其他列的勾選狀態
    And 自動捲動到頁面底部
    And 顯示退回理由區塊
    And 將焦點移到退回理由輸入框

  Scenario: 退回理由空白時不得退回
    Given 使用者已勾選待退回資料
    When 使用者按下退回但未填退回理由
    Then 前端顯示「退回理由不可留空」
    And 不得送出退回請求

  Scenario: 退回繳費資料成功後清空並隱藏退回區塊
    Given 使用者已勾選待退回資料並填妥退回理由
    When 使用者按下退回確認
    Then 前端送出退回請求
    And 成功後顯示「已退回」
    And 自動重新查詢
    And 退回理由區塊會被隱藏
