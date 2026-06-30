Feature: B01 訪談記錄
  As a 業務
  I want to 查詢訪談記錄並建立新的訪談資料
  So that 我能持續追蹤聯繫結果

  Background:
    Given 訪談記錄頁提供姓名、電話、Email、匯入日期與派發日期等查詢欄位
    And 訪談記錄頁提供結果清單與新增訪談記錄按鈕

  Scenario: 使用者依條件查詢訪談記錄
    Given 使用者已進入訪談記錄頁
    When 使用者輸入查詢條件後按下查詢
    Then 訪談記錄清單會先清空舊資料再重建結果
    And 畫面同步更新目前筆數

  Scenario: 使用者從列表建立新的訪談資料
    Given 使用者已在訪談記錄清單勾選一筆名單
    When 使用者按下新增訪談記錄
    Then 畫面會帶著該筆名單資料進入訪談明細頁
