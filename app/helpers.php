<?php
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
function external_link($link){


    if(!starts_with('http://',$link) && !starts_with('https://',$link)){
        return 'http://'.$link;
    }
    return $link;
}