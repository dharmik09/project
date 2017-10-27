<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProPtpParentTeenPair extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_ptp_parent_teen_pair` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ptp_parent_id` bigint(20) NOT NULL,
  `ptp_teenager` bigint(20) NOT NULL,
  `ptp_is_verified` tinyint(1) unsigned NOT NULL,
  `ptp_token` varchar(23) NOT NULL,
  `ptp_sent_by` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
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
