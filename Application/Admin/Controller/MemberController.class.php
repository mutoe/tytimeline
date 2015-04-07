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
}
