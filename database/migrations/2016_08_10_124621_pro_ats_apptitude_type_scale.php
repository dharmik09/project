<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProAtsApptitudeTypeScale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_ats_apptitude_type_scale` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
            `ats_apptitude_type_id` int(10) unsigned NOT NULL,
            `ats_high_min_score` int(10) unsigned DEFAULT NULL,
            `ats_high_max_score` int(10) unsigned DEFAULT NULL,
            `ats_moderate_min_score` int(10) unsigned DEFAULT NULL,
            `ats_moderate_max_score` int(10) unsigned DEFAULT NULL,
            `ats_low_min_score` int(10) unsigned DEFAULT NULL,
            `ats_low_max_score` int(10) unsigned DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp',
            `updated_at` timestamp NULL DEFAULT NULL COMMENT 'timestamp',
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
