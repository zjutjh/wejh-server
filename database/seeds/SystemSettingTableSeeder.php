<?php

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;
class SystemSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SystemSetting::create([
            'varname' => 'ycjw_port',
            'value' => '86',
        ]);
    }
}
