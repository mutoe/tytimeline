<?php
namespace Home\Controller;
use Think\Controller;
class GroundController extends Controller {

	public function index($content) {
		if($content == "new")				{$order = "create_time desc";	$loc = "最新";}
		elseif ($content == "hot")	{$order = "click desc";				$loc = "最热";}
		elseif ($content == "rand")	{$order = "RAND()";						$loc = "随机推荐";}
		else												{$order = "create_time desc";	$loc = "最新";}

		// 初始化分享信息
		$share = M('share');
		$data = $share -> limit(50) -> order( $order ) -> select();
		$this -> assign('data', $data);

		// 初始化“喜欢”信息
		if($user_id = is_login()) {
			$user_info = M('user_info');
			$like = $user_info -> where('user_id=%d', $user_id) -> getField('like_share');
			$like_arr = explode(',', $like);
			$this -> assign('like', $like_arr);
		}

		// 当前位置
		$this -> assign('location', $loc);

		$this -> display();
	}

}
