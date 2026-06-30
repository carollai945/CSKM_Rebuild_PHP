Feature: C046 學員明細維護
  As a 教務人員
  I want to 在學員明細頁調整區域、機構、課程與基本資料
  So that 單一學員的資料能正確保存

  Background:
    Given 學員明細頁提供區域下拉選單、機構下拉選單、課程下拉選單與儲存按鈕

  Scenario: 使用者切換區域時更新機構與學顧
    Given 使用者已進入學員明細頁
    When 使用者切換區域
    Then 機構下拉選單與學顧選單會更新為該區域可選項目

  Scenario: 使用者切換機構時更新課程
    Given 使用者已選定區域
    When 使用者切換機構
    Then 課程下拉選單會更新為該機構可選課程

  Scenario: 使用者儲存學員資料
    Given 使用者已完成學員資料編修
    When 使用者按下儲存
    Then 畫面會先檢查必填欄位
    And 檢查通過後才會保存學員資料
