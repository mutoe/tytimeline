<?php
namespace Admin\Controller;
use Think\Controller;
class BaseController extends Controller {
	public function _initialize() {
		if(!is_login()) {
			$this -> error('你还没有登陆', U('Home/Login/index'));
		} elseif(!is_admin()) {
			$this -> error('你没有权力这么做', U('Home/Index/index'));
		}
	}
}
