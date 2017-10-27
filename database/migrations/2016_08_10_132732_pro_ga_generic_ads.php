<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProGaGenericAds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_ga_generic_ads` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `ga_name` varchar(255) NOT NULL,
            `ga_apply_level` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0->all level,1->level1,2->level2,3->level3,4->level4',
            `ga_image` varchar(255) NOT NULL,
            `ga_start_date` date NOT NULL,
            `ga_end_date` date NOT NULL,
            `deleted` tinyint(1) unsigned NOT NULL DEFAULT '1',
            `created_at` timestamp NOT NULL,
            `updated_at` timestamp NOT NULL,
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
