Feature: 系統後台主資料與維運作業
  As a 系統管理員或主管
  I want to 維護區域、部門、人員狀態、備份、匯入與人員清單
  So that 系統組織資料與維運作業都能被正確管理

  Background:
    Given 區域維護頁提供新增、修改與儲存區域資料的欄位與按鈕
    And 匯入頁提供範例 Excel 檔下載與匯入入口
    And F00、F03、F04、F05 都提供手冊 PDF 按鈕

  Scenario: 維護區域資料
    Given 管理員已登入
    When 管理員在區域新增或修改頁按下儲存
    Then 區域清單會新增或更新對應的區域資料

  Scenario: 維護部門與職稱
    Given 管理員已登入
    When 管理員在部門與職稱相關頁面維護資料
    Then 部門或職稱清單會顯示最新的維護結果

  Scenario: 查詢人員列表
    Given 管理員或主管已登入
    When 使用者進入人員總覽頁
    Then 顯示在職與離職人員列表及區域統計

  Scenario: 標記員工離職
    Given 目標員工存在
    When 管理員設定 l_date
    Then 該員工在 F05 顯示為已離職
    And 該員工不得再登入

  Scenario: 查看資料庫備份並執行備份
    Given 管理員已登入
    When 使用者在備份頁查看備份清單或執行備份
    Then 畫面顯示資料庫備份檔案與日期資訊

  Scenario: 批次匯入電訪名單
    Given 管理員已登入
    When 使用者在匯入頁下載範例檔並上傳符合格式的 Excel 檔
    Then 匯入結果區會顯示成功筆數、失敗筆數與總筆數
