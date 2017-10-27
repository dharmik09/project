<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProL1ansLevel1Answers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $sql = "CREATE TABLE IF NOT EXISTS `pro_l1ans_level1_answers` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `l1ans_teenager` bigint(20) unsigned NOT NULL,
            `l1ans_activity` bigint(20) unsigned NOT NULL,
            `l1ans_answer` int(10) unsigned NOT NULL,
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
