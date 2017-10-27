<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProL4iamLevel4IntermediateActivityMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_l4iam_level4_intermediate_activity_media` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `l4iam_question_id` int(11) unsigned NOT NULL,
  `l4iam_media_name` varchar(255) NOT NULL,
  `l4iam_media_type` enum('I','V') NOT NULL DEFAULT 'I' COMMENT 'I::Image, V:: Video',
  `l4iam_media_desc` text,
  `deleted` tinyint(1) DEFAULT '1',
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
