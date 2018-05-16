//Add lastname column to teenager table ## 5-12-2017
ALTER TABLE `pro_t_teenagers` ADD `t_lastname` VARCHAR(50) NULL AFTER `t_name`;


//Add extra new phone field add to teenager table ## 5-12-2017
ALTER TABLE `pro_t_teenagers` ADD `t_phone_new` VARCHAR(15) NULL DEFAULT NULL;

//Add extra new description field to video table ## 7-12-2017
ALTER TABLE `pro_v_video` ADD `v_description` TEXT NULL AFTER `v_photo`;

//Add extra fields to teenager table ## 7-12-2017
ALTER TABLE `pro_t_teenagers` ADD `is_share_with_other_members` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '\"0\" => No, \"1\" => Yes' AFTER `t_phone_new`, ADD `is_share_with_parents` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '\"0\" => No, \"1\" => Yes' AFTER `is_share_with_other_members`, ADD `is_share_with_teachers` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '\"0\" => No, \"1\" => Yes' AFTER `is_share_with_parents`;

//Add new table for Testinomials ## 8-12-2017
CREATE TABLE IF NOT EXISTS `pro_t_testinomials` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `t_name` varchar(255) NOT NULL,
  `t_title` varchar(255) NOT NULL,
  `t_image` varchar(255) NOT NULL,
  `t_description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'timestamp',
  `deleted` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Active , 2 - Inactive, 3 - Deleted ',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

//Add new field to profession table ## 11-12-2017
ALTER TABLE `pro_pf_profession` ADD `pf_profession_tags` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `pf_profession_alias`;

//Add new table for HelpTexts ## 11-12-2017
CREATE TABLE IF NOT EXISTS `pro_h_helptext` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `h_title` varchar(255) NOT NULL,
  `h_slug` varchar(255) NOT NULL,
  `h_description` text NOT NULL,
  `h_page` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'timestamp',
  `deleted` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Active, 2 - Inactive, 3 - Deleted',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

//Add new table for Profession Certifications ## 11-12-2017
CREATE TABLE IF NOT EXISTS `pro_pc_profession_certifications` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `pc_name` varchar(255) NOT NULL,
  `pc_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'timestamp',
  `deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1 - Active, 2 - Inactive, 3 - Deleted',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

//Add new table for Profession Subjects ## 12-12-2017
CREATE TABLE IF NOT EXISTS `pro_ps_profession_subjects` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `ps_name` varchar(255) NOT NULL,
  `ps_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'timestamp',
  `deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1 - Active, 2 - Inactive, 3 - Deleted',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

//Add new field to profession table ## 12-12-2017
ALTER TABLE `pro_pf_profession` ADD `pf_certifications` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `pf_profession_tags`;

//Add new field to profession table ## 12-12-2017
ALTER TABLE `pro_pf_profession` ADD `pf_subjects` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `pf_certifications`;

//Add new field to multi-intelligence table ## 13-12-2017
ALTER TABLE `pro_mit_multiple_intelligence_types` ADD `mi_slug` VARCHAR(255) NOT NULL AFTER `mit_name`;

//Add new field to interest type table ## 13-12-2017
ALTER TABLE `pro_it_interest_types` ADD `it_slug` VARCHAR(255) NOT NULL AFTER `it_name`;

//Add new field to interest type table ## 13-12-2017
ALTER TABLE `pro_it_interest_types` ADD `it_description` TEXT NOT NULL AFTER `it_logo`;

//Add new field to interest type table ## 13-12-2017
ALTER TABLE `pro_it_interest_types` ADD `it_video` VARCHAR(255) NOT NULL AFTER `it_description`;

//Add new field to personality types table ## 14-12-2017
ALTER TABLE `pro_pt_personality_types` ADD `pt_slug` VARCHAR(255) NOT NULL AFTER `pt_name`;

//Add new field to apptitude types table ## 14-12-2017
ALTER TABLE `pro_apt_apptitude_types` ADD `apt_slug` VARCHAR(255) NOT NULL AFTER `apt_name`;

//Adding new filed into testinomials table for managing team CMS ## 18-12-2017 ##
ALTER TABLE `pro_t_testinomials` ADD `t_type` VARCHAR(20) NULL DEFAULT 'testinomials' AFTER `t_description`;

//Adding new field in teenagers table ## 20-12-2017
ALTER TABLE `pro_t_teenagers` ADD `t_view_information` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0 - India, 1 - Other' AFTER `is_share_with_teachers`;

//Make nickname nullable ## 27-12-2017 BD
ALTER TABLE `pro_t_teenagers` CHANGE `t_nickname` `t_nickname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;

