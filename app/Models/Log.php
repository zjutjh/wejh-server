<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $guarded = [];
    public function getLogsByAction($action, $value = null, $start_time = null)
    {
        $db = Log::where('action', $action);
        if ($value !== null)
        {
            $db->where('value', $value);
        }
        if ($start_time)
        {
            $db->where('created_at', '>', $start_time);
        }
        $logs = $db->get();
        return $logs;
    }
    public static function addLog($action, $value = 0, $note = "", $uid = 0)
    {
        return Log::create([
            'action' => htmlspecialchars($action),
            'value' => intval($value),
            'note' => htmlspecialchars($note),
            'uid' => intval($uid),
        ]);
    }
}
