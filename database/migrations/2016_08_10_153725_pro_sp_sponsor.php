<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProSpSponsor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_sp_sponsor` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `sp_email` varchar(50) NOT NULL,
  `sp_company_name` varchar(50) NOT NULL,
  `sp_admin_name` varchar(50) NOT NULL,
  `sp_address1` varchar(50) NOT NULL,
  `sp_address2` varchar(50) NOT NULL,
  `sp_pincode` int(6) unsigned NOT NULL,
  `sp_city` varchar(30) NOT NULL,
  `sp_state` varchar(30) NOT NULL,
  `sp_country` int(10) unsigned NOT NULL DEFAULT '0',
  `password` varchar(100) NOT NULL,
  `sp_isapproved` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '1 - Approved , 2 - Not Approved',
  `sp_credit` int(11) unsigned DEFAULT NULL,
  `sp_logo` varchar(100) DEFAULT NULL,
  `sp_photo` varchar(100) DEFAULT NULL,
  `sp_first_name` varchar(50) NOT NULL,
  `sp_last_name` varchar(50) NOT NULL,
  `sp_title` varchar(5) NOT NULL,
  `sp_phone` varchar(10) NOT NULL,
  `remember_token` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'timestamp',
  `deleted` tinyint(1) unsigned DEFAULT '1' COMMENT '1 - Active , 2 - Inactive, 3 - Deleted',
  PRIMARY KEY (`id`)
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
