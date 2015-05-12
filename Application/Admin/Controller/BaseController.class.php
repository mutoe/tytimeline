<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class BaseController extends CommonController {

	public function _initialize() {
		// 如果没有登陆就踢回
		if(!is_login()) {
			$this -> error('你还没有登陆', U('Home/Login/index'));
		} elseif(!is_admin()) {
			$this -> error('你没有权力这么做', U('Home/Index/index'));
		}
	}

}
