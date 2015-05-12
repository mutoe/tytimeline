<?php

function get_nickname($user_id = 0) {
	if($user_id == 0) {
		if(I('session.user_id', 0)) $user_id = I('session.user_id');
		else return null;
	}
	$user = M('user');
	$result = $user -> where('user_id=%d', $user_id) -> getField('nickname');
	return $result;
}

/**
 * 获取用户组
 */
function get_usergroup($user_id = 0, $show_id = true) {
	if($user_id == 0) {
		if(I('session.user_id', 0)) $user_id = I('session.user_id');
		else return null;
	}
	$user = M('user');
	$groupid = $user -> where('user_id=%d', $user_id) -> getField('group_id');
	if($show_id) return $groupid;
	$group = M('user_group');
	$result = $group -> where('group_id=%d', $groupid) -> getField('name');
	return $result;
}
function devide_month($ym) {
	$year = substr($ym, 0, 4);
	$month = substr($ym, 4, 2);
	return $year.'-'.$month;
}

/**
 * 格式化时间戳
 */
function time_format($time, $format='Y-m-d H:i:s') {
	return date($format, $time);
}
/**
 * 判断是否登录
 * @return int 0:未登录 正整数:用户ID
 */
function is_login() {
	if(is_null(session('user_id'))) return 0;
	return I('session.user_id');
}
/**
 * 获取管理员权限
 */
function is_admin($user_id = 0) {
	$admin_group = array(
		1, //超级管理员
		2, //站点管理员
	);
	$user_id != 0 or $user_id = I('session.user_id');
	$user = D('user');
	$result = $user -> where('user_id=%d',$user_id) -> getField('group_id');
	return in_array($result, $admin_group);
}


/**
 * 获取操作权限
 * @param string $method 权限名
 * @param int $id 辅助id
 * @return boolen
 * 			false:无权限
 * @author mt 2015.04.03
 */
function get_auth($method, $user_id = 0, $id = 0) {
	if($user_id == 0) $user_id = is_login();
	if($user_id == 0) return false;
	$user = M('User');
	$group = $user -> where('user_id=%d',$user_id) -> getField('group_id');
	$user_group = M('User_group');
	$auth = $user_group -> where('group_id=%d', $group) -> getField('auth');
	switch ($method) {
		case 'login':
			return $auth[0];
			break;
		case 'add_share':
			return $auth[1];
			break;
		case 'modi_share':
			$share = M('share');
			$data = $share -> where('share_id=%d', $id) -> getField('user_id');
			if($data == $user_id) return 1;
			return $auth[2];
			break;
		case 'like':
			return $auth[3];
			break;
		case 'comment':
			return $auth[4];
			break;
		case 'modify':
			$share = M('share');
			$data = $share -> where('share_id=%d', $id) -> getField('user_id');
			if($data == $user_id) return 1;
			return $auth[5];
			break;
		case 'delete':
			$share = M('share');
			$data = $share -> where('share_id=%d', $id) -> getField('user_id');
			if($data == $user_id) return 1;
			return $auth[6];
			break;
		case 'manage_comment':
			$comment = M('comment');
			$data = $comment -> where('comment_id=%d', $id) -> getField('user_id');
			if($data == $user_id) return 1;
			return $auth[7];
			break;
		case 'admin_page':
			return $auth[8];
			break;
		case 'manage_notice':
			return $auth[9];
			break;
		default:
			return false;
			break;
	}
}

/**
 * 输出tag列表 string形式
 */
function get_tag($string) {
	$tag = M('tag');
	$tag_list = $tag -> field('tag_id,tag_name') -> select();
	$array = json_decode($string);
	$result = '';
	foreach ($array as $tag_id) {
		foreach ($tag_list as $tag_value) {
			if($tag_value['tag_id'] == $tag_id) {
				$result .= $tag_value['tag_name'].',';
			}
		}
	}
	$result = trim($result,',');
	return $result;
}

/**
 * 根据catalog_id得到名字
 */
function get_catalog_name($catalog_id = 1) {
	$catalog = M('catalog');
	$result = $catalog -> where('catalog_id=%d', $catalog_id) -> getField('catalog_name');
	return $result;
}

