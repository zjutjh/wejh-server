<?php

namespace App\Console\Commands;

use App\Jobs\AsyncUserUnionid;
use Illuminate\Console\Command;

class AsyncWechatOpenid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'async:openid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步所有用户的openid';

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
        $userService = app('wechat')->user;
        $nextOpenId = null;
        $count = 0;
        do {
            $result = $userService->lists($nextOpenId);
            $nextOpenId = $result->next_openid;
            $data = $result->data;
            $list = $data['openid'];
            if (count($list) > 0) {
                foreach ($list as $key => $value) {
                    if ($value) {
                        $job = new AsyncUserUnionid($value);
                        dispatch($job);
                        echo ++$count . "\n";
                    }
                }
            }
        } while ($nextOpenId);
        echo '开始同步' . $count . '个任务' . "\n";
    }
}
