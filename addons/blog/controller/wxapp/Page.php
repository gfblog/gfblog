<?php

namespace addons\blog\controller\wxapp;

/**
 * 单页
 */
class Page extends Base
{

    protected $noNeedLogin = ['*'];

    /**
     * 关于我
     */
    public function aboutme()
    {
        $config = get_addon_config('blog');
        $my = [
            'avatar' => cdnurl($config['avatar'], true),
            'name'   => $config['name'],
            'enname' => $config['enname'],
            'intro'  => $config['intro'],
        ];
        $this->success('', ['my' => $my]);
    }

}
