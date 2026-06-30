Feature: F04 匯入電訪名單
  As a 系統管理員
  I want to 下載範例檔並匯入名單 Excel
  So that 電訪名單能批次建立

  Background:
    Given 匯入頁提供範例 Excel 下載連結、檔案上傳欄位與匯入按鈕

  Scenario: 管理員匯入電訪名單
    Given 管理員已進入匯入頁
    When 管理員下載範例檔並上傳符合格式的 Excel 檔後按下匯入
    Then 匯入結果區會顯示成功筆數、失敗筆數與總筆數
