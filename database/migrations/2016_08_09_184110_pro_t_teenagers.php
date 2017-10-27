<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProTTeenagers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
           $sql = "CREATE TABLE IF NOT EXISTS `pro_t_teenagers` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `t_uniqueid` varchar(23) NOT NULL,
          `t_school` int(11) unsigned NOT NULL DEFAULT '0',
          `t_name` varchar(50) NOT NULL,
          `t_nickname` varchar(50) NOT NULL,
          `t_email` varchar(50) DEFAULT NULL,
          `password` varchar(100) NOT NULL,
          `t_gender` tinyint(1) unsigned NOT NULL COMMENT '1-Male, 2-Female',
          `t_social_provider` varchar(100) DEFAULT NULL,
          `t_social_identifier` varchar(100) DEFAULT NULL,
          `t_social_accesstoken` text,
          `t_phone` varchar(15) NOT NULL,
          `t_birthdate` date DEFAULT NULL,
          `t_country` int(10) unsigned NOT NULL DEFAULT '0',
          `t_pincode` varchar(6) DEFAULT NULL,
          `t_location` varchar(50) DEFAULT NULL,
          `t_photo` varchar(100) DEFAULT NULL,
          `t_level` varchar(10) DEFAULT NULL,
          `t_credit` int(10) unsigned NOT NULL DEFAULT '0',
          `t_boosterpoints` int(10) unsigned NOT NULL DEFAULT '0',
          `t_isfirstlogin` tinyint(1) unsigned NOT NULL DEFAULT '1',
          `t_sponsor_choice` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 - Self, 2 - Sponsor, 3 - None',
          `t_rollnum` int(11) unsigned NOT NULL,
          `t_class` int(11) unsigned NOT NULL,
          `t_division` varchar(10) NOT NULL,
          `t_medium` varchar(15) NOT NULL,
          `t_academic_year` int(11) unsigned NOT NULL,
          `t_isverified` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1-Yes, 0-No',
          `t_payment_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-default,1-Pending,2-Approved',
          `t_device_token` varchar(255) DEFAULT NULL,
          `t_device_type` tinyint(1) NOT NULL DEFAULT '3' COMMENT '1 - IOS, 2 - Android, 3 -Web',
          `remember_token` varchar(100) DEFAULT NULL,
          `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NULL DEFAULT NULL,
          `deleted` tinyint(1) unsigned DEFAULT '1' COMMENT '1 - Active , 2 - Inactive, 3 - Deleted',
          PRIMARY KEY (`id`),
          UNIQUE KEY `t_uniqueid` (`t_uniqueid`,`t_email`,`t_phone`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
