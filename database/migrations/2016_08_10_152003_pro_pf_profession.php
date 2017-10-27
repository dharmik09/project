<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProPfProfession extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_pf_profession` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `pf_basket` int(10) unsigned NOT NULL COMMENT 'Basket Reference ID',
  `pf_name` varchar(100) NOT NULL,
  `pf_intro` longtext COMMENT 'HTML (Editor)',
  `pf_logo` varchar(100) DEFAULT NULL,
  `pf_video` varchar(100) DEFAULT NULL,
  `pf_video_type` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '1 - Normal , 2 - Youtube, 3 - Vimeo',
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
