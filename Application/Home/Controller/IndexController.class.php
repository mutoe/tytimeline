<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController {
	public function index() {
		// 初始化首页轮播图
		$banner = M('banner');
		$ban = $banner -> where('status=1') -> order('sort desc') -> select();
		$this -> assign('banner', $ban);

		// 初始化分享信息
		$share = M('share');
		$data = $share -> limit(50) -> order('click desc') -> select();
		$this -> assign('data', $data);

		// 初始化“喜欢”信息
		if($user_id = is_login()) {
			$user_info = M('user_info');
			$like = $user_info -> where('user_id=%d', $user_id) -> getField('like_share');
			$like_arr = explode(',', $like);
			$this -> assign('like', $like_arr);
		}

		$this -> display();
	}


}
