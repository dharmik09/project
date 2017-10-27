<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProTnTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_tn_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tn_userid` bigint(20) unsigned NOT NULL COMMENT 'Unique ID in system',
  `tn_user_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 - Teenager',
  `tn_transaction_id` varchar(50) NOT NULL COMMENT 'Unique transaction ID',
  `tn_transaction_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 - Self Sponsor',
  `tn_status` varchar(20) NOT NULL COMMENT 'Success/Fail/Pending etc',
  `tn_amount` float(16,4) NOT NULL COMMENT 'Paid Amount',
  `tn_currency` varchar(50) NOT NULL COMMENT 'INR/USD etc',
  `tn_device_type` tinyint(1) unsigned NOT NULL COMMENT '1 - IOS, 2 - Android, 3 -Web',
  `tn_extra` text COMMENT 'Serialized extra data if any',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
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
