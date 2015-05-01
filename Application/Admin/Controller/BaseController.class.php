<?php
namespace Admin\Controller;
use Think\Controller;
class BaseController extends Controller {
	public function _initialize() {

		// 如果还没有读取站点配置就从数据库调取
		if(C('SITE_VER') === null) {
			$config = M('config') -> getField('key,value');
			C($config);
		}

		// 如果没有登陆就踢回
		if(!is_login()) {
			$this -> error('你还没有登陆', U('Home/Login/index'));
		} elseif(!is_admin()) {
			$this -> error('你没有权力这么做', U('Home/Index/index'));
		}
	}
}
