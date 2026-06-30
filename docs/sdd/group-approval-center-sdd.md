# 申請 / 批核中心 SDD

## 對應 Story

- A03-1、A03-2、A05-1、A06-1、A06-2
- 補充實作流程：D00、D02、D03、D04、E02、E03

## 畫面與路由

- `/A/A03`、`/A/A03/AjaxSearchDefault`、`/A/A03/AjaxSearch`、`/A/A03/AjaxDelete`
- `/A/A05`、`/A/A05/AjaxSearchDefault`、`/A/A05/AjaxSearch`、`/A/A05/AjaxDelete`
- `/A/A06`、`/A/A06/AjaxSearchDefault`、`/A/A06/AjaxSearch`、`/A/A06/AjaxDelete`、`/A/A06/AjaxSignedData`
- `/D/D00`、`/D/D000`
- `/D/D02`、`/D/D020`
- `/D/D03`、`/D/D030~D033`
- `/D/D04`、`/D/D040`
- `/E/E02`、`/E/E03`、`/E/E03/Finance`、`/E/E03/returnBack`

## 流程設計

1. A03 / A05 / A06 建立主單據
2. 入口頁提供歷史列表與快捷編號
3. 單據送審後進入 D00 / D02 / D03 等批核管理頁
4. E02 / E03 負責請款單查詢、財務確認、退件
5. D04 作為學員意見管理與明細檢視

## 狀態與規則

- 單據需支援草稿、送審、核准、退回、取消
- 公告需有效日期控制
- 請款退件必須帶退件原因

## 資料

- `form`
- `invoice`
- `announcement`
- 請款相關資料表
- 審核意見與狀態欄位

## 權限

- 建立者：新增、修改、送審
- 主管 / CEO：批核或退回
- 財務：E03 確認與退件

## 特殊注意

- D00 實作對應請假單管理 / 批核；A04 簽呈流程另以獨立文件描述
- A02 / D03 的報表流程需視為「報表建立 + 批核」雙段式流程
