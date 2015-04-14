<?php
namespace Home\Controller;
use Think\Controller;

class LoginController extends BaseController {
	protected function _initialize() {
		//判断是否已经登陆
		if (I('session.user_id', 0)) {
			$this -> redirect('User/index', 0);
		}
		if(I('cookie.login_error', 0) >= 5) {
			$this -> error('由于您连续输错5次密码，已暂时禁止你的登录请求，请一小时后重试;', 5);
		}

	}
	//用户登录
	public function index() {
		//判断有无参数
		if (!I('post.nickname', 0)) {
			$this -> display();
		} else {
			//获取参数
			$name = I('post.nickname');
			$password = I('post.password');
			$password_md5 = md5($password);
			//执行登录，检查用户名密码
			$result = $this -> login($name, $password_md5);
			if ($result > 0) {
				//登陆成功
				
				/* UC同步登陆 */
				$uc = new \Ucenter\Client\Client();
				$name = mb_convert_encoding($name,'gbk','utf-8');
				$password = mb_convert_encoding($password,'gbk','utf-8');				
				$uc->uc_user_login($name, $password);
				$uc -> uc_user_synlogin($uid);

				//TODO: 记住我
				if(I('post.remember-me')){
					//cookie('user_id', $result);
					//cookie('user_mm', $this -> getmm($result));
				}
				session('user_id', $result);
				$this -> set_loginfo($result);//更新登录信息
				$this -> success('登陆成功', U('Index/index'), 2);
			} elseif ($result == -1) {
				/* UC开始 */
				$uc = new \Ucenter\Client\Client();
				$name = mb_convert_encoding($name,'gbk','utf-8');
				$password = mb_convert_encoding($password,'gbk','utf-8');
				list($uid, $username, $uc_password, $email) = $uc -> uc_user_login($name, $password);
				$uid = mb_convert_encoding($uid,'utf-8','gbk');
				$username = mb_convert_encoding($username,'utf-8','gbk');
				$uc_password = mb_convert_encoding($uc_password,'utf-8','gbk');
				$email = mb_convert_encoding($email,'utf-8','gbk');
				
				if($uid > 0) {
					$user_id = $this -> sign_from_uc($uid, $username, $uc_password, $email);
					$this -> init_user_info($user_id);//初始化用户信息

					//开始登陆
					if(I('post.remember-me')){
					//	cookie('user_id', $user_id);
					//	cookie('user_mm', $this -> getmm($user_id));
					}
					session('user_id', $user_id);
					$this -> set_loginfo($user_id);//更新登录信息
					$this -> success('登陆成功', U('Index/index'), 2);
				} elseif($uid == -1) {
					$this -> error('登陆失败:用户不存在,或者被删除');
				} elseif($uid == -2) {
					$this -> error('登陆失败:密码错');
				} else {
					$this -> error('登陆失败:未定义');
				}
				/* UC结束 */
			} elseif($result == -2) {
				//登陆失败:密码错误
				$this -> error('登陆失败：密码错误');
				$temp = I('cookie.login_error', 0);
				cookie('login_error', $temp++, 3600);
			}
		}
	}

	/**
	 * 从uc调回用户数据进行注册
	 */
	private function sign_from_uc($uid, $username, $password, $email) {
		$user = M("User");
		$data['user_id'] = $uid;
		$data['nickname'] = $username;
		$data['password'] = md5($password);
		$data['email'] = $email;
		$data['create_time'] = time();
		$data['lastlogin_time'] = time();
		$result = $user -> add($data);
		return $result;
	}

	/**
	 * 初始化用户信息表
	 */
	private function init_user_info($user_id) {
			$user_info = M('user_info');
			$user_info -> user_id = $user_id;
			$result = $user_info -> add();
			return $result;
	}

	/**
	 * 用户登陆操作
	 * @return int -1 用户名错误 -2 密码错误  成功则返回一个uid
	 */
	private function login($name, $password) {
		$user = M('User');
		$result = $user -> where("nickname='%s'", $name) -> find();
		if ($result['nickname'] == $name) {
			if ($result['password'] == $password) {
				return $result['user_id'];
			} else {
				return -2;
			}
		} else {
			return -1;
		}
	}


}
