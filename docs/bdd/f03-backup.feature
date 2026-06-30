Feature: F03 資料庫備份
  As a 系統管理員
  I want to 查看備份清單並執行資料庫備份
  So that 我能保留系統還原點

  Background:
    Given 備份頁提供備份清單與執行備份按鈕

  Scenario: 管理員查看備份清單或執行備份
    Given 管理員已進入備份頁
    When 管理員查看備份清單或按下執行備份
    Then 畫面顯示資料庫備份檔案與日期資訊
