<?php

namespace addons\blog\controller\wxapp;

use addons\blog\model\Category;
use addons\blog\model\Post as PostModel;
use addons\blog\model\Comment;
/**
 * 日志
 */
class Post extends Base
{

    protected $noNeedLogin = ['*'];

    /**
     * 日志列表
     */
    public function index()
    {
        $category = (int)$this->request->request('category/d');
        $page = (int)$this->request->request('page/d', 1);

        $page = max(1, $page);

        $postList = PostModel::with('category')
            ->where('status', 'normal')
            ->where($category ? "category_id='{$category}'" : "1=1")
            ->field('id,category_id,title,summary,diyname,image,createtime')
            ->page($page)
            ->order('weigh desc,id desc')
            ->select();
        foreach ($postList as $index => &$item) {
            $item['summary'] = mb_substr(trim(strip_tags($item['summary'])), 0, 100);
        }
        $this->success('', ['postList' => $postList]);
    }

    /**
     * 日志详情
     */
    public function detail()
    {
        $id = $this->request->request('id/d');
        $post = PostModel::get($id);
        if (!$post || $post['status'] == 'hidden') {
            $this->error(__('No specified article found'));
        }
        $category = Category::get($post['category_id']);
        if (!$category) {
            $this->error(__('No specified channel found'));
        }
        $post->setInc("views", 1);

        $commentList = Comment::where('post_id', $post->id)
            ->order('createtime', 'desc')
            ->where('status', 'normal')
            ->limit(10)
            ->select();
        foreach ($commentList as $index => $item) {
            $item->visible(['id', 'username', 'avatar', 'content', 'comments']);
        }
        $category->visible(['id', 'name']);
        $post->visible(['id', 'title', 'comments', 'content', 'description', 'image', 'summary', 'thumb', 'url', 'views']);
        $this->request->token();
        $this->success('', ['postInfo' => $post, 'categoryInfo' => $category, 'commentList' => $commentList]);
    }

}
