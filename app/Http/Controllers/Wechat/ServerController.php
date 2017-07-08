<?php
namespace App\Http\Controllers\Wechat;
use App\Models\FailedJob;
use App\Models\Job;
use App\Models\Log;
use App\Models\TemplateMessage;
use BadMethodCallException;
use Carbon\Carbon;
use EasyWeChat\Core\Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Auth;
use Illuminate\Support\Facades\Redis;
use EasyWeChat\Message\News;
use Emoji;
use Excel;
use App\Models\User;
use App\Jobs\SendTemplateMessage;
class ServerController extends Controller
{
    public $wechat;
    public $default_message = '这是一条默认消息';
    public function __construct()
    {
        $this->wechat = app('wechat');
        //$this->default_message = '这是一条默认消息';
    }
    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        $this->wechat->server->setMessageHandler(function($message) {
            $type = $message->MsgType;
            try {
                $response = $this->$type($message); //反射到各个类型的方法
                return $response;
            } catch (BadMethodCallException $e) {
                //do log or sth.
                return $this->default_message;//回复默认消息
            }
        });
        $response =  $this->wechat->server->serve();
        return $response;
    }
    /**
     * 处理事件消息
     * 通过反射分发消息给其他方法
     *
     * @return string
     */
    public function event($message) {
        $eventType = $message->Event;
        $response = $this->$eventType($message); //反射到各个事件类型的方法
        return $response;
    }
    /**
     * 模板消息送达通知
     *
     * @return string
     */
    public function TEMPLATESENDJOBFINISH($message) {
        if($templateMessage = model('templateMessage')::where('msgid', $message->MsgID)->first()) {
            $flag = $message->Status == 'success'?1:-1;
            $templateMessage->flag = $flag;
            $templateMessage->save();
        }
        return '';
    }
    /**`
     * 扫描二维码消息
     *
     * @return string
     */
    public function SCAN($message) {
        try {
            $bookid= str_replace('qrscene_', '', $message->EventKey);
            $openID = $message->FromUserName ;
            $redirect_url = "http://shuxiang.louisian.net/mobile";
            $url = 'http://oauth.craim.net/?url=' . urlencode($redirect_url);
            $weixin_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx8a4a0119a36c75d3&redirect_uri='. urlencode($url) .'&response_type=code&scope=snsapi_base&state=STATE';
            preg_match_all('/(\d+)$/', $bookid, $temp);
            $post_url = "http://shuxiang.louisian.net/mobile/user/scanQRcode?bookid=".$temp[0][0]."&openid=".$openID;
            $result = file_get_contents($post_url);
            $result = json_decode($result, true);
            return "您借的书本是\n《" . $result['data'] . "》\n\n<a href='" . $weixin_url . "'>查看</a>";
        } catch (Exception $e) {
            return '扫描二维码时出了点错误';
        }
    }
    /**
     * 处理文本消息
     *
     * @return string
     */
    public function text($message)
    {
        $response = model('keyword')->matchKeyword($message);
        return $response?$response:'';
    }
    /**
     * 处理浏览会员卡事件
     *
     * @return string
     */
    public function user_view_card($message)
    {
        return '';
    }
    /**
     * 处理从会员卡进入公众号事件
     *
     * @return string
     */
    public function user_enter_session_from_card($message)
    {
        return '';
    }
    /**
     * 处理点击事件消息
     *
     * @return string
     */
    public function click($message) {
        $response = model('keyword')->matchKeyword($message);
        return $response?$response:'';
    }
    /**
     * 处理关注事件消息
     *
     * @return string
     */
    public function subscribe($message) {
        $userService = $this->wechat->user;
        $wechat_user = $userService->get($message->FromUserName);
        //添加用户到数据库
        if($user = User::where('openid', $wechat_user->openid)->first()) {
            $user->nickname = $wechat_user->nickname;
            $user->avatar = $wechat_user->headimgurl;
            $user->subscribe = 1;
            $user->save();
        } else {
            $user = new User;
            $user->openid = $wechat_user->openid;
            $user->nickname = $wechat_user->nickname;
            $user->avatar = $wechat_user->headimgurl;
            $user->user_setting = config('user_setting.user_setting_default');
            $user->subscribe = 1;
            $user->save();
        }
        return "";
    }
    /**
     * 处理取消关注事件消息
     *
     * @return string
     */
    public function unsubscribe($message) {
        //do sth.
        if($user = User::where('openid', $message->FromUserName)->first()) {
            $user->subscribe = 0;
            $user->save();
        }
        return;
    }
    /**
     * 处理图片消息
     *
     * @return string
     */
    public function image($message) {
        return "";
    }
    /**
     * 处理语音消息
     *
     * @return string
     */
    public function voice($message) {
        return "";
    }
    /**
     * 处理视频消息
     *
     * @return string
     */
    public function video($message) {
        return "";
    }
    /**
     * 处理小视频消息
     *
     * @return string
     */
    public function shortvideo($message) {
        return "";
    }
    /**
     * 处理位置消息
     *
     * @return string
     */
    public function location($message) {
        return "";
    }
    /**
     * 处理链接消息
     *
     * @return string
     */
    public function link($message) {
        return "";
    }
}