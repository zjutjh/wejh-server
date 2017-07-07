<?php

use Illuminate\Database\Seeder;
use App\Models\UserGroup;

class UserGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserGroup::create([
            'group_name' => '普通用户',
            'permission' => []
        ]);

        UserGroup::create([
            'group_name' => '管理员',
            'permission' => [
                'is_administrator' => true
            ]
        ]);
    }
}
