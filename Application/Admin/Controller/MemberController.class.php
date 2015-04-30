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

	public function mod_user($user_id = 0) {
		if(!$user_id) $this -> error('参数操作');
		$user_group = M('user_group');
		$group = $user_group -> select();
		$this -> assign('group', $group);

		$user = M('user');
		$data = $user -> find($user_id);
		$this -> assign('data', $data);

		$this -> display();
	}
}
