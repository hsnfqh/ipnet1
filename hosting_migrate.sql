-- =============================================================
-- SAFE PATCH SQL - IPNET WORKFORCE
-- Jalankan di phpMyAdmin / MySQL hosting
-- Semua pakai IF NOT EXISTS / IGNORE, AMAN dijalankan berulang
-- =============================================================

-- ---------------------------------------------------------------
-- 1. TABEL TASKS: Tambah kolom start_date (WAJIB - penyebab error)
-- ---------------------------------------------------------------
ALTER TABLE `tasks`
    ADD COLUMN IF NOT EXISTS `start_date` DATE NULL AFTER `status`;

-- Tambah kolom lain yang mungkin belum ada di hosting
ALTER TABLE `tasks`
    ADD COLUMN IF NOT EXISTS `deadline_time` TIME NULL AFTER `deadline`;

ALTER TABLE `tasks`
    ADD COLUMN IF NOT EXISTS `doc_file` VARCHAR(255) NULL AFTER `description`;

ALTER TABLE `tasks`
    ADD COLUMN IF NOT EXISTS `deleted_at` TIMESTAMP NULL AFTER `updated_at`;

-- Isi start_date dari deadline untuk data lama yang masih NULL
UPDATE `tasks` SET `start_date` = DATE(`deadline`) WHERE `start_date` IS NULL AND `deadline` IS NOT NULL;

-- ---------------------------------------------------------------
-- 2. TABEL SCHEDULES: Kolom opsional
-- ---------------------------------------------------------------
ALTER TABLE `schedules`
    ADD COLUMN IF NOT EXISTS `category` VARCHAR(100) NULL DEFAULT 'Meeting' AFTER `project_id`;

ALTER TABLE `schedules`
    ADD COLUMN IF NOT EXISTS `deleted_at` TIMESTAMP NULL AFTER `updated_at`;

-- Buat start_time / end_time nullable (jika masih NOT NULL)
ALTER TABLE `schedules`
    MODIFY COLUMN `start_time` TIME NULL;

ALTER TABLE `schedules`
    MODIFY COLUMN `end_time` TIME NULL;

-- ---------------------------------------------------------------
-- 3. TABEL task_user (multi-assignee)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `task_user` (
    `task_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`task_id`, `user_id`),
    KEY `task_user_user_id_index` (`user_id`),
    CONSTRAINT `task_user_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
    CONSTRAINT `task_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- ---------------------------------------------------------------
-- 4. TABEL schedule_user (multi-assignee schedule)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `schedule_user` (
    `schedule_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`schedule_id`, `user_id`),
    KEY `schedule_user_user_id_index` (`user_id`),
    CONSTRAINT `schedule_user_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE,
    CONSTRAINT `schedule_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- ---------------------------------------------------------------
