<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Level4BasicActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `level4_basic_activity` (
            `id` bigint(12) NOT NULL AUTO_INCREMENT,
            `profession_id` bigint(12) DEFAULT NULL,
            `question_text` text,
            `points` int(8) DEFAULT NULL,
            `timer` int(10) DEFAULT NULL,
            `type` int(2) DEFAULT '1' COMMENT '0 multichoice / 1 - true/false',
            `deleted` tinyint(1) NOT NULL DEFAULT '1',
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
