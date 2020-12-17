<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsernameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() // 增加新数据表
    {
        if (!Schema::hasTable('user_name')) 
        {
            Schema::create('user_name', function (Blueprint $table) {
                if (!Schema::hasColumn('user_name', 'name'))  // 用户姓名
                    $table->string('name');
                if (!Schema::hasColumn('user_name', 'id')) // 用户学号
                    $table->string('id')->unique();  // 设置用户学号为唯一索引
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
        // 清空数据表
        Schema::dropIfExists('user_name');
    }
}
