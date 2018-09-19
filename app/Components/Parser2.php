<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 18.09.2018
 * Time: 15:12
 */

namespace App\Components;


class Parser2 extends Parser
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
        parent::__construct();
        $this->link = 'https://otziv-avto.ru/msk/';

        $this->loop_item = '.su-post';
        $this->loop_title = 'h2';
        $this->loop_address = '.su-post-excerpt';
        $this->loop_link = 'h2 a';


        $this->single_address = '.entry-content p:first-child';
        $this->single_site = '.entry-content p > a';


        $this->replace_search = '';
        $this->replace_to = '';



        $this->reviews_ignore_text = '';
        $this->reviews_all = '.comment-body';
        $this->reviews_title = 'fn';
        $this->reviews_text = '.comment-content';
        $this->reviews_rating = '.stars';
    }
}