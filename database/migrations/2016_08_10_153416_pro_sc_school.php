<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProScSchool extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_sc_school` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `sc_name` varchar(30) NOT NULL,
  `sc_address1` varchar(50) NOT NULL,
  `sc_address2` varchar(50) NOT NULL,
  `sc_pincode` int(6) unsigned NOT NULL,
  `sc_city` varchar(30) NOT NULL,
  `sc_state` varchar(30) NOT NULL,
  `sc_country` int(11) unsigned NOT NULL,
  `sc_isapproved` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '1 - Approved, 2 - Not Approved',
  `sc_logo` varchar(100) DEFAULT NULL,
  `sc_photo` varchar(100) DEFAULT NULL,
  `sc_first_name` varchar(50) NOT NULL,
  `sc_last_name` varchar(50) NOT NULL,
  `sc_title` varchar(5) NOT NULL,
  `sc_phone` varchar(10) NOT NULL,
  `sc_email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `remember_token` varchar(100) CHARACTER SET latin1 COLLATE latin1_spanish_ci DEFAULT NULL,
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
