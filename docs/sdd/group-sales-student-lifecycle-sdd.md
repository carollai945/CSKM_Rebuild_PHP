# 業務招生與學員服務 SDD

## 對應 Story

- B00-1、B01-1、B02-1、C04-1
- 補充前端子流程：B020~B023、C041~C046

## 畫面與路由

- `/B/B00`、`/B/B00/AjaxB00Model`、`/B/B00/AjaxSearch`、`/B/B00/Assign_Forward`、`/B/B00/Delete`、`/B/B00/New`
- `/B/B01`、`/B/B01/AjaxSearch`
- `/B/B02`、`/B/B02/AjaxSearch`、`/B/B02/getCrs`
- `/C/C04`、`/C/C04/search`、`/C/C04/changeSS`、`/C/C04/getOrg`、`/C/C04/getHrmang`、`/C/C04/getCrs`

## 流程設計

1. B00 建立與搜尋招生名單
2. B01 追蹤電訪進度
3. B02 追蹤學員狀態、服務、繳費與課程狀態
4. C04 管理學員與學顧關係，必要時透過 popup 重新指派

## 權限與資料範圍

- CEO：跨區域查詢與指派
- 區域主管：所屬區域管理
- 一般學顧：僅處理自己負責名單

## 資料

- 潛在學員 / 學員主檔
- 學顧指派欄位
- 服務記錄、繳費記錄、課程狀態

## 前端互動

- 多條件 AjaxSearch
- 下拉篩選
- 學顧更換 popup
- 列表頁進子頁編修
