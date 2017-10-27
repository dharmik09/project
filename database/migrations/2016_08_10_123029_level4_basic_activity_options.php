<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Level4BasicActivityOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `level4_basic_activity_options` (
                `id` bigint(12) NOT NULL AUTO_INCREMENT,
                `activity_id` bigint(12) DEFAULT NULL,
                `options_text` text,
                `correct_option` tinyint(1) DEFAULT '0' COMMENT '0: wrong, 1:right',
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL,
                `deleted` tinyint(1) DEFAULT '1',
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
