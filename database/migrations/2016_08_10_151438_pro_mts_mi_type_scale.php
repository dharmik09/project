<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProMtsMiTypeScale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_mts_mi_type_scale` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `mts_mi_type_id` int(10) unsigned NOT NULL,
  `mts_high_min_score` int(10) unsigned DEFAULT NULL,
  `mts_high_max_score` int(10) unsigned DEFAULT NULL,
  `mts_moderate_min_score` int(10) unsigned DEFAULT NULL,
  `mts_moderate_max_score` int(10) unsigned DEFAULT NULL,
  `mts_low_min_score` int(10) unsigned DEFAULT NULL,
  `mts_low_max_score` int(10) unsigned DEFAULT NULL,
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