-- 5. TABEL project_documents
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_documents` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NULL,
    `document_type` VARCHAR(100) NULL,
    `notes` TEXT NULL,
    `uploaded_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    KEY `project_documents_project_id_index` (`project_id`),
    CONSTRAINT `project_documents_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
);

-- ---------------------------------------------------------------
-- 6. TABEL PROJECTS: Kolom tambahan (satu per satu agar tidak error jika sudah ada)
-- ---------------------------------------------------------------
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `stage` VARCHAR(50) NULL DEFAULT 'Deliver' AFTER `status`;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `process_status` VARCHAR(100) NULL DEFAULT 'In Progress';
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `pm_id` BIGINT UNSIGNED NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `bdm_id` BIGINT UNSIGNED NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `documents_checklist` JSON NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `sla_tier` VARCHAR(50) NULL DEFAULT 'Bronze';
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `service_start_date` DATE NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `service_end_date` DATE NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `maintenance_frequency` VARCHAR(100) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `sla_coverage_hours` VARCHAR(100) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `progress` INT NOT NULL DEFAULT 0;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `acquire_status` VARCHAR(100) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `opportunity_source` VARCHAR(255) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `business_need_summary` TEXT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `stakeholders_data` JSON NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `initial_requirement` TEXT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `target_timeline_type` VARCHAR(100) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `competitor_analysis` TEXT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `partner_alignment` TEXT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `bd_assessment_score` INT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `sales_stage` VARCHAR(100) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `win_probability` INT NULL DEFAULT 0;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `expected_closing_date` DATE NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `lost_reason` TEXT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `lost_competitor` VARCHAR(255) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `quotation_number` VARCHAR(100) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `quotation_amount` DECIMAL(18,2) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `quotation_file` VARCHAR(500) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `po_spk_number` VARCHAR(100) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `po_spk_date` DATE NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `po_spk_file` VARCHAR(500) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `billing_terms` TEXT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `commercial_terms` TEXT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `sla_commitment` TEXT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `special_commitment` TEXT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `exclusions` TEXT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `customer_pic_technical` VARCHAR(255) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `customer_pic_business` VARCHAR(255) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `customer_pic_finance` VARCHAR(255) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `handover_status` VARCHAR(50) NULL DEFAULT 'Draft';
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `handover_target` VARCHAR(50) NULL DEFAULT 'pmo';
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `handover_data` JSON NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `handover_submitted_at` TIMESTAMP NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `handover_approved_at` TIMESTAMP NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `handover_approved_by` BIGINT UNSIGNED NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `handover_conditional_notes` TEXT NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `handover_conditional_deadline` DATE NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `commercial_handover_status` VARCHAR(50) NULL DEFAULT 'Draft';
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `commercial_handover_at` TIMESTAMP NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `commercial_handover_by` BIGINT UNSIGNED NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `ms_handover_status` VARCHAR(50) NULL DEFAULT 'Draft';
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `ms_accepted_at` TIMESTAMP NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `ms_accepted_by` BIGINT UNSIGNED NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `bdm_handover_status` VARCHAR(50) NULL DEFAULT 'Draft';
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `bdm_handover_at` TIMESTAMP NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `handover_document_file` VARCHAR(500) NULL;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `special_notes` TEXT NULL;

-- ---------------------------------------------------------------
-- 7. DAFTARKAN MIGRASI ke tabel migrations (agar php artisan tidak re-run)
-- ---------------------------------------------------------------
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2026_08_31_000000_create_task_user_and_schedule_user_tables', 10),
('2026_09_02_000000_add_deadline_time_to_tasks_table', 10),
('2026_09_03_000000_add_category_to_schedules_table', 10),
('2026_09_03_000001_make_times_nullable_in_schedules_table', 10),
('2026_09_03_000002_make_project_id_nullable_in_schedules_table', 10),
('2026_09_03_100000_add_pmo_and_stage_to_projects_table', 10),
('2026_09_03_110000_add_acquire_fields_to_projects_table', 10),
('2026_09_04_141500_add_presales_fields_to_projects_table', 10),
('2026_09_11_100000_add_handover_workflow_fields_to_projects_table', 10),
('2026_09_11_110000_create_bdm_module_tables', 10),
('2026_09_11_120000_create_sales_crm_tables', 10),
('2026_09_11_130000_create_managed_service_tables', 10),
('2026_09_14_100000_expand_financial_decimal_columns', 10),
('2026_09_14_120000_create_cro_module_tables', 10),
('2026_09_14_160000_create_admin_support_tables', 10),
('2026_09_15_170000_add_progress_to_projects_table', 10),
('2026_09_16_092424_add_sla_tier_to_projects_table', 10),
('2026_09_22_100000_add_handover_types_and_ms_fields_to_projects_table', 10),
('2026_09_22_110000_create_project_documents_table', 10),
('2026_09_22_233126_add_start_date_to_tasks_table', 10);

-- =============================================================
-- SELESAI - Database hosting siap digunakan tanpa error!
-- =============================================================
