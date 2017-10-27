<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProL2ansLevel2Answers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_l2ans_level2_answers` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
        `l2ans_teenager` bigint(20) unsigned NOT NULL,
        `l2ans_activity` bigint(20) unsigned NOT NULL,
        `l2ans_answer` int(11) NOT NULL,
        `l2ans_answer_timer` bigint(18) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp',
        `updated_at` timestamp NULL DEFAULT NULL COMMENT 'timestamp',
        `deleted` tinyint(1) unsigned DEFAULT '1' COMMENT '1 - Active , 2 - Inactive, 3 - Deleted',
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
