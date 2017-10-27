<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProCfgConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_cfg_configurations` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `cfg_key` varchar(255) NOT NULL,
            `cfg_value` varchar(255) NOT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `created_by` int(11) unsigned DEFAULT '0',
            `updated_at` timestamp NULL DEFAULT NULL,
            `updated_by` int(11) unsigned DEFAULT '0',
            `deleted` tinyint(1) unsigned DEFAULT '1',
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
