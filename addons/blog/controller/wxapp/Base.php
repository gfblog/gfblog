<?php

namespace addons\blog\controller\wxapp;

use app\common\controller\Api;
use think\Lang;

class Base extends Api
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();

        //这里手动载入语言包
        Lang::load(ROOT_PATH . '/addons/blog/lang/zh-cn.php');
        Lang::load(APP_PATH . '/index/lang/zh-cn/user.php');
    }

}
