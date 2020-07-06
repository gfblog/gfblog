<?php

namespace addons\blog\model;

use addons\blog\library\Markdown;
use think\Cache;
use think\Model;

class Post extends Model
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
        'create_date',
    ];
    protected static $config = [];

    protected static function init()
    {
        $config = get_addon_config('blog');
        self::$config = $config;
        self::afterInsert(function ($row) {
            $row->save(['weigh' => $row['id']]);
        });
    }

    public function getCreateDateAttr($value, $data)
    {
        return human_date($data['createtime']);
    }

    public function getSummaryAttr($value, $data)
    {
        return static::$config['markdown'] ? Markdown::text($value) : $value;
    }

    public function getContentAttr($value, $data)
    {
        return static::$config['markdown'] ? Markdown::text($value) : $value;
    }

    public function getThumbAttr($value, $data)
    {
        $value = $value ? $value : '/assets/addons/blog/img/thumb.png';
        return cdnurl($value, true);
    }

    public function getImageAttr($value, $data)
    {
        $value = $value ? $value : '/assets/addons/blog/img/thumb.png';
        return cdnurl($value, true);
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

    public function getUrlAttr($value, $data)
    {
        $diyname = isset($data['diyname']) && $data['diyname'] ? $data['diyname'] : $data['id'];
        $catename = isset($this->category) && $this->category ? $this->category->diyname : 'all';
        $cateid = isset($this->category) && $this->category ? $this->category->id : 0;
        return addon_url('blog/post/index', [':id' => $data['id'], ':diyname' => $diyname, ':catename' => $catename, ':cateid' => $cateid]);
    }

    public function getFullurlAttr($value, $data)
    {
        $diyname = isset($data['diyname']) && $data['diyname'] ? $data['diyname'] : $data['id'];
        $catename = isset($this->category) && $this->category ? $this->category->diyname : 'all';
        $cateid = isset($this->category) && $this->category ? $this->category->id : 0;
        return addon_url('blog/post/index', [':id' => $data['id'], ':diyname' => $diyname, ':catename' => $catename, ':cateid' => $cateid], true, true);
    }

    /**
     * 获取日志列表
     * @param $tag
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public static function getPostList($tag)
    {
        $config = get_addon_config('blog');
        $category = !isset($tag['category']) ? '' : $tag['category'];
        $condition = empty($tag['condition']) ? '' : $tag['condition'];
        $field = empty($params['field']) ? '*' : $params['field'];
        $flag = empty($tag['flag']) ? '' : $tag['flag'];
        $row = empty($tag['row']) ? 10 : (int)$tag['row'];
        $orderby = empty($tag['orderby']) ? 'createtime' : $tag['orderby'];
        $orderway = empty($tag['orderway']) ? 'desc' : strtolower($tag['orderway']);
        $limit = empty($tag['limit']) ? $row : $tag['limit'];
        $cache = !isset($tag['cache']) ? $config['cachelifetime'] === 'true' ? true : (int)$config['cachelifetime'] : (int)$tag['cache'];
        $orderway = in_array($orderway, ['asc', 'desc']) ? $orderway : 'desc';
        $cache = !$cache ? false : $cache;
        $where = ['status' => 'normal'];

        if ($category !== '') {
            $where['category_id'] = ['in', $category];
        }
        //如果有设置标志,则拆分标志信息并构造condition条件
        if ($flag !== '') {
            if (stripos($flag, '&') !== false) {
                $arr = [];
                foreach (explode('&', $flag) as $k => $v) {
                    $arr[] = "FIND_IN_SET('{$v}', flag)";
                }
                if ($arr) {
                    $condition .= "(" . implode(' AND ', $arr) . ")";
                }
            } else {
                $condition .= ($condition ? ' AND ' : '');
                $arr = [];
                foreach (explode(',', str_replace('|', ',', $flag)) as $k => $v) {
                    $arr[] = "FIND_IN_SET('{$v}', flag)";
                }
                if ($arr) {
                    $condition .= "(" . implode(' OR ', $arr) . ")";
                }
            }
        }
        $order = $orderby == 'rand' ? 'rand()' : (in_array($orderby, ['createtime', 'updatetime', 'views', 'weigh', 'id']) ? "{$orderby} {$orderway}" : "createtime {$orderway}");
        $order = $orderby == 'weigh' ? $order . ',id DESC' : $order;

        $postModel = self::with('category');
        $list = $postModel
            ->where($where)
            ->where($condition)
            ->field($field)
            ->cache($cache)
            ->order($order)
            ->limit($limit)
            ->select();
        //$list = collection($list)->toArray();
        return $list;
    }

    /**
     * 获取SQL查询结果
     */
    public static function getQueryList($tag)
    {
        $sql = isset($tag['sql']) ? $tag['sql'] : '';
        $bind = isset($tag['bind']) ? $tag['bind'] : [];
        $cache = !isset($tag['cache']) ? true : (int)$tag['cache'];
        $name = md5("sql-" . $tag['sql']);
        $list = Cache::get($name);
        if (!$list) {
            $list = \think\Db::query($sql, $bind);
            Cache::set($name, $list, $cache);
        }
        return $list;
    }

    public function category()
    {
        return $this->belongsTo('addons\blog\model\Category')->setEagerlyType(1);
    }

}
