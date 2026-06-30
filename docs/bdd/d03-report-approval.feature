Feature: D03 報表審核
  As a 主管或 CEO
  I want to 依角色確認報表意見與決策
  So that 各類報表能完成審核流程

  Background:
    Given 報表審核頁提供主管意見欄位、執行長批示欄位、核准按鈕與退回按鈕

  Scenario: 主管審核報表前必須填寫主管意見
    Given 使用者身份為主管
    And 報表已送審到主管
    When 主管未填主管意見就按下核准或退回
    Then 畫面提示需先填寫主管意見

  Scenario: CEO 審核報表前必須填寫執行長批示
    Given 使用者身份為 CEO
    And 報表已送審到 CEO
    When CEO 未填執行長批示就按下核准或退回
    Then 畫面提示需先填寫執行長批示

  Scenario: 主管或 CEO 填妥意見後送出核准或退回
    Given 使用者身份為主管或 CEO
    And 審核人已填妥對應意見
    When 審核人按下核准或退回
    Then 報表審核頁會送出對應層級的審核結果

  Scenario: 非審核角色不得處理報表
    Given 使用者身份為一般員工
    When 使用者嘗試進入報表審核頁
    Then 畫面不得顯示核准按鈕、退回按鈕與審核意見欄位
