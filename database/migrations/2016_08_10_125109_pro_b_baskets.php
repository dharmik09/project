<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProBBaskets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_b_baskets` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
                `b_name` varchar(50) NOT NULL,
                `b_intro` longtext NOT NULL COMMENT 'HTML (Editor)',
                `b_logo` varchar(100) DEFAULT NULL,
                `b_video_type` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '1 - Normal , 2 - Youtube, 3 - Vimeo',
                `b_video` varchar(100) DEFAULT NULL,
                `b_points` int(10) NOT NULL,
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
