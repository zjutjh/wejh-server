<?php
$domain = env('APP_FE_URL');

return [
    [
        'content' => '查成绩',
        'type' => 1,//1为等于，2为包含，3为正则
        'reply' => [
            'type' => 'function',
            'content' => 'score'
        ],
        'status' => 1,//是否禁用，0为禁用
    ],
    [
        'content' => '查饭卡',
        'type' => 1,//1为等于，2为包含，3为正则
        'reply' => [
            'type' => 'function',
            'content' => 'card'
        ],
        'status' => 1,//是否禁用，0为禁用
    ],
    [
        'content' => '查排考',
        'type' => 1,//1为等于，2为包含，3为正则
        'reply' => [
            'type' => 'function',
            'content' => 'exam'
        ],
        'status' => 1,//是否禁用，0为禁用
    ],
    [
        'content' => '绑定',
        'type' => 1,//1为等于，2为包含，3为正则
        'reply' => [
            'type' => 'news',
            'title' => '绑定',
            'description' => '点击此处绑定',
            'url' => $domain . '/login',
        ],
        'status' => 1,//是否禁用，0为禁用
    ],
];