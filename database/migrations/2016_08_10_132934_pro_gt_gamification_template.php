<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProGtGamificationTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_gt_gamification_template` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `gt_profession_id` int(11) unsigned NOT NULL,
            `gt_template_title` varchar(255) NOT NULL,
            `gt_template_image` varchar(255) DEFAULT NULL,
            `gt_template_descritpion` text NOT NULL,
            `gt_temlpate_answer_type` varchar(100) NOT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT '1',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
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
