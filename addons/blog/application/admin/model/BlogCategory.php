<?php

namespace app\admin\model;

use addons\blog\library\FulltextSearch;
use think\Model;

class BlogCategory extends Model
{

    // 表名
    protected $name = 'blog_category';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
        'url',
        'fullurl',
        'flag_text',
        'status_text'
    ];
    protected static $config = [];

    protected static function init()
    {
        $config = get_addon_config('blog');
        self::$config = $config;
        self::afterInsert(function ($row) {
            $row->save(['weigh' => $row['id']]);
        });
        self::afterWrite(function ($row) use ($config) {
            $changedData = $row->getChangedData();
            if (isset($changedData['status']) && $changedData['status'] == 'normal') {
                if ($config['baidupush']) {
                    //推送到百度收录
                    $urls = [$row->fullurl];
                    \think\Hook::listen("baidupush", $urls);
                }
            }
        });
    }

    public function getUrlAttr($value, $data)
    {
        $diyname = $data['diyname'] ? $data['diyname'] : $data['id'];
        return addon_url('blog/category/index', [':id' => $data['id'], ':diyname' => $diyname], static::$config['urlsuffix']);
    }

    public function getFullurlAttr($value, $data)
    {
        $diyname = $data['diyname'] ? $data['diyname'] : $data['id'];
        return addon_url('blog/category/index', [':id' => $data['id'], ':diyname' => $diyname], static::$config['urlsuffix'], true);
    }

    public function getFlagList()
    {
        return ['hot' => __('Hot'), 'index' => __('Index'), 'recommend' => __('Recommend')];
    }

    public function getFlagTextAttr($value, $data)
    {
        $value = $value ? $value : $data['flag'];
        $valueArr = explode(',', $value);
        $list = $this->getFlagList();
        return implode(',', array_intersect_key($list, array_flip($valueArr)));
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

}
