<?php

namespace app\admin\controller\blog;

use app\admin\model\BlogCategory;
use app\common\controller\Backend;
use fast\Tree;
use think\Controller;
use think\Request;

/**
 * 文章管理
 *
 * @icon fa fa-circle-o
 */
class Post extends Backend
{

    /**
     * BlogPost模型对象
     */
    protected $model = null;
    protected $categorylist = [];

    protected $noNeedRight = ['selectpage', 'check_element_available'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('BlogPost');
        $this->view->assign("flagList", $this->model->getFlagList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $tree = Tree::instance();
        $tree->init(collection(BlogCategory::order('weigh desc,id desc')->select())->toArray(), 'pid');
        $this->categorylist = $tree->getTreeList($tree->getTreeArray(0), 'name');
        $categorydata = [];
        foreach ($this->categorylist as $k => $v) {
            $categorydata[$v['id']] = $v;
        }
        $this->view->assign("categoryList", $categorydata);
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $this->model
                ->with('category')
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with('category')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 检测元素是否可用
     * @internal
     */
    public function check_element_available()
    {
        $id = $this->request->request('id');
        $name = $this->request->request('name');
        $value = $this->request->request('value');
        $name = substr($name, 4, -1);
        if (!$name) {
            $this->error(__('Parameter %s can not be empty', 'name'));
        }
        if ($id) {
            $this->model->where('id', '<>', $id);
        }
        $exist = $this->model->where($name, $value)->find();
        if ($exist) {
            $this->error(__('The data already exist'));
        } else {
            $this->success();
        }
    }

}
