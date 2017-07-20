<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Models\User;
use App\Models\UserLink;
use Illuminate\Console\Command;

class UserCensus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:census';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计当前用户数量';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = (User::count());
        Log::create([
            'action' => 'USER_COUNT',
            'value' => $count,
            'note' => date('Y-m-d H:i:s')."，用户总数",
            'uid' => 0,
        ]);
        echo date('Y-m-d H:i:s')."，用户总数".$count."\n";
        $wechat_user_count = UserLink::where('type', 'wechat')->count();
        Log::create([
            'action' => 'WECHAT_USER_COUNT',
            'value' => $wechat_user_count,
            'note' => date('Y-m-d H:i:s')."，绑定微信用户总数",
            'uid' => 0,
        ]);
        echo date('Y-m-d H:i:s')."，绑定微信用户总数".$wechat_user_count."\n";
        $weapp_user_count = UserLink::where('type', 'weapp')->count();
        Log::create([
            'action' => 'WECHAT_USER_COUNT',
            'value' => $weapp_user_count,
            'note' => date('Y-m-d H:i:s')."，绑定微信小程序用户总数",
            'uid' => 0,
        ]);
        echo date('Y-m-d H:i:s')."，绑定微信小程序用户总数".$weapp_user_count."\n";
    }
}
