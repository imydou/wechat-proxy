<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOAuthLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_auth_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_appid', 32);
            $table->string('client_redirect_uri');
            $table->string('client_scope', 128);
            $table->string('client_state', 128);
            $table->string('virtual_code', 128)->nullable();
            $table->string('virtual_access_token', 128)->nullable();
            $table->string('virtual_refresh_token', 128)->nullable();
            $table->string('openid', 128)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('o_auth_logs');
    }
}
