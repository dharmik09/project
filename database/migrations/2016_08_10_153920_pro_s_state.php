<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProSState extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         $sql = "CREATE TABLE IF NOT EXISTS `pro_s_state` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `s_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `s_code` varchar(10) CHARACTER SET utf8 NOT NULL,
  `c_code` int(10) NOT NULL,
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
       
    }
}
