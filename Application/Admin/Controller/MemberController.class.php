<?php
namespace Admin\Controller;
use Think\Controller;
class MemberController extends BaseController {

	public function user() {
		$user = M('user');
		$data = $user -> select();
		$this -> assign('data',$data);

		$this -> display();
	}

	public function admin() {
		$user = M('user');
		$map['group_id'] = array('IN','1,2');
		$data = $user -> where($map) -> select();
		$this -> assign('data',$data);

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
