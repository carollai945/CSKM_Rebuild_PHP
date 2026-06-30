# D04 學員意見管理 TDD 設計

## 對應

- BDD：`bdd/d04-student-feedback.feature`
- SDD：`sdd/d04-student-feedback-sdd.md`

## 測試主題

- D04 列表查詢與條件篩選
- D040 明細檢視
- 角色權限過濾（一般使用者 / 主管 / CEO）

## 代表案例

### Service 測試

| 測試 ID | 測試描述 |
|---|---|
| `D04-UNIT-01` | `D04_ajaxSearch_shouldFilterSuggestions`：以日期區間查詢時僅回傳區間內資料 |
| `D04-UNIT-02` | 以狀態篩選時僅回傳符合狀態的意見 |
| `D04-UNIT-03` | 以人員姓名關鍵字篩選時僅回傳符合人員的意見 |
| `D04-UNIT-04` | 主管或 CEO 可查詢所有區域資料 |
| `D04-UNIT-05` | 一般使用者查詢時後端自動依授權區域過濾，不得查看其他區域資料 |
| `D04-UNIT-06` | 查詢明細時回傳意見內容與回覆紀錄 |
| `D04-UNIT-07` | 查詢不存在的意見 ID 時拋出 `FEEDBACK_NOT_FOUND` |

### Controller 測試

| 測試 ID | 測試描述 |
|---|---|
| `D04-INT-01` | 主管呼叫 GET `/api/v1/student-feedbacks` 可取得所有區域資料 |
| `D04-INT-02` | 一般員工查詢時僅能取得授權區域資料 |
| `D04-INT-03` | 以日期條件查詢後回傳符合結果 |
| `D04-INT-04` | 查詢不存在的意見 ID 時回傳 404 |

## Workflow

- 新增意見 → 待處理
- 服務人員進入 D040 追蹤明細
- 主管可查看跨區域統計
