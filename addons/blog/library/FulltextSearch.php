<?php

namespace addons\blog\library;

use addons\xunsearch\library\Xunsearch;
use think\Config;
use think\Exception;
use think\View;

class FulltextSearch
{

    public static function config()
    {
        $data = [
            [
                'name'   => 'blog',
                'title'  => '简洁博客系统',
                'fields' => [
                    ['name' => 'pid', 'type' => 'id', 'title' => '主键'],
                    ['name' => 'id', 'type' => 'numeric', 'title' => 'ID'],
                    ['name' => 'title', 'type' => 'title', 'title' => '标题'],
                    ['name' => 'content', 'type' => 'body', 'title' => '内容',],
                    ['name' => 'type', 'type' => 'string', 'title' => '类型', 'index' => 'self'],
                    ['name' => 'url', 'type' => 'string', 'title' => '链接',],
                    ['name' => 'createtime', 'type' => 'date', 'title' => '发布日期',],
                    ['name' => 'views', 'type' => 'numeric', 'title' => '浏览次数',],
                    ['name' => 'comments', 'type' => 'numeric', 'title' => '评论次数',],
                ]
            ]
        ];
        return $data;
    }

    /**
     * 重置搜索索引数据库
     */
    public static function reset()
    {
        \addons\blog\model\Post::where('status', 'normal')->chunk(100, function ($list) {
            foreach ($list as $item) {
                self::add($item);
            }
        });
        return true;
    }

    /**
     * 添加索引
     * @param $row
     */
    public static function add($row)
    {
        self::update($row, true);
    }

    /**
     * 更新索引
     * @param      $row
     * @param bool $add
     */
    public static function update($row, $add = false)
    {
        if (is_numeric($row)) {
            $row = \addons\blog\model\Post::get($row);
            if (!$row) {
                return;
            }
        }
        if (isset($row['status']) && $row['status'] != 'normal') {
            self::del($row);
            return;
        }
        $data = [];
        if ($row instanceof \addons\blog\model\Post || $row instanceof \app\admin\model\BlogPost) {
            $data['id'] = isset($row['id']) ? $row['id'] : 0;
            $data['title'] = isset($row['title']) ? $row['title'] : '';
            $data['content'] = isset($row['content']) ? strip_tags($row['content']) : '';
            $data['type'] = isset($row['type']) ? $row['type'] : '';
            $data['url'] = isset($row['fullurl']) ? $row['fullurl'] : addon_url('blog/post/index', [':id' => $data['id']], true, true);
            $data['createtime'] = isset($row['createtime']) ? $row['createtime'] : '';
            $data['views'] = isset($row['views']) ? $row['views'] : '0';
            $data['comments'] = isset($row['comments']) ? $row['comments'] : '0';
            $data['pid'] = "p" . $data['id'];
        }
        if ($data) {
            Xunsearch::instance('blog')->update($data, $add);
        }
    }

    /**
     * 删除
     * @param $row
     */
    public static function del($row)
    {
        $pid = "p" . (is_numeric($row) ? $row : ($row && isset($row['id']) ? $row['id'] : 0));
        if ($pid) {
            Xunsearch::instance('blog')->del($pid);
        }

    }

    /**
     * 获取搜索结果
     * @return array
     */
    public static function search($q, $page = 1, $pagesize = 20, $order = '', $fulltext = true, $fuzzy = false, $synonyms = false)
    {
        return Xunsearch::instance('blog')->search($q, $page, $pagesize, $order, $fulltext, $fuzzy, $synonyms);
    }

    /**
     * 获取建议搜索关键字
     * @param string $q     关键字
     * @param int    $limit 返回条数
     */
    public static function suggestion($q, $limit = 10)
    {
        return Xunsearch::instance('blog')->suggestion($q, $limit);
    }

    /**
     * 获取搜索热门关键字
     * @return array
     * @throws \XSException
     */
    public static function hot()
    {
        return Xunsearch::instance('blog')->getXS()->search->getHotQuery();
    }

}