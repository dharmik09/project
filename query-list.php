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