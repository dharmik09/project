<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProCpmCartoonProfessionMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_cpm_cartoon_profession_mapping` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
            `cpm_cartoon_id` int(10) unsigned NOT NULL,
            `cpm_profession_id` int(10) unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp',
            `updated_at` timestamp NULL DEFAULT NULL COMMENT 'timestamp',
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
