<?php

namespace App\Models;
use App\Jobs\SendTemplateMessage;
use Illuminate\Database\Eloquent\Model;
use Log;

class TemplateMessage extends Model
{
    /**
     * 应该被转化为原生类型的属性
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * 储存模板消息
     * @param  string   $touser
     * @param  integer  $template_id
     * @param  string   $url
     * @param  mixed    $data
     * @param  integer  $msgid
     * @return bool
     */
    public function saveMessage($touser, $template_id, $url, $data, $msgid) {
        $template_message = new TemplateMessage;
        $template_message->touser = $touser;
        $template_message->template_id = $template_id;
        $template_message->url = $url;
        $template_message->data = $data;
        $template_message->msgid = intval($msgid);
        return $template_message->save();
    }

    /**
     * 重新发送指定msgid的模板消息
     * @param  integer  $msgid
     */
    public function resendMessageByMsgid($msgid) {
        $template_message = TemplateMessage::where('msgid', $msgid)->first();
        if($template_message) {
            $userId = $template_message->touser;
            $templateId = $template_message->template_id;
            $url = $template_message->url;
            $data = $template_message->data;
            $template_message->flag = 1;
            $template_message->save();
            $job = new SendTemplateMessage($userId, $templateId, $url, $data);
            dispatch($job);
        }
    }

    /**
     * 重新发送所有失败的模板消息
     */
    public function resendFailedMessage() {
        $list = TemplateMessage::where('flag', -1)->get();
        foreach ($list as $key => $value) {
            $this->resendMessageByMsgid($value->msgid);
        }
    }
}