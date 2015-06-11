<?php
namespace Admin\Controller;
use Think\Controller;
class MemberController extends BaseController {

	public function user($user_id = 0) {
		$user = M('user');

    if($user_id) $map['user_id'] = $user_id;
    else $map = "";

    $p = getpage($user, $map, 15);
    $this -> assign('page', $p -> show());

		$list = $user -> where($map) -> select();
		$this -> assign('data',$list);

		$this -> display();
	}

	public function admin($user_id = 0) {
		$user = M('user');
    if($user_id) $map['user_id'] = $user_id;
    else $map = "";
		$map['group_id'] = array('IN','1,2');

    $p = getpage($user, $map, 15);
    $this -> assign('page', $p -> show());

    $list = $user -> where($map) -> select();
    $this -> assign('data',$list);

    $this -> display();
	}

	/**
	 * 更改用户组
	 */
	public function modUser($user_id = 0) {
		if(!$user_id) $this -> error('参数有误');

		$user_group = M('user_group');
		$group = $user_group -> select();
		$this -> assign('group', $group);

		$user = M('user');
		$data = $user -> find($user_id);
		$this -> assign('data', $data);

		$this -> display();
	}

	public function submitModUser($user_id = 0) {
		if(!$user_id) $this -> error('参数有误');
		if($user_id == 1) $this -> error('你不能这么做');	// 不能修改id为1的权限

		$user = M('user');
		$data['user_id'] = $user_id;
		$data['nickname'] = I('post.nickname');

		$result = $user -> save($data);

		if($result) $this -> success('成功');
		else $this -> error('提交失败');
	}

	/**
	 * 最高权限用户管理
	 */
	public function submitSupUser($user_id = 0) {
		if(!$user_id) $this -> error('参数有误');
		if( get_usergroup( is_login() ) != 1 ) $this -> error('你没有这种权限');	//如果不是超级管理员，拒绝操作
		if($user_id == 1) $this -> error('你不能这么做');	// 不能修改id为1的权限

		$user = M('user');
		$data['user_id'] = $user_id;
		$data['email'] = I('post.email');
		$data['group_id'] = I('post.group_id');

		$result = $user -> save($data);

		if($result) $this -> success('成功');
		else $this -> error('提交失败');

	}

	/**
	 * 删除用户操作
	 */
	public function delUser($user_id = 0) {
		if(!$user_id) $this -> error('参数有误');
		$flag = 1;
		$user = M('user');
		$result = $user -> delete($user_id);
		if(!$result) $flag = 0;
		$user_info = M('user_info');
		$result = $user_info -> delete($user_id);
		if(!$result) $flag = 0;

		if($flag) {
			$this -> success();
		} else {
			$this -> error('删除失败！');
		}
	}

}
