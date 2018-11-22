<?php


namespace App\Parsers;


use App\Components\Crawler;


class OtzovikParser extends SelectorParser
{

    public $per_page = 30;

    public function iteratePages3($fn, $url = '',$params = [],$page = 1)
    {

        $params = array_merge($params, [
            'form_params' => [
                'action'              => 'wiloke_loadmore_listing_layout',
                'posts_per_page'      => $this->per_page,
                'listing_locations'   => '',
                'latLng'              => '',
                'listing_cats'        => '215',
                'get_posts_from'      => '',
                'is_focus_query'      => 'false',
                'is_open_now'         => 'false',
                'is_highest_rated'    => 'false',
                'price_segment'       => 'all',
                'paged'               => $page,
                'customerUTCTimezone' => 'UTC+5',
                's'                   => '',
                'displayStyle'        => 'pagination',
                'sUnit'               => 'KM',
                'sWithin'             => '10',
                'atts'                =>
                    [
                        'layout'                         => 'listing--list',
                        'get_posts_from'                 => 'listing_cat',
                        'listing_cat'                    =>
                            [
                                0 => '215',
                            ],
                        'listing_location'               => '',
                        'listing_tag'                    => '',
                        'include'                        => '',
                        'show_terms'                     => 'listing_location',
                        'filter_type'                    => 'none',
                        'btn_name'                       => 'Загрузить ещё',
                        'viewmore_page_link'             => '#',
                        'btn_position'                   => 'text-center',
                        'order_by'                       => 'menu_order post_date',
                        'order'                          => 'DESC',
                        'display_style'                  => 'pagination',
                        'btn_style'                      => 'listgo-btn--default',
                        'btn_size'                       => 'listgo-btn--small',
                        'posts_per_page'                 => '10',
                        'image_size'                     => 'wiloke_listgo_455x340',
                        'toggle_render_favorite'         => 'enable',
                        'favorite_description'           => 'Сохранить',
                        'toggle_render_view_detail'      => 'enable',
                        'view_detail_text'               => '',
                        'toggle_render_find_direction'   => 'enable',
                        'find_direction_text'            => '',
                        'toggle_render_link_to_map_page' => 'enable',
                        'link_to_map_page_text'          => '',
                        'toggle_render_post_excerpt'     => 'enable',
                        'toggle_render_address'          => 'enable',
                        'toggle_render_author'           => 'enable',
                        'toggle_render_rating'           => 'enable',
                        'limit_character'                => '100',
                        'filter_result_description'      => '*open_result* %found_listing% %result_text=Result|Results% *end_result* in %total_listing% Destinations',
                        'block_id'                       => '',
                        'css'                            => '',
                        'map_page'                       => '',
                        'term_ids'                       => '',
                        'post_authors'                   => '',
                        'created_at'                     => '',
                        'extract_class'                  => '',
                        'location_latitude_longitude'    => '',
                        's_within_radius'                => '',
                        's_unit'                         => '',
                        'isTax'                          => 'true',
                        'sidebar'                        => 'right',
                        'wrapper_class'                  => 'listings listings--list',
                        'item_class'                     => 'listing listing--list',
                        'before_item_class'              => 'col-xs-12',
                        'listing_locations'              => '',
                    ],
                'currentPageID'       => '7116',
            ],
        ]);
        $this->add_visited_page($page);
        return $this->fetch('POST', $url, $params)
            ->then('json_decode')
            ->then(function ($json) use ($params, $page, $url, $fn) {

                $promises = null;
                $crawler = new Crawler($json->data->content, null);

                $archiveData = $this->getDataOnPage($crawler);
                $promises[] = $fn($archiveData,$page);
                if (!$this->should_stop()) {


                    $max_page = ceil($json->data->total / $this->per_page);
                    for ($i = 1; $i <= $max_page; $i++) {
                        if ($this->add_visited_page($i)) {
                            $promises[] = $this->iteratePages3($this->donor->link, $url,$params, $i);
                        }
                    }
                }

                return \GuzzleHttp\Promise\each($promises);
            })
            ->otherwise(function (\Throwable $throwable) {

                info_error($throwable);
                throw $throwable;
            });
    }

}