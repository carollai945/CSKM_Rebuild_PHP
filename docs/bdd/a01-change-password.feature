Feature: A01 修改密碼
  As an 員工
  I want to 用舊密碼驗證後更新新密碼
  So that 我能維持帳號安全

  Background:
    Given 修改密碼頁顯示用戶帳號、原密碼、新密碼與驗證密碼欄位
    And 畫面提供確定按鈕與取消按鈕

  Scenario: 以正確舊密碼成功修改新密碼
    Given 員工已登入並進入修改密碼頁
    When 員工輸入正確的舊密碼
    And 員工輸入兩次一致的新密碼
    And 員工按下確定
    Then 系統更新密碼
    And 舊密碼不得再登入

  Scenario: 舊密碼錯誤時不得更新密碼
    Given 員工已登入並進入修改密碼頁
    When 員工輸入錯誤的舊密碼後按下確定
    Then 畫面顯示錯誤訊息
    And 系統不得更新密碼

  Scenario: 兩次新密碼不一致時不得更新密碼
    Given 員工已登入並進入修改密碼頁
    When 員工輸入兩次不同的新密碼後按下確定
    Then 畫面顯示錯誤訊息
    And 系統不得更新密碼

  Scenario: 頁面保留送出結果訊息
    Given 員工剛送出修改密碼表單
    When 系統驗證失敗並回到修改密碼頁
    Then 錯誤訊息會顯示在密碼表單下方
