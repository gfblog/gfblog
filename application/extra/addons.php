<?php

return array (
  'autoload' => false,
  'hooks' => 
  array (
    'app_init' => 
    array (
      0 => 'blog',
    ),
    'xunsearch_config_init' => 
    array (
      0 => 'blog',
    ),
    'xunsearch_index_reset' => 
    array (
      0 => 'blog',
    ),
    'view_filter' => 
    array (
      0 => 'blog',
    ),
    'testhook' => 
    array (
      0 => 'markdown',
    ),
  ),
  'route' => 
  array (
    '/$' => 'blog/index/index',
    '/archive' => 'blog/archive/index',
    '/search' => 'blog/search/index',
    '/[:diyname]$' => 'blog/category/index',
    '/[:catename]/[:diyname]$' => 'blog/post/index',
  ),
);