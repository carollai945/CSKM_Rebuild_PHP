# C03 教室管理 TDD 設計

## 1. 對應需求

- Story：`C03-1`
- 對應 BDD：`bdd/c03-classroom-management.feature`
- 對應 SDD：`sdd/c03-classroom-management-sdd.md`

## 2. 測試分層

### Unit（ClassroomServiceTest）

- 依區域查詢教室回傳正確清單
- 不帶區域查詢時回傳全部教室
- 新增教室成功並預設狀態為 `ACTIVE`
- 新增教室時無教務權限應丟出 `CLASSROOM_MANAGEMENT_FORBIDDEN`
- 新增教室時區域不存在應丟出 `REGION_NOT_FOUND`
- 修改教室成功並持久化
- 修改不存在教室應丟出 `CLASSROOM_NOT_FOUND`
- 修改時無教務權限應丟出 `CLASSROOM_MANAGEMENT_FORBIDDEN`
- 刪除教室成功
- 刪除不存在教室應丟出 `CLASSROOM_NOT_FOUND`
- 刪除時無教務權限應丟出 `CLASSROOM_MANAGEMENT_FORBIDDEN`

### Integration（ClassroomControllerTest）

- `C03_search_shouldReturnClassroomsByRegion`：依區域查詢教室
- `listClassroomsWithoutRegionFilterReturnsAll`：不帶區域查詢全部
- `academicUserCanCreateClassroom`：教務人員可新增教室
- `nonAcademicUserCannotCreateClassroom`：非教務人員不可新增
- `updateClassroomShouldPersistChanges`：修改教室成功
- `deleteClassroomShouldReturn204`：刪除教室回傳 204
- `updateNonExistentClassroomShouldReturn404`：修改不存在教室回傳 404
- `createClassroomWithInvalidRegionShouldReturn404`：新增時區域不存在回傳 404
- `createClassroomWithoutCapacityShouldReturn400`：容量缺漏回傳 400

### Frontend Unit（classroom.spec.ts）

- `fetchClassrooms` 不帶參數時呼叫 `GET /api/v1/classrooms`
- `fetchClassrooms` 帶 `regionId` 時附加查詢參數
- `createClassroom` 呼叫 `POST /api/v1/classrooms`
- `updateClassroom` 呼叫 `PUT /api/v1/classrooms/:id`
- `deleteClassroom` 呼叫 `DELETE /api/v1/classrooms/:id`

## 3. 測試案例清單

| ID | 層級 | 測試名稱 | 目的 |
|---|---|---|---|
| C03-UNIT-01 | Unit | `C03_search_shouldReturnClassroomsByRegion` | 依區域篩選教室 |
| C03-UNIT-02 | Unit | `listClassrooms_shouldReturnAllWhenRegionIdIsNull` | 全部教室查詢 |
| C03-UNIT-03 | Unit | `createClassroom_shouldSucceedForAcademicUser` | 教務新增成功 |
| C03-UNIT-04 | Unit | `createClassroom_shouldDefaultStatusToActive` | 預設狀態 ACTIVE |
| C03-UNIT-05 | Unit | `createClassroom_shouldFailWhenNotAcademic` | 無權限不得新增 |
| C03-UNIT-06 | Unit | `createClassroom_shouldFailWhenRegionNotFound` | 區域不存在 |
| C03-UNIT-07 | Unit | `updateClassroom_shouldPersistChanges` | 修改成功 |
| C03-UNIT-08 | Unit | `updateClassroom_shouldFailWhenNotFound` | 教室不存在 |
| C03-UNIT-09 | Unit | `updateClassroom_shouldFailWhenNotAcademic` | 無權限不得修改 |
| C03-UNIT-10 | Unit | `deleteClassroom_shouldSucceedForAcademicUser` | 刪除成功 |
| C03-UNIT-11 | Unit | `deleteClassroom_shouldFailWhenNotFound` | 教室不存在 |
| C03-UNIT-12 | Unit | `deleteClassroom_shouldFailWhenNotAcademic` | 無權限不得刪除 |
| C03-INT-01 | Integration | `C03_search_shouldReturnClassroomsByRegion` | 查詢整合 |
| C03-INT-02 | Integration | `academicUserCanCreateClassroom` | 新增整合 |
| C03-INT-03 | Integration | `nonAcademicUserCannotCreateClassroom` | 權限整合 |
| C03-INT-04 | Integration | `updateClassroomShouldPersistChanges` | 修改整合 |
| C03-INT-05 | Integration | `deleteClassroomShouldReturn204` | 刪除整合 |
| C03-FE-01 | Frontend | `fetchClassrooms without regionId` | 前端查詢 API |
| C03-FE-02 | Frontend | `createClassroom with payload` | 前端新增 API |
| C03-FE-03 | Frontend | `updateClassroom with payload` | 前端修改 API |
| C03-FE-04 | Frontend | `deleteClassroom by id` | 前端刪除 API |

## 4. Given / When / Then 範本

- Given：使用者已登入並具教務設定權限
- When：呼叫 POST `/api/v1/classrooms` 帶入區域、名稱與容量
- Then：教室建立成功，回傳 201 並顯示 `status: ACTIVE`

## 5. 邊界案例

- 容量為 0 或負數應回傳 400
- 區域不存在應回傳 404
- 教室不存在時修改或刪除應回傳 404
- 非教務人員執行修改 / 刪除應回傳 403

## 6. 完成標準

- Service 層所有分支均有對應 Unit 測試
- Controller 整合測試涵蓋 BDD 五個情境
- 前端 API 函式均有對應 spec 測試
