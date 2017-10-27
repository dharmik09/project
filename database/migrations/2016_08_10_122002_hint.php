<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Hint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `hint` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `applied_level` varchar(30) NOT NULL,
            `hint_type` tinyint(2) unsigned NOT NULL COMMENT '1->Global,2->Individual',
            `data_id` bigint(20) unsigned NOT NULL DEFAULT '0',
            `hint_text` varchar(255) NOT NULL,
            `hint_image` varchar(255) NOT NULL,
            `time` int(11) unsigned NOT NULL DEFAULT '0',
            `created_at` timestamp NOT NULL,
            `updated_at` timestamp NOT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT '1',
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
