# CSKM 重建資料模型設計

> 目標：把舊系統分散在 JSP / Controller / Model 中的資料概念，整理成適合前後端分離與 Java 21 後端重建的核心資料模型。

---

## 1. 設計原則

1. **先整理子域，再整理資料表**
2. **把角色權限、主檔、交易資料、流程資料分開**
3. **把狀態碼與流程紀錄拆開，不把所有歷史塞進主表**
4. **支援附件、批次、訊息與稽核**

---

## 2. 子域切分

| 子域 | 範圍 | 對應舊功能 |
|---|---|---|
| Identity & Access | 帳號、角色、menu 權限、資料範圍 | Login、F02、F05 |
| Organization Master | 區域、部門、職稱 | F00、F01 |
| Academic Master | 機構、課程、科目、教室 | C00、C01、C02、C03、C05 |
| CRM / Lead | 電訪名單、派發、電訪紀錄 | B00、B01、F04 |
| Student Lifecycle | 學員、學顧、服務紀錄、修課狀態 | B02、C04、C046 |
| Workflow Forms | 請假、簽呈、公告、請款等申請單 | A03、A04、A05、A06、D00、D02、E02、E03 |
| Reporting | 日報、週報、報表批核 | A02、D03、E01 |
| Finance | 繳費、繳費項目、財務確認、退回 | E00、E01、E02、E03 |
| File & Message | 附件、通知、訊息中心 | login、msglist、PDF、Excel |

---

## 3. 核心 Aggregate

## 3.1 Identity & Access

### `staff`

來源參考：

- `hr_management`
- `Hr.java`

核心欄位：

- `staff_id`
- `staff_no`
- `name`
- `abbr`
- `region_id`
- `department_id`
- `title_id`
- `join_date`
- `leave_date`
- `status`

### `staff_role`

- `staff_id`
- `role_code`
- `effective_from`
- `effective_to`

### `staff_permission`

- `staff_id`
- `menu_code`
- `can_view`
- `can_edit`
- `data_scope`

### `password_history`

- `staff_id`
- `password_hash`
- `changed_at`

---

## 3.2 Organization Master

### `region`

來源參考：`Branch.java`

- `region_id`
- `region_name`
- `region_english_name`
- `abbr`
- `status`

### `department`

來源參考：`Dept.java`

- `department_id`
- `region_id`
- `department_no`
- `department_name`
- `status`

### `title`

來源參考：`Title.java`

- `title_id`
- `region_id`
- `department_id`
- `title_no`
- `title_name`
- `status`

---

## 3.3 Academic Master

### `institute`

來源參考：`Institute.java`

- `institute_id`
- `region_id`
- `institute_no`
- `institute_name`
- `status`

### `course`

來源參考：`Course.java`

- `course_id`
- `course_no`
- `course_name`
- `institute_id`
- `status`

### `subject`

- `subject_id`
- `course_id`
- `subject_name`
- `sequence`
- `status`

### `classroom`

- `classroom_id`
- `region_id`
- `classroom_name`
- `capacity`
- `status`

---

## 3.4 CRM / Lead

### `lead`

- `lead_id`
- `name`
- `gender`
- `phone`
- `mobile`
- `email`
- `education_level`
- `source_code`
- `region_id`
- `assigned_staff_id`
- `status`
- `created_by`
- `created_at`

### `lead_assignment_history`

- `lead_id`
- `from_staff_id`
- `to_staff_id`
- `assigned_at`
- `assigned_by`

### `interview_record`

- `interview_id`
- `lead_id`
- `staff_id`
- `interview_date`
- `result_code`
- `content`
- `next_contact_date`

---

## 3.5 Student Lifecycle

### `student`

來源參考：`Student.java`、`C046.jsp`

- `student_id`
- `student_no`
- `name`
- `gender`
- `region_id`
- `phone`
- `mobile`
- `fax`
- `address`
- `birth_date`
- `email`
- `company_name`
- `title_name`
- `source_code`
- `level_code`
- `advisor_staff_id`
- `status`

### `student_course`

- `student_course_id`
- `student_id`
- `course_id`
- `status`
- `joined_at`
- `finished_at`

### `student_service_record`

- `service_record_id`
- `student_id`
- `staff_id`
- `service_type`
- `content`
- `service_date`

### `student_payment_item`

- `payment_item_id`
- `student_id`
- `course_id`
- `item_code`
- `amount`
- `currency`
- `due_date`

---

## 3.6 Workflow Forms

### 統一建議：`application`

舊系統雖有不同表單，但重建時建議共用主幹欄位，再以 subtype 擴充。

### `application`

