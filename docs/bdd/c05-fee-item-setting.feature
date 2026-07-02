Feature: C05 費用項目設定
  As a 財務人員 / 管理員
  I want to 設定課程費用項目
  So that 學生繳費時能套用正確的費用標準

  Background:
    Given 管理員已登入

  Scenario: C05-1 查詢費用項目清單
    When 使用者進入 C05 費用項目設定頁面
    Then 系統顯示所有費用項目的名稱、金額及狀態

  Scenario: C05-1 新增費用項目
    When 管理員點擊「新增」
    And 填入名稱「英語初級課程費」、金額「15000」
    And 點擊「儲存」
    Then 系統新增費用項目並顯示成功提示
    And 列表出現「英語初級課程費」

  Scenario: C05-1 非管理員不可修改費用項目
    Given 一般員工已登入
    When 嘗試新增或修改費用項目
    Then 系統回傳 403 禁止存取
