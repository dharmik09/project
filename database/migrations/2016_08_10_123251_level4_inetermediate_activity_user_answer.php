<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Level4InetermediateActivityUserAnswer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `level4_inetermediate_activity_user_answer` (
            `id` bigint(12) NOT NULL AUTO_INCREMENT,
            `l4iaua_teenager` bigint(12) DEFAULT NULL,
            `l4iaua_activity_id` bigint(12) DEFAULT NULL,
            `l4iaua_profession_id` int(6) DEFAULT NULL,
            `l4iaua_template_id` int(4) DEFAULT NULL,
            `l4iaua_answer` varchar(100) DEFAULT '0',
            `l4iaua_order` int(2) DEFAULT NULL,
            `l4iaua_earned_point` int(8) NOT NULL,
            `l4iaua_time` int(8) NOT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