- `application_id`
- `application_type`
  - `LEAVE_REQUEST`
  - `PETITION`
  - `ANNOUNCEMENT`
  - `INVOICE_REQUEST`
  - `REIMBURSEMENT`
- `application_no`
- `applicant_staff_id`
- `region_id`
- `department_id`
- `title`
- `content`
- `current_status`
- `submitted_at`
- `cancelled_at`
- `created_at`
- `updated_at`

### `application_leave_detail`

- `application_id`
- `leave_type`
- `start_date`
- `end_date`
- `hours`
- `reason`

### `application_announcement_detail`

- `application_id`
- `publish_from`
- `publish_to`
- `reference_application_id`

### `application_reimbursement_detail`

- `application_id`
- `amount`
- `currency`
- `expense_type`
- `payment_method`
- `remark`

---

## 3.7 Approval Workflow

### `approval_task`

- `approval_task_id`
- `application_type`
- `application_id`
- `step_code`
- `assignee_role`
- `assignee_staff_id`
- `status`
- `due_at`

### `approval_action_log`

- `approval_action_log_id`
- `approval_task_id`
- `action`
  - `SUBMIT`
  - `APPROVE`
  - `REJECT`
  - `CANCEL`
  - `BATCH_APPROVE`
  - `BATCH_REJECT`
- `actor_staff_id`
- `comment`
- `acted_at`

---

## 3.8 Reporting

### `report`

- `report_id`
- `report_no`
- `report_type`
  - `DAILY`
  - `WEEKLY`
- `staff_id`
- `staff_type`
  - `SALES`
  - `ADMIN`
- `period_start`
- `period_end`
- `subject`
- `content`
- `current_status`
- `submitted_at`

### `report_approval_task`

- `report_approval_task_id`
- `report_id`
- `step_code`
- `assignee_role`
- `status`

---

## 3.9 Finance

### `payment`

來源參考：`Payment_info.java`

- `payment_id`
- `student_id`
- `institute_id`
- `course_id`
- `payment_date`
- `currency`
- `amount`
- `finance_status`
- `academic_status`
- `payment_note`
- `remark`

### `payment_rejection`

來源參考：`Payment_info_Rej.java`

- `payment_rejection_id`
- `payment_id`
- `rejected_by`
- `reason`
- `rejected_at`

### `reimbursement`

- `reimbursement_id`
- `application_id`
- `amount`
- `currency`
- `finance_status`
- `return_reason`
- `confirmed_at`

---

## 3.10 File & Message

### `attachment`

- `attachment_id`
- `owner_type`
- `owner_id`
- `file_name`
- `content_type`
- `storage_path`
- `uploaded_by`
- `uploaded_at`

### `system_message`

- `message_id`
- `message_type`
- `target_staff_id`
- `title`
- `content`
- `is_read`
- `created_at`

### `audit_log`

- `audit_log_id`
- `actor_staff_id`
- `action`
- `target_type`
- `target_id`
- `before_json`
- `after_json`
- `created_at`

---

## 4. 關聯總覽

1. `region` 1:N `department`
2. `department` 1:N `title`
3. `region` 1:N `institute`
4. `institute` 1:N `course`
5. `course` 1:N `subject`
6. `staff` 1:N `staff_role`
7. `staff` 1:N `staff_permission`
8. `lead` 1:N `interview_record`
9. `student` 1:N `student_service_record`
10. `student` N:M `course`（透過 `student_course`）
11. `application` 1:N `approval_task`
12. `report` 1:N `report_approval_task`
13. `payment` 1:N `payment_rejection`
14. `application` / `report` / `payment` 1:N `attachment`

---

## 5. 狀態欄位設計原則

避免沿用舊系統 `1~7` 難以辨識的狀態碼，建議改為 enum：

- `DRAFT`
- `SUBMITTED`
- `MANAGER_APPROVED`
- `MANAGER_REJECTED`
- `CEO_APPROVED`
- `CEO_REJECTED`
- `CANCELLED`
- `COMPLETED`

同時保留：

- `status_changed_at`
- `status_changed_by`

---

## 6. 舊系統到重建模型的映射建議

| 舊概念 | 重建概念 |
|---|---|
| `hr_management` | `staff` + `staff_role` + `staff_permission` |
| `menu` | `staff_permission` / `permission_group` |
| `payment_info` | `payment` |
| 多種表單主檔 | `application` + type-specific detail |
| 各頁批核欄位 | `approval_task` + `approval_action_log` |
| 報表審核欄位 | `report` + `report_approval_task` |

---

## 7. 一句話結論

**重建時不應再以 JSP 頁面為單位切資料，而應以人員權限、主檔、學員生命周期、申請批核、報表、財務六大 Aggregate 群重新建立資料模型。**
