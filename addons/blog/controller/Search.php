<?php

namespace addons\blog\controller;

use addons\blog\library\FulltextSearch;

/**
 * 搜索控制器
 * Class Search
 * @package addons\blog\controller
 */
class Search extends Base
{
    public function index()
    {
        $config = get_addon_config('blog');
        if ($config['searchtype'] == 'xunsearch') {
            return $this->xunsearch();
        }
        $q = $this->request->get('q', $this->request->get('search', ''));
        $q = mb_substr($q, 0, 100);

        $filterlist = [];
        $orderlist = [];

        $orderby = $this->request->get('orderby', '');
        $orderway = $this->request->get('orderway', '', 'strtolower');
        $params = ['q' => $q];
        if ($orderby) {
            $params['orderby'] = $orderby;
        }
        if ($orderway) {
            $params['orderway'] = $orderway;
        }

        $sortrank = [
            ['name' => 'default', 'field' => 'weigh', 'title' => __('Default')],
            ['name' => 'views', 'field' => 'views', 'title' => __('Views')],
            ['name' => 'id', 'field' => 'id', 'title' => __('Post date')],
        ];

        $orderby = $orderby && in_array($orderby, ['default', 'id', 'views']) ? $orderby : 'default';
        $orderway = $orderway ? $orderway : 'desc';
        foreach ($sortrank as $k => $v) {
            $url = '?' . http_build_query(array_merge($params, ['orderby' => $v['name'], 'orderway' => ($orderway == 'desc' ? 'asc' : 'desc')]));
            $v['active'] = $orderby == $v['name'] ? true : false;
            $v['orderby'] = $orderway;
            $v['url'] = $url;
            $orderlist[] = $v;
        }
        $orderby = $orderby == 'default' ? 'weigh' : $orderby;

        $postlist = \addons\blog\model\Post::where('status', 'normal')
            ->where('title', 'like', "%{$q}%")
            ->with(['category'])
            ->order($orderby, $orderway)
            ->paginate($this->view->config['listpagesize'], false, ['type' => '\\addons\\blog\\library\\Bootstrap']);
        $postlist->appends($params);

        $this->view->assign("orderlist", $orderlist);
        $this->view->assign("postlist", $postlist);
        $this->view->assign('title', __("Search for %s", $q));
        if ($this->request->isAjax()) {
            return $this->view->fetch('/common/postlist');
        }
        return $this->view->fetch('/search');
    }

    /**
     * Xunsearch搜索
     * @return string
     * @throws \think\Exception
     */
    public function xunsearch()
    {
        $orderList = [
            'relevance'       => '默认排序',
            'createtime_desc' => '发布时间从新到旧',
            'createtime_asc'  => '发布时间从旧到新',
            'views_desc'      => '浏览次数从多到少',
            'views_asc'       => '浏览次数从少到多',
            'comments_desc'   => '评论次数从多到少',
            'comments_asc'    => '评论次数从少到多',
        ];

        $q = $this->request->get('q', $this->request->get('search', ''));
        $q = mb_substr($q, 0, 100);

        $page = $this->request->get('page/d', '1', 'trim');
        $order = $this->request->get('order', '', 'trim');
        $fulltext = $this->request->get('fulltext/d', '1', 'trim');
        $fuzzy = $this->request->get('fuzzy/d', '0', 'trim');
        $synonyms = $this->request->get('synonyms/d', '0', 'trim');

        $total_begin = microtime(true);
        $search = null;
        $pagesize = 10;

        $result = FulltextSearch::search($q, $page, $pagesize, $order, $fulltext, $fuzzy, $synonyms);

        // 计算总耗时
        $total_cost = microtime(true) - $total_begin;

        //获取热门搜索
        $hot = FulltextSearch::hot();

        $data = [
            'q'           => $q,
            'error'       => '',
            'total'       => $result['total'],
            'count'       => $result['count'],
            'search_cost' => $result['microseconds'],
            'docs'        => $result['list'],
            'pager'       => $result['pager'],
            'corrected'   => $result['corrected'],
            'highlight'   => $result['highlight'],
            'related'     => $result['related'],
            'search'      => $search,
            'fulltext'    => $fulltext,
            'synonyms'    => $synonyms,
            'fuzzy'       => $fuzzy,
            'order'       => $order,
            'orderList'   => $orderList,
            'hot'         => $hot,
            'total_cost'  => $total_cost,
        ];

        \think\Config::set('blog.title', __("Search for %s", $q));
        $this->view->assign("title", $q);
        $this->view->assign($data);
        return $this->view->fetch('/xunsearch');
    }

    public function suggestion()
    {
        $q = trim($this->request->get('q', ''));
        $q = mb_substr($q, 0, 100);

        $terms = [];
        $config = get_addon_config('blog');
        if ($config['searchtype'] == 'xunsearch') {
            $terms = FulltextSearch::suggestion($q);
        }
        return json($terms);
    }
}
