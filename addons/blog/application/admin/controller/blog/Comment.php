<?php

namespace app\admin\controller\blog;

use app\common\controller\Backend;

use think\Controller;
use think\Request;

/**
 * 博客评论管理
 *
 * @icon fa fa-circle-o
 */
class Comment extends Backend
{

    /**
     * BlogComment模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('BlogComment');
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $this->relationSearch = TRUE;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $this->model
                ->with('post')
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with('post')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
}
