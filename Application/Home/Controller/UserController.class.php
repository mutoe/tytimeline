<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends BaseController {

	public function index($user_id = 0) {
		if(!$user_id) $user_id = is_login();
		if(!$user_id) $this -> redirect('Login/index');

		//加载个人分享
		$share = M('share');
		$share_list = $share -> where('user_id=%d',$user_id) -> order('create_time desc') -> limit(40) -> select();
		$this -> assign('share', $share_list);

		//加载个人信息
		$user = M('user');
		$data = $user -> where('user_id=%d',$user_id) -> field('password,email', true) -> find();
		$user_info = M('user_info');
		$user_info_data = $user_info -> where('user_id=%d',$user_id) -> find();
		$this -> assign('user', $data);
		$this -> assign('user_info', $user_info_data);


		$this -> display();
	}


	/**
	 * 登出操作
	 */
	public function logout() {
		cookie('user_id',null);
		cookie('user_mm',null);
		session('user_id',null);
		$this -> success('退出成功，正在跳转到首页...', U('Index/index'), 1);
	}
}
