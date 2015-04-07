<?php

$s = new SaeStorage();
$url = rtrim( $s -> getUrl('Public', ''), '/');
//获得名为 Public 的 domain 的域名

return array(

'TMPL_PARSE_STRING' => array(    //使用模版变量替换，将__PUBLIC__替换为 Storage 的域名。
	//'__PUBLIC__' => $url,
	//'/Public/upload' => $s -> getUrl('public','upload'),
)

);
