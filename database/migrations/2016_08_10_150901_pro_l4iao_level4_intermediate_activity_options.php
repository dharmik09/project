<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProL4iaoLevel4IntermediateActivityOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_l4iao_level4_intermediate_activity_options` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `l4iao_question_id` int(11) unsigned NOT NULL,
  `l4iao_answer_text` text,
  `l4iao_answer_image` varchar(255) DEFAULT NULL,
  `l4iao_answer_image_description` text,
  `l4iao_correct_answer` varchar(255) DEFAULT NULL,
  `l4iao_answer_order` varchar(15) DEFAULT NULL,
  `l4iao_answer_group` int(11) NOT NULL DEFAULT '0',
  `l4iao_answer_response_text` varchar(255) DEFAULT NULL,
  `l4iao_answer_response_image` varchar(255) DEFAULT NULL,
  `l4iao_answer_points` int(11) unsigned NOT NULL DEFAULT '0',
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
