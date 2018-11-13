<?php

use App\Services\SettingService;
use libphonenumber\PhoneNumberUtil;

function nl2p($string)
{
    $paragraphs = '';

    foreach (explode("\n", $string) as $line) {
        if (trim($line)) {
            $paragraphs .= '<p>' . $line . '</p>';
        }
    }

    return $paragraphs;
}

function external_link($link)
{


    if (!starts_with('http://', $link) && !starts_with('https://', $link)) {
        return 'http://' . $link;
    }
    return $link;
}

/**
 * @param null $key
 * @param null $default
 * @return SettingService
 */
function setting($key = null, $default = null)
{
    if ($key) {
        return app(SettingService::class)->getSetting($key, $default);
    }
    return app(SettingService::class);
}

function filter_text($html)
{
    $html = nl2p($html);
    $html = preg_replace_callback('/<blockquote>/', function ($match) {
        $id = uniqid();
        return (
        "<div class=\"panel-group\" role=\"tablist\">
                <div class=\"panel panel-default\">
                    <div class=\"panel-heading\" role=\"tab\" id=\"collapseListGroupHeading$id\">
                        <h4 class=\"panel-title\">
                            <a
                                href=\"#collapseListGroup$id\"
                                class=\"collapsed\"
                                role=\"button\"
                                data-toggle=\"collapse\"
                            >Показать цитату</a>
                        </h4>
                    </div>
                    <div
                        class=\"panel-collapse collapse\"
                        role=\"tabpanel\"
                        id=\"collapseListGroup$id\"
                    >
                        <div class=\"panel-body\">
                            $match[0]"
        );
    }, $html);
    $html = preg_replace_callback('/<\/blockquote>/', function ($match) {
        return (
        "
                            $match[0]
                        </div>
                    </div>
                </div>
            </div>"
        );
    }, $html);

    return $html;
}


function get_links_from_text($text)
{
    preg_match_all('#((?:\S+@)?(https?:\/\/)?(?:www\.|(?!www))[a-zA-Z0-9а-я][а-яa-zA-Z0-9-]+[а-яa-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9]\.[^\s]{2,})#u', $text, $out);

    if (isset($out[0])) {
        foreach ($out[0] as $key => $item) {
            if(strpos($out[0][$key],'@') !== false){
                unset($out[0][$key]);
            }
        }
        return $out[0];
    }
    return [];
}

function find_numbers($text)
{
    return PhoneNumberUtil::getInstance()->findNumbers($text, 'RU');
}

function get_phones_from_text($text, $region = 'RU')
{
    $numbers    = PhoneNumberUtil::getInstance()->findNumbers($text, $region);
    $numbersArr = [];
    foreach ($numbers as $number) {
        $numbersArr[] = $number->rawString();
    }
    return implode(', ', $numbersArr);
}

function memory()
{
    return memory_get_peak_usage(true) / 1024 / 1024;
}

function get_domain($url){
    return parse_url($url)['host'] ?? '';
}
function closetags($html) {
    preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
    $openedtags = $result[1];

    preg_match_all('#</([a-z]+)>#iU', $html, $result);
    $closedtags = $result[1];
    $len_opened = count($openedtags);
    if (count($closedtags) == $len_opened) {
        return $html;
    }
    $openedtags = array_reverse($openedtags);
    for ($i=0; $i < $len_opened; $i++) {
        if (!in_array($openedtags[$i], $closedtags)){
            $html .= '</'.$openedtags[$i].'>';
        } else {
            unset($closedtags[array_search($openedtags[$i], $closedtags)]);
        }
    }
    return $html;
}
function normalize_url($url)
{
    ['scheme'=>$scheme,'host'=>$host,'path'=>$path] = parse_url($url);

    return "$scheme://$host$path";
}
function info_error(Throwable $throwable){
    info($throwable->getMessage(), ['trace'=>$throwable->getTraceAsString()]);
}
function phone(){
    return PhoneNumberUtil::getInstance();
}