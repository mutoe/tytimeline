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

function time_format($time, $format='Y-m-d H:i:s') {
	return date($format, $time);
}
/**
 * 判断是否登录
 * @return int 0:未登录 int:用户ID
 */
function is_login() {
	if(is_null(session('user_id'))) return 0;
	return I('session.user_id');
}
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
	$array = explode(',',$string);
	$result = '';
	foreach ($array as $tag_id) {
		foreach ($tag_list as $tag_value) {
			if($tag_value['tag_id'] == $tag_id) {
				$result .= $tag_value['tag_name'].' ';
			}
		}
	}
	return $result;
}
/**
 * 输出tag列表 前端badge形式
 */
function show_tag($string, $color = true) {
	if(S('tag')) {
		$tag_list = S('tag');
	} else {
		$tag = M('tag');
		$tag_list = $tag -> field('tag_id,tag_name') -> select();
		S('tag', $tag_list);
	}
	$array = explode(',',$string);
	if($color) {
		$color_list = array('','am-badge-primary','am-badge-danger','am-badge-warning','am-badge-success','am-badge-secondary');
	} else $color_list = array('');
	$result = '';
	foreach ($array as $tag_id) {
		foreach ($tag_list as $tag_value) {
			if($tag_value['tag_id'] == $tag_id) {
				$c = array_rand($color_list);
				$result .= '<span class="am-badge '. $color_list[$c] .'">'.$tag_value['tag_name'].'</span>&nbsp;';
			}
		}
	}
	return $result;
}


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
 * 天佑 timeline 密码加密技术 用邮箱作为盐分
 * @param mixed $user_id 用户id或者nickname
 * @param string 32位加密后的md5密码
 * @return string 32为加盐md5密码
 * @author mt
 * TODO:构思好了还未实现
 */
function md5tl($user, $md5passwd) {
	$user = M('user');
	$email = $user -> where('user_id=%d', $user_id) -> getField('email');
	return md5($md5passwd. $email);
}





