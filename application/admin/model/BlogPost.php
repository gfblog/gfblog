<?php

namespace app\admin\model;

use addons\blog\library\FulltextSearch;
use think\Model;

class BlogPost extends Model
{

    // 表名
    protected $name = 'blog_post';
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
            if ($config['searchtype'] == 'xunsearch') {
                //更新全文搜索
                FulltextSearch::update($row->id);
            }
        });
        self::afterDelete(function ($row) use ($config) {
            if ($config['searchtype'] == 'xunsearch') {
                //更新全文搜索
                FulltextSearch::del($row);
            }
        });
    }

    public function getUrlAttr($value, $data)
    {
        $diyname = isset($data['diyname']) && $data['diyname'] ? $data['diyname'] : $data['id'];
        $catename = isset($this->category) && $this->category ? $this->category->diyname : 'all';
        $cateid = isset($this->category) && $this->category ? $this->category->id : 0;
        return addon_url('blog/post/index', [':id' => $data['id'], ':diyname' => $diyname, ':catename' => $catename, ':cateid' => $cateid], static::$config['urlsuffix']);
    }

    public function getFullurlAttr($value, $data)
    {
        $diyname = isset($data['diyname']) && $data['diyname'] ? $data['diyname'] : $data['id'];
        $catename = isset($this->category) && $this->category ? $this->category->diyname : 'all';
        $cateid = isset($this->category) && $this->category ? $this->category->id : 0;
        return addon_url('blog/post/index', [':id' => $data['id'], ':diyname' => $diyname, ':catename' => $catename, ':cateid' => $cateid], static::$config['urlsuffix'], true);
    }

    public function getFlagList()
    {
        return ['hot' => __('Hot'), 'index' => __('Index'), 'recommend' => __('Recommend')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getFlagTextAttr($value, $data)
    {
        $value = $value ? $value : $data['flag'];
        $valueArr = explode(',', $value);
        $list = $this->getFlagList();
        return implode(',', array_intersect_key($list, array_flip($valueArr)));
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setFlagAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    public function category()
    {
        return $this->belongsTo('BlogCategory', 'category_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

}
