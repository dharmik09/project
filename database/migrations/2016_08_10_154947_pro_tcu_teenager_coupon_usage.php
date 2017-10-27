<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProTcuTeenagerCouponUsage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_tcu_teenager_coupon_usage` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tcu_teenager` bigint(20) unsigned NOT NULL,
  `tcu_coupon_id` bigint(20) unsigned NOT NULL,
  `tcu_allocated_email` varchar(30) NOT NULL,
  `tcu_consumed_email` varchar(30) NOT NULL,
  `tcu_type` varchar(10) NOT NULL,
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
