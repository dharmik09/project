
//Add lastname column to teenager table ## 5-12-2017
ALTER TABLE `pro_t_teenagers` ADD `t_lastname` VARCHAR(50) NULL AFTER `t_name`;


//Add extra new phone field add to teenager table ## 5-12-2017
ALTER TABLE `pro_t_teenagers` ADD `t_phone_new` VARCHAR(15) NULL DEFAULT NULL;

//Add extra new description field to video table ## 7-12-2017
ALTER TABLE `pro_v_video` ADD `v_description` TEXT NULL AFTER `v_photo`;

//Add extra fields to teenager table ## 7-12-2017
ALTER TABLE `pro_t_teenagers` ADD `is_share_with_other_members` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '\"0\" => No, \"1\" => Yes' AFTER `t_phone_new`, ADD `is_share_with_parents` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '\"0\" => No, \"1\" => Yes' AFTER `is_share_with_other_members`, ADD `is_share_with_teachers` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '\"0\" => No, \"1\" => Yes' AFTER `is_share_with_parents`;
