<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProTpaTeenagerProfessionAttempted extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_tpa_teenager_profession_attempted` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `tpa_teenager` bigint(20) unsigned NOT NULL,
  `tpa_peofession_id` int(10) unsigned NOT NULL,
  `tpa_type` varchar(10) DEFAULT NULL COMMENT '1::Video, 2:: Text',
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
