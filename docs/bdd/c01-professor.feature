Feature: C01 師資管理
  As a 教務人員 / 管理員
  I want to 管理師資基本資料
  So that 課程安排時能選用正確師資

  Background:
    Given 系統已有區域「台北」及課程「英語初級」
    And 管理員已登入

  Scenario: C01-1 瀏覽師資列表
    When 使用者進入 C01 師資管理頁面
    Then 系統顯示所有師資的姓名、電話、專長及狀態

  Scenario: C01-2 新增師資資料
    When 管理員點擊「新增師資」
    And 填入姓名「王小明」、電話「0912-345678」、專長「英語教學」
    And 點擊「儲存」
    Then 系統新增師資並顯示成功提示
    And 師資列表出現「王小明」

  Scenario: C01-2 上傳師資照片
    Given 師資「王小明」已存在
    When 管理員進入師資明細，點擊「上傳照片」
    And 選擇圖片檔案並確認上傳
    Then 系統儲存照片路徑並顯示師資照片

  Scenario: C01-2 非管理員不可新增師資
    Given 一般員工已登入
    When 嘗試新增師資
    Then 系統回傳 403 禁止存取
