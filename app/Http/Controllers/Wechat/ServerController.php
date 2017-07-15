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
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;
use EasyWeChat\Message\Material;
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
        $response = $this->matchKeyword($message);
        return $response ? $response : '';
    }

    /**
     * 处理关键字匹配
     * @param $message
     * @return mixed
     */
    public function matchKeyword($message) {
        $content = $message->MsgType == 'text' ? $message->Content : $message->EventKey;
        $keywords = config('keyword');
        foreach($keywords as $key => $value) {
            if($value['status'] == 1 && $this->isMatch($content, $value)) {
                return $this->reply($value['reply'], $message);
            }
        }
    }

    /**
     * @param $reply
     * @param $message
     * @return bool|Article|Image|Material|News|Text|Video|Voice
     */
    function reply($reply, $message) {
        switch ($reply['type']) {
            case 'userapi':
                $func = $reply['content'];
                try {
                    return wechatModule($func, $message);
                } catch (BadMethodCallException $e) {
                    //do log or sth.
                    return '';//回复默认消息
                }
                break;
            case 'text':
                $text = new Text(['content' => $reply['content']]);
                return $text;
                break;
            case 'image':
                $image = new Image(['media_id' => $reply['content']]);
                return $image;
                break;
            case 'video':
                $video = new Video([
                    'title' => isset($reply['title']) ? $reply['title'] : '',
                    'media_id' => $reply['content'],
                    'description' => isset($reply['description']) ? $reply['description'] : '',
                ]);
                return $video;
                break;
            case 'voice':
                $voice = new Voice(['media_id' => $reply['content']]);
                return $voice;
                break;
            case 'news':
                $news = new News([
                    'title' => isset($reply['title']) ? $reply['title'] : '',
                    'description' => isset($reply['description']) ? $reply['description'] : '',
                    'url' => isset($reply['url']) ? $reply['url'] : '',
                    'image' => isset($reply['image']) ? $reply['image'] : '',
                    // ...
                ]);
                return $news;
                break;
            case 'article':
                $article = new Article([
                    'title' => isset($reply['title']) ? $reply['title'] : '',
                    'author' => isset($reply['author']) ? $reply['author'] : '',
                    'content' => $reply['content'],
                    // ...
                ]);
                return $article;
                break;
            case 'material':
                $material = new Material('mpnews', $reply['content']);
                return $material;
                break;
            default:
                return false;
        }
    }

    /**
     * 处理关键词是否匹配
     * @param $message
     * @param $keyword
     * @return bool
     */
    public function isMatch($message, $keyword) {
        switch ($keyword['type']) {
            case '1':
                return $message == $keyword['content'];
                break;
            case '2':
                return !!strstr($message, $keyword['content']);
                break;
            case '3':
                return !!preg_match('/(' . $keyword['content'] . ')/is', $message);
                break;
            default:
                return false;
        }
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