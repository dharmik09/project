<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Level4BasicActivityAnswer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `level4_basic_activity_answer` (
            `id` bigint(12) NOT NULL AUTO_INCREMENT,
            `teenager_id` bigint(12) DEFAULT NULL,
            `activity_id` bigint(12) DEFAULT NULL,
            `answer_id` bigint(12) DEFAULT NULL,
            `earned_points` int(8) DEFAULT NULL,
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
