<?php

namespace addons\blog\controller;

use think\Config;

/**
 * Sitemap控制器
 * Class Sitemap
 * @package addons\blog\controller
 */
class Sitemap extends Base
{
    protected $noNeedLogin = ['*'];
    protected $options = [
        'item_key'  => '',
        'root_node' => 'urlset',
        'item_node' => 'url',
        'root_attr' => 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:mobile="http://www.baidu.com/schemas/sitemap-mobile/1/"'
    ];

    public function _initialize()
    {
        parent::_initialize();
        Config::set('default_return_type', 'xml');
    }

    /**
     * Sitemap
     */
    public function index()
    {
        $postList = \addons\blog\model\Post::where('status', 'normal')->cache(3600)->field('id,category_id,createtime')->paginate(500000);
        $list = [];
        foreach ($postList as $index => $item) {
            $list[] = [
                'loc'      => $item->fullurl,
                'priority' => 0.8
            ];
        }
        return xml($list, 200, [], $this->options);
    }
}
