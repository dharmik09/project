
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
  `deleted` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Active , 2 - Inactive, 3 - Deleted	',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
