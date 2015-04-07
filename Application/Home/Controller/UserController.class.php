<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends BaseController {
	public function index() {
		$this -> display();
	}
	public function logout() {
		cookie('user_id',null);
		cookie('user_mm',null);
		session('user_id',null);
		$this -> success('退出成功，正在跳转到首页...', U('Index/index'), 1);
	}
}
