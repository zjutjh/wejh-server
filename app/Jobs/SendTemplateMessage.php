<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendTemplateMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $templateId;
    protected $url;
    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $templateId, $url, $data)
    {
        $this->userId = $userId;
        $this->templateId = $templateId;
        $this->url = $url;
        $this->data = $data;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = null;
        try {
            $touser = $this->userId;
            $url = $this->url;
            $template_id = $this->templateId;
            $data = $this->data;
            @$notice = app('wechat')->notice;
            @$message = $notice->withTo($touser)->withUrl($url)->withTemplate($template_id)->withData($data);
            if($message != null) {
                @$result = $message->send();
            }
        } catch (Exception $e) {
        } finally {
            if(!empty($result)) {
                $msgid = $result->msgid;
                model('TemplateMessage')->saveMessage($touser, $template_id, $url, $data, $msgid);
                if(!$msgid) {
                    throw new Exception('发送失败');
                }
            }
        }
    }
}
