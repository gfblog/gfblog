<?php

namespace addons\blog\controller\wxapp;

use addons\blog\library\CommentException;
use addons\blog\model\Comment as CommentModel;
use think\Config;
use think\Exception;

/**
 * 评论
 */
class Comment extends Base
{

    protected $noNeedLogin = ['*'];

    /**
     * 评论列表
     */
    public function index()
    {
        $post_id = (int)$this->request->request('post_id/d');
        $page = (int)$this->request->request('page/d', 1);
        Config::set('paginate.page', $page);
        $commentList = \addons\blog\model\Comment::where('post_id', $post_id)
            ->order('createtime', 'desc')
            ->where('status', 'normal')
            ->page($page)->select();
        foreach ($commentList as $index => $item) {
            $item->visible(['id', 'username', 'avatar', 'content', 'comments']);
        }
        $this->success('', ['commentList' => $commentList]);
    }

    /**
     * 发表评论
     */
    public function post()
    {
        $result = false;
        $message = '';
        try {
            $params = $this->request->post();
            $result = CommentModel::postComment($params);
        } catch (CommentException $e) {
            if ($e->getCode() == 1) {
                $result = true;
            }
            $message = $e->getMessage();
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        if ($result) {
            $this->success(__($message ? $message : "发布成功"), ['token' => $this->request->token()]);
        } else {
            $this->error(__($message ? $message : "发布失败"), ['token' => $this->request->token()]);
        }
    }

}
