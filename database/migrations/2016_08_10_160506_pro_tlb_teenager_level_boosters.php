<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProTlbTeenagerLevelBoosters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_tlb_teenager_level_boosters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tlb_teenager` bigint(20) unsigned NOT NULL,
  `tlb_level` int(10) unsigned NOT NULL,
  `tlb_points` bigint(20) DEFAULT '0',
  `tlb_profession` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
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
