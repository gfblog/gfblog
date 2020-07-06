<?php

namespace addons\blog\controller;

use addons\blog\model\Post;
use think\Paginator;

/**
 * 博客首页
 */
class Index extends Base
{

    public function index()
    {
        $postlist = Post::where(['status' => 'normal'])
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
        if ($this->request->isAjax()) {
            return $this->view->fetch('/common/postlist');
        }

        $config = get_addon_config('blog');
        $this->view->assign('keywords', $config['keywords']);
        $this->view->assign('description', $config['description']);
        return $this->view->fetch('/index');
    }

}
