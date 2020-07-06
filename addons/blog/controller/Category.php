<?php

namespace addons\blog\controller;

use addons\blog\model\Category as CategoryModel;
use addons\blog\model\Comment;
use addons\blog\model\Post;
use think\Paginator;

/**
 * 博客分类
 */
class Category extends Base
{

    public function index()
    {
        $diyname = $this->request->param('diyname');
        if ($diyname && !is_numeric($diyname)) {
            $category = CategoryModel::getByDiyname($diyname);
        } else {
            $id = $diyname ? $diyname : $this->request->param('id', '');
            $category = CategoryModel::get($id);
        }
        if (!$category || $category['status'] != 'normal') {
            $this->error("分类未找到");
        }

        $postlist = Post::where(['status' => 'normal'])
            ->where('category_id', $category['id'])
            ->with('category')
            ->order('weigh desc,id desc')
            ->paginate($this->view->config['listpagesize'], false, ['type' => '\\addons\\blog\\library\\Bootstrap']);

        $page = Paginator::getCurrentPage();
        $urls = $postlist->getUrlRange($page - 1, $page + 1);
        $prevurl = $page == 1 ? '' : array_shift($urls);
        $nexturl = $page == $postlist->lastPage() ? '' : array_pop($urls);

        $this->view->assign("postlist", $postlist);
        $this->view->assign('prevurl', $prevurl);
        $this->view->assign('nexturl', $nexturl);
        $this->view->assign('category', $category);
        $this->view->assign('title', $category['name']);
        $this->view->assign('keywords', $category['keywords']);
        $this->view->assign('description', $category['description']);
        if ($this->request->isAjax()) {
            return $this->view->fetch('/common/postlist');
        }
        return $this->view->fetch('/category');
    }

}
