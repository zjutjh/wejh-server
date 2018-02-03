<?php
$openid = $message->FromUserName;

$app = app('wechat');
$userService = $app->user;
$user = $userService->get($openid);

$unionid = $user->unionid;

return 'openid: ' . $openid . "\n" . 'unionid: ' . $unionid;