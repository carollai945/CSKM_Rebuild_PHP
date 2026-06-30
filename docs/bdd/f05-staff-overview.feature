Feature: F05 人員總覽與離職狀態
  As a 系統管理員或主管
  I want to 查看在職／離職人員並更新離職狀態
  So that 人員資料能反映目前任職情況

  Background:
    Given 人員總覽頁提供在職清單、離職清單與離職日期欄位

  Scenario: 使用者查看人員總覽
    Given 管理員或主管已進入人員總覽頁
    When 頁面完成載入
    Then 畫面顯示在職與離職人員列表及區域統計

  Scenario: 管理員標記員工離職
    Given 目標員工存在
    When 管理員填寫離職日期
    Then 該員工會顯示為已離職
    And 該員工不得再登入
