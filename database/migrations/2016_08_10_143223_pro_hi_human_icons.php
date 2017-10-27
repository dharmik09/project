<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProHiHumanIcons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_hi_human_icons` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
            `hi_category` int(10) unsigned NOT NULL,
            `hi_name` varchar(100) NOT NULL,
            `hi_image` varchar(100) NOT NULL,
            `hi_added_by` bigint(20) unsigned NOT NULL DEFAULT '0',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 't',
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
