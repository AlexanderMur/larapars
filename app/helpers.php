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

function filter_text($html){
    $id = uniqid();


    $html = nl2p($html);
    $html = preg_replace_callback('/<blockquote>/',function ($match){
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
    },$html);
    $html = preg_replace_callback('/<\/blockquote>/',function ($match){
        return (
        "
                            $match[0]
                        </div>
                    </div>
                </div>
            </div>"
        );
    },$html);

    return $html;
}


function get_links_from_text($text){
    preg_match_all('~[a-z]+://\S+~', $text, $out);
    if(isset($out[0])){
        return $out[0];
    }
    return [];
}