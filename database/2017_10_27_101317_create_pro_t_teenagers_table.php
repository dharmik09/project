<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProTTeenagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('pro_t_teenagers'))
        {
            Schema::create('pro_t_teenagers', function (Blueprint $table) {
                $table->bigIncrements('id')->comment('Generating primary key');
                $table->string('t_uniqueid', 23)->unique();
                $table->unsignedInteger('t_school')->default(0);
                $table->string('t_name', 50);
                $table->string('t_nickname', 50);
                $table->string('t_email', 100)->nullable()->unique();
                $table->string('password', 100);
                $table->unsignedTinyInteger('t_gender', 1)->comment('1=>Male, 2=>Female');
                $table->string('t_social_provider', 100)->nullable();
                $table->string('t_social_identifier', 100)->nullable();
                $table->text('t_social_accesstoken')->nullable();
                $table->string('t_phone', 15)->unique();
                $table->date('t_birthdate')->nullable();
                $table->unsignedInteger('t_country')->default(0);
                $table->string('t_pincode', 10)->nullable();
                $table->string('t_location', 50)->nullable();
                $table->string('t_photo', 100)->nullable();
                $table->string('t_level', 10)->nullable();
                $table->unsignedInteger('t_credit')->default(0);
                $table->unsignedInteger('t_boosterpoints')->default(0);
                $table->unsignedTinyInteger('t_isfirstlogin', 1)->default(1);
                $table->unsignedTinyInteger('t_sponsor_choice', 1)->default(0)->comment('1=>Self, 2=>Sponsor, 3=>None');
                $table->unsignedInteger('t_rollnum');
                $table->unsignedInteger('t_class');
                $table->string('t_division', 10);
                $table->string('t_medium', 15);
                $table->unsignedInteger('t_academic_year');
                $table->unsignedTinyInteger('t_isverified', 1)->default(0)->comment('1-Yes, 0-No');
                $table->unsignedTinyInteger('t_payment_status', 1)->default(0)->comment('0=>default, 1=>Pending, 2=>Approved');
                $table->string('t_device_token', 255)->nullable();
                $table->tinyInteger('t_device_type', 1)->default(3)->comment('1=>IOS, 2=>Android, 3=>Web');
                $table->string('t_device_token', 255)->nullable();
                $table->tinyInteger('deleted', 1)->default(1)->comment('1=>Active , 2=>Inactive, 3=>Deleted');
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pro_t_teenagers');
    }
}
