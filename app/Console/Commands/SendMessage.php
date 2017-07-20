<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendTemplateMessage;

class SendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发送微信模板消息';

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
     * 发送模板消息样例
     *
     * @return mixed
     */
    public function handle()
    {
        $user_list = [];
        echo "获取完毕，执行消息发送工作\n";
        $count = 0;
        foreach ($user_list as $key => $value) {
            $userId = $value;
            $templateId = ''; // 模板消息id
            $url = '';
            $data = array(
                "first"  => "",
                "keyword1"   => "",
                "keyword2"  => date('Y-m-d'),
                "remark" => "\n",
            );
            $job = new SendTemplateMessage($userId, $templateId, $url, $data);
            dispatch($job);
            $count++;
            if($count % 500 == 0) {
                echo "已推送消息给{$count}位用户\n";
            }
        }
        echo "推送完毕，总共推送给{$count}位用户\n";
    }
}
