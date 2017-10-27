<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProL2acLevel2Activities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_l2ac_level2_activities` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
        `l2ac_text` text NOT NULL,
        `l2ac_points` int(10) unsigned DEFAULT '0',
        `l2ac_apptitude_type` tinyint(2) unsigned DEFAULT '0' COMMENT 'Reference Apptitude types',
        `l2ac_personality_type` tinyint(2) unsigned DEFAULT '0' COMMENT 'Reference Personality types',
        `l2ac_mi_type` tinyint(2) unsigned DEFAULT '0' COMMENT 'Reference Multiple Intelligence types',
        `l2ac_interest` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'Reference Interest types',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp',
        `updated_at` timestamp NULL DEFAULT NULL COMMENT 'timestamp',
        `deleted` tinyint(1) unsigned DEFAULT '1' COMMENT '1 - Active , 2 - Inactive, 3 - Deleted',
        `l2ac_image` varchar(255) DEFAULT NULL,
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
