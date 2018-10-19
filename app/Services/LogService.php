<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 04.10.2018
 * Time: 1:14
 */

namespace App\Services;


use App\ParserLog;

class LogService
{
    public static function log($style, $message, $url = null, $details = null)
    {
        ParserLog::create([
            'status'  => $style,
            'message' => $message,
            'url'     => $url,
            'details' => $details === null ? null : json_encode($details),
        ]);
    }

}