<?php
return array(

	'URL_MODEL'          => '1', //URL模式

	//数据库配置信息
	'DB_TYPE' => 'mysql', // 数据库类型
	'DB_HOST' => 'localhost', // 服务器地址
	'DB_NAME' => 'tytimeline', // 数据库名
	'DB_USER' => 'root', // 用户名
	'DB_PWD' => '222222', // 密码
	'DB_PORT' => 3306, // 端口
	'DB_PREFIX' => 'tytl_', // 数据库表前缀
	'DB_CHARSET' => 'utf8', // 字符集
	'DB_DEBUG' => TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增

	'TMPL_PARSE_STRING'  =>array(
     '__ROOT__' => '/tytimeline/index.php', // 更改默认的/Public 替换规则
	),

);
