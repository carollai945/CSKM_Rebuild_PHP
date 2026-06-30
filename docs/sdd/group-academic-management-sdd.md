# 教務主資料管理 SDD

## 對應 Story

- C00-1、C01-1、C01-2、C02-1、C02-2、C02-3、C03-1、C05-1

## 畫面與路由

- `/C/C00`、`/C/C00/srchOrg`、`/C/C00/srchCrs`、`/C/C00/srchSbj`、`/C/C00/new`、`/C/C00/edit`、`/C/C00/delete`
- `/C/C01`、`/C/C01/search`、`/C/C01/delete`、`/C/C010`、`/C/C010/delFile`
- `/C/C02`、`/C/C02/search`、`/C/C02/delete`、`/C/C02/export`、`/C/C020/new`、`/C/C020/edit`、`/C/C02/getOrg`、`/C/C02/getCrs`、`/C/C02/getSbj`
- `/C/C03`、`/C/C03/search`、`/C/C03/delete`、`/C/C030`
- `/C/C05`、`/C/C05/search`、`/C/C05/new`、`/C/C05/edit`、`/C/C05/delete`、`/C/C05/getOrg`、`/C/C05/getCrs`、`/C/C05/showPreview`

## 設計重點

- C00 / C02 / C05 都有區域→機構→課程連動
- C01 需支援檔案與圖片上傳
- C02 需支援搜尋、維護與 Excel 匯出
- C03 需支援區域別教室維護
- C05 需支援費用類型與預覽

## 資料

- `institute`
- `course`
- `prof_data`
- `classroom`
- 學生主檔
- `item_setting`

## 權限

- 教務 / 管理員為主要角色
- 部分學生資料可由業務角色查詢或維護