//Add new table for Profession Wise Certificates ## 29-12-2017
CREATE TABLE `pro_pwc_professions_wise_certificates` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
 `profession_id` int(11) NOT NULL,
 `certificate_id` int(11) NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 - Active, 2 - Inactive, 3 - Deleted',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1

//Change meta value field text ## 29-12-2017
ALTER TABLE `pro_tmd_teenager_meta_data` CHANGE `tmd_meta_value` `tmd_meta_value` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

//Added new table for manage app versions pro_v_version #02-01-2018 BD
CREATE TABLE `pro_v_versions` (
  `id` int(10) UNSIGNED NOT NULL,
  `force_update` tinyint(1) NOT NULL DEFAULT '0',
  `android_version` int(10) DEFAULT NULL,
  `ios_version` int(10) DEFAULT NULL,
  `web_version` int(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

//Add new table for Profession Wise Subjects ## 02-01-2018
CREATE TABLE `pro_pws_professions_wise_subjects` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' , `profession_id` INT NOT NULL , `subject_id` INT NOT NULL , `parameter_grade` ENUM('H','M','L') NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP NOT NULL , `deleted` TINYINT UNSIGNED NOT NULL DEFAULT '1' COMMENT '1 - Active, 2 - Inactive, 3 - Deleted' , PRIMARY KEY (`id`)) ENGINE = MyISAM;

//Add new table for teenager connections ##02-01-2018
CREATE TABLE IF NOT EXISTS `pro_tc_teen_connections` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `tc_sender_id` bigint(20) UNSIGNED NOT NULL,
  `tc_receiver_id` bigint(20) UNSIGNED NOT NULL,
  `tc_read_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - Unread, 1 - Read',
  `tc_status` int(1) NOT NULL DEFAULT '0' COMMENT '0 - Pending, 1 - Accept, 2 - Reject',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

//Add new table for Profession Tags ##03-01-2018
CREATE TABLE IF NOT EXISTS `pro_pt_profession_tags` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `pt_name` varchar(255) NOT NULL,
  `pt_image` varchar(255) NOT NULL,
  `pt_description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'timestamp',
  `deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1 - Active, 2 - Inactive, 3 - Deleted',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

//Add new table for Profession Wise Tags ## 03-01-2018
CREATE TABLE `pro_pwt_professions_wise_tags` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' , `profession_id` INT NOT NULL , `tag_id` INT NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP NOT NULL , `deleted` TINYINT UNSIGNED NOT NULL DEFAULT '1' COMMENT '1 - Active, 2 - Inactive, 3 - Deleted' , PRIMARY KEY (`id`)) ENGINE = MyISAM;

//Drop versions tables. We made new table for that
DROP TABLE `pro_v_versions`;

//Added new table for manage app versions pro_av_app_versions #03-01-2018 BD
CREATE TABLE `pro_av_app_versions` (
  `id` int(11) NOT NULL,
  `force_update` tinyint(1) NOT NULL DEFAULT '0',
  `device_type` tinyint(1) DEFAULT NULL,
  `message` text,
  `app_version` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `pro_av_app_versions` (`id`, `force_update`, `device_type`, `message`, `app_version`, `created_at`, `updated_at`) VALUES
(1, 0, 1, 'Success', '1', '2018-01-03 11:33:07', '2018-01-03 12:49:13'),
(2, 0, 2, 'Success', '1', '2018-01-03 11:33:47', '2018-01-03 12:49:21');

ALTER TABLE `pro_av_app_versions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pro_av_app_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

//Alter table pro_pf_profession for Alias field make nullable for ## 03-01-2018
ALTER TABLE `pro_pf_profession` CHANGE `pf_profession_alias` `pf_profession_alias` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

//Alter table pro_pf_profession to Remove tags, certification and subjects field ## 03-01-2018
ALTER TABLE `pro_pf_profession`
  DROP `pf_profession_tags`,
  DROP `pf_certifications`,
  DROP `pf_subjects`;

//Alter table pro_pfic_profession_intro_content add Country Id field ## 03-01-2018
ALTER TABLE `pro_pfic_profession_intro_content` ADD `country_id` INT NULL DEFAULT '1' AFTER `pfic_content`;

// Add new field in teenager connection request table ## 03/01/2018
ALTER TABLE `pro_tc_teen_connections` ADD `tc_unique_id` VARCHAR(23) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `id`, ADD UNIQUE (`tc_unique_id`);

//Add new field in teenagers table ## 06/01/2018
ALTER TABLE `pro_t_teenagers` ADD `t_about_info` TEXT NULL AFTER `t_view_information`;

//Add new field in pro_l2ac_level2_activities table ## 09-jan-2018
ALTER TABLE `pro_l2ac_level2_activities` ADD `section_type` TINYINT(4) NOT NULL DEFAULT '1' AFTER `l2ac_image`;

//Added new table for manage traits question #12-01-2018 Jaimin
CREATE TABLE `pro_tqq_traits_quality_activity` (
 `id` bigint(20) NOT NULL AUTO_INCREMENT,
 `tqq_text` text NOT NULL,
 `tqq_image` varchar(255) DEFAULT NULL,
 `tqq_points` int(10) DEFAULT '0',
 `tqq_active_date` date NOT NULL,
 `tqq_is_multi_select` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - Single Select, 1 - Multiselect',
 `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted` tinyint(1) DEFAULT '1' COMMENT ' 1 - Active , 2 - Inactive, 3 - Deleted',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1

//Added new table for manage traits question's option #12-01-2018 Jaimin
CREATE TABLE `pro_tqo_traits_quality_options` (
 `id` bigint(20) NOT NULL AUTO_INCREMENT,
 `tqq_id` bigint(20) NOT NULL,
 `tqo_option` varchar(255) NOT NULL,
 `tqo_is_true` tinyint(1) DEFAULT '1' COMMENT '0 - False, 1 - True',
 `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted` tinyint(1) DEFAULT '1' COMMENT ' 1 - Active , 2 - Inactive, 3 - Deleted',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1

//Added new table for manage traits answer #12-01-2018 Jaimin
CREATE TABLE `pro_tqa_traits_quality_answer` (
 `id` bigint(20) NOT NULL AUTO_INCREMENT,
 `tqq_id` bigint(20) NOT NULL,
 `tqo_id` bigint(20) NOT NULL,
 `tqa_from` bigint(20) NOT NULL,
 `tqa_to` bigint(20) NOT NULL,
 `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 `deleted` tinyint(1) DEFAULT '1' COMMENT '1 - Active , 2 - Inactive, 3 - Deleted',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1

//Added new field in profession table for unique slug #17-01-2018 Jaimin
ALTER TABLE `pro_pf_profession` ADD `pf_slug` VARCHAR(255) NULL DEFAULT NULL AFTER `pf_profession_alias`, ADD UNIQUE `unique_slug` (`pf_slug`);

//Add new table ## 18-01-2018
CREATE TABLE IF NOT EXISTS `pro_srp_star_rated_professions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `srp_teenager_id` bigint(20) NOT NULL,
  `srp_profession_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'timestamp',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

//Make t_phone field nullable in teenagers table ## 18-01-2018
ALTER TABLE `pro_t_teenagers` CHANGE `t_phone` `t_phone` VARCHAR(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

//Added new field in Profession Tags table for unique slug #23-01-2018 Jaimin
ALTER TABLE `pro_pt_profession_tags` ADD `pt_slug` VARCHAR(255) NOT NULL AFTER `pt_description`;

//Alter pfic_content field in pro_pfic_profession_intro_content table make Nullable #25-01-2018 Jaimin
ALTER TABLE `pro_pfic_profession_intro_content` CHANGE `pfic_content` `pfic_content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

//Increase t_location column size in teenager table ##26-01-2017
ALTER TABLE `pro_t_teenagers` CHANGE `t_location` `t_location` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

//Add new table to store user's matched profession scale
CREATE TABLE `pro_upms_user_profession_match_scale` (
  `id` int(11) NOT NULL,
  `teenager_id` int(11) DEFAULT NULL,
  `match_scale` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `pro_upms_user_profession_match_scale`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `pro_upms_user_profession_match_scale`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

//Added new table to store Teenager's promise score
CREATE TABLE `pro_teenager_promise_score` (
  `id` int(11) NOT NULL,
  `teenager_id` int(11) DEFAULT NULL,
  `apt_scientific_reasoning` varchar(100) DEFAULT '0',
  `apt_verbal_reasoning` varchar(100) DEFAULT '0',
  `apt_numerical_ability` varchar(100) DEFAULT '0',
  `apt_logical_reasoning` varchar(100) DEFAULT '0',
  `apt_social_ability` varchar(100) DEFAULT '0',
  `apt_artistic_ability` varchar(100) DEFAULT '0',
  `apt_spatial_ability` varchar(100) DEFAULT '0',
  `apt_creativity` varchar(100) DEFAULT '0',
  `apt_clerical_ability` varchar(100) DEFAULT '0',
  `it_people` varchar(100) DEFAULT '0',
  `it_nature` varchar(100) DEFAULT '0',
  `it_technical` varchar(100) DEFAULT '0',
  `it_creative_fine_arts` varchar(100) DEFAULT '0',
  `it_numerical` varchar(100) DEFAULT '0',
  `it_computers` varchar(100) DEFAULT '0',
  `it_research` varchar(100) DEFAULT '0',
  `it_performing_arts` varchar(100) DEFAULT '0',
  `it_social` varchar(100) DEFAULT '0',
  `it_sports` varchar(100) DEFAULT '0',
  `it_language` varchar(100) DEFAULT '0',
  `it_artistic` varchar(100) DEFAULT '0',
  `it_musical` varchar(100) DEFAULT '0',
  `mit_interpersonal` varchar(100) DEFAULT '0',
  `mit_logical` varchar(100) DEFAULT '0',
  `mit_linguistic` varchar(100) DEFAULT '0',
  `mit_intrapersonal` varchar(100) DEFAULT '0',
  `mit_musical` varchar(100) DEFAULT '0',
  `mit_spatial` varchar(100) DEFAULT '0',
  `mit_bodilykinesthetic` varchar(100) DEFAULT '0',
  `mit_naturalist` varchar(100) DEFAULT '0',
  `mit_existential` varchar(100) DEFAULT '0',
  `pt_conventional` varchar(100) DEFAULT '0',
  `pt_enterprising` varchar(100) DEFAULT '0',
  `pt_investigative` varchar(100) DEFAULT '0',
  `pt_social` varchar(100) DEFAULT '0',
  `pt_artistic` varchar(100) DEFAULT '0',
  `pt_mechanical` varchar(100) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `pro_teenager_promise_score`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `pro_teenager_promise_score`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

//Remove Old Notification Table ##29-01-2018 Jaimin
DROP TABLE pro_n_notifications

//Added new Notification Table ##29-01-2018 Jaimin
CREATE TABLE `pro_n_notifications` (
 `id` bigint(20) NOT NULL AUTO_INCREMENT,
 `n_sender_id` bigint(20) NOT NULL,
 `n_sender_type` tinyint(4) NOT NULL COMMENT '1 - Admin, 2 - Teenager',
 `n_receiver_id` bigint(20) NOT NULL,
 `n_receiver_type` tinyint(4) NOT NULL COMMENT '1 - Admin, 2 - Teenager',
 `n_record_id` bigint(20) NOT NULL DEFAULT '0',
 `n_notification_text` text NOT NULL,
 `n_notification_type` tinyint(4) NOT NULL COMMENT '1 - Procoins Gift, 2 - Coupan Gift, 3 - Connection Request, 4 - Profile View',
 `n_read_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - Not Read, 1 - Read',
 `created_at` timestamp NOT NULL,
 `updated_at` timestamp NOT NULL,
 `deleted` tinyint(4) DEFAULT '1' COMMENT '1 - Active , 2 - Inactive, 3 - Deleted ',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1

//Added new field in profession subject table ## 31-01-2017
ALTER TABLE `pro_ps_profession_subjects` ADD `ps_slug` VARCHAR(255) NOT NULL AFTER `ps_name`;

//Added new field in cartoon icons table ## 05-02-2017
ALTER TABLE `pro_ci_cartoon_icons` ADD `ci_description` TEXT NULL AFTER `ci_image`;

//Added new field in human icons table ## 06-02-2017
ALTER TABLE `pro_hi_human_icons` ADD `hi_description` TEXT NULL AFTER `hi_image`;

//Added new table to store max value of PROMISE parameters
CREATE TABLE `pro_promise_parameters_max_score` (
  `id` int(10) UNSIGNED NOT NULL,
  `parameter_slug` varchar(255) NOT NULL,
  `parameter_name` varchar(255) NOT NULL,
  `parameter_max_score` int(11) UNSIGNED NOT NULL,
  `parameter_low_score_for_H` int(11) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pro_promise_parameters_max_score`
--

INSERT INTO `pro_promise_parameters_max_score` (`id`, `parameter_slug`, `parameter_name`, `parameter_max_score`, `parameter_low_score_for_H`, `created_at`, `updated_at`) VALUES
(1, 'apt_scientific_reasoning', 'Scientific Reasoning', 5, 5, '2018-02-06 11:06:40', '2018-02-06 11:06:40'),
(2, 'apt_verbal_reasoning', 'Verbal Reasoning', 10, 7, '2018-02-06 11:06:40', '2018-02-06 11:06:40'),
(3, 'apt_numerical_ability', 'Numerical Ability', 4, 4, '2018-02-06 11:09:38', '2018-02-06 11:09:38'),
(4, 'apt_logical_reasoning', 'Logical Reasoning', 15, 9, '2018-02-06 11:09:38', '2018-02-06 11:09:38'),
(5, 'apt_social_ability', 'Social Ability', 5, 4, '2018-02-06 11:16:48', '2018-02-06 11:16:48'),
(6, 'apt_artistic_ability', 'Artistic Ability', 1, 1, '2018-02-06 11:16:48', '2018-02-06 11:16:48'),
(7, 'apt_spatial_ability', 'Spatial Ability', 3, 2, '2018-02-06 11:18:00', '2018-02-06 11:18:00'),
(8, 'apt_creativity', 'Creativity', 1, 1, '2018-02-06 11:18:00', '2018-02-06 11:18:00'),
(9, 'apt_clerical_ability', 'Clerical Ability', 1, 1, '2018-02-06 11:18:36', '2018-02-06 11:18:36'),
(10, 'pt_conventional', 'Conventional', 2, 2, '2018-02-06 11:33:47', '2018-02-06 11:33:47'),
(11, 'pt_enterprising', 'Enterprising', 1, 1, '2018-02-06 11:33:47', '2018-02-06 11:33:47'),
(12, 'pt_investigative', 'Investigative', 1, 1, '2018-02-06 11:35:13', '2018-02-06 11:35:13'),
(13, 'pt_social', 'Social', 2, 1, '2018-02-06 11:35:13', '2018-02-06 11:35:13'),
(14, 'pt_artistic', 'Artistic', 1, 1, '2018-02-06 11:36:12', '2018-02-06 11:36:12'),
(15, 'pt_mechanical', 'Mechanical', 1, 1, '2018-02-06 11:36:12', '2018-02-06 11:36:12'),
(16, 'mit_interpersonal', 'Interpersonal', 8, 5, '2018-02-06 11:40:28', '2018-02-06 11:40:28'),
(17, 'mit_logical', 'Logical', 20, 14, '2018-02-06 11:40:28', '2018-02-06 11:40:28'),
(18, 'mit_linguistic', 'Linguistic', 10, 8, '2018-02-06 11:42:07', '2018-02-06 11:42:07'),
(19, 'mit_intrapersonal', 'Intrapersonal', 7, 5, '2018-02-06 11:42:07', '2018-02-06 11:42:07'),
(20, 'mit_musical', 'Musical', 6, 5, '2018-02-06 11:43:06', '2018-02-06 11:43:06'),
(21, 'mit_spatial', 'Spatial', 9, 7, '2018-02-06 11:43:06', '2018-02-06 11:43:06'),
(22, 'mit_bodilykinesthetic', 'Bodily-Kinesthetic', 5, 5, '2018-02-06 11:44:38', '2018-02-06 11:44:38'),
(23, 'mit_naturalist', 'Naturalist', 6, 5, '2018-02-06 11:44:38', '2018-02-06 11:44:38'),
(24, 'mit_existential', 'Existential', 4, 3, '2018-02-06 11:45:10', '2018-02-06 11:45:10'),
(25, 'it_people', 'People', 1, 0, '2018-02-06 11:47:10', '2018-02-06 11:47:10'),
(26, 'it_nature', 'Nature and Travel', 5, 0, '2018-02-06 11:47:10', '2018-02-06 11:47:10'),
(27, 'it_technical', 'Technical and Engineering', 1, 0, '2018-02-06 11:48:16', '2018-02-06 11:48:16'),
(28, 'it_creative_fine_arts', 'Creative, Fine Arts', 2, 0, '2018-02-06 11:48:16', '2018-02-06 11:48:16'),
(29, 'it_numerical', 'Numbers, Accounts and Money', 1, 0, '2018-02-06 11:49:22', '2018-02-06 11:49:22'),
(30, 'it_computers', 'Computers, Programming, Logic', 1, 0, '2018-02-06 11:49:22', '2018-02-06 11:49:22'),
(31, 'it_research', 'Research', 1, 0, '2018-02-06 11:50:28', '2018-02-06 11:50:28'),
(32, 'it_performing_arts', 'Performing Arts', 3, 0, '2018-02-06 11:50:28', '2018-02-06 11:50:28'),
(33, 'it_social', 'Social', 1, 0, '2018-02-06 11:51:16', '2018-02-06 11:51:16'),
(34, 'it_sports', 'Sports', 3, 0, '2018-02-06 11:51:16', '2018-02-06 11:51:16'),
(35, 'it_language', 'Language, Reading, Writing', 1, 0, '2018-02-06 11:51:58', '2018-02-06 11:51:58'),
(36, 'it_artistic', 'Art and Fashion', 1, 0, '2018-02-06 11:51:58', '2018-02-06 11:51:58'),
(37, 'it_musical', 'Music and Singing', 1, 0, '2018-02-06 11:52:19', '2018-02-06 11:52:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pro_promise_parameters_max_score`
--
ALTER TABLE `pro_promise_parameters_max_score`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pro_promise_parameters_max_score`
--
ALTER TABLE `pro_promise_parameters_max_score`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

//Add new field in sponsor activity table ## 06-02-2017
ALTER TABLE `pro_sa_sponsor_activity` ADD `sa_size_type` VARCHAR(255) NULL DEFAULT NULL AFTER `sa_type`;

//Alter sponsor size type column datatype ## 07-02-2017
ALTER TABLE `pro_sa_sponsor_activity` CHANGE `sa_size_type` `sa_size_type` TINYINT(1) NULL DEFAULT NULL;

//Added new table for forum question #07-02-2018 Jaimin
CREATE TABLE `pro_fq_forum_questions` (
 `id` bigint(20) NOT NULL AUTO_INCREMENT,
 `fq_que` text NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NOT NULL,
 `deleted` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 - Active , 2 - Inactive, 3 - Deleted',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1

//Added new table for forum answer #07-02-2018 Jaimin
CREATE TABLE `pro_fq_forum_answers` (
 `id` bigint(20) NOT NULL AUTO_INCREMENT,
 `fq_que_id` bigint(20) NOT NULL,
 `fq_teenager_id` bigint(20) NOT NULL,
 `fq_ans` text NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NOT NULL,
 `deleted` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 - Active , 2 - Inactive, 3 - Deleted  ',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=latin1

//Add new field for sponsor activity table ## 08-02-2017
ALTER TABLE `pro_sa_sponsor_activity` ADD `sa_description` TEXT NULL AFTER `sa_credit_used`;

//Add new table teenager sponsorship program ## 09-02-2017
CREATE TABLE IF NOT EXISTS `pro_tsp_teenager_scholarship_program` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `tsp_activity_id` int(11) UNSIGNED NOT NULL,
  `tsp_teenager_id` int(11) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'timestamp',
  `deleted` tinyint(1) DEFAULT '1' COMMENT '1 - Active, 2 - Inactive, 3 - Deleted',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

//Add Teenager Profession progress score
CREATE TABLE `pro_l4aapa_level4_profession_progress` (
  `id` int(11) NOT NULL,
  `teenager_id` int(11) DEFAULT NULL,
  `profession_id` int(11) DEFAULT NULL,
  `level4_basic` int(11) DEFAULT '0',
  `level4_intermediate` int(11) DEFAULT '0',
  `level4_advance` int(11) DEFAULT '0',
  `level4_total` int(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `pro_l4aapa_level4_profession_progress` ADD PRIMARY KEY (`id`);

ALTER TABLE `pro_l4aapa_level4_profession_progress` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

// Add new fields for profile completion calculations ## 26-02-2018
ALTER TABLE `pro_t_teenagers` ADD `t_progress_calculations` INT(3) NOT NULL DEFAULT '0' AFTER `t_about_info`;
ALTER TABLE `pro_t_teenagers` ADD `t_logout_progress` INT(3) NOT NULL DEFAULT '0' AFTER `t_progress_calculations`;


// Add new table for profession institute ## 12-03-2018 Jaimin
CREATE TABLE `pro_pi_profession_institutes` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `school_id` varchar(255) NOT NULL,
 `institute_state` varchar(20) DEFAULT NULL,
 `college_institution` varchar(255) DEFAULT NULL,
 `address_line1` varchar(255) DEFAULT NULL,
 `address_line2` varchar(255) DEFAULT NULL,
 `city` varchar(100) DEFAULT NULL,
 `district` varchar(50) DEFAULT NULL,
 `pin_code` varchar(6) DEFAULT NULL,
 `website` varchar(255) DEFAULT NULL,
 `year_of_establishment` varchar(4) DEFAULT NULL,
 `affiliat_university` varchar(255) DEFAULT NULL,
 `year_of_affiliation` varchar(4) DEFAULT NULL,
 `location` varchar(4) DEFAULT NULL,
 `latitude` varchar(10) DEFAULT NULL,
 `longitude` varchar(10) DEFAULT NULL,
 `institute_type` varchar(100) DEFAULT NULL,
 `autonomous` tinyint(4) DEFAULT NULL COMMENT '1 - True, 0 - False',
 `management` varchar(100) DEFAULT NULL,
 `speciality` varchar(255) DEFAULT NULL,
 `girl_exclusive` tinyint(4) DEFAULT NULL COMMENT '1 - True, 0 - False',
 `hostel_count` varchar(10) DEFAULT NULL,
 `minimum_fee` bigint(255) DEFAULT NULL,
 `maximum_fee` bigint(255) DEFAULT NULL,
 `is_institute_signup` tinyint(4) DEFAULT NULL COMMENT '1 - True, 0 - False',
 `accreditation_score` varchar(10) DEFAULT NULL,
 `accreditation_body` varchar(255) DEFAULT NULL,
 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NOT NULL,
 `deleted` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 - Active, 2 - Inactive, 3 - Deleted ',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1

// Add new table for profession institute speciality course ## 19-03-2018 Jaimin
CREATE TABLE `pro_pis_profession_institutes_speciality` (
 `id` bigint(20) NOT NULL AUTO_INCREMENT,
 `pis_name` varchar(255) NOT NULL,
 `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted` tinyint(3) unsigned NOT NULL DEFAULT '1',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1

// Alter table profession institute for school_id ## 19-03-2018 Jaimin
ALTER TABLE `pro_pi_profession_institutes` CHANGE `school_id` `school_id` BIGINT NOT NULL;

// Add image column in table profession institute ## 22-03-2018 Jaimin
ALTER TABLE `pro_pi_profession_institutes` ADD `image` VARCHAR(255) NULL AFTER `accreditation_body`;

// Add table notification management ## 23-03-2018 Jaimin
CREATE TABLE `pro_tnm_teen_notification_management` (
 `id` bigint(20) NOT NULL AUTO_INCREMENT,
 `tnm_teenager` bigint(20) NOT NULL,
 `tnm_notification_delete` longtext,
 `tnm_notification_read` longtext,
 `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 - Active , 2 - Inactive, 3 - Deleted',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1

// Add excel management table for profession institute excel management ## 28-03-2018 Jaimin
CREATE TABLE `pro_meu_manage_excel_upload` (
 `id` bigint(20) NOT NULL AUTO_INCREMENT,
 `file_type` tinyint(4) NOT NULL COMMENT '1 - Basic Information, 2 - Accreditation',
 `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - Pending, 1 - Success, 2 - Failed',
 `description` text NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NOT NULL,
 `deleted` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 - Active, 2 - Inactive, 3 - Deleted',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1

// Add year of establishment column in table profession institute table ## 22-03-2018 Jaimin
ALTER TABLE `pro_pi_profession_institutes` CHANGE `year_of_establishment` `year_of_establishment` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

//Add new table for parent profession progress calculation
CREATE TABLE IF NOT EXISTS `pro_l4p_level4_parent_profession_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `profession_id` int(11) DEFAULT NULL,
  `level4_basic` int(11) DEFAULT '0',
  `level4_intermediate` int(11) DEFAULT '0',
  `level4_advance` int(11) DEFAULT '0',
  `level4_total` int(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

//Add new field in l2 activity table ## 03-04-2018
ALTER TABLE `pro_l2ac_level2_activities` ADD `l2ac_school_id` BIGINT(20) NOT NULL DEFAULT '0' AFTER `section_type`;

//Add country id field in profession_institutes table ## 03-04-2018
ALTER TABLE `pro_pi_profession_institutes` ADD `country_id` INT NOT NULL DEFAULT '1' AFTER `image`;

//Add new column in profession subjects table ## 11-04-2014
ALTER TABLE `pro_ps_profession_subjects` ADD `ps_description` TEXT NULL AFTER `ps_image`;
ALTER TABLE `pro_ps_profession_subjects` ADD `ps_video` TEXT NULL AFTER `ps_description`;

//Alter 'l2ac_interest' column in 'pro_l2ac_level2_activities' table ## 23-04-2018
ALTER TABLE `pro_l2ac_level2_activities` CHANGE `l2ac_interest` `l2ac_interest` TINYINT(2) NULL DEFAULT '0' COMMENT 'Reference Interest types';

//Add new field called country id in institute speciality ## 
ALTER TABLE `pro_pis_profession_institutes_speciality` ADD `country_id` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `pis_name`; 

//change the datatype for speciality column in pro_pi_profession_institutes table
ALTER TABLE `pro_pi_profession_institutes` CHANGE `speciality` `speciality` LONGTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL; 