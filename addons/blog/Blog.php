<?php

namespace addons\blog;

use addons\blog\library\FulltextSearch;
use app\common\library\Menu;
use think\Addons;
use think\Config;

/**
 * 博客插件
 */
class Blog extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'blog',
                'title'   => '博客管理',
                'sublist' => [
                    [
                        'name'    => 'blog/post',
                        'title'   => '日志管理',
                        'sublist' => [
                            ['name' => 'blog/post/index', 'title' => '查看'],
                            ['name' => 'blog/post/add', 'title' => '添加'],
                            ['name' => 'blog/post/edit', 'title' => '修改'],
                            ['name' => 'blog/post/del', 'title' => '删除'],
                            ['name' => 'blog/post/multi', 'title' => '批量更新'],
                        ]
                    ],
                    [
                        'name'    => 'blog/category',
                        'title'   => '分类管理',
                        'sublist' => [
                            ['name' => 'blog/category/index', 'title' => '查看'],
                            ['name' => 'blog/category/add', 'title' => '添加'],
                            ['name' => 'blog/category/edit', 'title' => '修改'],
                            ['name' => 'blog/category/del', 'title' => '删除'],
                            ['name' => 'blog/category/multi', 'title' => '批量更新'],
                        ]
                    ],
                    [
                        'name'    => 'blog/comment',
                        'title'   => '评论管理',
                        'icon'    => 'fa fa-comment',
                        'sublist' => [
                            ['name' => 'blog/comment/index', 'title' => '查看'],
                            ['name' => 'blog/comment/add', 'title' => '添加'],
                            ['name' => 'blog/comment/edit', 'title' => '修改'],
                            ['name' => 'blog/comment/del', 'title' => '删除'],
                            ['name' => 'blog/comment/multi', 'title' => '批量更新'],
                        ]
                    ],
                    [
                        'name'    => 'blog/block',
                        'title'   => '区块管理',
                        'icon'    => 'fa fa-th-large',
                        'sublist' => [
                            ['name' => 'blog/block/index', 'title' => '查看'],
                            ['name' => 'blog/block/add', 'title' => '添加'],
                            ['name' => 'blog/block/edit', 'title' => '修改'],
                            ['name' => 'blog/block/del', 'title' => '删除'],
                            ['name' => 'blog/block/multi', 'title' => '批量更新'],
                        ]
                    ]
                ]
            ]
        ];
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('blog');
        return true;
    }

    /**
     * 插件启用方法
     */
    public function enable()
    {
        Menu::enable('blog');
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        Menu::disable('blog');
    }

    /**
     * 应用初始化
     */
    public function appInit()
    {
        $config = get_addon_config('blog');
        $taglib = Config::get('template.taglib_pre_load');
        Config::set('template.taglib_pre_load', ($taglib ? $taglib . ',' : '') . 'addons\\blog\\taglib\\Blog');
        Config::set('blog', $config);
    }

    public function xunsearchConfigInit()
    {
        return FulltextSearch::config();
    }

    public function xunsearchIndexReset($project)
    {
        if (!$project['isaddon'] || $project['name'] != 'blog') {
            return;
        }
        return FulltextSearch::reset();
    }

    /**
     * 脚本替换
     */
    public function viewFilter(& $content)
    {
        $request = \think\Request::instance();
        $dispatch = $request->dispatch();

        if ($request->module() || !isset($dispatch['method'][0]) || $dispatch['method'][0] != '\think\addons\Route') {
            return;
        }
        $addon = isset($dispatch['var']['addon']) ? $dispatch['var']['addon'] : $request->param('addon');
        if ($addon != 'blog') {
            return;
        }
        $style = '';
        $script = '';
        $result = preg_replace_callback("/<(script|style)\s(data\-render=\"(script|style)\")([\s\S]*?)>([\s\S]*?)<\/(script|style)>/i", function ($match) use (&$style, &$script) {
            if (isset($match[1]) && in_array($match[1], ['style', 'script'])) {
                ${$match[1]} .= str_replace($match[2], '', $match[0]);
            }
            return '';
        }, $content);
        $content = preg_replace_callback('/^\s+(\{__STYLE__\}|\{__SCRIPT__\})\s+$/m', function ($matches) use ($style, $script) {
            return $matches[1] == '{__STYLE__}' ? $style : $script;
        }, $result ? $result : $content);
    }
}
