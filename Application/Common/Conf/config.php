<?php
return array(

//定义默认访问模块
'MODULE_ALLOW_LIST' => array('Home', 'Admin'),
'DEFAULT_MODULE' => 'Home',

'URL_MODEL'          => '2', //URL模式

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

//自定义跳转模板
'TMPL_ACTION_ERROR' => 'Common:error',			//默认错误跳转对应的模板文件
'TMPL_ACTION_SUCCESS' => 'Common:success',	//默认成功跳转对应的模板文件

'COOKIE_PREFIX' => 'tytl_',
'COOKIE_EXPIRE' => 3600 * 24 * 7 * 4,

'DATA_CACHE_TYPE' => 'file', // 数据缓存方式 文件
'DATA_CACHE_TIME' => 15, //15s

'SET_VERSION' => '0.1.8 alpha',//站点版本

);


