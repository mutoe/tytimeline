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
			$password = md5(I('post.password'));
			//执行登录，检查用户名密码
			$result = $this -> login($name, $password);
			if ($result > 0) {
				//登陆成功
				if(I('post.remember-me')){
					cookie('user_id', $result);
					cookie('user_mm', $this -> getmm($result));
				}
				session('user_id', $result);
				$this -> set_loginfo($this -> get_user());//更新登录信息
				$this -> success('登陆成功', U('Index/index'), 2);
			} elseif ($result == -1) {
				//登陆失败:没有找到用户
				$this -> error('登陆失败:不存在此用户');
			} elseif($result == -2) {
				//登陆失败:密码错误
				$this -> error('登陆失败：密码错误');
				$temp = I('cookie.login_error', 0);
				cookie('login_error', $temp++, 3600);
			}
		}
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

	public function sign() {
		$this -> display();
	}

	public function checkName() {
		$userModel = D("User");
		$findName = I('nickname');
		$data = $userModel -> where(array('nickname' => $findName)) -> find();
		if (!$data) {
			$this -> success('用户名可用~', true);
			//第二个参数说明该提交为ajax提交
		} else {
			$this -> error('用户名重复！', true);
		}
	}

	public function checkEmail() {
		$userModel = D("User");
		$findEmail = I('email');
		$data = $userModel -> where(array('email' => $findEmail)) -> find();
		if (!$data) {
			$this -> success('邮箱可用~', true);
			//第二个参数说明该提交为ajax提交
		} else {
			$this -> error('邮箱重复！', true);
		}
	}

	/**
	 * 提交注册
	 */
	public function checkSign() {
		$user = D("User");
		if (!$user -> create($_POST, 1)) {// 如果创建失败 抛出异常
			$this -> error('注册失败，请联系管理员；<br />'+ $user -> getError(), '', 5);
		} else {// 验证通过
			$user_id = $user -> add();

			//初始化用户信息
			$user_info = D('user_info');
			$user_info -> user_id = $user_id;
			$user_info -> add();

			//注册完毕后登陆
			session('user_id', $user_id);

			$this -> success('注册成功', U('Index/index'), 1);
		}
	}

}
