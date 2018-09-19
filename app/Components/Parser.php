<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 18.09.2018
 * Time: 15:12
 */

namespace App\Components;


class Parser
{
    public $loop_title;

    public $loop_address;
    public $link;
    public $loop_item;

    public $single_address;
    public $single_site;

    public $replace_search;
    public $replace_to;
    public $loop_link;


    public $reviews_ignore_text;
    public $reviews_all;
    public $reviews_title;
    public $reviews_text;
    public $reviews_rating;

    public function __construct()
    {
        $this->loop_item = 'main article';
        $this->loop_title = 'h3';
        $this->loop_address = '.entry-summary p:last-child';
        $this->loop_link = '.read-more a';


        $this->single_address = '.entry-content';
        $this->single_site = 'noindex';


        $this->replace_search = '</header><!-- .entry-header -->';
        $this->replace_to = '</div></header><!-- .entry-header -->';

        $this->link = 'https://avtosalon-otzyv.ru/';


        $this->reviews_ignore_text = '... Читать полностью';
        $this->reviews_all = 'main .full-testimonial';
        $this->reviews_title = 'h3';
        $this->reviews_text = '.rr_review_text';
        $this->reviews_rating = '.stars';
    }
}