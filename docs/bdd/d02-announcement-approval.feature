Feature: D02 公告審核
  As a 主管或 CEO
  I want to 依角色完成公告審核
  So that 公告能依層級完成審核

  Background:
    Given 公告審核頁提供待審清單、主管意見欄位、執行長批示欄位、核准按鈕與退回按鈕

  Scenario: 主管審核公告前必須填寫主管意見
    Given 使用者身份為主管
    And 公告已送審到主管
    When 主管未填主管意見就按下核准或退回
    Then 畫面提示需先填寫主管意見

  Scenario: CEO 審核公告前必須填寫執行長批示
    Given 使用者身份為 CEO
    And 公告已送審到 CEO
    When CEO 未填執行長批示就按下核准或退回
    Then 畫面提示需先填寫執行長批示

  Scenario: 主管送出主管層審核結果
    Given 使用者身份為主管
    And 主管已填妥主管意見
    When 主管按下核准或退回
    Then 公告審核頁會送出主管層對應的審核狀態

  Scenario: CEO 送出最終審核結果
    Given 使用者身份為 CEO
    And CEO 已填妥執行長批示
    When CEO 按下核准或退回
    Then 公告審核頁會送出最終審核狀態

  Scenario: 非公告審核角色不得執行核決
    Given 使用者身份為一般員工
    When 使用者嘗試進入公告審核頁
    Then 畫面不得顯示核准按鈕、退回按鈕與審核意見欄位
