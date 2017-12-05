
//Add lastname column to teenager table
ALTER TABLE `pro_t_teenagers` ADD `t_lastname` VARCHAR(50) NULL AFTER `t_name`;


//Add extra new phone field add to teenager table
ALTER TABLE `pro_t_teenagers` ADD `t_phone_new` VARCHAR(15) NULL DEFAULT NULL;
