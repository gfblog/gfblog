<?php

namespace addons\blog\library;

use addons\blog\library\aip\AipContentCensor;
use addons\blog\library\SensitiveHelper;

class Service
{
    /**
     * 检测内容是否合法
     * @param $content
     * @return bool
     */
    public static function isContentLegal($content)
    {
        $config = get_addon_config('blog');
        if ($config['audittype'] == 'local') {
            // 敏感词过滤
            $handle = SensitiveHelper::init()->setTreeByFile(ADDON_PATH . 'blog/data/words.dic');
            //首先检测是否合法
            $isLegal = $handle->islegal($content);
            return $isLegal ? true : false;
        } else {
            $client = new AipContentCensor($config['aip_appid'], $config['aip_apikey'], $config['aip_secretkey']);
            $result = $client->antiSpam($content);
            if (isset($result['result']) && $result['result']['spam'] > 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * 内容关键字自动加链接
     */
    public static function autolinks($value)
    {
        $links = [];

        $value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) {
            return '<' . array_push($links, $match[1]) . '>';
        }, $value);

        $config = get_addon_config('blog');
        $autolinks = $config['autolinks'];
        $value = preg_replace_callback('/(' . implode('|', array_keys($autolinks)) . ')/i', function ($match) use ($autolinks) {
            if (!isset($autolinks[$match[1]])) {
                return $match[0];
            } else {
                return '<a href="' . $autolinks[$match[1]] . '" target="_blank">' . $match[0] . '</a>';
            }
        }, $value);
        return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) {
            return $links[$match[1] - 1];
        }, $value);
    }

}