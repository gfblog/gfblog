<?php

namespace addons\blog\controller;

use addons\blog\model\Post;

/**
 * 博客归档
 */
class Archive extends Base
{

    public function index()
    {
        $postlist = Post::where('status', 'normal')
            ->with(['category'])
            ->field('id,title,createtime,diyname,category_id')
            ->order("createtime", "desc")
            ->cache(3600 * 365)
            ->select();
        $yearlist = [];
        foreach ($postlist as $k => $v) {
            $yearlist[date("Y", $v['createtime'])][] = ['id' => $v['id'], 'title' => $v['title'], 'url' => $v['url']];
        }
        $this->view->assign('yearlist', $yearlist);
        $this->view->assign('title', '日志归档');
        return $this->view->fetch('/archive');
    }

}
