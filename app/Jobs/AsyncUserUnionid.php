<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AsyncUserUnionid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $openid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($openid)
    {
        $this->openid = $openid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $userService = app('wechat')->user;
        $openid = $this->openid;
        $userInfo = $userService->get($openid);
        if($unionid = $userInfo->unionid) {
            (new \App\Models\OpenidLink)->updateOrCreate([
                'unionid' => $unionid,
                'type' => 'wechat'
            ],[
                'openid' => $openid
            ]);
            if ($userLink = (new \App\Models\UserLink)->where('openid', $unionid)->where('type', 'weapp')->first()) {
                $uid = $userLink->uid;
                (new \App\Models\UserLink)->updateOrCreate([
                    'uid' => $uid,
                    'type' => 'wechat'
                ],[
                    'access_token' => '',
                    'openid' => $openid
                ]);
            }
        }
    }
}
