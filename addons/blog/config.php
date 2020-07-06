<?php

return array (
  0 => 
  array (
    'name' => 'name',
    'title' => '博客名称',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '郭峰博客',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'enname',
    'title' => '博客英文名称',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'GuoFeng\'s Blog',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'name' => 'theme',
    'title' => '皮肤名称',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'default',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  3 => 
  array (
    'name' => 'keywords',
    'title' => '关键字',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '郭峰,郭峰博客',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  4 => 
  array (
    'name' => 'description',
    'title' => '描述',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '郭峰,郭峰博客',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  5 => 
  array (
    'name' => 'intro',
    'title' => '个人简介',
    'type' => 'text',
    'content' => 
    array (
    ),
    'value' => '崇尚科技、自由和创造力',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  6 => 
  array (
    'name' => 'listpagesize',
    'title' => '列表每页显示数量',
    'type' => 'number',
    'content' => 
    array (
    ),
    'value' => '10',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  7 => 
  array (
    'name' => 'commentpagesize',
    'title' => '评论每页显示数量',
    'type' => 'number',
    'content' => 
    array (
    ),
    'value' => '10',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  8 => 
  array (
    'name' => 'avatar',
    'title' => '头像',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => '/uploads/20200629/35974ff01f6209768949989d6863165e.jpeg',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  9 => 
  array (
    'name' => 'donate',
    'title' => '打赏图片',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => '/uploads/20200629/d6c0bda160d38b1cbbf3c27e31865e4d.png',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  10 => 
  array (
    'name' => 'logo',
    'title' => 'Logo图片',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => '/uploads/20200629/cbaba996106e30c1ccd25fd66aeec9c0.png',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  11 => 
  array (
    'name' => 'background',
    'title' => '背景图片',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => '/uploads/20200629/6373366cda04f43c042ca259324274ff.jpg',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  12 => 
  array (
    'name' => 'copyright',
    'title' => '底部版权信息',
    'type' => 'text',
    'content' => 
    array (
    ),
    'value' => 'Copyright @ 2020',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  13 => 
  array (
    'name' => 'iscommentaudit',
    'title' => '发表评论审核',
    'type' => 'radio',
    'content' => 
    array (
      1 => '全部审核',
      0 => '无需审核',
      -1 => '仅含有过滤词时审核',
    ),
    'value' => '-1',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  14 => 
  array (
    'name' => 'audittype',
    'title' => '审核方式',
    'type' => 'radio',
    'content' => 
    array (
      'local' => '本地',
      'baiduyun' => '百度云',
    ),
    'value' => 'local',
    'rule' => 'required',
    'msg' => '',
    'tip' => '如果启用百度云，请输入百度云AI平台应用的AK和SK',
    'ok' => '',
    'extend' => '',
  ),
  15 => 
  array (
    'name' => 'aip_appid',
    'title' => '百度AI平台应用Appid',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => '',
    'msg' => '',
    'tip' => '百度云AI开放平台应用AppId',
    'ok' => '',
    'extend' => '',
  ),
  16 => 
  array (
    'name' => 'aip_apikey',
    'title' => '百度AI平台应用Apikey',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => '',
    'msg' => '',
    'tip' => '百度云AI开放平台应用ApiKey',
    'ok' => '',
    'extend' => '',
  ),
  17 => 
  array (
    'name' => 'aip_secretkey',
    'title' => '百度AI平台应用Secretkey',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => '',
    'msg' => '',
    'tip' => '百度云AI开放平台应用Secretkey',
    'ok' => '',
    'extend' => '',
  ),
  18 => 
  array (
    'name' => 'searchtype',
    'title' => '搜索方式',
    'type' => 'radio',
    'content' => 
    array (
      'local' => '本地搜索，采用Like(无需配置,效率低)',
      'xunsearch' => '采用Xunsearch全文搜索(需安装插件+配置)',
    ),
    'value' => 'local',
    'rule' => 'required',
    'msg' => '',
    'tip' => '如果启用Xunsearch全文搜索，需安装Xunsearch插件并配置Xunsearch服务器',
    'ok' => '',
    'extend' => '',
  ),
  19 => 
  array (
    'name' => 'baidupush',
    'title' => '百度主动推送链接',
    'type' => 'radio',
    'content' => 
    array (
      1 => '开启',
      0 => '关闭',
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '',
    'tip' => '如果开启百度主动推送链接，将在日志发布时自动进行推送',
    'ok' => '',
    'extend' => '',
  ),
  20 => 
  array (
    'name' => 'pagemode',
    'title' => '分页模式',
    'type' => 'radio',
    'content' => 
    array (
      'normal' => '普通分页模式',
      'infinite' => '采用无限加载更多模式',
    ),
    'value' => 'infinite',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  21 => 
  array (
    'name' => 'commentavatarmode',
    'title' => '评论头像模式',
    'type' => 'radio',
    'content' => 
    array (
      'letter' => '根据名称使用字母头像',
      'gravatar' => '根据Email使用Gravatar头像',
    ),
    'value' => 'gravatar',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  22 => 
  array (
    'name' => 'markdown',
    'title' => '日志使用Markdown格式',
    'type' => 'radio',
    'content' => 
    array (
      0 => '否',
      1 => '是',
    ),
    'value' => '1',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  23 => 
  array (
    'name' => 'cachelifetime',
    'title' => '缓存默认时长',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'true',
    'rule' => 'required',
    'msg' => '',
    'tip' => '0表示不缓存,具体数字表示缓存时长,true表示永久缓存',
    'ok' => '',
    'extend' => '',
  ),
  24 => 
  array (
    'name' => 'urlsuffix',
    'title' => 'URL后缀',
    'type' => 'string',
    'content' => 
    array (
      1 => '开启',
      0 => '关闭',
    ),
    'value' => 'html',
    'rule' => '',
    'msg' => '如果不需要后缀可以设置为空',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  25 => 
  array (
    'name' => 'domain',
    'title' => '绑定二级域名前缀',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  26 => 
  array (
    'name' => 'rewrite',
    'title' => '伪静态',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      'index/index' => '/$',
      'archive/index' => '/archive',
      'search/index' => '/search',
      'category/index' => '/[:diyname]$',
      'post/index' => '/[:catename]/[:diyname]$',
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
);
