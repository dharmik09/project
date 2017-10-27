<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProPParent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_p_parent` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `p_first_name` varchar(50) NOT NULL,
  `p_last_name` varchar(50) NOT NULL,
  `p_address1` varchar(50) NOT NULL,
  `p_address2` varchar(50) NOT NULL,
  `p_pincode` int(6) NOT NULL,
  `p_city` varchar(30) NOT NULL,
  `p_state` varchar(30) NOT NULL,
  `p_country` int(10) unsigned NOT NULL DEFAULT '0',
  `p_gender` tinyint(1) NOT NULL,
  `p_photo` varchar(100) DEFAULT NULL,
  `p_email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `p_user_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1:: Parent, 2 :: Counselor',
  `p_isverified` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `remember_token` varchar(100) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
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
