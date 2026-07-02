Feature: D01 簽呈批核
  As a 主管或 CEO
  I want to 查看待批核的簽呈清單並核准或退回
  So that 員工的簽呈申請能在線上完成審核流程

  Background:
    Given 使用者已登入系統且具備簽呈批核權限

  Scenario: 主管查看待批核簽呈清單
    Given 有員工提交了簽呈申請
    When 主管進入簽呈批核頁面
    Then 系統顯示所有狀態為「待審核」的簽呈清單
    And 清單欄位包含簽呈標題、申請人、申請日期與目前狀態

  Scenario: 主管核准簽呈
    Given 主管查看待批核簽呈清單
    When 主管點選某筆簽呈的「核准」按鈕
    Then 系統將該簽呈狀態更新為「已核准」
    And 清單自動刷新，已核准的簽呈從待批清單移除

  Scenario: 主管退回簽呈
    Given 主管查看待批核簽呈清單
    When 主管點選某筆簽呈的「退回」按鈕
    Then 系統將該簽呈狀態更新為「已退回」
    And 清單自動刷新，已退回的簽呈從待批清單移除

  Scenario: 無待批核簽呈時顯示空清單
    Given 目前沒有任何待批核的簽呈
    When 主管進入簽呈批核頁面
    Then 頁面顯示「目前無待批核簽呈」提示訊息

  Scenario: 非主管角色無法存取批核頁面
    Given 使用者角色為一般員工（Staff）
    When 使用者嘗試存取簽呈批核頁面
    Then 系統回傳 403 禁止存取

  Scenario: CEO 可查看所有區域的待批核簽呈
    Given 使用者角色為 CEO
    When CEO 進入簽呈批核頁面
    Then 系統顯示所有區域的待批核簽呈清單

  Scenario: 主管只能查看自己管轄範圍內的簽呈
    Given 使用者角色為 RegMgr（區域主管）
    When 區域主管進入簽呈批核頁面
    Then 系統只顯示該區域員工的待批核簽呈
