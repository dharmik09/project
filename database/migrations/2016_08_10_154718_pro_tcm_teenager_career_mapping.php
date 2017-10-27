<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProTcmTeenagerCareerMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_tcm_teenager_career_mapping` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `tcm_profession` bigint(20) unsigned NOT NULL,
  `tcm_scientific_reasoning` enum('L','M','H') NOT NULL,
  `tcm_verbal_reasoning` enum('L','M','H') NOT NULL,
  `tcm_numerical_ability` enum('L','M','H') NOT NULL,
  `tcm_logical_reasoning` enum('L','M','H') NOT NULL,
  `tcm_social_ability` enum('L','M','H') NOT NULL,
  `tcm_artistic_ability` enum('L','M','H') NOT NULL,
  `tcm_spatial_ability` enum('L','M','H') NOT NULL,
  `tcm_creativity` enum('L','M','H') NOT NULL,
  `tcm_clerical_ability` enum('L','M','H') NOT NULL,
  `tcm_doers_realistic` enum('L','M','H') NOT NULL,
  `tcm_thinkers_investigative` enum('L','M','H') NOT NULL,
  `tcm_creators_artistic` enum('L','M','H') NOT NULL,
  `tcm_helpers_social` enum('L','M','H') NOT NULL,
  `tcm_persuaders_enterprising` enum('L','M','H') NOT NULL,
  `tcm_organizers_conventional` enum('L','M','H') NOT NULL,
  `tcm_linguistic` enum('L','M','H') NOT NULL,
  `tcm_logical` enum('L','M','H') NOT NULL,
  `tcm_musical` enum('L','M','H') NOT NULL,
  `tcm_spatial` enum('L','M','H') NOT NULL,
  `tcm_bodily_kinesthetic` enum('L','M','H') NOT NULL,
  `tcm_naturalist` enum('L','M','H') NOT NULL,
  `tcm_interpersonal` enum('L','M','H') NOT NULL,
  `tcm_intrapersonal` enum('L','M','H') NOT NULL,
  `tcm_existential` enum('L','M','H') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(1) unsigned DEFAULT NULL,
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
