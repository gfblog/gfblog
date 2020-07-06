<?php

namespace addons\blog\controller;

use think\addons\Controller;
use think\Config;

/**
 * Blog控制器基类
 */
class Base extends Controller
{

    // 初始化
    public function __construct()
    {
        parent::__construct();
        $config = get_addon_config('blog');
        // 设定主题模板目录
        $this->view->engine->config('view_path', $this->view->engine->config('view_path') . $config['theme'] . DS);
        // 加载自定义标签库
        //$this->view->engine->config('taglib_pre_load', 'addons\blog\taglib\Blog');
        $config['indexurl'] = addon_url('blog/index/index', [], false);
        $categorylist = \addons\blog\model\Category::where('status', 'normal')->order('weigh desc,id desc')->cache(true)->select();
        $this->view->assign("categorylist", $categorylist);
        Config::set('blog', $config);
    }

}
