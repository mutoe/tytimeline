<?php
namespace Home\Controller;
use Common\Controller\CommonController;

class BaseController extends CommonController {
	protected function _initialize() {
	  parent::_initialize();
		if(I('cookie.user_id',0)) {//如果cookie不为空
			if(is_null(session('user_id'))) {//如果未登录
				if($this -> checkpwd(cookie('user_id'), cookie('user_mm'))) {
					$this -> set_loginfo(cookie('user_id'));
					session('user_id',cookie('user_id')); //自动登录
				}
			}
		}

    // 检查是否存在未读通知 MARK 运行效率问题
    $user_id = is_login();
    if($user_id) {
      $notice = M('notice');
      $count = $notice -> where('user_id=%d AND status=1', $user_id) -> count();
      $user_info = M('user_info');
      $user_info -> where('user_id=%d', $user_id) -> setField('unread_notice', $count);
      $this -> assign('unread_notice', $count);
    }

	}

	protected function set_loginfo($user_id) {
		$user = M('user');
		$user -> where('user_id=%d', $user_id) -> setInc('login_times');
		$data['lastlogin_time'] = time();
		$data['lastlogin_ip'] = get_client_ip();
		return $user -> where('user_id=%d',$user_id) -> save($data);
	}

	protected function setHeat() {
		$share = M('share');
		$list = $share -> getField('share_id', true);
		$shard = A('Shard');
		foreach ($list as $value) {
			$result = $shard -> get_heat($value);
		}
		$list = $share -> getField('share_id,heat');
	}

  /**
   * 用户反馈处理方法
   */
  public function handleFeedbackResult($result, $feedback_id = 0) {
    if(!is_admin() or !IS_AJAX or $feedback_id == 0) $this -> error("非法请求！");
    $admin = A('Admin/Base');
    $admin -> handleFeedbackResult($result, $feedback_id);
  }

}
