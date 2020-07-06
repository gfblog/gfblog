<?php

namespace addons\blog\controller\wxapp;

use addons\blog\model\Category;
use think\Config;

/**
 * 公共
 */
class Common extends Base
{

    protected $noNeedLogin = '*';

    /**
     * 初始化
     */
    public function init()
    {

        //首页Tab列表
        $tabList = [['id' => 0, 'title' => '全部']];
        $channelList = Category::where('status', 'normal')
            ->field('id,pid,name,nickname,diyname')
            ->order('weigh desc,id desc')
            ->select();
        foreach ($channelList as $index => $item) {
            $tabList[] = ['id' => $item['id'], 'title' => $item['name']];
        }

        //配置信息
        $upload = Config::get('upload');
        $upload['cdnurl'] = $upload['cdnurl'] ? $upload['cdnurl'] : cdnurl('', true);
        $upload['uploadurl'] = $upload['uploadurl'] == 'ajax/upload' ? cdnurl('/ajax/upload', true) : $upload['cdnurl'];
        $config = [
            'upload' => $upload
        ];

        $data = [
            'tabList' => $tabList,
            'config'  => $config
        ];
        $this->success('', $data);
    }
}
