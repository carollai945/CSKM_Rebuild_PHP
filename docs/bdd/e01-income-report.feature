Feature: E01 收入統計報表
  As a 財務人員或管理員
  I want to 切換年度與顯示格式查看收入統計
  So that 我能快速比較年度收入資料

  Background:
    Given 收入統計頁提供年份選單與小數位選單

  Scenario: 使用者切換年份或小數位後重算統計表
    Given 使用者已進入收入統計頁
    When 使用者改變年份或小數位顯示格式
    Then 收入統計表會清空舊資料並重新顯示新的統計結果
