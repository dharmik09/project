<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProCms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_cms` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
            `cms_slug` varchar(255) DEFAULT NULL,
            `cms_subject` varchar(255) NOT NULL,
            `cms_body` longtext NOT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `created_by` int(11) unsigned DEFAULT '0',
            `updated_at` timestamp NULL DEFAULT NULL,
            `updated_by` int(11) unsigned DEFAULT '0',
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
