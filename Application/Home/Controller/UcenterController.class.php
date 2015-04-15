<?php
namespace Home\Controller;
use Think\Controller;

class UcenterController extends Controller {
	
	public function ucAvatar($uid='38237') {
		$uc = new \Ucenter\Client\Client();
		$ucresult = $uc->uc_check_avatar($uid);
		if($ucresult == 1){
			echo file_get_contents('http://bbs.cqjtu.edu.cn/uc_server/avatar.php?uid='.$uid);
		}elseif($ucresult == 0){
			echo 'meiyou';
		}
	}	
	
	public function ucUserRegister($username,$password,$email) {
		$uid = $uc->uc_user_register($username, $password, $email);
		if($uid <= 0) {
			if($uid == -1) {
				echo '用户名不合法';
			} elseif($uid == -2) {
				echo '包含要允许注册的词语';
			} elseif($uid == -3) {
				echo '用户名已经存在';
			} elseif($uid == -4) {
				echo 'Email 格式有误';
			} elseif($uid == -5) {
				echo 'Email 不允许注册';
			} elseif($uid == -6) {
				echo '该 Email 已经被注册';
			} else {
				echo '未定义';
			}
		} else {
			echo '注册成功';
		}		
	}	
		
	public function ucUserLogin($username,$password) {
		list($uid, $username, $password, $email) = $uc->uc_user_login($username, $password);
		if($uid > 0) {
			echo '登录成功';
		} elseif($uid == -1) {
			echo '用户不存在,或者被删除';
		} elseif($uid == -2) {
			echo '密码错';
		} else {
			echo '未定义';
		}
	}
	
	public function ucGetUser($username='noctis') {
		$uc = new \Ucenter\Client\Client();
		if($data = $uc->uc_get_user($username)) {
			list($uid, $username, $email) = $data;
			print_r($data);
		} else {
			echo '用户不存在';
		}		
	}	
	
	public function ucUserEdit($username,$oldpassword,$newpassword,$emailnew) {
		$ucresult = $uc->uc_user_edit($username, $oldpassword, $newpassword,
		$emailnew);
		if($ucresult == -1) {
			echo '旧密码不正确';
		} elseif($ucresult == -4) {
			echo 'Email 格式有误';
		} elseif($ucresult == -5) {
			echo 'Email 不允许注册';
		} elseif($ucresult == -6) {
			echo '该 Email 已经被注册';
		}		
	}
	
	public function ucUserDelete($uid) {
		$ucresult = $uc->uc_user_delete($uid);
		if($ucresult == 1){
			echo '成功';
		}elseif($ucresult == 0){
			echo '失败';
		}
	}	
	
	public function ucUserDeleteavatar($uid) {
		$uc->uc_user_deleteavatar($uid);
	}	
	
	public function ucUserSynlogin($username='noctis',$password='freyo0729') {
		$uc = new \Ucenter\Client\Client();
		list($uid, $username, $password, $email) = $uc->uc_user_login($username, $password);
		if($uid > 0) {
			//echo '登录成功';
			echo $uc->uc_user_synlogin($uid);
		} elseif($uid == -1) {
			echo '用户不存在,或者被删除';
		} elseif($uid == -2) {
			echo '密码错';
		} else {
			echo '未定义';
		}	
	}	

	public function ucUserSynlogout() {
		$uc->ucUserSynlogout;
	}	

	public function ucUserCheckemail($email) {
		$ucresult = $uc->uc_user_checkemail($email);
		if($ucresult > 0) {
			echo 'Email 格式正确';
		} elseif($ucresult == -4) {
			echo 'Email 格式有误';
		} elseif($ucresult == -5) {
			echo 'Email 不允许注册';
		} elseif($ucresult == -6) {
			echo '该 Email 已经被注册';
		}		
	}		
	
	public function ucUserCheckname($username) {
		$ucresult = $uc->uc_user_checkname($username);
		if($ucresult > 0) {
			echo '用户名可用';
		} elseif($ucresult == -1) {
			echo '用户名不合法';
		} elseif($ucresult == -2) {
			echo '包含要允许注册的词语';
		} elseif($ucresult == -3) {
			echo '用户名已经存在';
		}	
	}	
	
	public function ucUserAddprotected($username,$admin) {
		
	}	
	
	public function ucUserDeleteprotected($username) {
		
	}

	public function ucUserGetprotected() {
		$uc = new \Ucenter\Client\Client();
		$ucresult = $uc->uc_user_getprotected();
		print_r($ucresult);
	}

	public function ucUserMerge($oldusername,$newusername,$uid,$password,$email) {
		
	}

	public function ucUserMergeRemove($username) {
		
	}
	
	public function ucUserGetcredit($appid,$uid,$credit) {
		
	}	
}
