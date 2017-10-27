<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProCpCoupons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_cp_coupons` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
                `cp_code` varchar(25) NOT NULL COMMENT 'UNIQUE',
                `cp_description` text,
                `cp_image` varchar(50) DEFAULT NULL,
                `cp_sponsor` int(10) unsigned NOT NULL,
                `cp_validfrom` date NOT NULL,
                `cp_validto` date NOT NULL,
                `cp_limit` int(11) unsigned NOT NULL,
                `cp_used` int(11) unsigned NOT NULL,
                `cp_credit_used` int(11) unsigned NOT NULL,
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
