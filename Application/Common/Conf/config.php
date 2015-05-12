<?php
return array(

	// 定义默认访问模块
	'MODULE_ALLOW_LIST'	=> array('Home', 'Admin'),
	'DEFAULT_MODULE'		=> 'Home',

	'URL_MODEL'					=> '1', // URL模式

	'TMPL_PARSE_STRING'	=> array(
     '__ROOT__' => '/timeline/index.php',
	),

	// 数据库配置信息
	'DB_TYPE'		=> 'mysql', // 数据库类型
	'DB_HOST'		=> 'localhost', // 服务器地址
	'DB_NAME'		=> 'tytimeline', // 数据库名
	'DB_USER'		=> 'tytimeline', // 用户名
	'DB_PWD'		=> 'tytimeline123!@#', // 密码
	'DB_PORT'		=> 3306, // 端口
	'DB_PREFIX'	=> 'tytl_', // 数据库表前缀
	'DB_CHARSET'=> 'utf8', // 字符集

	// 自定义跳转模板
	'TMPL_ACTION_ERROR'		=> 'Common:error',			// 默认错误跳转对应的模板文件
	'TMPL_ACTION_SUCCESS' => 'Common:success',	// 默认成功跳转对应的模板文件

	'COOKIE_PREFIX' => 'tytl_',
	'COOKIE_EXPIRE' => 3600 * 24 * 7 * 4,

	'DATA_CACHE_TYPE' => 'file', // 数据缓存方式 文件
	'DATA_CACHE_TIME' => 15, // 15s

	// 路由支持
	'URL_ROUTER_ON'   => true,
	'URL_ROUTE_RULES'	=> array(

		// 公共控制器
	  'FuckIE'				=> array('Base/FuckIE'),

		// 广场
	  'hot'						=> array('Ground/index', 'content=hot'),
	  'new'						=> array('Ground/index', 'content=new'),
	  'rand'					=> array('Ground/index', 'content=rand'),

		// 分类
		'catalog/:id\d'	=> array('Catalog/detail'),

		// 标签
		'tag/:tag_id\d'	=> array('Tag/detail'),

		// 用户
		'user/:user_id\d'		=> array('User/index'),

		// 分享
		':share_id\d'		=> array('Shard/detail'),

	),

);


