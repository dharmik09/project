<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProCicCartoonIconsCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_cic_cartoon_icons_category` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
            `cic_name` varchar(100) NOT NULL,
            `cic_from` bigint(10) NOT NULL DEFAULT '0' COMMENT '0 = admin, 1 = teen',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp',
            `updated_at` timestamp NULL DEFAULT NULL COMMENT 'timestamp',
            `deleted` tinyint(1) DEFAULT '1' COMMENT '1 - Active , 2 - Inactive, 3 - Deleted',
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";
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
