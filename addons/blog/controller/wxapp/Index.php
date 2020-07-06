<?php

namespace addons\blog\controller\wxapp;

use addons\blog\model\Category;
use addons\blog\model\Post;

/**
 * 首页
 */
class Index extends Base
{

    protected $noNeedLogin = '*';

    /**
     * 首页
     */
    public function index()
    {
        //焦点图
        $bannerList = Post::where('status', 'normal')
            ->where("FIND_IN_SET( 'index',`flag`)")
            ->field('id,title,image,createtime')
            ->limit(4)
            ->select();

        $tabList = [
            ['id' => 0, 'title' => '全部'],
        ];
        $categoryList = Category::where('status', 'normal')
            ->field('id,pid,name,nickname,diyname')
            ->order('weigh desc,id desc')
            ->cache(false)
            ->select();
        foreach ($categoryList as $index => $item) {
            $tabList[] = ['id' => $item['id'], 'title' => $item['name']];
        }
        $postList = Post::
        with('category')
            ->where('status', 'normal')
            ->field('id,category_id,title,image,summary,createtime')
            ->page(1)
            ->order('weigh desc,id desc')
            ->select();
        foreach ($postList as $index => &$item) {
            $item['summary'] = trim(strip_tags($item['summary']));
        }
        $data = [
            'bannerList' => $bannerList,
            'tabList'    => $tabList,
            'postList'   => $postList,
        ];
        $this->success('', $data);
    }

}
