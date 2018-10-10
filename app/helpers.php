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
    $html = str_replace('<blockquote>','        
<div class="panel-group" role="tablist">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="collapseListGroupHeading' . $id . '">
            <h4 class="panel-title">
                <a
                    href="#collapseListGroup' . $id . '"
                    class="collapsed"
                    role="button"
                    data-toggle="collapse"
                >Показать цитату</a>
            </h4>
        </div>
        <div
            class="panel-collapse collapse"
            role="tabpanel"
            id="collapseListGroup' . $id . '"
        >
            <div class="panel-body">
                <blockquote>
        ',$html);

    $html = str_replace('</blockquote>','        
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
        ',$html);
    return $html;
}