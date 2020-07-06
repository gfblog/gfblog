<?php

namespace addons\blog\model;

use addons\blog\library\Service;
use addons\blog\library\CommentException;
use app\common\library\Email;
use think\Exception;
use think\Model;
use think\Validate;

class Comment extends Model
{

    // 表名
    protected $name = 'blog_comment';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
        'create_date'
    ];
    protected $config;

    public function initialize()
    {
        $this->config = get_addon_config('blog');
        parent::initialize();
    }

    public function getCreateDateAttr($value, $data)
    {
        return human_date($data['createtime']);
    }

    public function getAvatarAttr($value, $data)
    {
        if ($this->config['commentavatarmode'] == 'letter') {
            return letter_avatar($data['username']);
        } else {
            return isset($data['avatar']) && $data['avatar'] ? $data['avatar'] : "https://secure.gravatar.com/avatar/" . md5($data['email']) . "?s=96&d=&r=X";
        }
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

    public static function postComment($params)
    {
        $config = get_addon_config('blog');
        $request = request();
        $request->filter('trim,strip_tags,htmlspecialchars');
        $post_id = (int)$request->post("post_id");
        $pid = intval($request->post("pid", 0));
        $username = $request->post("username");
        $email = $request->post("email", "");
        $website = $request->post("website", "");
        $content = $request->post("content");
        $avatar = $request->post("avatar", "");
        $subscribe = intval($request->post("subscribe", 0));
        $useragent = substr($request->server('HTTP_USER_AGENT', ''), 0, 255);
        $ip = $request->ip();
        $website = $website != '' && substr($website, 0, 7) != 'http://' && substr($website, 0, 8) != 'https://' ? "http://" . $website : $website;
        $content = nl2br($content);
        $token = $request->post('__token__');

        $post = Post::get($post_id, ['category']);
        if (!$post || $post['status'] == 'hidden') {
            throw new Exception("日志未找到");
        }

        //审核状态
        $status = 'normal';
        if ($config['iscommentaudit'] == 1) {
            $status = 'hidden';
        } elseif ($config['iscommentaudit'] == 0) {
            $status = 'normal';
        } elseif ($config['iscommentaudit'] == -1) {
            if (!Service::isContentLegal($content)) {
                $status = 'hidden';
            }
        }

        $rule = [
            'pid|父ID'      => 'require|number',
            'username|用户名' => 'require|length:1,30',
            'email|邮箱'     => 'email|length:3,30',
            'website|网站'   => 'url|length:3,100',
            'content|内容'   => 'require|length:3,250',
            '__token__'    => 'require|token',
        ];
        $data = [
            'pid'       => $pid,
            'username'  => $username,
            'email'     => $email,
            'website'   => $website,
            'content'   => $content,
            '__token__' => $token,
        ];
        $message = [
            'username.length' => '用户名长度必须在1-30之间',
            'content.length'  => '评论最少输入3个字符'
        ];
        $validate = new Validate($rule, $message);
        $result = $validate->check($data);
        if (!$result) {
            throw new Exception($validate->getError());
        }

        $lastcomment = self::get(['post_id' => $post_id, 'email' => $email, 'ip' => $ip]);
        if ($lastcomment && time() - $lastcomment['createtime'] < 30) {
            throw new Exception("对不起！您发表评论的速度过快！请稍微休息一下，喝杯咖啡");
        }
        if ($lastcomment && $lastcomment['content'] == $content) {
            throw new Exception("您可能连续了相同的评论，请不要重复提交");
        }
        $data = [
            'pid'       => $pid,
            'post_id'   => $post_id,
            'username'  => $username,
            'email'     => $email,
            'content'   => $content,
            'avatar'    => $avatar,
            'ip'        => $ip,
            'useragent' => $useragent,
            'subscribe' => (int)$subscribe,
            'website'   => $website,
            'status'    => $status
        ];
        self::create($data);

        //发送通知
        if ($status === 'hidden') {
            throw new CommentException("发表评论成功，但评论需要显示审核后才会展示", 1);
        } else {
            $post->setInc('comments');
            if ($pid) {
                //查找父评论，是否并发邮件通知
                $parent = self::get($pid);
                if ($parent && $parent['subscribe'] && Validate::is($parent['email'], 'email') && $status == 'normal') {
                    $title = "{$parent['username']}，您发表在《{$post['title']}》上的评论有了新回复 - {$config['name']}";
                    $post_url = $post->fullurl;
                    $unsubscribe_url = addon_url("blog/comment/unsubscribe", ['id' => $parent['id'], 'key' => md5($parent['id'] . $parent['email'])]);
                    $content = "亲爱的{$parent['username']}：<br />您于" . date("Y-m-d H:i:s") .
                        "在《<a href='{$post_url}' target='_blank'>{$post['title']}</a>》上发表的评论<br /><blockquote>{$parent['content']}</blockquote>" .
                        "<br />{$username}发表了回复，内容是<br /><blockquote>{$content}</blockquote><br />您可以<a href='{$post_url}'>点击查看评论详情</a>。" .
                        "<br /><br />如果你不愿意再接受最新评论的通知，<a href='{$unsubscribe_url}'>请点击这里取消</a>";
                    $email = new Email;
                    $result = $email
                        ->to($parent['email'])
                        ->subject($title)
                        ->message('<div style="min-height:550px; padding: 100px 55px 200px;">' . $content . '</div>')
                        ->send();
                }
            }
        }
        return true;
    }

    public function sublist()
    {
        return $this->hasMany("Comment", "pid");
    }

}
