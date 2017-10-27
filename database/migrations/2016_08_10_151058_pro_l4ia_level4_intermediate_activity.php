<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProL4iaLevel4IntermediateActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_l4ia_level4_intermediate_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `l4ia_profession_id` int(11) unsigned NOT NULL,
  `l4ia_question_text` text CHARACTER SET utf8 NOT NULL,
  `l4ia_question_time` int(11) unsigned NOT NULL,
  `l4ia_question_point` int(11) unsigned NOT NULL,
  `l4ia_question_description` text CHARACTER SET utf8,
  `l4ia_question_answer_description` text CHARACTER SET utf8,
  `l4ia_question_template` int(11) NOT NULL,
  `l4ia_question_right_message` text CHARACTER SET utf8,
  `l4ia_question_wrong_message` text CHARACTER SET utf8,
  `l4ia_shuffle_options` tinyint(1) NOT NULL DEFAULT '0',
  `l4ia_options_metrix` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '1',
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
