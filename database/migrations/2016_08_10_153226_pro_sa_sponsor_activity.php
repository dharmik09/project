<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProSaSponsorActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_sa_sponsor_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sa_sponsor_id` int(11) unsigned NOT NULL,
  `sa_type` tinyint(1) unsigned NOT NULL COMMENT '1->Ads,2->Event,3->Contest',
  `sa_name` varchar(255) NOT NULL,
  `sa_apply_level` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0->all level,1->level1,2->level2,3->level3,4->level4',
  `sa_location` varchar(100) NOT NULL,
  `sa_image` varchar(255) NOT NULL,
  `sa_credit_used` int(11) unsigned NOT NULL,
  `sa_start_date` date NOT NULL,
  `sa_end_date` date NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
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
