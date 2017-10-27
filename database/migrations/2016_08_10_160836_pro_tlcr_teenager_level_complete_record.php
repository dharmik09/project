<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProTlcrTeenagerLevelCompleteRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_tlcr_teenager_level_complete_record` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tlcr_teenager` bigint(12) DEFAULT NULL,
  `tlcr_level` int(3) DEFAULT NULL,
  `tlcr_timer` int(9) DEFAULT NULL,
  `tlcr_booster_flag` tinyint(2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
