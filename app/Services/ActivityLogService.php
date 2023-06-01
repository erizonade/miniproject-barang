<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class ActivityLogService
{
    public static function log($message, $idbarang, $data = [])
    {
        $log = [
            'timestamp' => now(),
            'message' => $message,
            'data' => $data,
        ];

        Redis::hset('activity_log', time().$idbarang, json_encode($log));
    }

    public static function getAllLogs()
    {
        $logs = Redis::hgetall('activity_log');

        return array_map('json_decode', $logs);
    }
}