/**
 * 字符串(以","分隔)转化为json串
 */
function str2json($string) {
	$array = explode(',',$string);
	$result = json_encode($array);
	return $result;
}

/**
 * 输出tag列表 前端badge形式
 */
function show_tag($json, $color = true, $css = false) {
	if(S('tag')) {
		$tag_list = S('tag');
	} else {
		$tag = M('tag');
		$tag_list = $tag -> field('tag_id,tag_name') -> select();
		S('tag', $tag_list);
	}
	$array = json_decode($json);
	if($color) {
		$color_list = array('','am-badge-primary','am-badge-danger','am-badge-warning','am-badge-success','am-badge-secondary');
	} else $color_list = array('');
	$result = '';
	foreach ($array as $tag_id) {
		foreach ($tag_list as $tag_value) {
			if($tag_value['tag_id'] == $tag_id) {
				$c = array_rand($color_list);
				if($css) {
					$result .= '<button alt="点击添加到标签" data-tag-id="'.$tag_value['tag_name'].'" class="tag-badge am-badge '. $color_list[$c] .'">'.$tag_value['tag_name'].'</button>&nbsp;';
				} else {
					if( C("URL_MODEL" == 1) ) {
						$result .= '<a href="'.__ROOT__.'/index.php/tag/'.$tag_id.'"><span class="am-badge '. $color_list[$c] .'">'.$tag_value['tag_name'].'</span></a>&nbsp;';
					} else {
						$result .= '<a href="'.__ROOT__.'/tag/'.$tag_id.'"><span class="am-badge '. $color_list[$c] .'">'.$tag_value['tag_name'].'</span></a>&nbsp;';
					}
				}
			}
		}
	}
	return $result;
}

/**
 * 获取时间差
 * @param timestamp
 */
function get_timing($stamp = 0) {
	$now_time = time();
	$timing = $now_time - $stamp;

	if($timing < 60) $result = $timing.' 秒';
	elseif($timing < 60 *60) $result = (int)($timing / 60).' 分钟';
	elseif($timing < 3600 *24) $result = (int)($timing / 3600).' 个小时';
	elseif($timing < 86400 *30) $result = (int)($timing / 86400).' 天';
	elseif($timing < 2592000 *12) $result = (int)($timing / 2592000).' 个月';
	else $result = (int)($timing / (365*86400) ).' 年';
	return ' '.$result;
}

/**
 * 获取喜欢分享的状态
 */
function get_like_status($share_id = 0, $user_id = 0) {
	if(!$share_id) return false;
	if(!$user_id) $user_id = is_login();
	if(!$user_id) return false;

	$user = M('user_info');
	$like_str = $user -> where('user_id=%d', $user_id) -> getField('like_share');
	$like_arr = explode(',', $like_str);
	return in_array($share_id, $like_arr);
}

/**
 * 判断是否移动端函数，来自Thinkphp论坛
 */
function ismobile() {
  // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
  if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
    return true;

  //此条摘自TPM智能切换模板引擎，判断是否为客户端
  if(isset ($_SERVER['HTTP_CLIENT']) &&'PhoneClient'==$_SERVER['HTTP_CLIENT'])
    return true;
  //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
  if (isset ($_SERVER['HTTP_VIA']))
    //找不到为flase,否则为true
    return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
  //判断手机发送的客户端标志,兼容性有待提高
  if (isset ($_SERVER['HTTP_USER_AGENT'])) {
    $clientkeywords = array(
      'nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile'
    );
    //从HTTP_USER_AGENT中查找手机浏览器的关键字
    if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
      return true;
    }
  }
  //协议法，因为有可能不准确，放到最后判断
  if (isset ($_SERVER['HTTP_ACCEPT'])) {
    // 如果只支持wml并且不支持html那一定是移动设备
    // 如果支持wml和html但是wml在html之前则是移动设备
    if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
      return true;
    }
  }
  return false;
}

function substring($str , $len = 20){
	$result = mb_substr($str, 0, $len, 'utf-8');
	if( strlen($str) > strlen($result) ) $result .= '...';
	return $result;
}
