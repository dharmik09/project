<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProL4aaLevel4AdvanceActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_l4aa_level4_advance_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `l4aa_type` tinyint(1) unsigned NOT NULL,
  `l4aa_description` text NOT NULL,
  `l4aa_text` text NOT NULL,
  `deleted` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
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
