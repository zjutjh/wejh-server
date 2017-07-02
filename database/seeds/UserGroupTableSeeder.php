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
            'ids' => 0,
            'table' => 'user_groups'
        ]);

        $table_info = UserGroup::first();

        UserGroup::create([
            'id' => $table_info->ids,
            'group_name' => '普通用户',
            'permission' => []
        ]);
        $table_info->increment('ids');

        $table_info = UserGroup::where('table', 'user_groups')->first();
        UserGroup::create([
            'id' => $table_info->ids,
            'group_name' => '管理员',
            'permission' => [
                'is_administrator' => true
            ]
        ]);
        $table_info->increment('ids');
    }
}
