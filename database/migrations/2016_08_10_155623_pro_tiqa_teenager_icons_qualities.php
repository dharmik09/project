<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProTiqaTeenagerIconsQualities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_tiqa_teenager_icons_qualities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tiqa_teenager` bigint(20) unsigned NOT NULL,
  `tiqa_ti_id` bigint(20) unsigned NOT NULL,
  `tiqa_quality_id` int(11) unsigned NOT NULL,
  `tiqa_response` tinyint(1) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '1',
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
