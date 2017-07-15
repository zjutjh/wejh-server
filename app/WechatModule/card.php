<?php
use App\Models\User;
use EasyWeChat\Message\News;

function fixCardResponseText($msg)
{
    $answer = "";
    $answer .= "【校园卡】\n余额: "."\u{1F4B0}".$msg['卡余额'];
    $answer .= "\n【今日账单】";
    if(is_array($msg['今日账单']) && count($msg['今日账单']) != 0) {
        foreach ($msg['今日账单'] as $key => $value) {
            $answer .= "\n"."\u{25B6}".$value['站点'];
            $answer .= "\n时间：".$value['到账时间'];
            $answer .= "\n交易额：".$value['交易额'];
        }
    }
    else
        $answer .= "\n(暂无今日账单)";
    $answer .= "\n<a href='" . url('/card') . "'>查看详细账单</a>\n";
    return $answer;
}

$openid = $message->FromUserName;
if(!$user = User::getUserByOpenid($openid)) {
    $news = new News([
        'title' => '请先绑定账号',
        'description' => '使用本公众号服务需要先进入微精弘绑定账号',
        'url' => url('/'),
    ]);
    return $news;
}
if(!$user->uno) {
    $news = new News([
        'title' => '请先绑定精弘通行证',
        'description' => '使用本公众号服务需要先绑定精弘通行证',
        'url' => url('/login')
    ]);
    return $news;
}
if(!$card_password = $user['ext']['passwords']['card_password']) {
    $news = new News([
        'title' => '请先绑定校园卡账号',
        'description' => '使用查校园卡服务需要先进入微精弘绑定校园卡账号',
        'url' => url('/setting/card/login')
    ]);
    return $news;
}
$api = new \App\Models\Api;
$card = $api->getCardBalance($user->uno, decrypt($card_password), 3);
if(is_array($card))
{
    $text = fixCardResponseText($card);
    return respText($text);
}
if($error = $api->getError()) {
    if($error == '用户名或密码错误') {
        $news = new News([
            'title' => '用户名或密码错误',
            'description' => '请重新校园卡账号',
            'url' => url('/setting/card/login')
        ]);
        return $news;
    } else if ($error == '服务器错误') {
        $news = new News([
            'title' => '服务器错误',
            'description' => '请尝试进入微精弘做查询操作',
            'url' => url('/')
        ]);
        return $news;
    }
    return $error;
}
return '查询饭卡错误';