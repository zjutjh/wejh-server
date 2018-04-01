<?php

namespace App\Jobs;

use App\Models\OpenidLink;
use App\Models\UserLink;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AyncUserUnionid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $openid;
    protected $userService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($openid)
    {
        $app = app('wechat');
        $this->openid = $openid;
        $this->userService = $app->user;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $userService = $this->userService;
            $openid = $this->openid;
            $userInfo = $userService->get($openid);
            if($unionid = $userInfo->unionid) {
                OpenidLink::updateOrCreate([
                    'unionid' => $unionid,
                    'type' => 'wechat'
                ],[
                    'openid' => $openid
                ]);
                if ($userLink = UserLink::where('openid', $unionid)->where('type', 'weapp')->first()) {
                    $uid = $userLink->uid;
                    UserLink::updateOrCreate([
                        'uid' => $uid,
                        'type' => 'wechat'
                    ],[
                        'access_token' => '',
                        'openid' => $openid
                    ]);
                }
            }
        } catch (Exception $e) {
        } finally {
        }
    }
}
