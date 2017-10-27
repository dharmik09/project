<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProAuAudits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pro_au_audits` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `au_user_id` int(20) unsigned NOT NULL COMMENT 'Logged In User Id',
            `au_user_type` tinyint(1) unsigned NOT NULL COMMENT 'Reference of  pro_r_roles table',
            `au_action` varchar(255) NOT NULL COMMENT 'CRUD - CREATE READ UPDATE DELETE',
            `au_object_type` varchar(255) NOT NULL COMMENT 'Table Name/ Function Name',
            `au_object_id` varchar(255) NOT NULL COMMENT 'Table Row Id/ URL',
            `au_origin` tinyint(1) unsigned NOT NULL COMMENT '1 - Android, 2 - IOS , 3 - Web',
            `au_message` text,
            `au_other` longtext COMMENT 'Serialize data',
            `au_ip` varchar(45) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL,
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
