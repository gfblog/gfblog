<?php

namespace addons\blog\taglib;

use fast\Random;
use think\Cache;
use think\template\TagLib;

class Blog extends TagLib
{

    /**
     * 定义标签列表
     */
    protected $tags = [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'config'      => ['attr' => 'name', 'close' => 0],
        'execute'     => ['attr' => 'sql', 'close' => 0],
        'block'       => ['attr' => 'id,name', 'close' => 0],
        'query'       => ['attr' => 'id,empty,key,mod,sql,cache', 'close' => 1],
        'arclist'     => ['attr' => 'id,row,limit,empty,key,mod,cache,orderby,orderway,imgwidth,imgheight,condition,model,type,field,flag', 'close' => 1],
        'blocklist'   => ['attr' => 'id,row,limit,empty,key,mod,cache,orderby,orderway,imgwidth,imgheight,condition,name', 'close' => 1],
    ];

    public function tagExecute($tag, $content)
    {
        $sql = isset($tag['sql']) ? $tag['sql'] : '';
        $sql = addslashes($sql);
        $parse = '<?php ';
        $parse .= '\think\Db::execute(\'' . $sql . '\');';
        $parse .= ' ?>';
        return $parse;
    }

    public function tagQuery($tag, $content)
    {
        $id = isset($tag['id']) ? $tag['id'] : 'item';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod = isset($tag['mod']) ? $tag['mod'] : '2';
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        $var = Random::alnum(10);
        $parse = '<?php ';
        $parse .= '$__' . $var . '__ = \addons\blog\model\Post::getQueryList([' . implode(',', $params) . ']);';
        $parse .= ' ?>';
        $parse .= '{volist name="$__' . $var . '__" id="' . $id . '" empty="' . $empty . '" key="' . $key . '" mod="' . $mod . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        $parse .= '{php}$__LASTLIST__=$__' . $var . '__;{/php}';
        return $parse;
    }

    public function tagArclist($tag, $content)
    {
        $id = $tag['id'];
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod = isset($tag['mod']) ? $tag['mod'] : '2';
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['category', 'condition', 'keywords'])) {
                $v = $this->autoBuildVar($v);
            }
            if (in_array($k, ['category', 'keywords'])) {
                $v = preg_match("/^\d+[0-9\,]+\d+$/i", $v) ? '"' . $v . '"' : $v;
            } else {
                $v = '"' . $v . '"';
            }
            $params[] = '"' . $k . '"=>' . $v;
        }
        $var = Random::alnum(10);
        $parse = '<?php ';
        $parse .= '$__' . $var . '__ = \addons\blog\model\Post::getPostList([' . implode(',', $params) . ']);';
        $parse .= ' ?>';
        $parse .= '{volist name="$__' . $var . '__" id="' . $id . '" empty="' . $empty . '" key="' . $key . '" mod="' . $mod . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        $parse .= '{php}$__LASTLIST__=$__' . $var . '__;{/php}';
        return $parse;
    }

    public function tagConfig($tag)
    {
        $name = $tag['name'];
        $parse = '<?php ';
        $parse .= 'echo \think\Config::get("' . $name . '");';
        $parse .= ' ?>';
        return $parse;
    }

    public function tagBlock($tag)
    {
        return \addons\blog\model\Block::getBlockContent($tag);
    }

    public function tagBlocklist($tag, $content)
    {
        $id = $tag['id'];
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod = isset($tag['mod']) ? $tag['mod'] : '2';
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        $var = Random::alnum(10);
        $parse = '<?php ';
        $parse .= '$__' . $var . '__ = \addons\blog\model\Block::getBlockList([' . implode(',', $params) . ']);';
        $parse .= ' ?>';
        $parse .= '{volist name="$__' . $var . '__" id="' . $id . '" empty="' . $empty . '" key="' . $key . '" mod="' . $mod . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        $parse .= '{php}$__LASTLIST__=$__' . $var . '__;{/php}';
        return $parse;
    }
}
