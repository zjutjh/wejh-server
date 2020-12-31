<?php
namespace App\Services;

class AnnouncementService {

    public static function getAnnouncement()
    {
        $title = env('ANNOUNCEMENT_TITLE', '公告');
        $content = env('ANNOUNCEMENT_CONTENT', '<p>有任何问题，请加QQ群:462530805</p>');
        $show = env('ANNOUNCEMENT_SHOW', true);
        $footer = env('ANNOUNCEMENT_FOOTER', '');
        $clipboard = env('ANNOUNCEMENT_CLIPBOARD', '');
        $clipboardTip = env('ANNOUNCEMENT_CLIPBOARD_TIP', '');
        return [
            'id' => env('ANNOUNCEMENT_ID', 1),
            'show' => $show,
            'title' => $title,
            'content' => $content,
            'footer' => $footer,
            'clipboard' => $clipboard,
            'clipboardTip' => $clipboardTip
        ];
    }

}
