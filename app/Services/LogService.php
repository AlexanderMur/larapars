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
    public static function log($style, $message, $url = null, $parser_id = null, $data = null)
    {
        ParserLog::create([
            'parser_id' => $parser_id,
            'status'    => $style,
            'message'   => $message,
            'url'       => $url,
            'data'      => json_encode($data),
        ]);
    }

}