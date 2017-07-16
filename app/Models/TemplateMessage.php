<?php

namespace App\Models;
use App\Jobs\SendTemplateMessage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class TemplateMessage extends Model
{
    const TEMPLATE_MESSAGES = 'template_messages';
    const TEMPLATE_MESSAGES_FAILED = 'template_messages_failed'; // -1
    const TEMPLATE_MESSAGES_SUCCESS = 'template_messages_success'; // 1
    const TEMPLATE_MESSAGES_UNKNOWN = 'template_messages_unknown'; // 0
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
        self::setMessageByMsgid(self::TEMPLATE_MESSAGES, $msgid, $template_message);
        self::setMessageByMsgid(self::TEMPLATE_MESSAGES_UNKNOWN, $msgid, $template_message);
        return true;
    }

    /**
     * 重新发送指定msgid的模板消息
     * @param  integer  $msgid
     */
    public function resendMessageByMsgid($msgid) {
        $template_message = self::getMessageByMsgid($msgid);
        if($template_message) {
            $userId = $template_message->touser;
            $templateId = $template_message->template_id;
            $url = $template_message->url;
            $data = $template_message->data;
            $template_message->flag = 1;
            self::setMessageByMsgid(self::TEMPLATE_MESSAGES, $msgid, $template_message);
            $job = new SendTemplateMessage($userId, $templateId, $url, $data);
            dispatch($job);
        }
    }

    /**
     * 重新发送所有失败的模板消息
     */
    public function resendFailedMessage() {
        $list = Redis::hvals(self::TEMPLATE_MESSAGES_FAILED);
        foreach ($list as $key => $value) {
            $value = json_decode($value);
            $this->resendMessageByMsgid($value->msgid);
        }
    }

    public static function getMessageByMsgid($msgid) {
        if(!$message = Redis::hget(self::TEMPLATE_MESSAGES, $msgid)) {
            return null;
        }
        $message = json_decode($message);
        return $message;
    }

    public static function setMessageByMsgid($key, $msgid, $data) {
        Redis::hset($key, $msgid, json_encode($data));
        return true;
    }

    public static function delMessageByMsgid($key, $msgid) {
        Redis::hdel($key, $msgid);
    }

    /**
     * 通过标识获取hash的key
     * @param $flag
     * @return string
     */
    public static function getKeyByFlag($flag) {
        switch ($flag) {
            case '1':
                return self::TEMPLATE_MESSAGES_SUCCESS;
                break;
            case '-1':
                return self::TEMPLATE_MESSAGES_FAILED;
                break;
            case '0':
                return self::TEMPLATE_MESSAGES_UNKNOWN;
            default:
                return self::TEMPLATE_MESSAGES;
        }
    }

    public static function setStatus($msgid, $flag) {
        if(!$message = self::getMessageByMsgid($msgid)) {
            return false;
        }
        // 删除对应状态的域的数据
        self::delMessageByMsgid(self::getKeyByFlag($message->flag), $msgid);
        // 插入改变的状态的域
        self::setMessageByMsgid(self::getKeyByFlag($flag), $msgid, $message);

        // 改变状态，并更新原有数据
        $message->flag = intval($flag);
        self::setMessageByMsgid(self::TEMPLATE_MESSAGES, $msgid, $message);

    }
}