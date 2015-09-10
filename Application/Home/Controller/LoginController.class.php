<?php
namespace Home\Controller;
use Think\Controller;

class LoginController extends BaseController {

	protected function _empty(){
		header("HTTP/1.0 404 Not Found");
		$this -> display('Common:404');
	}

	protected function _initialize() {
		//判断是否已经登陆
		if (I('session.user_id', 0)) {
			$this -> redirect('User/index', 0);
		}
		if(I('cookie.login_error', 0) >= 5) {
			$this -> error('由于您连续输错5次密码，已暂时禁止你的登录请求，请一小时后重试;', 5);
		}

	}
	// 用户登录
	public function index() {
		// 判断有无参数
		if (!I('post.nickname', 0)) {
			$this -> display();
		} else {
			// 获取参数
			$name = strtolower(I('post.nickname'));
			$password = I('post.password');
			$password_md5 = md5($password);
			// 执行登录，检查用户名密码
			$result = $this -> login($name, $password_md5);
			if ($result > 0) {
				// 登陆成功

				/* UC同步登陆 */
/*			$uc = new \Ucenter\Client\Client();
				$name = mb_convert_encoding($name,'gbk','utf-8');
				$password = mb_convert_encoding($password,'gbk','utf-8');
				list($uid, $username, $password, $email) = $uc->uc_user_login($name, $password);
				if($uid > 0){
					echo $uc->uc_user_synlogin($uid);
				}*/

				//TODO: 记住我
				//if(I('post.remember-me')){
					//cookie('user_id', $result);
					//cookie('user_mm', $this -> getmm($result));
				//}
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
					$username = strtolower($username);
					$this -> sign_from_uc($uid, $username, $uc_password, $email);
					$this -> init_user_info($uid);	// 初始化用户信息

					// 开始登陆
					//if(I('post.remember-me')){
					//	cookie('user_id', $uid);
					//	cookie('user_mm', $this -> getmm($uid));
					//}
					session('user_id', $uid);
					$this -> set_loginfo($uid);//更新登录信息
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
				// 登陆失败:密码错误
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
	 * 初始化用户信息
	 */
	private function init_user_info($user_id) {
			$user_info = M('user_info');
			$user_info -> user_id = $user_id;
			$result = $user_info -> add();

      $album = M('album');
      $data['create_time'] = time();
      $data['user_id'] = $user_id;
      $data['title'] = "默认图集";
      $result = $album -> add($data);
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

	/**
	 * 外网用户注册
	 */
	public function register() {
		$this -> display();
	}

	public function checkRegister() {
		$data = I('post.');

		// 检查用户名密码是否被占用
		$uc = new \Ucenter\Client\Client();
		$temp['email'] = mb_convert_encoding($data['email'],'gbk','utf-8');
		$ucresult = uc_user_checkemail($temp['email']);
		if($ucresult == -6) $this -> error('该 Email 已经被注册');
		$temp['nickname'] = mb_convert_encoding($data['nickname'],'gbk','utf-8');
		$ucresult = uc_user_checkname($temp['nickname']);
		if($ucresult == -3) $this -> error('用户名已经存在');

		// 通过检查，开始注册
		$user = M("User");
		$max_id = $user -> max('user_id');
		if($max_id < 10000000) $uid = 10000001;
		else $uid = $max_id++;
		$data['user_id'] = $uid;
		$data['nickname'] = $data['nickname'];
		$data['password'] = md5($data['password']);
		$data['email'] = $data['email'];
		$data['create_time'] = time();
		$data['lastlogin_time'] = time();
		$data['group_id'] = 9;	// 设置用户组为非在校用户
		$result = $user -> add($data);
		if($result) {
			$this -> init_user_info($uid);	// 初始化用户信息
			$this -> success($uid);
		} else {
			$this -> error("数据写入失败");
		}
	}

}
