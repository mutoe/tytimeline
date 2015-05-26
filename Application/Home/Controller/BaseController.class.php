<?php
namespace Home\Controller;
use Common\Controller\CommonController;

class BaseController extends CommonController {
	protected function _initialize() {
		if(I('cookie.user_id',0)) {//如果cookie不为空
			if(is_null(session('user_id'))) {//如果未登录
				if($this -> checkpwd(cookie('user_id'), cookie('user_mm'))) {
					$this -> set_loginfo(cookie('user_id'));
					session('user_id',cookie('user_id')); //自动登录
				}
			}
		}
		//dump(I('session.'));
	}

	protected function set_loginfo($user_id) {
		$user = M('user');
		$user -> where('user_id=%d', $user_id) -> setInc('login_times');
		$data['lastlogin_time'] = time();
		$data['lastlogin_ip'] = get_client_ip();
		return $user -> where('user_id=%d',$user_id) -> save($data);
	}

	public function avatar($user_id = 0) {
		$url = 'http://bbs.cqjtu.edu.cn/uc_server/avatar.php?uid='.$user_id.'&size=big';
		echo file_get_contents($url);
	}

}
