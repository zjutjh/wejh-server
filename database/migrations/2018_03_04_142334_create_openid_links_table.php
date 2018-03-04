<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpenidLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('openid_links', function (Blueprint $table) {
            $table->increments('id')->comment("自增长id");
            $table->string('unionid')->comment("unionid");
            $table->string('type')->comment("类型");
            $table->string('openid')->comment("openid");
            $table->timestamps();
            $table->unique(['unionid', 'type']);
            $table->index(['unionid', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('openid_links');
    }
}
